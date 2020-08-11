<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderDeliveryTimeRepository")
 */
class OrderDeliveryTime extends ApplicationType implements \Serializable, \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[0-9]+$/i", match=true)
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $time;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_-]+$/i" ,match=true)
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $orderDeliveryCode;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $global;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $selectedByDefault;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * Each OrderDeliveryTime relates can relate to (have) many PhotoRetouchingPricing objects.
     * Each PhotoRetouchingPricing relates to (has) one OrderDeliveryTime
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PhotoRetouchingPricing", mappedBy="orderDeliveryTime")
     */
    private $retouchPrice;

    /**
     * Each OrderDeliveryTime relates can relate to (have) many OrderCreation objects.
     * Each OrderCreation relates to (has) one OrderDeliveryTime
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="deliveryTime")
     */
    private $orders;

    public function __construct()
    {
        $this->retouchPrice = new ArrayCollection();
        $this->orders = new ArrayCollection();

        $this->appType = self::DEFAULT_APP_TYPE;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime($time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return Collection|PhotoRetouchingPricing[]
     */
    public function getRetouchPrice(): Collection
    {
        return $this->retouchPrice;
    }

    public function addRetouchPrice(PhotoRetouchingPricing $retouchPrice): self
    {
        if (!$this->retouchPrice->contains($retouchPrice)) {
            $this->retouchPrice[] = $retouchPrice;
            $retouchPrice->setOrderDeliveryTime($this);
        }

        return $this;
    }

    public function removeRetouchPrice(PhotoRetouchingPricing $retouchPrice): self
    {
        if ($this->retouchPrice->contains($retouchPrice)) {
            $this->retouchPrice->removeElement($retouchPrice);
            // set the owning side to null (unless already changed)
            if ($retouchPrice->getOrderDeliveryTime() === $this) {
                $retouchPrice->setOrderDeliveryTime(null);
            }
        }

        return $this;
    }

    public function isGlobal(): ?bool
    {
        return $this->global;
    }

    public function getGlobal(): ?bool
    {
        return $this->global;
    }

    public function setGlobal(?bool $global): self
    {
        $this->global = $global;

        return $this;
    }

    public function isSelectedByDefault(): ?bool
    {
        return $this->selectedByDefault;
    }

    public function getSelectedByDefault(): ?bool
    {
        return $this->selectedByDefault;
    }

    public function setSelectedByDefault(?bool $selectedByDefault): self
    {
        $this->selectedByDefault = $selectedByDefault;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getOrderDeliveryCode(): ?string
    {
        return $this->orderDeliveryCode;
    }

    /**
     * @param null|string $orderDeliveryCode
     * @return OrderDeliveryTime
     */
    public function setOrderDeliveryCode(?string $orderDeliveryCode): self
    {
        $this->orderDeliveryCode = $orderDeliveryCode;
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
            $order->setDeliveryTime($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getDeliveryTime() === $this) {
                $order->setDeliveryTime(null);
            }
        }

        return $this;
    }

    /**
     * VALIDATION
     */

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     * @param array $payload
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->global === true && empty($this->orderDeliveryCode) && $this->appType == OrderDeliveryTime::IMMOSQUARE_TYPE) {
            $context->buildViolation('This value should not be blank.')
                ->atPath('orderDeliveryCode')
                ->addViolation();
        }

        if ($this->selectedByDefault === true && $this->global === false && $this->appType == OrderDeliveryTime::IMMOSQUARE_TYPE) {
            $context->buildViolation('This value should not be blank.')
                ->atPath('global')
                ->addViolation();
        }
    }

    /**
     * OVERRIDING
     */

     /**
      * {@inheritDoc}
      */
     public function jsonSerialize(): array
     {
         return [
           "id" => $this->id,
           "time" => $this->time,
           "unit" => $this->unit,
           "global" => $this->global,
           "selectedByDefault" => $this->selectedByDefault
         ];
     }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->time,
          $this->unit,
          $this->global,
          $this->selectedByDefault
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->time,
          $this->unit,
          $this->global,
          $this->selectedByDefault
        ) = unserialize($data);
    }
}
