<?php

namespace App\Form\Emmobilier;

use App\Entity\Picture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Form\Shared\Type\TextareaType;

class RefusedPictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', HiddenType::class, [
              'data' => Picture::REFUSED
            ])
            ->add('commentary', TextareaType::class, [
              'label' => 'orders.refuse_modal.commentary',
              'attr' => ['class' => 'js-commentary']
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
