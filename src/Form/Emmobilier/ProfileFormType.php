<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as FOSProfileFormType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Shared\Type\LanguageType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ProfileFormType extends AbstractType
{
    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->remove('username')
                ->remove('current_password')
                ->add('imageFile', VichImageType::class, [
                    'allow_delete' => false,
                    'label' => false,
                    'download_label' => 'option.field.picture',
                    'download_uri' => false,
                    'image_uri' => false,
                    'attr' => ['class'=> 'image-uploader-input']
                ])
                ->add('firstName', TextType::class, array('label' => 'user.field.first_name', 'label_attr' => ['required' => true]))
                ->add('lastName', TextType::class, array('label' => 'user.field.last_name', 'label_attr' => ['required' => true]))
                ->add('emailSecondary', TextType::class, array('label' => 'user.field.email_secondary'))
                ->add('language', LanguageType::class, array('label' => 'user.field.language'))
                ->add('billingAddress', BillingAddressType::class, array('label' => 'user.field.language'))
                ->add('acceptConditions', HiddenType::class, ['data' => true])
              ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FOSProfileFormType::class ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
