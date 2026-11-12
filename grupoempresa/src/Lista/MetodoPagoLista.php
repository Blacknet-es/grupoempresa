<?php

namespace App\Lista;

use App\Entity\MetodoPago;
use Trendix\AdminBundle\Component\Lista\DataType\TextType;
use Trendix\AdminBundle\Component\Lista\ListaAbstractBuilder;
use Trendix\AdminBundle\Component\Lista\ListaAbstractType;

class MetodoPagoLista extends ListaAbstractType
{
    public function buildList(ListaAbstractBuilder $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('nombre', TextType::class, ['label' => 'Nombre'])

            ->addAction('edit', 'edit', [
                'route' => 'metodo_pago_edit',
                'route_list_params' => ['id' => 'id']
            ])
        ->addAction('primary', 'plus', [
            'route' => 'metodo_pago_new'
        ]);
        ;

    }

    public function getOptions()
    {
        return array(
            //'permanent_filter' => 'a.deleted = false'
        );
    }

    public function getClass()
    {
        return MetodoPago::class;
    }
}