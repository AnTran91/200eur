<?php

namespace App\Validator\Validators;


use App\Entity\PictureDiscountPerRetouch;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use App\Validator\Constraints\PromoLimit;

use App\Entity\Promo;
use App\Entity\PictureDiscount;
use App\Entity\User;

use App\Handlers\PromoHandler;

class PromoLimitValidator extends ConstraintValidator
{
    /**
     * @var PromoHandler
     */
    protected $promoHandler;
	
	/**
	 * Constructor
	 *
	 * @param PromoHandler $promoHandler
	 */
    public function __construct(PromoHandler $promoHandler)
    {
        $this->promoHandler = $promoHandler;
    }
	
	/**
	 * @param $value
	 * @param Constraint $constraint
	 *
	 * @return PromoLimitValidator
	 *
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function validate($value, Constraint $constraint)
    {
    	/** @var User $user */
	    $user = method_exists($constraint, 'getUser') ? $constraint->getUser() : null;
        if (is_null($value) || is_null($user)) {
            return $this;
        }

        if (!$user instanceof User || !$value instanceof Promo || !is_object($value) || !method_exists($value, 'getPromoCode')) {
            throw new UnexpectedTypeException($value, Promo::class);
        }

        if (!$value instanceof PictureDiscount) {
            return $this;
        }

        if ($value->getHasNumberOfUse()) {
            if (!is_null($value->getUseLimit()) && $value->getUseLimit() <= $this->promoHandler->getNumberOfUse($value->getId())) {
	            if (isset($constraint->maxUseMessage)) {
		            $this->context->buildViolation($constraint->maxUseMessage)
		                            ->setParameter('{{ value }}', $this->formatValue($value))
		                            ->setInvalidValue($value)
		                            ->setCode(PromoLimit::MAX_USE_ERROR)
		                            ->addViolation();
	            }
            }
            if (!is_null($value->getUseLimitPerUser()) && $value->getUseLimitPerUser() <= $this->promoHandler->getNumberOfUsePerUser($value->getId(), $user->getId())) {
	            if (isset($constraint->maxUsePerUserMessage)) {
		            $this->context->buildViolation($constraint->maxUsePerUserMessage)
		                            ->setParameter('{{ value }}', $this->formatValue($value))
		                            ->setInvalidValue($value)
		                            ->setCode(PromoLimit::MAX_USE_PER_USER_ERROR)
		                            ->addViolation();
	            }
            }
        }

        if (!$value->getPromotionsPerRetouch()->isEmpty()) {
	
	        // Verify picture max total image per user
	        $totalPictureNumberGroupedByRetouch = $this->promoHandler->getPictureDetailsGroupedByRetouchPerUser($value, $user);
	        $remainingPictures = $value->getPromotionsPerRetouch()->map(function (PictureDiscountPerRetouch $pictureCounterPerRetouch) use ($totalPictureNumberGroupedByRetouch) {
		        $imageLimit = !is_null($pictureCounterPerRetouch->getImageLimitPerUser()) && $pictureCounterPerRetouch->getImageLimitPerUser() > 0 ? $pictureCounterPerRetouch->getImageLimitPerUser() : $pictureCounterPerRetouch->getImageLimit();
	        	$imagesInDatabasePerRetouch = $totalPictureNumberGroupedByRetouch[$pictureCounterPerRetouch->getRetouch()->getId()] ?? [];
		        return $imageLimit - (isset($imagesInDatabasePerRetouch['picture_number']) ? $imagesInDatabasePerRetouch['picture_number'] : 0);
	        })->toArray();
	
	        if (max($remainingPictures) <= 0) {
		        if (isset($constraint->maxPictureUseMessage)) {
			        $this->context->buildViolation($constraint->maxPictureUseMessage)
				        ->setParameter('{{ value }}', $this->formatValue($value))
				        ->setInvalidValue($value)
				        ->setCode(PromoLimit::MAX_PICTURE_USE_ERROR)
				        ->addViolation();
		        }
	        }
	        
        	// Verify picture max total image per promo
            $totalPictureNumberGroupedByRetouch = $this->promoHandler->getPictureDetailsGroupedByRetouch($value);
            $remainingPictures = $value->getPromotionsPerRetouch()->map(function (PictureDiscountPerRetouch $pictureCounterPerRetouch) use ($totalPictureNumberGroupedByRetouch) {
	            $imagesInDatabasePerRetouch = $totalPictureNumberGroupedByRetouch[$pictureCounterPerRetouch->getRetouch()->getId()] ?? [];
                return $pictureCounterPerRetouch->getImageLimit() - (isset($imagesInDatabasePerRetouch['picture_number']) ? $imagesInDatabasePerRetouch['picture_number'] : 0);
            })->toArray();

            if (max($remainingPictures) <= 0) {
	            if (isset($constraint->maxPictureUseMessage)) {
		            $this->context->buildViolation($constraint->maxPictureUseMessage)
		                ->setParameter('{{ value }}', $this->formatValue($value))
		                ->setInvalidValue($value)
		                ->setCode(PromoLimit::MAX_PICTURE_USE_ERROR)
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
        return PromoLimit::class;
    }
}