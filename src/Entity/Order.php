<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="emmo_order")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true, hardDelete=false)
 */
class Order extends ApplicationType implements \Serializable
{
    /**
     * CLASS CONSTANT ORDER STATUS
     */
    public const IN_PRODUCTION  = 'order.status.in_production';
    public const AWAITING_FOR_PAYMENT  = 'order.status.awaiting_for_payment';
    public const AWAITING_FOR_CLIENT_RESPONSE  = 'order.status.awaiting_for_client_response';

    public const AWAITING_FOR_COMPLETION_BY_CLIENT = 'order.status.awaiting_complete_by_client';

    public const SEND_TO_CLIENT = 'order.status.send_to_client';
    public const DELIVERY_SHORT_TIME_READY = 'order.status.delivery_short_time_ready';

    public const COMPLETED = 'order.status.completed';
    public const PARTIALLY_COMPLETED = 'order.status.partially_completed';
    public const DECLINED_BY_PRODUCTION = 'order.status.declined_by_production';
    public const ERROR_CB  = 'order.status.error_cb';

    public const APPROVISIONNEMENT = 'order.status.approvisionnement_tirelire';

    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $orderNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     */
    protected $orderStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $sendEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $uploadFolder;

    /**
     * @var integer Total amount exclude taxe (only containe the price of the order)
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $totalAmount;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     */
    private $taxPercentage;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $reductionPercentage;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=3, scale=0, nullable=true)
     */
    private $totalReductionOnPictures;

    /**
     * @var integer
     *
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalReductionAmount;

    /**
     * DATE ATTRIBUTS
     */

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     */
    protected $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deliveranceDate;

    /**
     * @var \DateTime $paymentDate
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $paymentDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * Many OrderCreation have One user.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders", fetch="EXTRA_LAZY")
     */
    protected $client;

    /**
     * Many OrderCreation have One user.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", fetch="EXTRA_LAZY")
     */
    protected $affectedTo;

    /**

     * Many Picture has One OrderCreation.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="order", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @Assert\Valid()
     */
    protected $pictures;

    /**
     * Many Promo has One OrderCreation.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Promo", inversedBy="orders", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id_promo", referencedColumnName="id", nullable=true)
     */
    protected $promotion;

    /**
     * One Transaction has One OrderCreation.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Transaction", mappedBy="orderTransaction", cascade={"persist"})
     */
    protected $transaction;

    /**
     * One OrderCreation has One Production.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Production", inversedBy="orderEmmo", fetch="EXTRA_LAZY")
     */
    protected $production;

    /**
     * One OrderCreation has One OrderDeliveryTime.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderDeliveryTime", inversedBy="orders", fetch="EXTRA_LAZY")
     */
    private $deliveryTime;

    /**
     * Each OrderCreation relates to many Invoice.
     * Each Invoice also relates to many OrderCreation
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Invoice", inversedBy="currentOrders", cascade={"all"}, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $invoices;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $isChange = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $isRecharge = false;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    /**
     * Lifecycle Callbacks
     */

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function setUpdatedAt()
    {
        if (is_null($this->updatedAt)){
            $this->creationDate = new \DateTime();
        }

        $this->updatedAt = new \DateTime();
    }

    /**
     * GETTERS & SETTERS
     */

    public function getIsChange(): ?bool
    {
        return $this->isChange;
    }

    public function setIsChange(bool $isChange): self
    {
        $this->isChange = $isChange;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?int $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): self
    {
        $old_order_status = $this->orderStatus;

        $this->orderStatus = $orderStatus;

        if ($old_order_status != $this->orderStatus) {
            if (in_array($this->orderStatus, [self::DELIVERY_SHORT_TIME_READY, self::SEND_TO_CLIENT, self::COMPLETED])) {
                $this->deliveranceDate = new \DateTime('now');
            }
        }

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

    public function getSendEmail(): ?bool
    {
        return $this->sendEmail;
    }

    public function setSendEmail(bool $sendEmail): self
    {
        $this->sendEmail = $sendEmail;

        return $this;
    }

    public function getUploadFolder(): ?string
    {
        return $this->uploadFolder;
    }

    public function setUploadFolder(string $uploadFolder): self
    {
        $this->uploadFolder = $uploadFolder;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function setPictures(ArrayCollection $pictures): self
    {
        foreach ($pictures as $picture) {
            $this->addPicture($picture);
        }

        return $this;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setOrder($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getOrder() === $this) {
                $picture->setOrder(null);
            }
        }

        return $this;
    }

    public function getPromotion(): ?Promo
    {
        return $this->promotion;
    }

    public function setPromotion(?Promo $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getProduction(): ?Production
    {
        return $this->production;
    }

    public function setProduction(?Production $production): self
    {
        $this->production = $production;

        return $this;
    }

    public function getAffectedTo(): ?User
    {
        return $this->affectedTo;
    }

    public function setAffectedTo(?User $affectedTo): self
    {
        $this->affectedTo = $affectedTo;

        return $this;
    }

    public function getDeliveranceDate(): ?\DateTimeInterface
    {
        return $this->deliveranceDate;
    }

    public function setDeliveranceDate(?\DateTimeInterface $deliveranceDate): self
    {
        $this->deliveranceDate = $deliveranceDate;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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

    public function getDeliveryTime(): ?OrderDeliveryTime
    {
        return $this->deliveryTime;
    }

    public function setDeliveryTime(?OrderDeliveryTime $deliveryTime): self
    {
        $this->deliveryTime = $deliveryTime;

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

    public function getAmountIncludingTaxAfterReduction(): float
    {
        if (!is_null($this->findOneInvoiceByType([Invoice::MONTHLY_PER_USER])) || $this->getTotalAmount() == 0) {
          return $this->getTotalAmount();
        }

        $taxPrice = ($this->getTotalAmount() - $this->getTotalReductionAmount()) * ($this->getTaxPercentage() / 100);
        $totalPriceIncludingTax = $taxPrice + $this->getTotalAmount();

        return $totalPriceIncludingTax - $this->getTotalReductionAmount();
    }

    public function setTransaction(?Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
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

    /**
     * @return Collection|Invoice[]
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
        }

        return $this;
    }

    public function findOneInvoiceByType(array $types): ?Invoice
    {
        foreach ($types as $type) {
            $invoices = $this->invoices->filter(function (Invoice $invoice) use ($type){
                return $invoice->getType() == $type;
            });

            if (!$invoices->isEmpty()) {
                return $invoices->first();
            }
        }

        return null;
    }

	public function totalInvoiceAmount(): ?float
	{
		$sum = 0;
		foreach ($this->invoices as $invoice) {
			$sum += $invoice->getTotalAmountPaid();
		}

		return $sum;
	}

    public function findInvoiceByType(array $types): ArrayCollection
    {
        $invoices = $this->invoices->filter(function (Invoice $invoice) use ($types){
          return in_array($invoice->getType(), $types);
        });

        return $invoices;
    }

    /**
     * CUSTOM METHOD
     */

    public function isPayed():bool
    {
        return is_null($this->paymentDate) && in_array($this->getOrderStatus(), [Order::ERROR_CB, Order::AWAITING_FOR_PAYMENT, Order::AWAITING_FOR_COMPLETION_BY_CLIENT]);
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->orderNumber ? (string) $this->orderNumber : $this->creationDate->format('d-m-Y');
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->orderNumber,
          $this->orderStatus,
          $this->creationDate,
          $this->sendEmail,
          $this->uploadFolder,
          $this->deletedAt,
          $this->updatedAt,
          $this->totalAmount,
          $this->taxPercentage,
          $this->reductionPercentage,
          $this->deliveranceDate,
          $this->client,
          $this->affectedTo,
          $this->pictures,
          $this->promotion,
          $this->transaction,
          $this->production,
          $this->deliveryTime
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->orderNumber,
          $this->orderStatus,
          $this->creationDate,
          $this->sendEmail,
          $this->uploadFolder,
          $this->deletedAt,
          $this->updatedAt,
          $this->totalAmount,
          $this->taxPercentage,
          $this->reductionPercentage,
          $this->deliveranceDate,
          $this->client,
          $this->affectedTo,
          $this->pictures,
          $this->promotion,
          $this->transaction,
          $this->production,
          $this->deliveryTime
        ) = unserialize($data);
    }

    public function getIsRecharge(): ?bool
    {
        return $this->isRecharge;
    }

    public function setIsRecharge(bool $isRecharge): self
    {
        $this->isRecharge = $isRecharge;

        return $this;
    }
}
