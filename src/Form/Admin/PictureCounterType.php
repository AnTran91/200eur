<?php

namespace App\Form\Admin;

use App\Entity\PictureCounter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Form\Admin\EventListener\PictureCounterTypeListener;

class PictureCounterType extends AbstractType
{
    /**
     * @var array
     */
    private $promoTypes;

    /**
     * Constructor
     *
     * @param array $promoTypes
     */
    public function __construct(array $promoTypes)
    {
        $this->promoTypes = $promoTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('promoCode', TextType::class, [
              'label' => 'admin.promo.code',
              'attr' => ['autocomplete' => 'off'],
            ])
            ->add('startDate', DateType::class, [
              'label' => 'admin.common.start_date',
              'widget' => 'single_text',
              // prevents rendering it as type="date", to avoid HTML5 date pickers
              'html5' => false,
              'format' => 'dd/MM/yyyy',
              // adds a class that can be selected in JavaScript
              'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off'],
            ])
            ->add('endDate', DateType::class, [
              'label' => 'admin.common.end_date',
              'widget' => 'single_text',
              // prevents rendering it as type="date", to avoid HTML5 date pickers
              'html5' => false,
              'format' => 'dd/MM/yyyy',
              // adds a class that can be selected in JavaScript
              'attr' => ['class' => 'js-datepicker', 'autocomplete' => 'off'],
            ])
            ->add('expired', CheckboxType::class, ['label' => 'admin.promo.expired', 'attr' => ['class' => 'form-check-input']])
            ->add('promoType', ChoiceType::class, [
              'label' => false,
              'choices' => $this->promoTypes,
              'attr' => ['class' => 'js-promo-type']
            ])
            ->add('promotionsPerRetouch', CollectionType::class, array(
                'label' => false,
                'entry_type' => PictureCounterPerRetouchType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr'         => ['class' => "js-collection"]
            ))
            ->addEventSubscriber(new PictureCounterTypeListener())
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PictureCounter::class,
            'translation_domain' => 'admin'
        ]);
    }
}
