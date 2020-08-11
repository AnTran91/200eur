<?php

namespace App\Form\Admin;

use App\Entity\PictureCounterPerRetouch;
use App\Entity\Retouch;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PictureCounterPerRetouchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageCounterLimit', NumberType::class, [
              'label' => 'admin.promo.image_counter_limit'
            ])
            ->add('imageCounterLimitWithReduction', NumberType::class, [
              'label' => 'admin.promo.image_counter_limit_with_reduction'
            ])
            ->add('imageCounterReduction', PercentType::class, [
              'type' => 'integer',
              'label' => 'admin.promo.image_counter_reduction'
            ])
            ->add('retouch', EntityType::class, [
              'class' => Retouch::class,
              'label' => 'admin.promo.retouchs',
	          'query_builder' => function (EntityRepository $er) {
		            return $er->createQueryBuilder('u')
			            ->where('u.appType = :appType')
			            ->setParameter(":appType", Retouch::EMMOBILIER_TYPE);
	          }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PictureCounterPerRetouch::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'picture_counter_per_retouch';
    }
}
