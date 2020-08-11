<?php

namespace App\Events;

use Symfony\Component\EventDispatcher\Event;

use App\Entity\Invoice;

class MissingInvoicePdfEvent extends Event
{
    /**
     * @var Invoice
     */
    protected $invoice;

    /**
     * Constructor
     *
     * @param Invoice         $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the current invoice
     *
     * @return Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

}
