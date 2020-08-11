<?php

namespace App\Form\Admin;

use App\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityManagerInterface;

class FieldType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $dynamicFormTypeOptions;
    private $paramsValidationType;


    public function __construct(EntityManagerInterface $entityManager, array $dynamicFormType, $paramsValidationType)
    {
        $this->entityManager = $entityManager;
        $this->dynamicFormTypeOptions = array_flip($dynamicFormType);
        $this->paramsValidationType = $paramsValidationType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
              'label' => 'admin.field.name'
            ])
            ->add('labelText', TextType::class, [
              'label' => 'admin.field.label_text'
            ])
            ->add('emptyData', TextType::class, [
              'label' => 'admin.field.empty_data'
            ])
            ->add('mapped', CheckboxType::class, [
              'label' => 'admin.field.mapped'
            ])
            ->add('price', MoneyType::class, [
              'label' => 'admin.field.price'
            ])
            ->add('addThePriceWhenValueEqualsTo', TextType::class, [
              'label' => 'admin.field.field_price_equal_to'
            ])
            ->add('fieldType', ChoiceType::class, [
              'label' => 'admin.field.field_type',
              'attr' => ['class' => 'js-field-type'],
              'choices' => $this->dynamicFormTypeOptions
            ])
            ->add('orderNumber', IntegerType::class, [
              'label' => 'admin.field.order_number'
            ])
            ->add('HTMLClass', TextType::class, [
              'label' => 'admin.field.html_class'
            ])
            ->add('disabled', CheckboxType::class, [
              'label' => 'admin.field.disabled'
            ])
            ->add('disabledOn', EntityType::class, [
              'class' => Field::class,
              'label' => 'admin.field.disabled_on',
              'placeholder' => 'admin.common.none',
              'group_by' => function(Field $choiceValue, $key, $value) {
                return $choiceValue->getFieldGroup()->getLabelText();
              },
            ])
            ->add('choices', CollectionType::class, array(
                'label' => false,
                'entry_type' => FieldChoiceType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr'         => ['class' => "js-choices-collection"]
            ))
            ->add('renovations', CollectionType::class, array(
                'label' => false,
                'entry_type' => FieldRenovationFormType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr'         => ['class' => "js-renovations-collection"]
            ))
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )

        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $field = $event->getData();
        $form = $event->getForm();

        $labelText = null;

        if (!is_null($field) && $field->getId() !== null) {
          $repository = $this->entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
          $translations = $repository->findTranslations($field);

          if (isset($translations['en'])) {
            $labelText = $translations['en']['labelText'] ?? $labelText;
          }
        }

        $form->add('labelTextEn', TextType::class, [
                'label' => 'admin.field.label_text_en',
                'data' => $labelText
              ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'field_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Field::class,
            'translation_domain' => 'admin'
        ]);
    }
}
