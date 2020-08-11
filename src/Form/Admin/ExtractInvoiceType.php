<?php
namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\User;


class ExtractInvoiceType extends AbstractType
{
    /**
     * @var array
     */
    private $paymentPeriod;

    /**
     * ExtractInvoiceType constructor.
     *
     * @param array $paymentPeriod
     */
    public function __construct(array $paymentPeriod)
    {
        $this->paymentPeriod = $paymentPeriod;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', TextType::class, [
                          'label' => 'admin.common.date',
                          'constraints' => [new Assert\NotNull()],
                          // adds a class that can be selected in JavaScript
                          'attr' => ['class' => 'js-datepicker form-control', 'autocomplete' => 'off'],
            ])
            ->add('user', EntityType::class, ['class' => User::class,
                                              'placeholder' => 'admin.common.all',
                                              'label' => 'admin.user.user'])
            ->add('paymentType', ChoiceType::class, ['label' => 'admin.order.payment_type',
                                                      'choices' => $this->paymentPeriod,
                                                      'placeholder' => 'admin.common.all_except_wallet_invoice'])
            ->add('zip', SubmitType::class, ['label' => 'Zip', 'attr' => ['class' => 'btn btn-primary'],])
            ->add('excel', SubmitType::class, ['label' => 'Excel', 'attr' => ['class' => 'btn btn-primary'],])
            ;
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
          'translation_domain' => 'admin',
        ]);
    }
}
