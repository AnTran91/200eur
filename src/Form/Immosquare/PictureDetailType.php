<?php

namespace App\Form\Immosquare;


use App\Entity\Retouch;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('service', EntityType::class, [
                'class' => Retouch::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->where('r.appType = :retouch_type')
                        ->setParameter('retouch_type', Retouch::IMMOSQUARE_TYPE)
                        ;
                },
                'choice_value' => function (Retouch $entity = null) {
                    return $entity ? $entity->getRetouchCode() : '';
                },
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('settings', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }

}