<?php

namespace App\Form\Shared\DataTransformer;

use App\Entity\Promo;
use App\Repository\PromoRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CouponCodeToPromoTransformer implements DataTransformerInterface
{
    /**
     * @var PromoRepository
     */
    private $promoRepository;

    public function __construct(PromoRepository $promoRepository)
    {
        $this->promoRepository = $promoRepository;
    }

    /**
     * Transforms an object (promo) to a string (number).
     *
     * {@inheritdoc}
     */
    public function transform($promo)
    {
        if (null === $promo || !$promo instanceof Promo) {
            return '';
        }

        return $promo->getId();
    }

    /**
     * Transforms a string (number) to an object (promo).
     *
     * {@inheritdoc}
     */
    public function reverseTransform($promoCode)
    {
        // no promo number? It's optional, so that's ok
        if (!$promoCode) {
            return null;
        }

        $promo = $this->promoRepository
            // query for the promo with this id
            ->findOneBy(['promoCode' => $promoCode])
        ;

        if (null === $promo) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An promo with number "%s" does not exist!',
                $promoCode
            ));
        }
        return $promo;
    }
}
