<?php


namespace App\Classes;


class Menu extends \Trendix\AdminBundle\Utility\Menu
{
    public function getMenu()
    {
        return array(
            array(
                'id' => 'dashboard',
                'label' => 'Pantalla principal',
                'icon' => 'home',
                'route' => 'dashboard'
            ),
            array(
                'id' => 'Socios',
                'label' => 'Socios',
                'icon' => 'accounts-alt',
                'submenu' => $this->getSociosSubmenu()
            ),
            array(
                'id' => 'Socios_por_ciudades',
                'label' => 'Socios por ciudades',
                'icon' => 'accounts-alt',
                'submenu' => $this->getSociosPorZonaSubmenu()
            ),
            array(
                'id' => 'Socios_por_pago',
                'label' => 'Socios por M. Pago',
                'icon' => 'accounts-alt',
                'submenu' => $this->getSociosPorPagoSubmenu()
            ),
            array(
                'id' => 'Ficheros',
                'label' => 'Ficheros',
                'icon' => 'file',
                'submenu' => $this->getFicherosSubmenu()
            ),
            array(
                'id' => 'metodos-pago',
                'label' => 'Métodos de Pago',
                'icon' => 'money',
                'submenu' => $this->getMetodoPagoSubmenu()
            )
        );
    }

    private function getSociosSubmenu()
    {
        return array(
            array(
                'label' => 'Socios Activos',
                'route' => 'socio_index',
            ),
            array(
                'label' => 'Socios de Baja',
                'route' => 'socio_baja_lista',
            ),
            array(
                'label' => 'Listado Para Obsequio',
                'route' => 'socio_obsequio_lista',
            ),
            array(
                'label' => 'Nuevo Socio',
                'route' => 'socio_new',
            ),
        );
    }

    private function getSociosPorZonaSubmenu()
    {
        $em = $this->container->get('doctrine')->getManager();
        $ciudades = $em->getRepository('App:Socio')->findCiudades();

        $submenu = [];
        foreach ($ciudades as $ciudad) {
            $submenu[] = [
                'label' => $ciudad['ciudad'],
                'route' => 'socio_ciudad',
                'params' => ['ciudad' => $ciudad['ciudad']]
            ];
        }

        return $submenu;
    }

    private function getSociosPorPagoSubmenu()
    {
        $em = $this->container->get('doctrine')->getManager();
        $metodos = $em->getRepository('App:MetodoPago')->findAll();

        $submenu = [];
        foreach ($metodos as $pago) {
            $submenu[] = [
                'label' => $pago->__toString(),
                'route' => 'socio_pago',
                'params' => ['metodopago' => $pago->getId()]
            ];
        }

        return $submenu;
    }

    private function getFicherosSubmenu()
    {
        return array(
            array(
                'label' => 'Listado',
                'route' => 'file_index',
            ),
            array(
                'label' => 'Nuevo',
                'route' => 'file_new',
            ),
        );
    }

    private function getMetodoPagoSubmenu()
    {
        return array(
            array(
                'label' => 'Listado',
                'route' => 'metodo_pago_index',
            ),
            array(
                'label' => 'Nuevo',
                'route' => 'metodo_pago_new',
            ),
        );
    }
}