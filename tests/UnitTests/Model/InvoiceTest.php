<?php

namespace tests\UnitTests\Model;

use App\Entity\Invoice;
use App\Entity\Order;
use App\Entity\BillingAddress;

use PHPUnit\Framework\TestCase;


class InvoiceTest extends TestCase
{

  // public function testPdfFileName()
  // {
  //
  // }

  public function testCalculatePrices()
  {
    $orders = [new Order(), new Order()];

    $orders[0]->setTotalAmount(5);
    $orders[1]->setTotalAmount(5);

    $invoice = new Invoice();

    foreach($orders as $order){
      $invoice->addCurrentOrder($order);
    }

    $invoice->calculatePrices();
    $this->assertSame($invoice->getTotalAmountPaid(), 10);

    $invoice->setTaxPercentage(20);
    $invoice->calculatePrices();
    $this->assertSame($invoice->getTotalAmountPaid(), 12);
  }

}
