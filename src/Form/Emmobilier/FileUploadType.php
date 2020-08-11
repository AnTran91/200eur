<?php

namespace App\Form\Emmobilier;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class FileUploadType extends AbstractType
{
    /**
     * @var array
     */
    private $allowedMimeTypes;

    /**
     * @var int
     */
    private $sizeLimit;

    /**
     * @var string
     */
    private $chunkSize;
	private $chunkSizeLimit;
	private $inputName;
	private $fileName;
	private $multipleUuid;
	private $uuid;
	private $totalParts;
	private $totalFileSize;
	private $partIndex;
	private $partByteOffset;
	private $resume;
	
	public function __construct(array $validationConfigs, array $requestConfigs)
    {
        $this->allowedMimeTypes = $validationConfigs['allowedMimeTypes'];
        $this->sizeLimit = $validationConfigs['sizeLimit'];
        $this->chunkSizeLimit = $validationConfigs['chunkSize'];
	
	    $this->inputName = $requestConfigs['inputName'];
	    $this->fileName = $requestConfigs['fileName'];
	    $this->uuid = $requestConfigs['uuid'];
	    $this->multipleUuid = $requestConfigs['multipleUuid'];
	    $this->totalParts = $requestConfigs['totalparts'];
	    $this->totalFileSize = $requestConfigs['filesSize'];
	    $this->partIndex = $requestConfigs['partindex'];
	    $this->chunkSize = $requestConfigs['chunkSize'];
	    $this->partByteOffset = $requestConfigs['partByteOffset'];
	    $this->resume = $requestConfigs['resume'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($this->uuid, TextType::class, [
                'constraints' => array(
                  new Assert\NotBlank()
                )
            ])
            ->add($this->fileName, TextType::class, [
                'constraints' => array(
                  new Assert\NotBlank(),
                )
            ])
            ->add($this->totalFileSize, TextType::class, [
                'constraints' => array(
                  new Assert\NotBlank(),
                  new Assert\LessThanOrEqual($this->sizeLimit)
                )
            ])
            ->add($this->inputName, FileType::class)
            // chunked upload
            ->add($this->totalParts, TextType::class)
            ->add($this->chunkSize, TextType::class, [
              'constraints' => array(
                new Assert\LessThanOrEqual($this->chunkSizeLimit)
              )
            ])
            ->add($this->partByteOffset, TextType::class)
            ->add($this->partIndex, TextType::class)
	        ->add($this->resume, TextType::class)
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
