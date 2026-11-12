<?php

namespace App\Lista;

use App\Entity\File;
use Trendix\AdminBundle\Component\Lista\DataType\TextType;
use Trendix\AdminBundle\Component\Lista\ListaAbstractBuilder;
use Trendix\AdminBundle\Component\Lista\ListaAbstractType;

class FileLista extends ListaAbstractType
{
    public function buildList(ListaAbstractBuilder $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('nombre', TextType::class, ['label' => 'Nombre'])

            ->addAction('edit', 'edit', [
                'route' => 'file_edit',
                'route_list_params' => ['id' => 'id']
            ])
            ->addAction('primary', 'plus', [
                'route' => 'file_new'
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
        return File::class;
    }
}