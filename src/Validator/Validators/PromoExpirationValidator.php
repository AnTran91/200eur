<?php

namespace App\Validator\Validators;

use App\Entity\Promo;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use App\Validator\Constraints\PromoExpiration;

class PromoExpirationValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (is_null($value)) {
            return $this;
        }

        if (!$value instanceof Promo || !is_object($value) || !method_exists($value, 'isExpired') || !method_exists($value, 'getPromoCode')) {
            throw new UnexpectedTypeException($value, Promo::class);
        }

        if ($value->isExpired()) {
	        if (isset($constraint->message)) {
		        $this->context->buildViolation($constraint->message)
		          ->setParameter('{{ string }}', $value->getPromoCode())
		          ->setInvalidValue($value)
		          ->setCode(PromoExpiration::EXPIRE_ERROR)
		          ->addViolation();
	        }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return PromoExpiration::class;
    }
}
