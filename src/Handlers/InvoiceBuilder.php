<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handlers;

use App\Entity\Invoice;
use App\Entity\Order;
use App\Entity\Organization;
use App\Entity\User;

class InvoiceBuilder
{
    /**
     * @var OrderHandler $orderHandler
     */
    private $orderHandler;

    /**
     * @var Invoice $invoice
     */
    private $invoice;

    /**
     * @var OrderHandler $orderHandler
     */
    public function __construct(OrderHandler $orderHandler)
    {
        $this->orderHandler = $orderHandler;
    }

    /**
     * Create order object
     *
     * @param Invoice|null $invoice
     * @return InvoiceBuilder
     */
    public function reset(?Invoice $invoice = null): self
    {
        $this->invoice = $invoice;

        return $this;
    }

  /**
   * Generate the inovice for wallet
   *
   * @param Order $order
   * @return InvoiceBuilder
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
    public function generateRechargeInvoice(Order $order): self
    {
        if (is_null($this->invoice)) {
            $this->initInvoice($order->getClient(), null, Invoice::RECHARGE);
        }
        $this->invoice
          ->addCurrentOrder($order)
          ->setTotalAmountPaid($order->getClient()->getBillingAddress()->getCountry() === "FR" ? ($order->getTotalAmount() * 6 / 5) : $order->getTotalAmount())
          ->setTotalAmount($order->getTotalAmount())
          ->setTaxPercentage($order->getTaxPercentage())
          ->setReductionPercentage($order->getReductionPercentage())
          ->setTotalReductionOnPictures($order->getTotalReductionOnPictures())
          ->setTotalReductionAmount($order->getTotalReductionAmount())
          ->setAppType($order->getAppType())
        ;

        $order->addInvoice($this->invoice);

        return $this;
    }
	
	/**
	 * Generate the inovice for this order
	 *
	 * @param Order $order
	 * @return InvoiceBuilder
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function generateInvoice(Order $order): self
    {
        if (is_null($this->invoice)) {
            $this->initInvoice($order->getClient(), null);
        }
        $this->invoice
          ->addCurrentOrder($order)
          ->setTotalAmountPaid(is_null($order->getTransaction()) ? $order->getAmountIncludingTaxAfterReduction() : $order->getTransaction()->getAmount())
          ->setTotalAmount($order->getTotalAmount())
          ->setTaxPercentage($order->getTaxPercentage())
          ->setReductionPercentage($order->getReductionPercentage())
          ->setTotalReductionOnPictures($order->getTotalReductionOnPictures())
          ->setTotalReductionAmount($order->getTotalReductionAmount())
          ->setAppType($order->getAppType())
        ;

        $order->addInvoice($this->invoice);

        return $this;
    }

    /**
     * Generate the wallet invoice for this order
     *
     * @param Order $order
     * @return InvoiceBuilder
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function generateTheWalletInvoice(Order $order): self
    {
        if (is_null($this->invoice)) {
            $this->initInvoice($order->getClient(), null, Invoice::WALLET);
        }

        $afterWallet = $order->getClient()->getWallet()->getCurrentAmount();
        $beforeWallet = $afterWallet + $order->getAmountIncludingTaxAfterReduction();

        $this->invoice
            ->addCurrentOrder($order)
            ->setTotalAmountPaid(is_null($order->getTransaction()) ? $order->getAmountIncludingTaxAfterReduction() : $order->getTransaction()->getAmount())
            ->setTotalAmount($order->getTotalAmount())
            ->setTaxPercentage($order->getTaxPercentage())
            ->setReductionPercentage($order->getReductionPercentage())
            ->setTotalReductionOnPictures($order->getTotalReductionOnPictures())
            ->setTotalReductionAmount($order->getTotalReductionAmount())
            ->setAppType($order->getAppType())
            ->setBeforeWallet(is_null($this->invoice->getBeforeWallet()) ? $beforeWallet : null)
            ->setAfterWallet(is_null($this->invoice->getAfterWallet()) ? $afterWallet : null)
            ->setPdfFileName(sprintf('COMMANDE-TIRELIRE-%06d-%s.pdf', $order->getOrderNumber(), (new \DateTime('now'))->format('d-m-Y')));
        ;

        $order->addInvoice($this->invoice);

        return $this;
    }
	
	/**
	 * Generate the additional invoice for this order
	 *
	 * @param Order $order
	 * @return InvoiceBuilder
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function generateTheAdditionalInvoice(Order $order): self
    {
        $invoice = $order->findOneInvoiceByType([Invoice::ORDINARY, Invoice::WALLET]);

        if (is_null($this->invoice)) {
            $this->initInvoice($invoice->getClient(), null, Invoice::ADDITIONAL);
        }

        $this->invoice
          ->addCurrentOrder($order)
          ->setTotalAmountPaid($order->getAmountIncludingTaxAfterReduction() - $invoice->getTotalAmountPaid())
          ->setTotalAmount($order->getTotalAmount() - $invoice->getTotalAmount())
          ->setTaxPercentage($order->getTaxPercentage())
          ->setReductionPercentage($order->getReductionPercentage())
          ->setTotalReductionOnPictures($order->getTotalReductionOnPictures())
          ->setTotalReductionAmount($order->getTotalReductionAmount())
          ->setAppType($order->getAppType())
        ;

        $order->addInvoice($this->invoice);

        return $this;
    }
	
	/**
	 * Generate the organization monthly inovice for this order
	 *
	 * @param Order $order
	 * @param Organization|null $organization
	 * @return InvoiceBuilder
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function generateOrganizationMonthlyInvoice(Order $order, ?Organization $organization): self
    {
        if (is_null($this->invoice)) {
            $this->initInvoice($organization->getOwner(), $organization, Invoice::MONTHLY_PER_ORGANIZATION);
        }

        $totalToPay = $order->getTotalReductionAmount() + $this->invoice->getTotalAmount();
        $totalTax = $totalToPay * ($this->invoice->getTaxPercentage() / 100);

        $this->invoice
          ->addCurrentOrder($order)
          ->setTotalAmountPaid($totalTax + $totalToPay)
          ->setTotalAmount($totalToPay)
          ->setTotalReductionOnPictures($this->invoice->getTotalReductionOnPictures() + $order->getTotalReductionOnPictures())
          ->setAppType($order->getAppType())
        ;

        $order->addInvoice($this->invoice);

        return $this;
    }
	
	/**
	 * Generate the user monthly inovice for this order
	 *
	 * @param Order $order
	 * @param User|null $user
	 * @return InvoiceBuilder
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    public function generateUserMonthlyInovice(Order $order, ?User $user): self
    {
        if (is_null($this->invoice)) {
            $this->initInvoice($user, null, Invoice::MONTHLY_PER_USER);
        }

        $totalReductionPrice = $this->invoice->getTotalReductionAmount() + $order->getTotalReductionAmount();
        $totalToPay = $order->getTotalAmount() + $this->invoice->getTotalAmount();
        $totalTax = ($totalToPay * ($this->invoice->getTaxPercentage() / 100)) - $totalReductionPrice;

        $this->invoice
          ->addCurrentOrder($order)
          ->setTotalReductionAmount($totalReductionPrice)
          ->setTotalAmountPaid($totalTax + $totalToPay)
          ->setTotalAmount($totalToPay)
          ->setTotalReductionOnPictures($this->invoice->getTotalReductionOnPictures() + $order->getTotalReductionOnPictures())
          ->setAppType($order->getAppType())
        ;

        $order->addInvoice($this->invoice);

        return $this;
    }
	
	/**
	 * Generate the monthly invoice for this order
	 *
	 * @param User|null $user
	 * @param Organization|null $organization
	 * @param string $invoiceType
	 * @return InvoiceBuilder
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
    private function initInvoice(?User $user, ?Organization $organization, string $invoiceType = Invoice::ORDINARY): self
    {
        $invoiceNumber = $this->orderHandler->getTheLastInvoiceNumber();
        $tax = $this->orderHandler->calculateTax($user);
        $this->invoice = new Invoice();

        $this->invoice
          ->setOrganization($organization)
          ->setClient($user)
          ->setTaxPercentage($tax)
          ->setPaymentDate(new \DateTime('now'))
          ->setType($invoiceType)
        ;

        if ($invoiceType !== Invoice::WALLET){
            $this->invoice->setInvoiceNumber(sprintf('%06d', $invoiceNumber));
            $this->invoice->setPdfFileName(sprintf('%06d-%s.pdf', $invoiceNumber, (new \DateTime('now'))->format('d-m-Y')));
        }

        return $this;
    }

    /**
    * @return Invoice
    */
    public function getInvoice()
    {
        return $this->invoice;
    }
}
