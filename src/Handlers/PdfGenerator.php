<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Entity\Invoice;
use App\Handlers\PDFHandlers as PDFHandlers;

class PdfGenerator
{
    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * @var PDFHandlers\AdditionalInvoice
     */
    private $additionalInvoice;

    /**
     * @var PDFHandlers\OrdinaryInvoice
     */
    private $ordinaryInvoice;

    /**
     * @var PDFHandlers\RechargeInvoice
     */
    private $rechargeInvoice;

    /**
     * @var PDFHandlers\WalletInvoice
     */
    private $walletInvoice;

    /**
     * @var PDFHandlers\OrganizationMonthlyInvoice
     */
    private $organizationMonthly;

    /**
     * @var PDFHandlers\UserMonthlyInvoice
     */
    private $userMonthlyInvoice;

    /**
     * PdfGenerator constructor.
     *
     * @param PDFHandlers\AdditionalInvoice             $additionalInvoice      Create additional type pdf invoice
     * @param PDFHandlers\OrdinaryInvoice               $ordinaryInvoice        Create ordinary type pdf invoice
     * @param PDFHandlers\WalletInvoice               $walletInvoice        Create wallet type pdf invoice
     * @param PDFHandlers\OrganizationMonthlyInvoice    $organizationMonthly    Create organization monthly type pdf invoice
     * @param PDFHandlers\UserMonthlyInvoice            $userMonthlyInvoice     Create user monthly type pdf invoice
     * @param PDFHandlers\RechargeInvoice            $rechargeInvoice     Create recharge type pdf invoice
     */
    public function __construct(PDFHandlers\AdditionalInvoice $additionalInvoice, PDFHandlers\OrdinaryInvoice $ordinaryInvoice, PDFHandlers\OrganizationMonthlyInvoice $organizationMonthly, PDFHandlers\UserMonthlyInvoice $userMonthlyInvoice, PDFHandlers\WalletInvoice $walletInvoice, PDFHandlers\RechargeInvoice $rechargeInvoice)
    {
        $this->additionalInvoice = $additionalInvoice;
        $this->ordinaryInvoice = $ordinaryInvoice;
        $this->organizationMonthly = $organizationMonthly;
        $this->userMonthlyInvoice = $userMonthlyInvoice;
        $this->walletInvoice = $walletInvoice;
        $this->rechargeInvoice = $rechargeInvoice;
    }

    /**
     * Set the invoice that will be used later to create the pdf
     *
     * @param Invoice $invoice
     * @return void
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * This method create the invoice and save it in the system file. (with ORDINARY type)
     *
     * @return void
     */
    public function createInvoicePdf(): void
    {
        if (is_null($this->invoice)){
            throw new \InvalidArgumentException("You forgot to pass the invoice");
        }

        if ($this->invoice->getType() == Invoice::RECHARGE) {
            $this->rechargeInvoice->createInvoicePdf($this->invoice);
        }

        if ($this->invoice->getType() == Invoice::WALLET) {
            $this->walletInvoice->createInvoicePdf($this->invoice);
        }

        // if the missing invoice is complementary or additional
        if ($this->invoice->getType() == Invoice::ADDITIONAL) {
            $this->additionalInvoice->createInvoicePdf($this->invoice);
        }

        // if the missing invoice is monthly and for an agency or a network
        if ($this->invoice->getType() == Invoice::MONTHLY_PER_ORGANIZATION) {
            $this->organizationMonthly->createInvoicePdf($this->invoice);
        }

        // if the missing invoice is monthly and for a single user
        if ($this->invoice->getType() == Invoice::MONTHLY_PER_USER) {
            $this->userMonthlyInvoice->createInvoicePdf($this->invoice);
        }

        // if the missing invoice is ordinary
        if ($this->invoice->getType() == Invoice::ORDINARY) {
            $this->ordinaryInvoice->createInvoicePdf($this->invoice);
        }
    }
}
