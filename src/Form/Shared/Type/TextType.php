<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType as BaseTextType;

class TextType extends BaseTextType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], ['placeholder' => $view->vars['label']]);
        $view->vars['label'] = false;
    }
}
