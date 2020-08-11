<?php

namespace App\Form\Admin;

use App\Entity\FieldRenovationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraints as Assert;

class FieldRenovationFormType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('typeName', TextType::class, [
              'label' => 'admin.renovation.type_name'
            ])
            ->add('fieldRenovationChoices', CollectionType::class, array(
                'label' => false,
                'entry_type' => FieldRenovationChoicesType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr'         => ['class' => "js-renovations-child-collection"]
            ))
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $fieldRenovation = $event->getData();
        $form = $event->getForm();

        $typeName = null;

        if (!is_null($fieldRenovation) && $fieldRenovation->getId() !== null) {
          $repository = $this->entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
          $translations = $repository->findTranslations($fieldRenovation);

          if (isset($translations['en'])) {
            $typeName = $translations['en']['typeName'] ?? $typeName;
          }
        }

        $form->add('typeNameEn', TextType::class, [
                'label' => 'admin.renovation.type_name_en',
                'data' => $typeName,
                'constraints' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])]
              ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'field_renovation_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FieldRenovationType::class,
        ]);
    }
}
