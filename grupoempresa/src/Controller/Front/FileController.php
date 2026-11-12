<?php


namespace App\Controller\Front;

use App\Entity\Evento;
use App\Form\ContactoType;
use App\Form\ParticipaType;
use App\Form\Step2Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trendix\AdminBundle\Controller\BaseController;
use Trendix\AdminBundle\Entity\NewsletterSubscriber;
use Trendix\AdminBundle\Entity\User;

class FileController extends BaseController
{
    protected function getUploadsPath()
    {
        return '/uploads/';
    }

    protected function getUploadsDir()
    {
        $dir = __DIR__ . '/../../../public' . $this->getUploadsPath();
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    protected function getPrivatePath()
    {
        return '/documents/';
    }

    protected function getPrivatesDir()
    {
        $dir = __DIR__ . '/../../..' . $this->getPrivatePath();
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * @param $file UploadedFile
     * @param $key
     * @return string
     */
    public function saveFile($file, $private = false): string
    {
        if ($file) {
            $fileName = 'document' . uniqid() . '.' . $file->guessExtension();

            $dir = $private ? $this->getPrivatesDir() : $this->getUploadsDir();
            $file->move(
                $dir,
                $fileName
            );
            return $fileName;
        }
        return '';
    }

    /**
     * @Route("/files/sede", name="subir_pdf_sede")
     */
    public function sedeAction(Request $request)
    {
        $file = $request->files->get('file');
        if($file) {
            $name = $this->saveFile($file, true);
            return new JsonResponse([
                'success' => true,
                'fileName' => $name
            ]);
        }
        return new JsonResponse([
            'success' => false,
            'fileName' => ''
        ]);
    }

    /**
     * @Route("/files/public", name="subir_publico")
     */
    public function publicAction(Request $request)
    {
        $file = $request->files->get('file');
        if($file) {
            $name = $this->saveFile($file, false);
            return new JsonResponse([
                'success' => true,
                'fileName' => $name
            ]);
        }
        return new JsonResponse([
            'success' => false,
            'fileName' => ''
        ]);
    }

}
