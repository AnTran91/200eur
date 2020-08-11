<?php

namespace App\Validator\Constraints;

use App\Validator\Validators\URLAndFileValidator;
use Symfony\Component\Validator\Constraints\File;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 * @property int $maxSize
 */
class URLAndFile extends File
{
    const SIZE_NOT_DETECTED_ERROR = '6d55c3f4-e58e-4fe3-91ee-74b492199956';
    const CORRUPTED_IMAGE_ERROR = '5d4163f3-648f-4e39-87fd-cc5ea7aad2d1';

    public $corruptedMessage = 'The image file is corrupted.';

    /**
     * @inheritDoc
     */
    public function validatedBy()
    {
        return URLAndFileValidator::class ;
    }
}