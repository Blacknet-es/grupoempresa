<?php

namespace App\Form;

use App\Entity\Socio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SocioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numSocio', TextType::class, [
                'label' => 'Número de socio (Modificar sólo en caso de que no coincida)',
                'constraints' => [new NotBlank()],
                ])
            ->add('nombre', null, [
                'constraints' => [new NotBlank()],
            ])
            ->add('apellidos', null, [
                'constraints' => [new NotBlank()],
            ])
            ->add('dni', null, [
                'constraints' => [new NotBlank()],
            ])
            ->add('fecha_nacimiento',  DateType::class, [
                'label' => 'Fecha de Nacimiento',
                'html5' => true,
                'widget' => 'single_text',
                'constraints' => [new NotBlank()]
            ])
            ->add('fecha_alta', DateType::class, [
                'label' => 'Fecha de Alta',
                'html5' => true,
                'widget' => 'single_text',
                'constraints' => [new NotBlank()]
            ])
            ->add('domicilio', null, [
                'constraints' => [new NotBlank()],
            ])
            ->add('ciudad', null, [
                'constraints' => [new NotBlank()],
            ])
            ->add('codigo_postal', null, [
                'constraints' => [new NotBlank()],
            ])
            ->add('provincia', null, [
                'constraints' => [new NotBlank()],
            ])
            ->add('telefono')
            ->add('telefono2', TextType::class, [
                'label' => 'Móvil',
                'constraints' => [new NotBlank()]
            ])
            ->add('email')
            ->add('activo', CheckboxType::class, ['label' => 'Activo (Desmarcar si es baja)'])
            ->add('fecha_baja',  DateType::class, [
                'label' => 'Fecha de Baja (Cólocar sólo si el usuario se ha dado de baja)',
                'html5' => true,
                'widget' => 'single_text'])
            ->add('metodoPago')
            ->add('modalidadPago', ChoiceType::class, [
                'label' => 'Frecuencia del Pago',
                'choices' => [
                    Socio::PAGO_MENSUAL => Socio::PAGO_MENSUAL,
                    Socio::PAGO_TRIMESTRAL => Socio::PAGO_TRIMESTRAL,
                    Socio::PAGO_ANUAL => Socio::PAGO_ANUAL
                ]
            ])
            ->add('cuota')
            ->add('cuentaBancaria', TextType::class, ['label' => 'Cuenta Bancaria', 'required' => false])
            ->add('difusion', CheckboxType::class, ['label' => 'Desea Recibir Notificaciones', 'required' => false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Socio::class,
        ]);
    }
}
