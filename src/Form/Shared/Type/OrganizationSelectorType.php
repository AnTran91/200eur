<?php

namespace App\Form\Shared\Type;

use App\Form\Shared\DataTransformer\RegistrationCodeToOrganizationTransformer;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationSelectorType extends AbstractType
{
   /**
     * @var RegistrationCodeToOrganizationTransformer $transformer
     */
    private $transformer;
	
	/**
	 * constructor
	 * @param RegistrationCodeToOrganizationTransformer $transformer
	 */
    public function __construct(RegistrationCodeToOrganizationTransformer $transformer)
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
            'invalid_message' => 'This value is not valid.',
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }
}
