<?php

namespace App\Validator\Validators;

use App\Entity\Promo;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use App\Validator\Constraints\PromoDateInterval;

class PromoDateIntervalValidator extends ConstraintValidator
{
    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (is_null($value)) {
            return $this;
        }

        if (!$value instanceof Promo || !is_object($value) || !method_exists($value, 'getStartDate') || !method_exists($value, 'getEndDate') || !method_exists($value, 'getPromoCode')) {
            throw new UnexpectedTypeException($value, Promo::class);
        }

        $today = new \DateTime('now');

        // if it's valid date interval
        if ($today->format("Y-m-d") < $value->getStartDate()->format("Y-m-d") || $today->format("Y-m-d") > $value->getEndDate()->format("Y-m-d")) {
	        if (isset($constraint->message)) {
		        $this->context->buildViolation($constraint->message)
		          ->setParameter('{{ string }}', $value->getPromoCode())
		          ->setInvalidValue($value)
		          ->setCode(PromoDateInterval::DATE_INTERVAL_ERROR)
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
        return PromoDateInterval::class;
    }
}
