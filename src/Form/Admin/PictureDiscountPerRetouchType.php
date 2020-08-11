<?php

namespace App\Form\Admin;

use App\Entity\PictureDiscountPerRetouch;
use App\Entity\Retouch;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PictureDiscountPerRetouchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reduction', PercentType::class, [
              'label' => 'admin.promo.reduction',
              'type' => 'integer'
            ])
            ->add('imageLimitPerUser', IntegerType::class, [
              'label' => 'admin.promo.image_limit_pre_user'
            ])
	        ->add('imageLimitPerOrder', IntegerType::class, [
		        'label' => 'admin.promo.image_limit_pre_order'
	        ])
            ->add('imageLimit', IntegerType::class, [
              'label' => 'admin.promo.image_limit',
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
            'data_class' => PictureDiscountPerRetouch::class,
            'translation_domain' => 'admin',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'picture_discount_per_retouch';
    }
}
