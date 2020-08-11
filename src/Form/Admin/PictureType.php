<?php

namespace App\Form\Admin;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pictureName', TextType::class, ['label' => 'admin.picture.name'])
            ->add('pictureDetail', CollectionType::class, array(
                'label' => false,
                'entry_type' => PictureDetailType::class,
                'entry_options' => array(
                  'label' => false
                ),
                'allow_add' => true,
                'allow_delete' => false,
                'by_reference' => false,
                'attr'         => [
                   'class' => "js-child-collection",
               ],
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
            'translation_domain' => 'admin',
            'attr' => ['class' => 'js-form']
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'picture';
    }
}
