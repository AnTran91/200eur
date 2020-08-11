<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Repository\PromoRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Promo;
use App\Entity\PictureCounter;
use App\Entity\PictureDiscount;
use App\Entity\User;

class PromoHandler
{
    /**
     * @var PromoRepository
     */
    protected $promoRepository;

    /**
     * @var SessionInterface
     */
    protected $sessionManager;

    /**
     * Constructor
     *
     * @param SessionInterface $session
     * @param PromoRepository $promoRepository
     */
    public function __construct(SessionInterface $session, PromoRepository $promoRepository)
    {
        $this->promoRepository = $promoRepository;
        $this->sessionManager = $session;
    }

    /**
     * Get The promo object
     *
     * @return Promo|null
     */
    public function getCurrentPromo(): ?Promo
    {
        if (!$this->sessionManager->has('promo_code') || is_null($this->sessionManager->get('promo_code'))) {
            return null;
        }
        return $this->promoRepository->findOneBy(['promoCode' => $this->sessionManager->get('promo_code')]);
    }

    /**
     * Calculate the reduction
     *
     * @param array $orderedPictures The ordered pictures
     * @param User $user The current user
     *
     * @param Promo|null $promo
     * @return array
     */
    public function calculatePromoReduction(array $orderedPictures, User $user, ?Promo $promo): array
    {
        $promo = !is_null($promo) ? $promo : $this->getCurrentPromo();

        if (is_null($promo)) {
            return ['totalReductionPrice' => 0, 'totalReductionPicture' => 0];
        }

        if ($promo instanceof PictureCounter) {
            return $this->calculatePictureCounterReduction($orderedPictures, $user, $promo);
        }

        if ($promo instanceof PictureDiscount) {
            return $this->calculatePictureDiscountReduction($orderedPictures, $promo, $user);
        }

        throw new \LogicException("Non reachable bloc");
    }

    // /**
    //  * Calculate the Picture Counter reduction
    //  *
    //  * @param array $orderedPictures The ordered pictures
    //  * @param User  $user            The current user
    //  * @param PictureCounter $promo
    //  *
    //  * @return array
    //  */
    // public function calculatePictureCounterReduction(array $orderedPictures, User $user, PictureCounter $promo): array
    // {
    //     $totalReductionPrice = 0;
    //     $totalReductionPicture = 0;
    //     $totalPictureNumberGroupedByRetouch = $this->promoRepository->findThePicturesNumberByPromoAndUserGroupedByRetouch($promo->getId(), $user->getId());

    //     foreach ($promo->getPromotionsPerRetouch() as $pictureCounterPerRetouch) {
    //         if (!isset($orderedPictures[$pictureCounterPerRetouch->getRetouch()->getId()])) {
    //             continue;
    //         }
    //         // collect the information about the pictures from the session
    //         $pricesPerRetouch = $orderedPictures[$pictureCounterPerRetouch->getRetouch()->getId()] ?? [];
            
    //         // collect the information about the pictures from the database
    //         $imagesPerRetouch = array_filter($totalPictureNumberGroupedByRetouch, function ($totalPictureNumber) use ($pictureCounterPerRetouch) {
    //             return $totalPictureNumber['retouch_id'] === $pictureCounterPerRetouch->getRetouch()->getId();
    //         });

    //         $totalPicturesInDatabase = count($imagesPerRetouch) !== 0 ? current($imagesPerRetouch)['image_numbers'] : 0;
    //         $totalPictureToBeUsed = $pictureCounterPerRetouch->getImageCounterLimit() + $pictureCounterPerRetouch->getImageCounterLimitWithReduction();
            
    //         $totalNumber = $pricesPerRetouch['picture_number'] + ($totalPicturesInDatabase <= 0 ? 0 : ($totalPicturesInDatabase % ($totalPictureToBeUsed)));
    //         $totalPictureUsed = $this->getNumberOfPictureWithReduction($pictureCounterPerRetouch->getImageCounterLimit(), $pictureCounterPerRetouch->getImageCounterLimitWithReduction(), $totalNumber);
	        
