<?php
namespace App\Form\Admin\EventListener;

use App\Utils\File\Base64EncodedFile;
use App\Utils\File\UploadedBase64EncodedFile;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FileToBase64EncodedStringTransformer implements DataTransformerInterface
{
    /**
     * @var bool
     */
    private $strict;
    /**
     * @param bool $strict
     */
    public function __construct($strict = true)
    {
        $this->strict = $strict;
    }
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (! $value instanceof \SplFileInfo) {
            return '';
        }
        if (false === $file = @file_get_contents($value->getPathname(), FILE_BINARY)) {
            throw new TransformationFailedException(sprintf('Unable to read the "%s" file', $value->getPathname()));
        }
        return base64_encode($file);
    }
    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return null;
        }
        try {
            return new UploadedBase64EncodedFile(new Base64EncodedFile($value, $this->strict));
        } catch (\Exception $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
