<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class PlacementType extends AbstractType
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Form configuration
     *
     * @param array $placementOptions
     */
    public function __construct(array $placementOptions)
    {
        $this->options = $placementOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['options'] = $this->options['options'];
        $view->vars['default'] = $this->options['default'];
        $view->vars['split_by'] = $this->options['split_by'];

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'image_placement';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }
}
