<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints as Assert;

use App\Form\Shared\Type\WalletAmountType;

class WalletType extends AbstractType
{
    /**
     * @var int
     */
    private $walletThreshold;

    public function __construct(int $walletThreshold)
    {
        $this->walletThreshold = $walletThreshold;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'currentAmount',
                WalletAmountType::class,
                array(
                'label' => 'wallet.field.not_refundable',
                'constraints' => [
                  new Assert\NotBlank(),
                  new Assert\Range(array('min' => 1, 'max' => $this->walletThreshold))
                ]
              )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