    //         $totalReductionPrice += $this->calculateReduction(($pricesPerRetouch['price_per_unit'] * $totalPictureUsed), $pictureCounterPerRetouch->getImageCounterReduction());
    //         $totalReductionPicture += $totalPictureUsed;
    //     }
	
    //     return ['totalReductionPrice' => $totalReductionPrice, 'totalReductionPicture' => $totalReductionPicture];
    // }

    /**
     * Calculate the Picture Counter reduction
     *
     * @param array $orderedPictures The ordered pictures
     * @param User  $user            The current user
     * @param PictureCounter $promo
     *
     * @return array
     */
    public function calculatePictureCounterReduction(array $orderedPictures, User $user, PictureCounter $promo): array
    {
        $totalReductionPrice = 0;
        $totalReductionPicture = 0;
        $totalPictureNumberGroupedByRetouch = $this->promoRepository->findThePicturesNumberByPromoAndUserGroupedByRetouch($promo->getId(), $user->getId());

        foreach ($promo->getPromotionsPerRetouch() as $pictureCounterPerRetouch) {
            if (!isset($orderedPictures[$pictureCounterPerRetouch->getRetouch()->getId()])) {
                continue;
            }
            // collect the information about the pictures from the session
            $pricesPerRetouch = $orderedPictures[$pictureCounterPerRetouch->getRetouch()->getId()] ?? [];
            
            // collect the information about the pictures from the database
            $imagesPerRetouch = array_filter($totalPictureNumberGroupedByRetouch, function ($totalPictureNumber) use ($pictureCounterPerRetouch) {
                return $totalPictureNumber['retouch_id'] === $pictureCounterPerRetouch->getRetouch()->getId();
            });

            // $totalPicturesInDatabase = count($imagesPerRetouch) !== 0 ? current($imagesPerRetouch)['image_numbers'] : 0;
            $totalPictureToBeUsed = $pictureCounterPerRetouch->getImageCounterLimit() + $pictureCounterPerRetouch->getImageCounterLimitWithReduction();
            
            // $totalNumber = $pricesPerRetouch['picture_number'] + ($totalPicturesInDatabase <= 0 ? 0 : ($totalPicturesInDatabase % ($totalPictureToBeUsed)));
            $totalNumber = $pricesPerRetouch['picture_number'];
            $totalPictureUsed = $this->getNumberOfPictureWithReduction($pictureCounterPerRetouch->getImageCounterLimit(), $pictureCounterPerRetouch->getImageCounterLimitWithReduction(), $totalNumber);
            
            $totalReductionPrice += $this->calculateReduction(($pricesPerRetouch['price_per_unit'] * $totalPictureUsed), $pictureCounterPerRetouch->getImageCounterReduction());
            $totalReductionPicture += $totalPictureUsed;
        }
    
        return ['totalReductionPrice' => $totalReductionPrice, 'totalReductionPicture' => $totalReductionPicture];
    }

    /**
     * calculate the number of picture with reduction
     *
     * @param int $limit
     * @param int $reductionLimit
     * @param int $totalPicture
     *
     * @return int
     */
    public function getNumberOfPictureWithReduction(int $limit, int $reductionLimit, int $totalPicture): int
    {
        $numberOfPictureWithReduction = 0;
        for ($i=$limit; $i < $totalPicture; $i+=($limit + $reductionLimit)) {
          $numberOfPictureWithReduction += (($totalPicture - $i) > $reductionLimit ? $reductionLimit: ($totalPicture - $i));
        }

        return $numberOfPictureWithReduction;
    }
	
