<?php

namespace Tests\UnitTests\Form\Traits;

use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait ValidatorExtensionTrait
{
    /**
     * @return ValidatorInterface mocked object
     */
    public function mockValidator()
    {
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
        ->will($this->returnValue(new ConstraintViolationList()));
        $validator->method('getMetadataFor')
        ->will($this->returnValue(new ClassMetadata(Form::class)));

        return $validator;
    }
}
