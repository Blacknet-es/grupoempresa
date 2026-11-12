<?php

namespace App\Controller\Admin;

use App\Entity\Socio;
use App\Form\SocioType;
use App\Lista\BajasLista;
use App\Lista\ObsequioLista;
use App\Lista\SocioLista;
use App\Repository\SocioRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Trendix\AdminBundle\Component\Lista\ListaFactory;
use Trendix\AdminBundle\Component\Menu\MenuGenerator;
use Trendix\AdminBundle\Controller\BaseController;
use Trendix\AdminBundle\Controller\Traits\DownloadExcelListTrait;

/**
 * @Route("/socio")
 */
class SocioController extends BaseController
{
    use DownloadExcelListTrait;

    /**
     * @Route("/", name="socio_index")
     * @Route("/ciudad/{ciudad}", name="socio_ciudad")
     * @Route("/metodopago/{metodopago}", name="socio_pago")
     */
    public function index(SocioRepository $socioRepository, $ciudad = null, $metodopago = null): Response
    {
        $this->initializeEntityManager();

        $permanent_filter = "a.activo = true";
        if (null !== $ciudad) {
            $permanent_filter.= " AND a.ciudad LIKE '%".$ciudad."%'";
        }

        if (null !== $metodopago) {
            $permanent_filter.= " AND a.metodoPago = ".$metodopago;
        }

        $lista = $this->createList(new SocioLista(), ['permanent_filter' => $permanent_filter]);
        $request = Request::createFromGlobals();

        $this->addData('lista', $lista->createView());
        if (null !== $ciudad) {
            $this->addData('title', 'Listado de Socios de '.$ciudad);
        } else {
            $this->addData('title', 'Listado de Socios');
        }

        if ($request->isMethod('POST')) {
            return $this->render('TrendixAdminBundle:Lista:lista.html.twig', $this->getData());
        } else {
            return $this->render('TrendixAdminBundle:Lista:index.html.twig', $this->getData());
        }
    }

    /**
     * @Route("/lista/baja", name="socio_baja_lista")
     */
    public function listaBajaAction(SocioRepository $socioRepository): Response
    {
        return $this->generateListWithRenderView(new BajasLista(), 'Listado de Socios de Baja');
    }

    /**
     * @Route("/lista/obsequio", name="socio_obsequio_lista")
     */
    public function listaObsequiAction(SocioRepository $socioRepository): Response
    {
        $this->initializeEntityManager();
        return $this->generateListWithRenderView(new ObsequioLista(), 'Listado de Socios para obsequio');
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/download/file", name="download_list")
     */
    public function downloadListAction(Request $request)
    {
        return parent::downloadListFile($request);
    }

    /**
     * @Route("/new", name="socio_new", methods={"GET","POST"})
     * @Route("/edit/{id}", name="socio_edit", methods={"GET","POST"})
     */
    public function new(Request $request, $id = null): Response
    {
        $this->initializeEntityManager();
        if (null !== $id) {
            $socio = $this->em->getRepository('App:Socio')->find($id);
        } else {
            $socio = new Socio();
            $socio->setNumSocio($this->em->getRepository('App:Socio')->getNewNumSocio());
            $socio->setActivo(true);
            $socio->setFechaAlta(new \DateTime());
        }

        $form = $this->createForm(SocioType::class, $socio);
        try {
            $form->handleRequest($request);
        } catch (\Exception $e) {
            return $this->render('socio/new.html.twig', [
                'socio' => $socio,
                'form' => $form->createView(),
                'error' => 'El formulario contiene Errores, revise que todos los datos son correctos'
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($socio);
            $entityManager->flush();

            return $this->redirectToRoute('socio_index');
        }

        return $this->render('socio/new.html.twig', [
            'socio' => $socio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ver/{id}", name="socio_show", methods={"GET"})
     */
    public function show(Socio $socio): Response
    {
        return $this->render('socio/show.html.twig', [
            'socio' => $socio,
        ]);
    }

    /**
     * @Route("/baja/{id}", name="socio_baja", methods={"GET"})
     */
    public function baja(Socio $socio): Response
    {
        $this->initializeEntityManager();
        $socio->setActivo(false)
            ->setFechaBaja(new \DateTime());
        $this->em->persist($socio);
        $this->em->flush();

        return $this->render('socio/show.html.twig', [
            'socio' => $socio,
        ]);
    }

    public static function getSubscribedServices()
    {
        $defaultServices = parent::getSubscribedServices();
        $customizedServices = [
            'admin.menu.generator' => '?'.MenuGenerator::class,
            'admin.lista.factory' => '?'.ListaFactory::class,
            'translator' => '?'.TranslatorInterface::class
        ];

        $services = array_merge($defaultServices, $customizedServices);
        return $services;
    }
}
