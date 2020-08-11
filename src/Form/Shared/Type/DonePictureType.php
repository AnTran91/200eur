<?php

namespace App\Form\Shared\Type;

use App\Entity\Picture;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

class DonePictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pictureName', TextType::class, ['label' => $options['label'], 'attr' => ['class' => 'js-file-name']])
            ->add('status', PictureStatusType::class, ['label' => 'admin.picture.status'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            'translation_domain' => 'admin',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'done_picture';
    }
}
