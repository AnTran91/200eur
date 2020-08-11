<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class PaymentCardType extends AbstractType
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Form configuration
     *
     * @param array $paymentCardOptions
     */
    public function __construct(array $paymentCardOptions)
    {
        $this->options = $paymentCardOptions;
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
        return 'payment_card';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
