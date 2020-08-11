<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AgencyRepository")
 *
 * @UniqueEntity(
 *     fields={"registrationCode"},
 *     repositoryMethod="findUniqueCriteria"
 * )
 */
class Agency extends Organization
{
    /**
     * @var string
     *
     * @Assert\NotNull()
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var Network
     *
     * One Network has Many Agency.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Network", inversedBy="agencies")
     */
    private $network;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(?Network $network): self
    {
        $this->network = $network;

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
