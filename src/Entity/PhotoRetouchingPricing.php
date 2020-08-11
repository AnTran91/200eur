<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRetouchingPricingRepository")
 */
class PhotoRetouchingPricing implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var float
     *
     * @Assert\NotBlank(groups={"retouchCreation"})
     * @Assert\Range(min = 1, max = 99999, groups={"retouchCreation"})
     * @Assert\Regex(pattern="/\d/" ,match=true, groups={"retouchCreation"})
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    protected $price;

    /**
     * @var Retouch
     *
     * @Assert\NotBlank(groups={"retouchCreation"})
     * @ORM\ManyToOne(targetEntity="App\Entity\Retouch", inversedBy="pricings")
     */
    protected $retouch;

    /**
     * @var OrderDeliveryTime
     *
     * @Assert\NotBlank(groups={"retouchCreation"})
     * @ORM\ManyToOne(targetEntity="App\Entity\OrderDeliveryTime", inversedBy="retouchPrice")
     */
    protected $orderDeliveryTime;

    public function getId()
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getRetouch(): ?Retouch
    {
        return $this->retouch;
    }

    public function setRetouch(?Retouch $retouch): self
    {
        $this->retouch = $retouch;

        return $this;
    }

    public function getOrderDeliveryTime(): ?OrderDeliveryTime
    {
        return $this->orderDeliveryTime;
    }

    public function setOrderDeliveryTime(?OrderDeliveryTime $orderDeliveryTime): self
    {
        $this->orderDeliveryTime = $orderDeliveryTime;

        return $this;
    }

    /**
     * OVERRIDING
     */

    public function __toString()
    {
      return (string) $this->price;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->price
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->price
        ) = unserialize($data);
    }
}
