<?php

namespace App\Validator\Validators;

use App\Entity\Promo;
use App\Entity\User;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use App\Validator\Constraints\PromoType;

class PromoTypeValidator extends ConstraintValidator
{
	/**
	 *
	 * @param Promo $value
	 * @param Constraint $constraint
	 * @return PromoTypeValidator
	 */
    public function validate($value, Constraint $constraint)
    {
	    /** @var User $user */
	    $user = method_exists($constraint, 'getUser') ? $constraint->getUser() : null;
        if (is_null($value) || is_null($user)) {
            return $this;
        }
	    
        if (!$user instanceof User || !is_object($value) || !method_exists($value, 'getPromoType')) {
            throw new UnexpectedTypeException($value, Promo::class);
        }

        if ($value->getPromoType() === Promo::ASSIGN_TO_ALL_CUSTOMERS) {
            return $this;
        }

        if ($value->getPromoType() === Promo::ASSIGN_TO_NETWORK) {
            $organization = $user->getOrganization();
            if (method_exists($organization , 'getNetwork') && !is_null($organization->getNetwork())) {
              $organization = $organization->getNetwork();
            }
	
	        $organizationId = !is_null($organization) ? $organization->getId() : null;
	        if ($organizationId !== $value->getOrganization()->getId()) {
	            if (isset($constraint->wrongNetworkErrorMessage)) {
		            $this->context->buildViolation($constraint->wrongNetworkErrorMessage)
		            ->setParameter('{{ string }}', $value->getPromoCode())
		            ->setInvalidValue($value)
		            ->setCode(PromoType::WRONG_ORGANIZATION_ERROR)
		            ->addViolation();
	            }
            }
        }

        if ($value->getPromoType() === Promo::ASSIGN_TO_AGENCY) {
            $organizationId = !is_null($user->getOrganization()) ? $user->getOrganization()->getId() : null;
            if ($organizationId !== $value->getOrganization()->getId()) {
	            if (isset($constraint->wrongAgencyErrorMessage)) {
		            $this->context->buildViolation($constraint->wrongAgencyErrorMessage)
		            ->setParameter('{{ string }}', $value->getPromoCode())
		            ->setInvalidValue($value)
		            ->setCode(PromoType::WRONG_ORGANIZATION_ERROR)
		            ->addViolation();
	            }
            }
        }

        if ($value->getPromoType() == Promo::ASSIGN_TO_SPECIFIC_CUSTOMERS) {
            if ($value->getClients()->filter(function (User $client) use ($user) {
                return $client->getId() === $user->getId();
            })->isEmpty()) {
	            if (isset($constraint->wrongAccountErrorMessage)) {
		            $this->context->buildViolation($constraint->wrongAccountErrorMessage)
		              ->setParameter('{{ string }}', $value->getPromoCode())
		              ->setInvalidValue($value)
		              ->setCode(PromoType::WRONG_ACCOUNT_ERROR)
		              ->addViolation();
	            }
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return PromoType::class;
    }
}
