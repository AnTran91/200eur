<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromoRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"promo" = "Promo", "pictureDiscount" = "PictureDiscount", "pictureCounter" = "PictureCounter"})
 */
class Promo implements \Serializable
{
    /**
     * CLASS CONSTANT
     */
    public const ASSIGN_TO_ALL_CUSTOMERS = 'promo.type.assign_to_all_customers';
    public const ASSIGN_TO_NETWORK = 'promo.type.assign_to_network';
    public const ASSIGN_TO_AGENCY = 'promo.type.assign_to_agency';
    public const ASSIGN_TO_SPECIFIC_CUSTOMERS = 'promo.type.assign_to_specific_customers';

    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 150)
     *
     * @ORM\Column(type="string", length=150)
     */
    protected $promoCode;

    /**
     * @var \DateTimeInterface
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     *
     * @ORM\Column(type="date")
     */
    protected $startDate;

    /**
     * @var \DateTimeInterface
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     *
     * @ORM\Column(type="date")
     *
     * @Assert\Expression("value and this.getStartDate() and value > this.getStartDate()")
     */
    protected $endDate;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $expired;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150)
     */
    protected $promoType;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * @var ArrayCollection
     *
     * One Promo has Many Orders.
     *
     * @ORM\OneToMany(targetEntity="Order", mappedBy="promotion", fetch="EXTRA_LAZY")
     */
    protected $orders;

    /**
     * @var Organization
     *
     * Each Promo relates to (has) one Organization.
     * Each Organization can relate/has to (have) many Promo objects
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="promotions")
     */
    protected $organization;

    /**
     * @var User
     *
     * One Agency has Many Promo.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="promotions")
     */
    protected $clients;

    /**
     * @ORM\Column(type="integer")
     */
    protected $minimumImage;

    /**
     * @ORM\Column(type="integer")
     */
    protected $minimumPrestation;

    /**
     * GETTERS & SETTERS
     */

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->clients = new ArrayCollection();

        $this->promoType = self::ASSIGN_TO_ALL_CUSTOMERS;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPromoCode(): ?string
    {
        return $this->promoCode;
    }

    public function setPromoCode(string $promoCode): self
    {
        $this->promoCode = $promoCode;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isExpired(): ?bool
    {
        return $this->expired;
    }

    public function getExpired(): ?bool
    {
        return $this->expired;
    }

    public function setExpired(?bool $expired): self
    {
        $this->expired = $expired;

        return $this;
    }

    public function getPromoType(): ?string
    {
        return $this->promoType;
    }

    public function setPromoType(string $promoType): self
    {
        $this->promoType = $promoType;

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setPromotion($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getPromotion() === $this) {
                $order->setPromotion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(User $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
        }

        return $this;
    }

    public function removeClient(User $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
        }

        return $this;
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

    public function getMinimumImage(): ?string
    {
        return $this->minimumImage;
    }

    public function setMinimumImage(string $minimumImage): self
    {
        $this->minimumImage = $minimumImage;

        return $this;
    }

    public function getMinimumPrestation(): ?string
    {
        return $this->minimumPrestation;
    }

    public function setMinimumPrestation(string $minimumPrestation): self
    {
        $this->minimumPrestation = $minimumPrestation;

        return $this;
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return (string) $this->promoCode;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->promoCode,
          $this->startDate,
          $this->endDate,
          $this->expired,
          $this->promoType,
          $this->orders,
          $this->clients->toArray()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->promoCode,
          $this->startDate,
          $this->endDate,
          $this->expired,
          $this->promoType,
          $this->orders,
          $this->clients
        ) = unserialize($data);
    }
}
