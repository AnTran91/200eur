<?php
/*
 * This file is part of the Emmobilier package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Shared\Type;

use App\Entity\FieldRenovationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\FieldRenovationChoices;

class ImageRenovationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	/** @var FieldRenovationType $renovationType */
	    foreach ($options['renovation_types'] as $renovationType) {
            $builder
              ->add(
                  $renovationType->getId(),
                  ChoiceType::class,
                  array(
                    'label' => $renovationType->getTypeName(),
                    'attr' => ['class' => 'js-renovation-choices'],
                    'choices' => array_merge([new FieldRenovationChoices()], $renovationType->getFieldRenovationChoices()->toArray()),
                    'choice_attr' => function (FieldRenovationChoices $entity, $key, $value) {
                        // adds a class like attending_yes, attending_no, etc
                        if (!is_null($entity->getPicturePath())) {
                          return ['data-img-src' => $entity->getPicturePath()];
                        }
                        return [];
                    },
                    'choice_label' => function (FieldRenovationChoices $entity) {
                        return $entity->getId();
                    },
                    'choice_value' => function ($entity = null) {
                      if ($entity instanceOf FieldRenovationChoices) {
                        return $entity->getId();
                      }
                      return $entity;
                  },
                  )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'renovation_types' => [],
            'html5' => false,
            'by_reference' => false,
            'error_bubbling' => false,
            'data_class' => null,
            'empty_data' => null,
            'compound' => true
        ));

        $resolver->setAllowedTypes('renovation_types', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'image_renovation_type';
    }
}
