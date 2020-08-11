<?php

namespace App\Form\Admin;

use App\Entity\User;

use App\Entity\Group;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Form\Shared\Type\OrganizationSelectorType;

use App\Form\Shared\Type\BillingAddressType;


class UserType extends AbstractType
{
    private $languageOptions;
    private $roles;
    private $userPaymentType;


    public function __construct(array $languageOptions, array $roleHierarchy, array $userPaymentType){
        $this->languageOptions = $languageOptions;
        $this->userPaymentType = $userPaymentType;
        $this->roles = $roleHierarchy;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', HiddenType::class)
            ->add('firstName', TextType::class, ['label' => 'admin.user.first_name'])
            ->add('lastName', TextType::class, ['label' => 'admin.user.last_name'])
            ->add('email', EmailType::class, ['label' => 'admin.user.email'])
            ->add('emailSecondary', EmailType::class, ['label' => 'admin.user.email_secondary'])
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
            ->add('roles', ChoiceType::class, [
              'label' => 'admin.user.roles',
              'choices' => $this->roles,
              'multiple' => true,
              'by_reference' => false,
              'attr' => ['class' => 'js-multi-select-selector'],
            ])
            ->add('type', ChoiceType::class, ['label' => 'admin.order.payment_type', 'choices' => $this->userPaymentType])
            ->add('organization', OrganizationSelectorType::class, array(
              'label' => 'admin.user.register_code'
            ))
            ->add('language', ChoiceType::class, ['label' => 'admin.user.language', 'choices' => $this->languageOptions])
            ->add('imageFile', VichImageType::class, [
                'allow_delete' => false,
                'label' => false,
                'download_label' => 'option.field.picture',
                'download_uri' => false,
                'image_uri' => false,
                'attr' => ['class'=> 'image-uploader-input']
            ])
            ->add('groups', EntityType::class, [
              'class' => Group::class,
              'choice_label' => 'name',
              'multiple' => true,
              'by_reference' => false,
              'attr' => ['class' => 'js-multi-select-selector'],
              'label' => 'admin.group.group',
              'placeholder' => 'admin.common.none'
            ])
            ->add('wallet', WalletType::class, ['label' => false])
            ->add('enabled', CheckboxType::class, ['data' => false, 'label' => 'admin.user.enabled', 'attr' => ['class' => 'form-check-input']])
            ->add('billingAddress', BillingAddressType::class, array('label' => False))

            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                array($this, 'onPreSubmit')
            )
        ;

    }

    public function onPreSubmit(FormEvent $event)
    {
        $user = $event->getData();

        if (!$user) {
            return;
        }

        $user['username'] = $user['email'] ?? null;
        $event->setData($user);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'admin',
            'validation_groups' => function (FormInterface $form) {
                $user = $form->getData();

                if (!$user->getId()) {
                    return ['AdminUserCreation', 'Registration'];
                }

                return ['AdminUserCreation'];
            },
        ]);
    }
}
