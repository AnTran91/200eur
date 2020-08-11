<?php

namespace App\Form\Admin\Filters;

use App\Entity\User;
use App\Entity\Organization;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Translation\TranslatorInterface;

class InvoiceFilterType extends AbstractType
{
    /**
     * @var array
     */
    private $invoiceTypes;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $appTypes;

    public function __construct(TranslatorInterface $translator, array $paymentPeriod, array $applicationsTypes)
    {
        $this->invoiceTypes = $paymentPeriod;
        $this->translator = $translator;
        $this->appTypes = $applicationsTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invoiceNumber', TextType::class, [
              'label' => 'admin.invoice.id'
            ])
            ->add('appType', ChoiceType::class, [
                'label' => 'admin.application.type',
                'placeholder' => 'admin.common.show_all',
                'choices' => $this->appTypes
            ])
            ->add('type', ChoiceType::class, [
              'label' => 'admin.invoice.types',
              'placeholder' => 'admin.common.none',
              'choices' => $this->invoiceTypes
            ])
            ->add('client', EntityType::class, [
              'class' => User::class,
              'label' => 'admin.order.client',
	          'attr' => ['class' => 'js-select-list'],
              'placeholder' => 'admin.common.show_all',
              'query_builder' => function (EntityRepository $er) {
                  return $er->createQueryBuilder('u')
                          ->addSelect('wallet')
                          ->join('u.wallet', 'wallet')
                          ->orderBy('u.email', 'ASC');
              }
            ])
            ->add('organization', EntityType::class, [
              'class' => Organization::class,
	          'attr' => ['class' => 'js-select-list'],
              'label' => 'admin.invoice.organization',
              'placeholder' => 'admin.common.show_all'
            ])
            ->add('creationDate', DateIntervalType::class, [
              'label' => 'admin.common.date',
              'widget' => 'single_text',
              // adds a class that can be selected in JavaScript
              'attr' => ['class' => 'js-datepicker form-control', 'autocomplete' => 'off'],
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
