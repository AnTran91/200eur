<?php

namespace App\Form\Admin\Filters;

use App\Entity\Promo;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class PromoTypeFilter extends AbstractType
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
                  'format' => 'MM/dd/yyyy',
                  // adds a class that can be selected in JavaScript
                  'attr' => ['class' => 'js-datepicker form-control', 'autocomplete' => 'on'],
                ])
                ->add('endDate', DateType::class, [
                  'label' => 'admin.common.end_date',
                  'widget' => 'single_text',
                  // prevents rendering it as type="date", to avoid HTML5 date pickers
                  'html5' => false,
                  'format' => 'MM/dd/yyyy',
                  // adds a class that can be selected in JavaScript
                  'attr' => ['class' => 'js-datepicker form-control', 'autocomplete' => 'on'],
                ])
                ->add('promoType', ChoiceType::class, [
                  'label' => 'admin.promo.promo_type',
                  'choices' => $this->promoTypes,
                  'attr' => ['class' => 'js-promo-type']
                ])
                ->add('expired', CheckboxType::class, [
                  'label' => 'admin.promo.expired',
                  'attr' => ['class' => 'form-check-input']
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promo::class,
            'validation_groups' => false,
            'translation_domain' => 'admin',
            'csrf_protection' => false
        ]);
    }
	
	/**
	 * This will remove formTypeName from the form
	 *
	 * @return null|string
	 */
    public function getBlockPrefix(): ?string
    {
        return 'filter';
    }
}
