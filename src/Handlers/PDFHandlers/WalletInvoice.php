<?php

namespace App\Handlers\PDFHandlers;


use App\Entity\Invoice;
use App\Handlers\Traits\LogEntryTrait;
use Gedmo\Loggable\Entity\LogEntry;

class WalletInvoice extends BaseInvoiceType
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
        $this->pdf->setBeforeWallet(!is_null($invoice->getBeforeWallet()) ? $this->priceFormatter($invoice->getBeforeWallet()) : null);
        $this->pdf->setAfterWallet(!is_null($invoice->getAfterWallet()) ? $this->priceFormatter($invoice->getAfterWallet()) : null);
        $this->populateInvoiceTable($invoice);

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
    private function populateInvoiceTable(Invoice $invoice): void
    {
        list($initialFiles, $paramPrice) = $this->getFormattedOrder($invoice);

        $this->pdf->setTable($initialFiles, $paramPrice);

        $priceExcludingTax = $invoice->getTotalAmount();
        $taxPrice = ($priceExcludingTax - $invoice->getTotalReductionAmount()) * ($invoice->getTaxPercentage() / 100);

        $tvaData = [
            'tva_number' => 1,
            'price_excluding_price' => $this->priceFormatter($priceExcludingTax - $invoice->getTotalReductionAmount()),
            'tax_percentage' => $this->priceFormatter($invoice->getTaxPercentage())."%",
            'tax_price' => $this->priceFormatter($taxPrice)
        ];

        $this->pdf->setTaxPrice($tvaData, $invoice->getCurrentOrder()->getTransaction(), [$invoice->getCurrentOrder()->getPromotion()]);

        if ($invoice->getTotalReductionAmount() > 0) {
            $totalData[$this->translator->trans('fpdf_invoice.total_reduction_price')] = $this->priceFormatter($invoice->getTotalReductionAmount());
        }

        $totalData[$this->translator->trans('fpdf_invoice.total_wt')] = $this->priceFormatter($priceExcludingTax - $invoice->getTotalReductionAmount());
        
        if ($invoice->getTotalReductionAmount() > 0) {
            $totalData[$this->translator->trans('fpdf_invoice.total_reduction_picture')] = $this->translator->trans('fpdf_invoice.picture_unit', ['%nb' => $invoice->getTotalReductionOnPictures()]);
        }
        $totalData[$this->translator->trans('fpdf_invoice.total_vat')] = $this->priceFormatter($taxPrice);

        // $totalData[$this->translator->trans('fpdf_invoice.total_price')] = $this->priceFormatter($invoice->getTotalAmountPaid());
        $totalData[$this->translator->trans('fpdf_invoice.total_price')] = $this->priceFormatter(0);
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
        $logEntryRepo = $this->em->getRepository(LogEntry::class);

        foreach ($invoice->getCurrentOrder()->getPictures() as $picture) {
            foreach ($picture->getPictureDetail() as $pictureDetail) {

                if (!empty($logEntryRepo->getLogEntries($pictureDetail->getParam()))) {
                    $fieldOld = array();
                    $currentLogParam = $this->getTheClosestLogBeforePaymentDate($logEntryRepo->getLogEntries($pictureDetail->getParam()), $invoice->getCurrentOrder()->getPaymentDate());

                    $fields = $this->em->getRepository(\App\Entity\Field::class)->findAllByFieldThatHavePrice(null);
                    if ($currentLogParam != NULL) {
                        $paramOld = clone $pictureDetail->getParam();
                        $logEntryRepo->revert($paramOld, $currentLogParam->getVersion());
                        $fieldOld = $this->getParamsTotalPrice([$paramOld], $fields, []);

                        foreach ($fieldOld as $key => $value) {
                            $fieldTemp = $this->em->getRepository(\App\Entity\Field::class)->find($key);
                            if (!isset($paramPrice[$fieldTemp->getId()])) {
                                $paramPrice[$fieldTemp->getId()] = ['field_group' => $fieldTemp->getFieldGroup(), 'vat_number' => 1, 'total_price' => 0, 'price_per_unit' => $fieldTemp->getPrice(), 'quantity' => 0];
                            }
                            $paramPrice[$fieldTemp->getId()]['total_price'] += $fieldTemp->getPrice();
                            $paramPrice[$fieldTemp->getId()]['quantity'] ++;
                        }
                    }
                }
                // foreach ($pictureDetail->getFieldDetails() as $fieldDetail) {
                //     if (!isset($paramPrice[$fieldDetail->getField()->getId()])) {
                //         $paramPrice[$fieldDetail->getField()->getId()] = ['field_group' => $fieldDetail->getField()->getFieldGroup(), 'vat_number' => 1, 'total_price' => 0, 'price_per_unit' => $fieldDetail->getPrice(), 'quantity' => 0];
                //     }
                //     $paramPrice[$fieldDetail->getField()->getId()]['total_price'] += $fieldDetail->getPrice();
                //     $paramPrice[$fieldDetail->getField()->getId()]['quantity'] ++;
                // }

                if ($invoice->getCurrentOrder()->getPaymentDate() && ($invoice->getCurrentOrder()->getPaymentDate() < $pictureDetail->getCreated())) {
                    continue;
                }

                $logs = $logEntryRepo->getLogEntries($pictureDetail);
                if ($invoice->getCurrentOrder()->getPaymentDate() && !is_null($logs)) {
                    $currentLog = $this->getTheClosestLogToPaymentDate($logs, $invoice->getCurrentOrder()->getPaymentDate());
                    $logEntryRepo->revert($pictureDetail, $currentLog->getVersion());
                }

                if (!isset($initialFiles[$pictureDetail->getRetouch()->getId()])) {
                    $initialFiles[$pictureDetail->getRetouch()->getId()] = ['retouch' => $pictureDetail->getRetouch(), 'vat_number' => 1, 'total_price' => 0, 'quantity' => 0];
                }

                $initialFiles[$pictureDetail->getRetouch()->getId()]['total_price'] += $pictureDetail->getPrice();
                $initialFiles[$pictureDetail->getRetouch()->getId()]['quantity'] ++;
                $initialFiles[$pictureDetail->getRetouch()->getId()]['price_per_unit'] = ($initialFiles[$pictureDetail->getRetouch()->getId()]['total_price'] / $initialFiles[$pictureDetail->getRetouch()->getId()]['quantity']);
            }
        }

        return [$initialFiles, $paramPrice];
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