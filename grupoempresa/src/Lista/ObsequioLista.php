<?php

namespace App\Lista;

use App\Entity\Socio;
use Trendix\AdminBundle\Component\Lista\DataType\BooleanType;
use Trendix\AdminBundle\Component\Lista\DataType\TextType;
use Trendix\AdminBundle\Component\Lista\ListaAbstractBuilder;
use Trendix\AdminBundle\Component\Lista\ListaAbstractType;

class ObsequioLista extends ListaAbstractType
{
    public function buildList(ListaAbstractBuilder $builder, array $options)
    {
        $builder
            ->add('numSocio', TextType::class, ['label' => 'Número de socio'])
            ->add('nombre', TextType::class, ['label' => 'Nombre'])
            ->add('apellidos', TextType::class, ['label' => 'Apellidos'])
            ->add('telefono', TextType::class, ['label' => 'Teléfono'])
            ->add('telefono2', TextType::class, ['label' => 'Teléfono 2'])
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add('id', TextType::class, ['label' => 'Id'])


            ->addAction('edit', 'edit', [
            'route' => 'socio_edit',
            'route_list_params' => ['id' => 'id']
            ])
            ->addAction('show', 'eye', [
                'route' => 'socio_show',
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
            'searchableFields' => ['id', 'nombre', 'apellidos', 'dni', 'telefono'],
            'strictSearch' => false,
            'permanent_filter' => 'a.activo = true',
            'downloadable' => true
        );
    }
}