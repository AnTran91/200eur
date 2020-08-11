<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Intl\Intl;

/**
 * @ORM\Embeddable
 */
class BillingAddress implements \Serializable
{
    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @var string
     *
     * @Assert\NotBlank(groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     * @Assert\Length(min = 2, max = 70, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     * @Assert\Length(min = 2, max = 100, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     * @Assert\Length(min = 2, max = 255, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=255, nullable=True)
     */
    protected $address;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 100, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=100, nullable=True)
     */
    protected $country;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 150, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=150, nullable=True)
     */
    protected $city;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 20, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=20, nullable=True)
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     * @ORM\Column(type="string", length=50, nullable=True)
     */
    protected $phone;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 200, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=200, nullable=True)
     */
    protected $company;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 255, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $networkName;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 255, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $secondaryAddress;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 255, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $corporateName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     *
     * @Assert\Length(max = 200, groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     * @Assert\Expression("this.getCountry() in ['FR'] or (this.getCountry() not in ['FR'] and value !== null)", groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     */
    private $TVA;

    /**
     * GETTERS & SETTERS
     */

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getNetworkName(): ?string
    {
        return $this->networkName;
    }

    public function setNetworkName(?string $networkName): self
    {
        $this->networkName = $networkName;

        return $this;
    }

    public function getSecondaryAddress(): ?string
    {
        return $this->secondaryAddress;
    }

    public function setSecondaryAddress(?string $secondaryAddress): self
    {
        $this->secondaryAddress = $secondaryAddress;

        return $this;
    }

    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    public function setCorporateName(?string $corporateName): self
    {
        $this->corporateName = $corporateName;

        return $this;
    }

    public function getTVA(): ?string
    {
        return $this->TVA;
    }

    public function setTVA(?string $TVA): self
    {
        $this->TVA = $TVA;

        return $this;
    }

    /**
     * CUSTOM METHOD
     */
    public function getFullAddress()
    {
        return sprintf("%s, %s, %s", $this->zipCode, $this->city, Intl::getRegionBundle()->getCountryName($this->country, 'fr'));
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->firstName,
          $this->lastName,
          $this->address,
          $this->country,
          $this->city,
          $this->zipCode,
          $this->phone,
          $this->company,
          $this->networkName,
          $this->secondaryAddress,
          $this->corporateName,
          $this->TVA
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->firstName,
          $this->lastName,
          $this->address,
          $this->country,
          $this->city,
          $this->zipCode,
          $this->phone,
          $this->company,
          $this->networkName,
          $this->secondaryAddress,
          $this->corporateName,
          $this->TVA
        ) = unserialize($data);
    }
}
