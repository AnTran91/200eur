<?php

namespace App\Form\Admin;

use App\Entity\FieldGroup;
use App\Entity\Retouch;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FieldGroupType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $dynamicFormPosition;
	
	/**
	 * @var TranslatorInterface
	 */
	private $translator;

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, array $dynamicFormPostion)
    {
        $this->entityManager = $entityManager;
        $this->dynamicFormPosition = array_flip($dynamicFormPostion);
	    $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('labelText', TextType::class, [
              'label' => 'admin.field_group.label_text'
            ])
            ->add('position', ChoiceType::class, [
              'label' => 'admin.field_group.position',
              'placeholder'=> 'admin.common.none',
              'choices' => $this->dynamicFormPosition
            ])
            ->add('orderNumber', IntegerType::class, [
              'label' => 'admin.field_group.order_number'
            ])
            ->add('retouch', EntityType::class, [
              'class' => Retouch::class,
              'multiple' => true,
              'by_reference' => false,
              'group_by' => function(Retouch $choice) {
		          return $this->translator->trans($choice->getAppType(), [], 'admin');
              },
              'label' => 'admin.field_group.retouch',
              'placeholder' => 'admin.common.show_all',
              'attr' => ['class' => 'js-multi-select-selector']
            ])
            ->add('fields', CollectionType::class, array(
                'label' => false,
                'entry_type' => FieldType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr'         => ['class' => "js-fields-collection"]
            ))
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $fieldGroup = $event->getData();
        $form = $event->getForm();

        $labelText = null;

        if (!is_null($fieldGroup) && $fieldGroup->getId() !== null) {
          $repository = $this->entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
          $translations = $repository->findTranslations($fieldGroup);

          if (isset($translations['en'])) {
            $labelText = $translations['en']['labelText'] ?? $labelText;
          }
        }

        $form->add('labelTextEn', TextType::class, [
          'label' => 'admin.field_group.label_text_en',
          'data' => $labelText,
          'constraints' => [new Assert\NotBlank()]
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FieldGroup::class,
            'translation_domain' => 'admin'
        ]);
    }
}
