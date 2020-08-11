<?php

namespace App\Form\Admin\Filters;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionFilterType extends AbstractType
{
    /**
     * @var array
     */
    private $systemPayOptions;

    /**
     * TransactionFilterType constructor.
     *
     * @param array $systemPayStatus
     */
    public function __construct(array $systemPayStatus)
    {
        $this->systemPayOptions = $systemPayStatus;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('transactionNumber', IntegerType::class, [
                'label' => 'admin.transaction.number'
            ])
            ->add('date', DateIntervalType::class, [
                'label' => 'admin.common.date',
                'widget' => 'single_text',
                // adds a class that can be selected in JavaScript
                'attr' => ['class' => 'js-datepicker form-control', 'autocomplete' => 'off'],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => $this->systemPayOptions,
	            'attr' => ['class' => 'js-select-list'],
                'label' => 'admin.common.status'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
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