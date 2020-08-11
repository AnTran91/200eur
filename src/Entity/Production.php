<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductionRepository")
 */
class Production implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * One Production relates to Many Orders.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="production", fetch="EXTRA_LAZY")
     */
    private $orderEmmo;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=50)
     */
    private $country;

    public function __construct()
    {
        $this->orderEmmo = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrderEmmo(): Collection
    {
        return $this->orderEmmo;
    }

    public function addOrderEmmo(Order $orderEmmo): self
    {
        if (!$this->orderEmmo->contains($orderEmmo)) {
            $this->orderEmmo[] = $orderEmmo;
            $orderEmmo->setProduction($this);
        }

        return $this;
    }

    public function removeOrderEmmo(Order $orderEmmo): self
    {
        if ($this->orderEmmo->contains($orderEmmo)) {
            $this->orderEmmo->removeElement($orderEmmo);
            // set the owning side to null (unless already changed)
            if ($orderEmmo->getProduction() === $this) {
                $orderEmmo->setProduction(null);
            }
        }

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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
        return (string) $this->country;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->country
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->country
        ) = unserialize($data);
    }
}
