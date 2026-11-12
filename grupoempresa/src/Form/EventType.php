<?php

namespace App\Form;

use App\Entity\Area;
use App\Entity\Delegacion;
use App\Entity\Event;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->em = $options['em'];

        $builder
            ->add('Nombre')
            ->add('direccion')
            ->add('fechaEvento', DateType::class, ['widget' => 'single_text'])
            ->add('zona', ChoiceType::class, [
                'label' => 'Zona / Barriada',
                'choices' => $this->getBarriadas()
            ])
            ->add('programaElectoral')
            ->add('descripcion')
            ->add('area', EntityType::class, [
                'class' => Area::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.deleted = false')
                        ->orderBy('u.nombre', 'ASC');
                    }
                ]
            )
            ->add('delegacion', EntityType::class, [
                    'class' => Delegacion::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->where('u.deleted = false')
                            ->orderBy('u.nombre', 'ASC');
                    }
                ]
            )
            ->add('tags', EntityType::class, [
                    'class' => Tag::class,
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.nombre', 'ASC');
                    }
                ]
            )
            ->add('gruposFicheros', CollectionType::class, [
                'entry_type' => GrupoFicheroType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'required' => false
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
           $form = $event->getForm();
           $form->add('zona', TextType::class);
        });
    }

    private function getBarriadas()
    {
        $dql = "SELECT DISTINCT e.zona FROM App:Event e ORDER BY e.zona ASC";
        $barriadas = $this->em->createQuery($dql)->getArrayResult();

        $result = [];

        foreach ($barriadas as $barriada) {
            $result[$barriada['zona']] = $barriada['zona'];
        }

        return $result;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'em' => null
        ]);
    }
}