	/**
	 * Calculate the Picture Discount reduction
	 *
	 * @param array $orderedPictures The ordered pictures
	 * @param PictureDiscount $promo
	 * @param User $user
	 *
	 * @return array
	 */
    public function calculatePictureDiscountReduction(array $orderedPictures, PictureDiscount $promo, User $user): array
    {
        $totalReductionPrice = 0;
        $totalReductionPicture = 0;
        $totalPictureNumberGroupedByRetouch = $this->getPictureDetailsGroupedByRetouch($promo);
	    $totalPictureNumberGroupedByRetouchPerUser = $this->getPictureDetailsGroupedByRetouchPerUser($promo, $user);

        // check minumum conditions for picture and prestation
        $minimumImageCondition = $promo->getMinimumImage();
        $minimumPrestationCondition = $promo->getMinimumPrestation();
        
        $prestationNumber = 0;
        $imageNumber = 0;

        foreach ($orderedPictures as $key => $data) {
            $prestationNumber += $data['picture_number'];
            if (isset($data['total_picture'])) {
                $imageNumber += $data['total_picture'];
            }
        }
        // dump($prestationNumber);
        // dump($imageNumber); exit;
        // var_dump($imageNumber < $minimumImageCondition); exit;
        // var_dump($prestationNumber < $minimumPrestationCondition); exit;
        if ($prestationNumber < $minimumPrestationCondition || $imageNumber < $minimumImageCondition) {
            return ['totalReductionPrice' => 0, 'totalReductionPicture' => 0];
        }

        foreach ($promo->getPromotionsPerRetouch() as $pictureCounterPerRetouch) {
            if (!isset($orderedPictures[$pictureCounterPerRetouch->getRetouch()->getId()])) {
                continue;
            }
            // collect the information about the pictures in the session and database
            $imagesInSessionPerRetouch = $orderedPictures[$pictureCounterPerRetouch->getRetouch()->getId()] ?? [];
            $totalPicturesInDatabase = $totalPictureNumberGroupedByRetouch[$pictureCounterPerRetouch->getRetouch()->getId()] ?? [];
	        $totalPicturesInDatabasePerUser = $totalPictureNumberGroupedByRetouchPerUser[$pictureCounterPerRetouch->getRetouch()->getId()] ?? [];
            // get the total remaining pictures in this promo code with this retouch
	        $totalRemainingPicturesPerPromo = $pictureCounterPerRetouch->getImageLimit() - (isset($totalPicturesInDatabase['picture_number']) ? $totalPicturesInDatabase['picture_number'] : 0);
	        $totalRemainingPicturesPerUser = $totalRemainingPicturesPerPromo;
	        if (!is_null($pictureCounterPerRetouch->getImageLimitPerUser()) && $totalRemainingPicturesPerUser > 0){
		        $totalRemainingPicturesPerUser = $pictureCounterPerRetouch->getImageLimitPerUser() - (isset($totalPicturesInDatabasePerUser['picture_number']) ? $totalPicturesInDatabasePerUser['picture_number'] : 0);
	        }
	        $totalRemainingPictures = min($totalRemainingPicturesPerUser, $totalRemainingPicturesPerPromo);
	        
            // get the number of the pictures that should be used to make the discount
            $totalPictureToBeUsed = $totalRemainingPictures;
            if (!is_null($pictureCounterPerRetouch->getImageLimitPerOrder()) && is_numeric($pictureCounterPerRetouch->getImageLimitPerOrder()) && intval($pictureCounterPerRetouch->getImageLimitPerOrder()) < $totalRemainingPictures) {
              $totalPictureToBeUsed = $pictureCounterPerRetouch->getImageLimitPerOrder();
            }
            // if there is not pictures remaining to make the discount then (on to the next one)
            if ($totalPictureToBeUsed <= 0) {
                continue;
            }
            // the discounted pictures that we currently using
            $totalPictureUsed = $imagesInSessionPerRetouch['picture_number'] >= $totalPictureToBeUsed ? $totalPictureToBeUsed : $imagesInSessionPerRetouch['picture_number'];
            // calculate the reduction
            $totalReductionPrice += $this->calculateReduction(($imagesInSessionPerRetouch['price_per_unit'] * $totalPictureUsed), $pictureCounterPerRetouch->getReduction());
            $totalReductionPicture += $totalPictureUsed;
        }


        return ['totalReductionPrice' => $totalReductionPrice, 'totalReductionPicture' => $totalReductionPicture];
    }
	
