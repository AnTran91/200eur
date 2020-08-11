<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"organization" = "Organization", "agency" = "Agency", "network" = "Network"})
 *
 */
class Organization
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User
     *
     * @Assert\NotBlank()
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $owner;

    /**
     * @var string
     *
     * @Assert\Length(max = 255)
     *
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    protected $registrationCode;

    /**
     * @var ArrayCollection
     *
     * One Organization has Many Promo.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Promo", mappedBy="organization", fetch="EXTRA_LAZY")
     */
    protected $promotions;

    /**
     * @var ArrayCollection
     *
     * One Organization has Many Invoice.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Invoice", mappedBy="organization", fetch="EXTRA_LAZY")
     */
    protected $invoices;

    /**
     * @var ArrayCollection
     *
     * One Organization has Many User.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="organization")
     */
    protected $employees;

    public function __construct()
    {
        $this->promotions = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->employees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistrationCode(): ?string
    {
        return $this->registrationCode;
    }

    public function setRegistrationCode(?string $registrationCode): self
    {
        $this->registrationCode = $registrationCode;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Promo[]
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promo $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions[] = $promotion;
            $promotion->setOrganization($this);
        }

        return $this;
    }

    public function removePromotion(Promo $promotion): self
    {
        if ($this->promotions->contains($promotion)) {
            $this->promotions->removeElement($promotion);
            // set the owning side to null (unless already changed)
            if ($promotion->getOrganization() === $this) {
                $promotion->setOrganization(null);
            }
        }

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
            $invoice->setOrganization($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->contains($invoice)) {
            $this->invoices->removeElement($invoice);
            // set the owning side to null (unless already changed)
            if ($invoice->getOrganization() === $this) {
                $invoice->setOrganization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getEmployees(): ?Collection
    {
        return $this->employees;
    }

    public function addEmployee(User $employee): self
    {
        if (!$this->employees->contains($employee)) {
            $this->employees[] = $employee;
            $employee->setOrganization($this);
        }

        return $this;
    }

    public function removeEmployee(User $employee): self
    {
        if ($this->employees->contains($employee)) {
            $this->employees->removeElement($employee);
            // set the owning side to null (unless already changed)
            if ($employee->getOrganization() === $this) {
                $employee->setOrganization(null);
            }
        }

        return $this;
    }
}
