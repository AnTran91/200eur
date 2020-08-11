<?php

namespace App\Handlers\PDFHandlers;

use App\Entity\Invoice;
use App\Entity\Order;

class UserMonthlyInvoice extends BaseInvoiceType
{
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
        $this->pdf->setOrders($invoice->getCurrentOrders());
        $this->populateUserMonthlyInvoiceTable($invoice);

        $targetDir = join(DIRECTORY_SEPARATOR, [$this->media, $invoice->getPaymentDate()->format('Y-m-d')]);
        $this->storage->doCreateDirIfNotExists($targetDir);
        $this->pdf->Output(join(DIRECTORY_SEPARATOR, [$targetDir, $invoice->getPdfFileName() ]), 'F');
    }

    public function populateUserMonthlyInvoiceTable(Invoice $invoice)
    {
        list($initialFiles, $paramPrice) = $this->getFormattedOrder($invoice);

        $this->pdf->setTable($initialFiles, $paramPrice);

        $priceExcludingTax = $invoice->getTotalAmount();
        $taxPrice = ($priceExcludingTax - $invoice->getTotalReductionAmount()) * ($invoice->getTaxPercentage() / 100);

        $tvaData = [
            'tva_number' => 1,
            'price_excluding_price' => $priceExcludingTax,
            'tax_percentage' => $invoice->getTaxPercentage()."%",
            'tax_price' => $this->priceFormatter($taxPrice)
        ];

        $this->pdf->setTaxPrice($tvaData);

        $totalData[$this->translator->trans('fpdf_invoice.total_wt')] = $this->priceFormatter($priceExcludingTax);
        $totalData[$this->translator->trans('fpdf_invoice.total_vat')] = $this->priceFormatter($taxPrice);
        if ($invoice->getTotalReductionAmount() > 0) {
            $totalData[$this->translator->trans('fpdf_invoice.total_reduction_picture')] = $this->translator->trans('fpdf_invoice.picture_unit', ['%nb' => $invoice->getTotalReductionOnPictures()]);
            $totalData[$this->translator->trans('fpdf_invoice.total_reduction_price')] = $this->priceFormatter($invoice->getTotalReductionAmount());
        }

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

        foreach ($invoice->getCurrentOrders() as $order) {
            if ($order->getOrderStatus() == Order::DECLINED_BY_PRODUCTION) {
              continue;
            }

            foreach ($order->getPictures() as $picture) {
                foreach ($picture->getPictureDetail() as $pictureDetail) {
                    foreach ($pictureDetail->getFieldDetails() as $fieldDetail) {
                        if (!isset($paramPrice[$fieldDetail->getField()->getId()])) {
                            $paramPrice[$fieldDetail->getField()->getId()] = ['field_group' => $fieldDetail->getField()->getFieldGroup(), 'vat_number' => 1, 'total_price' => 0, 'price_per_unit' => $fieldDetail->getPrice(), 'quantity' => 0];
                        }
                        $paramPrice[$fieldDetail->getField()->getId()]['total_price'] += $fieldDetail->getPrice();
                        $paramPrice[$fieldDetail->getField()->getId()]['quantity'] ++;
                    }

                    if (!isset($initialFiles[$pictureDetail->getRetouch()->getId()])) {
                        $initialFiles[$pictureDetail->getRetouch()->getId()] = ['retouch' => $pictureDetail->getRetouch(), 'vat_number' => 1, 'total_price' => 0, 'quantity' => 0];
                    }

                    $initialFiles[$pictureDetail->getRetouch()->getId()]['total_price'] += $pictureDetail->getPrice();
                    $initialFiles[$pictureDetail->getRetouch()->getId()]['quantity'] ++;
                    $initialFiles[$pictureDetail->getRetouch()->getId()]['price_per_unit'] = number_format(($initialFiles[$pictureDetail->getRetouch()->getId()]['total_price'] / $initialFiles[$pictureDetail->getRetouch()->getId()]['quantity']), 2);
                }
            }
        }

        return [$initialFiles, $paramPrice];
    }

}