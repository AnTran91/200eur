<?php

namespace Tests\FunctionalTests\Controller\Admin;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Invoice;

use App\Handlers\InvoiceBuilder;
use App\Handlers\OrderBuilder;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class InvoiceBuilderTests extends WebTestCase
{
    /**
    * @var InvoiceBuilder
    */
    private $invoiceFactory;

    /**
    * @var Order
    */
    private $order;

    /**
    * @var Invoice
    */
    private $invoice;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->invoiceFactory = static::createClient()
                                    ->getContainer()
                                    ->get('test.invoice_factory');
    }

    /**
    * @return User mock
    */
    public function getUser(int $id)
    {
        $mockUser = $this->createMock('App\Entity\User');
        $mockUser->method('getId')
                 ->willReturn($id);
        return $mockUser;
    }

    /**
    * @return Invoice mock
    */
    public function getInvoice(int $orderId)
    {
        $mockInvoice = $this->createMock('App\Entity\Invoice');
        $mockInvoice->method('getCurrentOrder')
                    ->willReturn($this->getOrder($orderId, 1, 25, 0));
        $mockInvoice->method('getTotalAmountPaid')
                    ->willReturn(25);
        $mockInvoice->method('getTotalAmount')
                    ->willReturn(25);
        return $mockInvoice;
    }

    /**
    * @return Organization mock
    */
    public function getOrganization(int $id)
    {
        $mockOrganization = $this->createMock('App\Entity\Network');
        $mockOrganization->method('getId')
                         ->willReturn($id);

        return $mockOrganization;
    }

    /**
    * @return Order mock
    */
    public function getOrder(int $orderId, int $userId, float $amount, float $tax)
    {
        $mockOrder = $this->createMock('App\Entity\Order');
        $mockOrder->method('getId')
                 ->willReturn($orderId);
        $mockOrder->method('getClient')
                ->willReturn($this->getUser($userId));
        $mockOrder->method('getAmountIncludingTaxAfterReduction')
                ->willReturn($amount);
        $mockOrder->method('getTaxPercentage')
                ->willReturn($tax);

        return $mockOrder;
    }

    /**
    * @return Order mock
    */
    public function getOldOrder(int $orderId, int $userId, float $amount, float $tax)
    {
        $mockOrder = $this->createMock('App\Entity\Order');
        $mockOrder->method('getId')
                 ->willReturn($orderId);
        $mockOrder->method('getClient')
                ->willReturn($this->getUser($userId));
        $mockOrder->method('getAmountIncludingTaxAfterReduction')
                ->willReturn($amount);
        $mockOrder->method('getTaxPercentage')
                ->willReturn($tax);
        $mockOrder->method('findOneInvoiceByType')
                ->willReturn($this->getInvoice(10));

        return $mockOrder;
    }

    public function testGenerateInvoice()
    {
        $this->order = $this->getOrder(1, 1, 20, 0);
        $this->invoiceFactory->generateInvoice($this->order);

        $this->invoice = $this->invoiceFactory->getInvoice();

        $this->assertNotNull($this->invoice);
        $this->assertEquals($this->invoice->getTotalAmountPaid(), 20);

    }

    public function testGenerateAdditionalInvoice()
    {
        $this->order = $this->getOldOrder(1, 1, 20, 0);
        $this->invoice = $this->getInvoice(1);

        $this->invoiceFactory->reset($this->invoice);
        $this->invoiceFactory->generateTheAdditionalInvoice($this->order);

        $this->invoice = $this->invoiceFactory->getInvoice();

        $this->assertNotNull($this->invoice);
        $this->assertEquals($this->invoice->getTotalAmountPaid(), 25);
    }

    public function testInitInvoice()
    {
        $this->user = $this->getUser(1);
        $this->invoiceFactory->initInvoice($this->user, null);

        $invoice = $this->invoiceFactory->getInvoice();
        $this->assertNotNull($invoice);

    }

    public function testGenerateUserMonthlyInvoice()
    {
        $order = $this->getOrder(1, 10, 20, 0);
        $user = $this->getUser(10);

        $testInvoice = $this->getInvoice(1);
        $this->invoiceFactory->reset($testInvoice);

        $this->invoiceFactory->generateUserMonthlyInovice($order, $user);

        $invoice = $this->invoiceFactory->getInvoice();
        $this->assertNotNull($invoice);
    }
}
