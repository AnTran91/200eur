<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Invoice extends ApplicationType implements \Serializable
{
    /**
    * CONSTANT TYPES
    */
    public const MONTHLY_PER_USER = 'invoice.types.user_monthly';
    public const MONTHLY_PER_ORGANIZATION = 'invoice.types.organization_monthly';
    public const ORDINARY = 'invoice.types.ordinary';
    public const ADDITIONAL = 'invoice.types.additional_invoice';
    public const ADDITIONAL_ORDER = 'invoice.types.additional_order';
    public const WALLET = 'invoice.types.wallet_invoice';
    public const RECHARGE = 'invoice.types.recharge_invoice';

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="decimal", precision=10, scale=0, nullable=true)
     */
    protected $invoiceNumber;

    /**
     * @var int
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $pdfFileName;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $totalAmountPaid;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $totalAmount;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    protected $taxPercentage;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    protected $reductionPercentage;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=3, scale=0, nullable=true)
     */
    protected $totalReductionOnPictures;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $totalReductionAmount;

    /**
     * @var \DateTime $creationDate
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $creationDate;

    /**
     * @var \DateTime $paymentDate
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $paymentDate;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $type;

    /**
     * @var float
     *
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $beforeWallet;

    /**
     * @var float
     *
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $afterWallet;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * @var Order
     *
     * Each OrderCreation relates to (has) exactly one Invoice.
     * Each Invoice also relates to (has) exactly one OrderCreation
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Order", mappedBy="invoices", cascade={"all"}, fetch="EXTRA_LAZY")
     */
    protected $currentOrders;

    /**
     * @var Organization
     *
     * Each Invoice relates to (has) one Organization.
     * Each Organization can relate/has to (have) many Invoice objects
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="invoices", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $organization;

    /**
     * @var User
     *
     * Each Invoice relates to (has) one User.
     * Each User can relate/has to (have) many Invoice objects
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;

    /**
     * One invoice can have one transaction.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Transaction")
     */
    protected $transaction;

    public function __construct()
    {
        $this->currentOrders = new ArrayCollection();

        $this->appType = static::DEFAULT_APP_TYPE;
    }

    /**
     * Lifecycle Callbacks
     */

    /**
     * Set CreatedAt
     *
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->creationDate = new \DateTime();
    }

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getTotalAmountPaid()
    {
        return $this->totalAmountPaid;
    }

    public function setTotalAmountPaid($totalAmountPaid): self
    {
        $this->totalAmountPaid = $totalAmountPaid;

        return $this;
    }

    public function getTaxPercentage()
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage($taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }

    public function getReductionPercentage()
    {
        return $this->reductionPercentage;
    }

    public function setReductionPercentage($reductionPercentage): self
    {
        $this->reductionPercentage = $reductionPercentage;

        return $this;
    }

    public function getTotalReductionOnPictures()
    {
        return $this->totalReductionOnPictures;
    }

    public function setTotalReductionOnPictures($totalReductionOnPictures): self
    {
        $this->totalReductionOnPictures = $totalReductionOnPictures;

        return $this;
    }

    public function getTotalReductionAmount()
    {
        return $this->totalReductionAmount;
    }

    public function setTotalReductionAmount($totalReductionAmount): self
    {
        $this->totalReductionAmount = $totalReductionAmount;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function setTotalAmount($totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        if ($this->getType() === Invoice::ADDITIONAL) {
            return sprintf('%06d', $this->invoiceNumber);
        }

        return sprintf('%06d', $this->invoiceNumber);
    }

    public function setInvoiceNumber($invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getPdfFileName(): ?string
    {
        return $this->pdfFileName;
    }

    public function setPdfFileName(?string $pdfFileName): self
    {
        $this->pdfFileName = $pdfFileName;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function calculatePrices()
    {
        $this->totalAmount = 0;
        foreach ($this->currentOrders as $order) {
            if ($order->getOrderStatus() != Order::DECLINED_BY_PRODUCTION) {
                $this->totalAmount += $order->getTotalAmount();
            }
        }
        $this->totalAmountPaid = $this->totalAmount + ($this->totalAmount * $this->taxPercentage/100) - $this->totalReductionAmount; 
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getCurrentOrders(): Collection
    {
        return $this->currentOrders;
    }

    public function addCurrentOrder(Order $currentOrder): self
    {
        if (!$this->currentOrders->contains($currentOrder)) {
            $this->currentOrders[] = $currentOrder;
            $currentOrder->addInvoice($this);
        }

        return $this;
    }

    public function removeCurrentOrder(Order $currentOrder): self
    {
        if ($this->currentOrders->contains($currentOrder)) {
            $this->currentOrders->removeElement($currentOrder);
            $currentOrder->removeInvoice($this);
        }

        return $this;
    }

    public function getCurrentOrder(): ?Order
    {
        if (!$this->currentOrders->isEmpty() && $this->currentOrders->count() == 1) {
            return $this->currentOrders->first();
        }

        return null;
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return (string) $this->invoiceNumber;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->totalAmountPaid,
          $this->totalAmount,
          $this->taxPercentage,
          $this->reductionPercentage,
          $this->creationDate,
          $this->paymentDate
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->totalAmountPaid,
          $this->totalAmount,
          $this->taxPercentage,
          $this->reductionPercentage,
          $this->creationDate,
          $this->paymentDate
        ) = unserialize($data);
    }

    /**
     * Get the value of beforeWallet
     *
     * @return  float
     */ 
    public function getBeforeWallet()
    {
        return $this->beforeWallet;
    }

    /**
     * Set the value of beforeWallet
     *
     * @param  float  $beforeWallet
     *
     * @return  self
     */ 
    public function setBeforeWallet(float $beforeWallet)
    {
        $this->beforeWallet = $beforeWallet;

        return $this;
    }

    /**
     * Get the value of afterWallet
     *
     * @return  float
     */ 
    public function getAfterWallet()
    {
        return $this->afterWallet;
    }

    /**
     * Set the value of afterWallet
     *
     * @param  float  $afterWallet
     *
     * @return  self
     */ 
    public function setAfterWallet(float $afterWallet)
    {
        $this->afterWallet = $afterWallet;

        return $this;
    }

    /**
     * Get one invoice can have one transaction.
     */ 
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set one invoice can have one transaction.
     *
     * @return  self
     */ 
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }
}