	/**
	 * Get the discounted pictures that already uses by the current user and promo (Restructuring the data)
	 *
	 * @param PictureDiscount $promo
	 * @param User $user
	 *
	 * @return array
	 */
	public function getPictureDetailsGroupedByRetouchPerUser(PictureDiscount $promo, User $user): array
	{
		$totalPictureNumberGroupedByRetouchAndOrder = $this->promoRepository->findThePicturesNumberByPromoAndUserGroupedByRetouchAndOrder($promo->getId(), $user->getId());
		$totalPictureNumbers = [];
		
		foreach ($promo->getPromotionsPerRetouch() as $pictureDiscountPerRetouch) {
			$pictureDetailPerRetouch = array_filter($totalPictureNumberGroupedByRetouchAndOrder, function ($pictureDetail) use ($pictureDiscountPerRetouch) {
				return $pictureDetail['retouch_id'] === $pictureDiscountPerRetouch->getRetouch()->getId();
			});
			
			foreach ($pictureDetailPerRetouch as $pictureDetail) {
				$pictureNumber = $pictureDetail['image_numbers'];
				if (!is_null($pictureDiscountPerRetouch->getImageLimitPerUser()) && is_numeric($pictureDiscountPerRetouch->getImageLimitPerUser()) && $pictureNumber > intval($pictureDiscountPerRetouch->getImageLimitPerUser())) {
					$pictureNumber = $pictureDiscountPerRetouch->getImageLimitPerUser();
				}
				if (!isset($totalPictureNumbers[$pictureDetail['retouch_id']])) {
					$totalPictureNumbers[$pictureDetail['retouch_id']] = ['picture_number' => 0];
				}
				$totalPictureNumbers[$pictureDetail['retouch_id']]['picture_number'] += $pictureNumber;
			}
		}
		
		return $totalPictureNumbers;
	}
	
    /**
     * Get the discounted pictures that already uses by the current promo (Restructuring the data)
     *
     * @param PictureDiscount $promo
     * @return array
     */
    public function getPictureDetailsGroupedByRetouch(PictureDiscount $promo): array
    {
        $totalPictureNumberGroupedByRetouchAndOrder = $this->promoRepository->findThePicturesNumberByPromoGroupedByRetouchAndOrder($promo->getId());
        $totalPictureNumbers = [];

        foreach ($promo->getPromotionsPerRetouch() as $pictureDiscountPerRetouch) {
            $pictureDetailPerRetouch = array_filter($totalPictureNumberGroupedByRetouchAndOrder, function ($pictureDetail) use ($pictureDiscountPerRetouch) {
                return $pictureDetail['retouch_id'] === $pictureDiscountPerRetouch->getRetouch()->getId();
            });

            foreach ($pictureDetailPerRetouch as $pictureDetail) {
                $pictureNumber = $pictureDetail['image_numbers'];
                if (!is_null($pictureDiscountPerRetouch->getImageLimitPerOrder()) && is_numeric($pictureDiscountPerRetouch->getImageLimitPerOrder()) && $pictureNumber > intval($pictureDiscountPerRetouch->getImageLimitPerOrder())) {
                    $pictureNumber = $pictureDiscountPerRetouch->getImageLimitPerOrder();
                }
                if (!isset($totalPictureNumbers[$pictureDetail['retouch_id']])) {
                    $totalPictureNumbers[$pictureDetail['retouch_id']] = ['picture_number' => 0];
                }
                $totalPictureNumbers[$pictureDetail['retouch_id']]['picture_number'] += $pictureNumber;
            }
        }

        return $totalPictureNumbers;
    }
	
	/**
	 * Get number of promo use
	 *
	 * @param int $promoId
	 *
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function getNumberOfUse(int $promoId): int
    {
	    /** @var int $useNumber */
        extract($this->promoRepository->findTheUseNumberByPromo($promoId));
        
        return $useNumber;
    }
	
	/**
	 * Get number of promo use per user
	 *
	 * @param int $promoId
	 * @param int $userId
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function getNumberOfUsePerUser(int $promoId, int $userId): int
    {
	    /** @var int $useNumber */
        extract($this->promoRepository->findTheUseNumberByPromoAndUser($promoId, $userId));

        return $useNumber;
    }

    /**
     * Calculate the percentage of one number
     *
     * @param float $number
     * @param float $percentage
     *
     * @return float
     */
    public function calculateReduction(float $number, float $percentage): float
    {
        return ($number * $percentage) / 100;
    }
}
