<?php
namespace App\Form\Admin;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

use Symfony\Component\Asset\Packages;

use Symfony\Component\Validator\Constraints as Assert;


class MailerType extends AbstractType
{
    /**
     * @var UploaderHelper
     */
    private $helper;

    /**
     * @var Packages
     */
    private $assetsHelper;

    /**
     * MailerType constructor.
     *
     * @param UploaderHelper $helper
     * @param Packages $assetsHelper
     */
    public function __construct(UploaderHelper $helper, Packages $assetsHelper)
    {
        $this->helper = $helper;
        $this->assetsHelper = $assetsHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('to', EntityType::class, [
                'class' => User::class,
                'label' => 'admin.email.to',
                'attr' => ['class' => 'js-select-users'],
                'choice_attr' => function (User $entity, $key, $value) {
                    $path = $this->helper->asset($entity, 'imageFile');
                    if (!empty($path)) {
                        return ['data-data' => json_encode(['image' => $path])];
                    }
                    return ['data-data' => json_encode(['image' => $this->assetsHelper->getUrl('unknown_user.png', 'admin_images')])];
                },
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'admin.email.subject',
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => ['rows'=>'10'],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver){
        $resolver->setDefaults([
          'translation_domain' => 'admin',
        ]);
    }
}
