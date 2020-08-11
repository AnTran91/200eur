<?php

namespace App\Form\Admin\Filters;

use App\Entity\Retouch;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Validator\Constraints as Assert;

class FieldGroupFilterType extends AbstractType
{
    /**
     * @var array
     */
    private $appTypeOptions;

    public function __construct(array $applicationsTypes)
    {
        $this->appTypeOptions = $applicationsTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('retouchType', ChoiceType::class, [
                'choices' => $this->appTypeOptions,
                'label' => 'admin.retouch.retouch_type',
                'attr' => ['class' => 'js-group-params-retouch-type']
            ])
            ->add('retouch', EntityType::class, [
                'class' => Retouch::class,
                'label' => 'admin.retouch.retouch',
                'choice_attr' => function (Retouch $entity, $key, $value) {
                    // adds a class like attending_yes, attending_no, etc
                    if (!is_null($entity->getAppType())) {
                        return ['class' => $entity->getAppType()];
                    }
                    return [];
                },
                'placeholder' => 'admin.common.none',
                'attr' => ['class' => 'js-group-params-retouch'],
                'constraints' => [new Assert\NotBlank()]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'admin',
            'csrf_protection' => false
        ]);
    }
}
