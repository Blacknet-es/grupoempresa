<?php

namespace App\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Trendix\AdminBundle\Controller\BaseController;

class DashboardController extends BaseController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function indexAction()
    {
        $this->initializeEntityManager();
        $totalSocios = $this->em->getRepository('App:Socio')->countActivos();
        $siguienteNumSocio = $this->em->getRepository('App:Socio')->getNewNumSocio();

        $ficheros = $this->em->getRepository('App:File')->findAll();

        $this->addData([
            'socios_activos' => $totalSocios,
            'siguiente_num_socio' => $siguienteNumSocio,
            'ficheros' => $ficheros
        ]);
        return $this->render('admin/dashboard.html.twig', $this->getData());
    }
}
