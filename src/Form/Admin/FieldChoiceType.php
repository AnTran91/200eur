<?php

namespace App\Form\Admin;

use App\Entity\FieldChoices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Constraints as Assert;

class FieldChoiceType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * FieldChoiceType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choiceLabel', TextType::class, [
              'label' => 'admin.field_choice.choice_label'
            ])
            ->add('choiceValue', TextType::class, [
              'label' => 'admin.field_choice.choice_value'
            ])
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )
        ;
    }

    public function onPreSetData(FormEvent $event)
    {
        $choices = $event->getData();
        $form = $event->getForm();

        $choiceLabel = null;

        if (!is_null($choices) && $choices->getId() !== null) {
          $repository = $this->entityManager->getRepository('Gedmo\Translatable\Entity\Translation');
          $translations = $repository->findTranslations($choices);

          if (isset($translations['en'])) {
            $choiceLabel = $translations['en']['choiceLabel'] ?? $choiceLabel;
          }
        }

        $form->add('choiceLabelEn', TextType::class, [
                'label' => 'admin.field_choice.choice_label_en',
                'data' => $choiceLabel,
                'constraints' => [new Assert\NotBlank()]
              ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'choices_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FieldChoices::class,
            'translation_domain' => 'admin'
        ]);
    }
}
