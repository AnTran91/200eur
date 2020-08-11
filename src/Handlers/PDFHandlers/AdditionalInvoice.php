<?php

namespace App\Handlers\PDFHandlers;


use App\Entity\Invoice;
use App\Handlers\Traits\LogEntryTrait;
use Gedmo\Loggable\Entity\LogEntry;

class AdditionalInvoice extends BaseInvoiceType
{
    use LogEntryTrait;

    /**
     * @inheritdoc
     */
    public function createInvoicePdf(Invoice $invoice): void
    {
        $this->pdf->setInvoiceType($invoice->getType());
        $this->pdf->setTranslator($this->translator);
        $this->pdf->AddPage();
        $this->pdf->setHeader($this->logoPath);
        $this->pdf->setInvoiceTitle($invoice, $invoice->getPaymentDate()->format('d-m-Y'));
        $this->pdf->setClient($invoice->getClient());
        $this->pdf->setOrder($invoice->getCurrentOrder());
        $this->populateAdditionalInvoiceTable($invoice);
        $targetDir = join(DIRECTORY_SEPARATOR, [$this->media, $invoice->getPaymentDate()->format('Y-m-d')]);
        $this->storage->doCreateDirIfNotExists($targetDir);
        $this->pdf->Output(join(DIRECTORY_SEPARATOR, [$targetDir, $invoice->getPdfFileName() ]), 'F');
    }

    /**
     * This method create the invoice content
     *
     * @param Invoice $invoice
     * @return void
     */
    public function populateAdditionalInvoiceTable(Invoice $invoice): void
    {
        list($initialFiles, $paramPrice, $originalFiles, $paramPriceOld) = $this->getFormattedOrder($invoice);

        $this->pdf->setAdditionalTable($initialFiles, $paramPrice, $originalFiles, $paramPriceOld);

        $priceExcludingTax =  $invoice->getTotalAmount();
        $taxPrice = $priceExcludingTax * ($invoice->getTaxPercentage() / 100);

        $tvaData = [
            'tva_number' => 1,
            'price_excluding_price' => $priceExcludingTax,
            'tax_percentage' => $invoice->getTaxPercentage()."%",
            'tax_price' => $this->priceFormatter($taxPrice)
        ];

        $this->pdf->setTaxPrice($tvaData);

        $totalData[$this->translator->trans('fpdf_invoice.total_wt')] = $this->priceFormatter($priceExcludingTax);
        $totalData[$this->translator->trans('fpdf_invoice.total_vat')] = $this->priceFormatter($taxPrice);
        $totalData[$this->translator->trans('fpdf_invoice.total_price')] = $this->priceFormatter($invoice->getTotalAmountPaid());

        $this->pdf->setTotalPrice($totalData);
    }

