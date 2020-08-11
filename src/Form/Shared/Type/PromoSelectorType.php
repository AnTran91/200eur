<?php

namespace App\Form\Shared\Type;

use App\Form\Shared\DataTransformer\CouponCodeToPromoTransformer;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class PromoSelectorType extends AbstractType
{
    private $transformer;

    public function __construct(CouponCodeToPromoTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'promo.msg.promo_not_found',
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'promo_selector';
    }
}
