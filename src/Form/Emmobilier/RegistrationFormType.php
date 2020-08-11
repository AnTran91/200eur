<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as FOSRegistrationFormType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2;

use App\Form\Shared\Type\OrganizationSelectorType;


class RegistrationFormType extends AbstractType
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
                ->remove('email')
                ->remove('plainPassword')
                ->add('email', EmailType::class, array('label' => 'form.email', 'label_attr' => ['required' => true], 'translation_domain' => 'FOSUserBundle'))
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
                ->add('billingAddress', BillingAddressType::class, array(
                  'label' => false,
                  'validation_groups' => ['EmmobilierRegistration'])
                )
                ->add('organization', OrganizationSelectorType::class, array(
                  'label' => 'user.field.registerCode'
                ))
                ->add('emailSecondary', TextType::class, array('label' => 'user.field.email_secondary'))
                ->add('captcha', RecaptchaType::class, array(
                  'constraints' => new Recaptcha2([
                    'message' => 'recaptcha.msg',
                    'groups' => ['EmmobilierRegistration']
                  ]),
                ))
                ->add('acceptConditions', CheckboxType::class, array(
                  'label' => 'user.field.acceptConditions'
                ))
                ->add('receiveNewsletter', CheckboxType::class, array(
                  'label' => 'user.field.receiveNewsletter'
                ))
                ->add('receiveTargetedEmailsFromPromotion', CheckboxType::class, array(
                  'label' => 'user.field.receiveTargetedEmailsFromPromotion'
                ))
              ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FOSRegistrationFormType::class ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'service_user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
