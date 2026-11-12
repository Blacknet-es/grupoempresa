<?php

namespace App\Controller\Admin;

use App\Entity\File;
use App\Form\FileType;
use App\Lista\FileLista;
use App\Repository\FileRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Trendix\AdminBundle\Controller\BaseController;

/**
 * @Route("/file")
 */
class FileController extends BaseController
{
    /**
     * @Route("/", name="file_index", methods={"GET"})
     */
    public function index(FileRepository $fileRepository): Response
    {
        return $this->generateListWithRenderView(new FileLista(), 'Listado de Ficheros');

    }

    /**
     * @Route("/new", name="file_new", methods={"GET","POST"})
     * @Route("/edit/{id}", name="file_edit", methods={"GET","POST"})
     */
    public function new(Request $request, $id = null): Response
    {
        $this->initializeEntityManager();
        if (null !== $id) {
            $file = $this->em->getRepository('App:File')->find($id);
        } else {
            $file = new File();
        }

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('fichero')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception($e->getMessage());
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $file->setUrl($newFilename);
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('file_index');
        }

        return $this->render('file/new.html.twig', [
            'file' => $file,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="file_delete", methods={"POST"})
     */
    public function delete(Request $request, File $file): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('file_index');
    }
}
