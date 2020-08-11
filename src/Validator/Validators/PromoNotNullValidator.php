<?php

namespace App\Validator\Validators;

use App\Validator\Constraints\PromoNotNull;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PromoNotNullValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_object($value) || is_null($value) || false === $value || (empty($value) && '0' != $value)) {
	        if (isset($constraint->message)) {
		        $this->context->buildViolation($constraint->message)
		        ->setParameter('{{ string }}', strval($value))
		        ->setInvalidValue($value)
		        ->setCode(PromoNotNull::NOT_VALID_PROMO_ERROR)
		        ->addViolation();
	        }
        }
    }

    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return PromoNotNull::class;
    }
}