    /**
     * This function return a formatted order
     *
     * @param Invoice $invoice
     * @return array
     */
    private function getFormattedOrder(Invoice $invoice): array
    {
        $initialFiles = array();
        $paramPrice = array();
        $paramPriceOld = array();
        $originalFiles = array();
        $logEntryRepo = $this->em->getRepository(LogEntry::class);

        foreach ($invoice->getCurrentOrder()->getPictures() as $picture) {
            foreach ($picture->getPictureDetail() as $pictureDetail) {
                $currentLog = $this->getTheClosestLogBeforePaymentDate($logEntryRepo->getLogEntries($pictureDetail), $invoice->getCurrentOrder()->getPaymentDate());

                // prepare param for BIS
                if (!empty($logEntryRepo->getLogEntries($pictureDetail->getParam()))) {
                    $fieldOld = array();
                    $currentParam = array();
                    $currentLogParam = $this->getTheClosestLogBeforePaymentDate($logEntryRepo->getLogEntries($pictureDetail->getParam()), $invoice->getCurrentOrder()->getPaymentDate());

                    $fields = $this->em->getRepository(\App\Entity\Field::class)->findAllByFieldThatHavePrice(null);
                    if ($currentLogParam != NULL) {
                        $paramOld = clone $pictureDetail->getParam();
                        $logEntryRepo->revert($paramOld, $currentLogParam->getVersion());
                        $fieldOld = $this->getParamsTotalPrice([$paramOld], $fields, []);
                    }
                    
                    $currentParam = $pictureDetail->getParam();
                    $fieldCurrent = $this->getParamsTotalPrice([$currentParam], $fields, []);
                }
                
                // dump(isset($fieldOld));
                // dump($fieldCurrent); exit;
                if (isset($fieldCurrent) || isset($fieldOld)) {
                    if (!empty(array_diff_key($fieldCurrent, $fieldOld))) {
                        foreach (array_diff_key($fieldCurrent, $fieldOld) as $key => $value) {
                            $fieldTemp = $this->em->getRepository(\App\Entity\Field::class)->find($key);
                            if (!isset($paramPrice[$fieldTemp->getId()])) {
                                $paramPrice[$fieldTemp->getId()] = ['field_group' => $fieldTemp->getFieldGroup(), 'total_price' => 0, 'price_per_unit' => $fieldTemp->getPrice(), 'quantity' => 0];
                            }
                            $paramPrice[$fieldTemp->getId()]['total_price'] += $fieldTemp->getPrice();
                            $paramPrice[$fieldTemp->getId()]['quantity'] ++;
                        }

                    }

                    if (!empty(array_diff_key($fieldOld, $fieldCurrent))) {
                        foreach (array_diff_key($fieldOld, $fieldCurrent) as $key => $value) {
                            $fieldTemp = $this->em->getRepository(\App\Entity\Field::class)->find($key);
                            if (!isset($paramPriceOld[$fieldTemp->getId()])) {
                                $paramPriceOld[$fieldTemp->getId()] = ['field_group' => $fieldTemp->getFieldGroup(), 'total_price' => 0, 'price_per_unit' => $fieldTemp->getPrice(), 'quantity' => 0];
                            }
                            $paramPriceOld[$fieldTemp->getId()]['total_price'] += $fieldTemp->getPrice();
                            $paramPriceOld[$fieldTemp->getId()]['quantity'] ++;
                        }
                    }
                    unset($fieldOld);
                    unset($fieldCurrent);
                }
                
                // foreach ($pictureDetail->getFieldDetails() as $fieldDetail) {
                //     if (!isset($paramPrice[$fieldDetail->getField()->getId()])) {
                //         $paramPrice[$fieldDetail->getField()->getId()] = ['field_group' => $fieldDetail->getField()->getFieldGroup(), 'total_price' => 0, 'price_per_unit' => $fieldDetail->getPrice(), 'quantity' => 0];
                //     }
                //     $paramPrice[$fieldDetail->getField()->getId()]['total_price'] += $fieldDetail->getPrice();
                //     $paramPrice[$fieldDetail->getField()->getId()]['quantity'] ++;
                // }

                //prepare pictureDetail options for BIS
	            $pictureDetailOld = null;
                if (!is_null($currentLog)) {
                    $pictureDetailOld = clone $pictureDetail;
                    $logEntryRepo->revert($pictureDetailOld, $currentLog->getVersion());
                }

                if (($invoice->getCurrentOrder()->getPaymentDate() >= $pictureDetail->getCreated()) && $pictureDetailOld != NULL && $pictureDetail->equals($pictureDetailOld)) {
                    continue;
                }

                if (!isset($initialFiles[$pictureDetail->getRetouch()->getId()])) {
                    $initialFiles[$pictureDetail->getRetouch()->getId()] = ['retouch' => $pictureDetail->getRetouch(), 'vat_number' => 1, 'total_price' => 0, 'quantity' => 0];
                }

                $initialFiles[$pictureDetail->getRetouch()->getId()]['total_price'] += $pictureDetail->getPrice();
                $initialFiles[$pictureDetail->getRetouch()->getId()]['quantity'] ++;
                $initialFiles[$pictureDetail->getRetouch()->getId()]['price_per_unit'] = ($initialFiles[$pictureDetail->getRetouch()->getId()]['total_price'] / $initialFiles[$pictureDetail->getRetouch()->getId()]['quantity']);

                if ($pictureDetailOld != NULL && !$pictureDetail->equals($pictureDetailOld)) {
                    if (!isset($originalFiles[$pictureDetailOld->getRetouch()->getId()])) {
                        $originalFiles[$pictureDetailOld->getRetouch()->getId()] = ['retouch' => $pictureDetailOld->getRetouch()->getTitle(), 'vat_number' => 1, 'total_price' => 0, 'quantity' => 0];
                    }

                    $originalFiles[$pictureDetailOld->getRetouch()->getId()]['total_price'] += $pictureDetailOld->getPrice();
                    $originalFiles[$pictureDetailOld->getRetouch()->getId()]['quantity'] ++;
                    $originalFiles[$pictureDetailOld->getRetouch()->getId()]['price_per_unit'] = ($originalFiles[$pictureDetailOld->getRetouch()->getId()]['total_price'] / $originalFiles[$pictureDetailOld->getRetouch()->getId()]['quantity']);
                }
            }
        }

        return [$initialFiles, $paramPrice, $originalFiles, $paramPriceOld];
    }

    /**
     * Calculates the price for all selected params
     *
     * @param array|null    $uploadedFiles
     * @param array         $fields
     * @param array         $paramPrice
     *
     * @return array
     */
    public function getParamsTotalPrice(?array $uploadedFiles, array $fields, array $paramPrice): array
    {
        if (!is_null($uploadedFiles) && is_array($uploadedFiles)) {
            foreach ($uploadedFiles as $uploadedFile) {
                foreach ($fields as $field) {
                    if (!is_null($field->getPrice()) && isset($uploadedFile[$field->getName()]) && $uploadedFile[$field->getName()] == $field->getAddThePriceWhenValueEqualsTo()) {
                        if (!isset($paramPrice[$field->getId()])) {
                            $paramPrice[$field->getId()] = ['field_group' => $field->getFieldGroup(), 'field' => $field, 'total_price' => 0, 'price_per_unit' => $field->getPrice(), 'picture_number' => 0];
                        }
                        $paramPrice[$field->getId()]['total_price'] += $field->getPrice();
                        $paramPrice[$field->getId()]['picture_number'] ++;
                    }
                }
            }
        }
        return $paramPrice;
    }
}