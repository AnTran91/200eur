<?php

namespace Tests\UnitTests\Form\FOSUser;

use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ValidatorExtensionTypeTestCase
 * FormTypeValidatorExtension added as default. Useful for form types with `constraints` option.
 */
class ValidatorExtensionTypeTestCase extends TypeTestCase
{
    /**
     * @return array
     */
    protected function getTypeExtensions()
    {
        $validator = $this->getMockBuilder('Symfony\Component\Validator\Validator\ValidatorInterface')->getMock();
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));
        return array(
            new FormTypeValidatorExtension($validator),
        );
    }
}
