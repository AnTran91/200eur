<?php

namespace App\Form\Shared\Type;

use Symfony\Component\Form\Extension\Core\Type\BaseType;

class BlockTitleType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'block_title';
    }
}
