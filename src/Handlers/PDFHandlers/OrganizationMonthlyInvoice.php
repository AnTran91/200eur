<?php

namespace App\Handlers\PDFHandlers;

use App\Entity\Invoice;

class OrganizationMonthlyInvoice extends BaseInvoiceType
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
        $this->pdf->setClient($invoice->getOrganization()->getOwner());
        $this->populateOrganisationMonthlyInvoiceTable($invoice);

        $targetDir = join(DIRECTORY_SEPARATOR, [$this->media, $invoice->getPaymentDate()->format('Y-m-d')]);
        $this->storage->doCreateDirIfNotExists($targetDir);
        $this->pdf->Output(join(DIRECTORY_SEPARATOR, [$targetDir, $invoice->getPdfFileName() ]), 'F');
    }

    /**
     * @param Invoice $invoice
     */
    public function populateOrganisationMonthlyInvoiceTable(Invoice $invoice)
    {
        $this->pdf->setReductionTable($this->getFormattedOrder($invoice));

        $priceExcludingTax = $invoice->getTotalAmount();
        $taxPrice = ($priceExcludingTax - $invoice->getTotalReductionAmount()) * ($invoice->getTaxPercentage() / 100);

        $tvaData = [
            'tva_number' => 1,
            'price_excluding_price' => $priceExcludingTax,
            'tax_percentage' => $invoice->getTaxPercentage()."%",
            'tax_price' => $this->priceFormatter($taxPrice)
        ];

        $this->pdf->setTaxPrice($tvaData, null, $invoice->getOrganization()->getPromotions()->toArray());

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
        $formattedOrders = array();

        foreach ($invoice->getCurrentOrders() as $order){
            $formattedOrder['order_number'] = sprintf('%04d', $order->getOrderNumber());
            $formattedOrder['order_creation_date'] = $order->getCreationDate()->format('d/m/Y');
            $formattedOrder['order_client'] = $order->getClient()->getFullName();
            $formattedOrder['order_reduction_picture'] = $this->translator->trans('fpdf_invoice.picture_unit', ['%nb' => $order->getTotalReductionOnPictures()]);
            $formattedOrder['order_price'] = $order->getTotalReductionAmount();
            
            array_push($formattedOrders, $formattedOrder);
        }

        return $formattedOrders;
    }
}