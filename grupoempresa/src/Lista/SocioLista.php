<?php

namespace App\Lista;

use App\Entity\Socio;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Trendix\AdminBundle\Component\Lista\DataType\BooleanType;
use Trendix\AdminBundle\Component\Lista\DataType\JoinType;
use Trendix\AdminBundle\Component\Lista\DataType\TextType;
use Trendix\AdminBundle\Component\Lista\ListaAbstractBuilder;
use Trendix\AdminBundle\Component\Lista\ListaAbstractType;

class SocioLista extends ListaAbstractType
{
    public function buildList(ListaAbstractBuilder $builder, array $options)
    {
        $builder
            ->add('numSocio', TextType::class, ['label' => 'Número de socio'])
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('apellidos', TextType::class, ['label' => 'Apellidos'])
            ->add('dni', TextType::class, ['label' => 'DNI'])
            ->add('ciudad', TextType::class, ['label' => 'Ciudad'])
            ->add('metodoPago.nombre', JoinType::class, ['label' => 'M.Pago'])
            ->add('telefono2', TextType::class, ['label' => 'Móvil'])
            ->add('telefono', TextType::class, ['label' => 'Teléfono'])
            ->add('email', TextType::class, ['label' => 'Email'])
            //->add('activo', BooleanType::class, ['label' => 'Activo'])
            ->add('id', TextType::class, ['label' => 'Id'])


            ->addAction('edit', 'edit', [
            'route' => 'socio_edit',
            'route_list_params' => ['id' => 'id']
            ])
            ->addAction('show', 'eye', [
                'route' => 'socio_show',
                'route_list_params' => ['id' => 'id']
            ])
            ->addAction('baja', 'baja', [
                'route' => 'socio_baja',
                'route_list_params' => ['id' => 'id']
            ])
            ->addAction('primary', 'plus', [
                'route' => 'socio_new'
            ]);

        return $this;
    }

    public function getClass()
    {
        return Socio::class;
    }

    public function getOptions()
    {
        return array(
            'order_column' => 'a.numSocio',
            'order_direction' => 'DESC',
            'searchableFields' => ['id', 'nombre', 'apellidos', 'dni'],
            'strictSearch' => false,
            'permanent_filter' => 'a.activo = true',
            'downloadable' => true
        );
    }
}
