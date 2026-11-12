<?php

namespace App\Controller\Admin;

use App\Entity\MetodoPago;
use App\Form\MetodoPagoType;
use App\Lista\MetodoPagoLista;
use App\Repository\MetodoPagoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Trendix\AdminBundle\Controller\BaseController;

/**
 * @Route("/metodo/pago")
 */
class MetodoPagoController extends BaseController
{
    /**
     * @Route("/", name="metodo_pago_index")
     */
    public function index(MetodoPagoRepository $metodoPagoRepository): Response
    {
        return $this->generateListWithRenderView(new MetodoPagoLista(), 'Listado de Métodos de Pago');
    }

    /**
     * @Route("/new", name="metodo_pago_new", methods={"GET","POST"})
     * @Route("/edit/{id}", name="metodo_pago_edit", methods={"GET","POST"})
     */
    public function new(Request $request, $id = null): Response
    {
        $this->initializeEntityManager();
        $metodoPago = new MetodoPago();
        if (null !== $id) {
            $metodoPago = $this->em->getRepository('App:MetodoPago')->find($id);
        }
        $form = $this->createForm(MetodoPagoType::class, $metodoPago);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($metodoPago);
            $entityManager->flush();

            return $this->redirectToRoute('metodo_pago_index');
        }

        return $this->render('metodo_pago/new.html.twig', [
            'metodo_pago' => $metodoPago,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="metodo_pago_show", methods={"GET"})
     */
    public function show(MetodoPago $metodoPago): Response
    {
        return $this->render('metodo_pago/show.html.twig', [
            'metodo_pago' => $metodoPago,
        ]);
    }
}
