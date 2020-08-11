<?php

namespace App\Form\Admin;

use App\Entity\Retouch;

use App\Form\Admin\EventListener\RetouchTypeListener;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RetouchType extends AbstractType
{
    /**
     * @var array
     */
    private $applicationsTypeOptions;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, array $applicationsTypes)
    {
        $this->applicationsTypeOptions = $applicationsTypes;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'admin.retouch.title_fr'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'admin.retouch.description_fr',
                'attr' => ['class' => 'js-wyswyg-selector']
            ])
            ->add('helpLink', TextType::class, [
                'label' => 'Youtube video link'
            ])
            ->add('appType', ChoiceType::class, [
                'choices' => $this->applicationsTypeOptions,
                'label' => 'admin.retouch.retouch_type',
                'attr' => ['class' => 'js-retouch-type']
            ])
            ->add('pricings', CollectionType::class, array(
                'label' => false,
                'entry_type' => RetouchPriceType::class,
                'entry_options' => array(
                    'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => [
                    'class' => "js-collection",
                ],
            ))
            ->addEventSubscriber(new RetouchTypeListener($this->entityManager))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Retouch::class,
            'allow_extra_fields' => true,
            'translation_domain' => 'admin',
            'validation_groups' => 'retouchCreation'
        ]);
    }
}
