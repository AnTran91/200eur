<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\Shared\Type\Base64EncodedFileType;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\CallbackTransformer;

class FileUploadType extends AbstractType
{
    /**
     * @var array
     */
    private $allowedMimeTypes;

    /**
     * @var int
     */
    private $chunkSize;

    public function __construct(array $validationConfigs)
    {
        $this->allowedMimeTypes = $validationConfigs['allowedMimeTypes'];
        $this->chunkSize = $validationConfigs['chunkSize'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('metadata', TextType::class)
            ->add('file', FileType::class, [
                'constraints' => array(
                  new Assert\File([
                    'mimeTypes' => $this->allowedMimeTypes,
                    'maxSize' =>  $this->chunkSize,
                    'binaryFormat' => true
                  ])
                )
            ])
            ->add('base64', Base64EncodedFileType::class)
        ;

        $builder->get('metadata')
            ->addModelTransformer(new CallbackTransformer(
                function ($metadataAsArray) {
                    // transform the array to a string
                    return !is_array($metadataAsArray) ? $metadataAsArray : json_encode($metadataAsArray, JSON_UNESCAPED_SLASHES);
                },
                function ($metadataAsString) {
                    // transform the string back to an array
                    return !is_string($metadataAsString) ? $metadataAsString : json_decode($metadataAsString, true);
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data' => null,
            'csrf_protection' => false,
        ]);
    }

    /**
     * This will remove formTypeName from the form
     *
     * @return null
     */
    public function getBlockPrefix()
    {
        return null;
    }
}
