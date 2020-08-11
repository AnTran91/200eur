<?php

namespace App\Form\Admin;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Vich\UploaderBundle\Form\Type\VichImageType;

use App\Form\Shared\Type\BillingAddressType;

class ProfilType extends AbstractType
{
    /**
     * @var array
     */
    private $languageOptions;

    public function __construct(array $languageOptions)
    {
        $this->languageOptions = $languageOptions;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'admin.user.first_name', 'label_attr' => ['class' => 'form-label']])
            ->add('lastName', TextType::class, ['label' => 'admin.user.last_name', 'label_attr' => ['class' => 'form-label']])
            ->add('email', EmailType::class, ['label' => 'admin.user.email', 'label_attr' => ['class' => 'form-label']])
            ->add('emailSecondary', EmailType::class, ['label' => 'admin.user.email_secondary', 'label_attr' => ['class' => 'form-label']])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array('label' => 'form.password', 'label_attr' => ['required' => true]),
                'second_options' => array('label' => 'form.password_confirmation', 'label_attr' => ['required' => true]),
                'invalid_message' => 'fos_user.password.mismatch'
            ))
            ->add('language', ChoiceType::class, ['label' => 'admin.user.language', 'choices' => $this->languageOptions, 'label_attr' => ['class' => 'form-label']])
            ->add('imageFile', VichImageType::class, [
                'allow_delete' => false,
                'label' => false,
                'download_label' => 'option.field.picture',
                'download_uri' => false,
                'image_uri' => false,
                'attr' => ['class'=> 'image-uploader-input']
            ])
            ->add('billingAddress', BillingAddressType::class, ['label' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => 'AdminUserCreation',
            'translation_domain' => 'admin',
        ]);
    }
}
