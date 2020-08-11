<?php

namespace App\Handlers\OrderHandlers;

use App\Handlers\Traits\LogEntryTrait;
use App\Handlers\Traits\OrderFormatterTrait;

use Gedmo\Loggable\Entity\LogEntry;

use Doctrine\DBAL\ConnectionException;

use App\Entity as Entity;

class RevertToBackupOrder extends OrderHandlerBase
{
    use LogEntryTrait;
	use OrderFormatterTrait;

    /**
     * Recalculate the order price
     *
     * @param Entity\Order $order The order object.
     *
     * @return Entity\Order
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function revertOrder(Entity\Order $order): Entity\Order
    {
        try {
            $logEntryRepo = $this->em->getRepository(LogEntry::class);

            $retouchPrices = array();
            $paramPrice = array();

            foreach ($order->getPictures() as $picture) {
                foreach ($picture->getPictureDetail() as $pictureDetail) {

                    $logs = $logEntryRepo->getLogEntries($pictureDetail);
                    if ($order->getPaymentDate() && !is_null($logs)) {
                        $currentLog = $this->getTheClosestLogToPaymentDate($logs, $order->getPaymentDate());
                        $logEntryRepo->revert($pictureDetail, $currentLog->getVersion());

                        $this->em->persist($pictureDetail);
                    }

                    $this->pictureDetailsFormatter($pictureDetail, $order->getDeliveryTime(), $retouchPrices, $paramPrice);
                }
            }
	
	        /**
	         * @var int $dutyFree
	         * @var int $taxPercentage
	         * @var int $totalReductionPercentage
	         * @var int $totalReductionPicture
	         * @var int $totalReductionPrice
	         */
            extract($this->calculatePrices($retouchPrices, array_sum(array_column($paramPrice, 'total_price')), $order->getClient(), $order->getPromotion()));

            $order->setTotalAmount($dutyFree)
              ->setTaxPercentage($taxPercentage)
              ->setReductionPercentage($totalReductionPercentage)
              ->setTotalReductionOnPictures($totalReductionPicture)
              ->setTotalReductionAmount($totalReductionPrice)
            ;

            $this->em->persist($order);
            $this->em->flush();

            return $order;
        } catch (ConnectionException $e) {
            // Rollback the failed transaction attempt
            $this->em->getConnection()->rollback();
        }
    }
}
