<?php


namespace App\Controller\Front;

use App\Entity\Evento;
use App\Entity\Proveedor;
use App\Form\ContactoType;
use App\Form\ParticipaType;
use App\Form\Step2Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;
use Trendix\AdminBundle\Controller\BaseController;
use Trendix\AdminBundle\Entity\NewsletterSubscriber;
use Trendix\AdminBundle\Entity\User;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dashboard');
    }
}
