<?php

namespace App\Lista;

use App\Entity\Socio;
use Trendix\AdminBundle\Component\Lista\DataType\BooleanType;
use Trendix\AdminBundle\Component\Lista\DataType\TextType;
use Trendix\AdminBundle\Component\Lista\ListaAbstractBuilder;
use Trendix\AdminBundle\Component\Lista\ListaAbstractType;

class BajasLista extends ListaAbstractType
{
    public function buildList(ListaAbstractBuilder $builder, array $options)
    {
        $builder
            ->add('numSocio', TextType::class, ['label' => 'Número de socio'])
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('apellidos', TextType::class, ['label' => 'Apellidos'])
            ->add('dni', TextType::class, ['label' => 'DNI'])
            ->add('ciudad', TextType::class, ['label' => 'Ciudad'])
            ->add('activo', BooleanType::class, ['label' => 'Activo'])
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
            'permanent_filter' => 'a.activo = false',
            'downloadable' => true
        );
    }
}