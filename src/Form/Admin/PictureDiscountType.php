<?php

namespace App\Form\Admin;

use App\Entity\PictureDiscount;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Validator\Constraints as Assert;
use App\Form\Admin\EventListener\PictureDiscountTypeListener;

class PictureDiscountType extends AbstractType
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
            ->add('promoType', ChoiceType::class, [
              'label' => false,
              'choices' => $this->promoTypes,
              'attr' => ['class' => 'js-promo-type']
            ])

            ->add('hasNumberOfUse', CheckboxType::class, [
              'label' => 'admin.promo.has_number_of_use',
              'attr' => ['class' => 'js-update-the-form']
            ])
            ->add('useLimitPerUser', IntegerType::class, [
              'label' => 'admin.promo.use_limit_pre_user'
            ])
            ->add('useLimit', IntegerType::class, [
              'label' => 'admin.promo.use_limit',
              'constraints' => [
                new Assert\NotBlank()
              ]
            ])
            ->add('minimumImage', TextType::class, [
              'label' => 'admin.promo.minimum.image',
              'attr' => ['autocomplete' => 'off'],
            ])
            ->add('minimumPrestation', TextType::class, [
              'label' => 'admin.promo.minimum.prestation',
              'attr' => ['autocomplete' => 'off'],
            ])

            ->add('expired', CheckboxType::class, ['label' => 'admin.promo.expired', 'attr' => ['class' => 'form-check-input']])

            ->add('promotionsPerRetouch', CollectionType::class, array(
                'label' => false,
                'entry_type' => PictureDiscountPerRetouchType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr'         => ['class' => "js-collection"]
            ))
            ->addEventSubscriber(new PictureDiscountTypeListener())
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PictureDiscount::class,
            'translation_domain' => 'admin',
        ]);
    }
}
