<?php

namespace App\Form;

use App\Entity\MetodoPago;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetodoPagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('clave', null, ['label' => 'Código Interno'])
            ->add('periodo', null, ['label' => 'Periodo (en Meses)'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MetodoPago::class,
        ]);
    }
}
