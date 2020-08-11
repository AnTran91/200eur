<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType as BaseTextareaType;

class TextareaType extends BaseTextareaType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], ['placeholder' => $view->vars['label'] , 'cols' => '15', 'rows' => '10']);
        $view->vars['label'] = false;
    }
}
