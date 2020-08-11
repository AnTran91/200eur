<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NetworkRepository")
 *
 * @UniqueEntity(
 *     fields={"registrationCode"},
 *     repositoryMethod="findUniqueCriteria"
 * )
 */
class Network extends Organization
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * One Network has Many Agency.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Agency", mappedBy="network")
     */
    private $agencies;

    public function __construct()
    {
        $this->agencies = new ArrayCollection();
        parent::__construct();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Agency[]
     */
    public function getAgencies(): Collection
    {
        return $this->agencies;
    }

    public function addAgency(Agency $agency): self
    {
        if (!$this->agencies->contains($agency)) {
            $this->agencies[] = $agency;
            $agency->setNetwork($this);
        }

        return $this;
    }

    public function removeAgency(Agency $agency): self
    {
        if ($this->agencies->contains($agency)) {
            $this->agencies->removeElement($agency);
            // set the owning side to null (unless already changed)
            if ($agency->getNetwork() === $this) {
                $agency->setNetwork(null);
            }
        }

        return $this;
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
