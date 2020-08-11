<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class WalletAmountType extends AbstractType
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Form configuration
     *
     * @param array $walletAmountOptions
     */
    public function __construct(array $walletAmountOptions)
    {
        $this->options = $walletAmountOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['options'] = $this->options['options'];
        $view->vars['default'] = $this->options['default'];

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'amount_wallet';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return MoneyType::class;
    }
}
