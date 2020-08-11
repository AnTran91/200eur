<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use FOS\UserBundle\Model\User  as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 *
 * @UniqueEntity(fields={"email"}, groups={"EmmobilierProfile", "AdminUserCreation"})
 */
class User extends BaseUser
{
    /**
     * CLASS CONSTANT
     */

    /**
     * @var string user types
     */
    public const USER_MONTHLY = 'user.types.monthly';
    public const USER_ORDINARY = 'user.types.ordinary';

    /**
     * @var string default user role
     */
    const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var File
     *
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Assert\Image(
     *     detectCorrupted = true,
     *     maxSize = "3024k",
     *    groups= {"EmmobilierProfile"}
     * )
     *
     * @Vich\UploadableField(mapping="image_user", fileNameProperty="imageName")
     */
    protected $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=True)
     */
    protected $imageName;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups= {"EmmobilierProfile", "AdminUserCreation"})
     * @Assert\Length(min = 2, max = 200, groups= {"EmmobilierProfile", "AdminUserCreation"})
     *
     * @ORM\Column(type="string", length=200, nullable=True)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups= {"EmmobilierProfile", "AdminUserCreation"})
     * @Assert\Length(min = 2, max = 200, groups= {"EmmobilierProfile", "AdminUserCreation"})
     *
     * @ORM\Column(type="string", length=150, nullable=True)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 255, groups= {"EmmobilierRegistration", "EmmobilierProfile", "AdminUserCreation"})
     * @Assert\Email(groups= {"EmmobilierRegistration", "EmmobilierProfile", "AdminUserCreation"})
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $emailSecondary;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 200, groups= {"EmmobilierProfile", "AdminUserCreation"})
     *
     * @ORM\Column(type="string", length=200, nullable=True)
     */
    protected $registerCode;

    /**
     * @var string
     *
     * @Assert\Length(min = 2, max = 200, groups= {"EmmobilierProfile", "AdminUserCreation"})
     *
     * @ORM\Column(type="string", length=200, nullable=True)
     */
    protected $language;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=True)
     */
    protected $userDirectory;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=True)
     */
    protected $apiToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebookId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $facebookAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $googleId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $googleAccessToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
    * @var \DateTime
    *
    * @ORM\Column(type="datetime", nullable=true)
    */
    protected $createdAt;

    /**
     * @var bool
     *
     * @Assert\NotBlank(groups= {"EmmobilierRegistration", "EmmobilierProfile"})
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $acceptConditions;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $receiveNewsletter;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $receiveTargetedEmailsFromPromotion;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $type;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * Separating Concerns using Embeddables
     *
     * @var BillingAddress
     *
     * @ORM\Embedded(class = "BillingAddress")
     *
     * @Assert\Valid()
     */
    protected $billingAddress;

    /**
     * @var ArrayCollection
     *
     * One User has Many Orders.
     * @ORM\OneToMany(targetEntity="Order", mappedBy="client", fetch="EXTRA_LAZY")
     */
    protected $orders;

    /**
     * @var ArrayCollection
     *
     * Many Transaction relates to one User.
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="client", fetch="EXTRA_LAZY")
     */
    protected $transactions;

    /**
     * @var Wallet
     *
     * One client has One wallet.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Wallet", mappedBy="client", cascade={"persist", "remove"}, fetch="EAGER")
     * @Assert\Valid()
     */
    protected $wallet;

    /**
     * @var Organization
     *
     * One Organization has Many client.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="employees", fetch="EXTRA_LAZY")
     */
    protected $organization;

    /**
     * @var ArrayCollection
     *
     * One Promo has Many client.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Promo", mappedBy="clients", fetch="EXTRA_LAZY")
     */
    protected $promotions;

    /**
     * @var ArrayCollection
     *
     * Many User has Many groups.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;


    public function __construct()
    {
        $this->billingAddress = new BillingAddress();
        $this->orders = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->groups = new ArrayCollection();

        $this->type = self::USER_ORDINARY;

        parent::__construct();


    }

    /**
     * Lifecycle Callbacks
     */

    /**
     * Set CreatedAtAt
     *
     * @ORM\PrePersist
     */
    public function createdAt()
    {
        $this->createdAt = new \DateTime('now');
	    $this->updatedAt = new \DateTime('now');
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function updatedAt()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return User
     * @throws \Exception
     */
    public function setImageFile(File $image = null): self
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(BillingAddress $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

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

    public function getEmailSecondary(): ?string
    {
        return $this->emailSecondary;
    }

    public function setEmailSecondary(?string $emailSecondary): self
    {
        $this->emailSecondary = $emailSecondary;

        return $this;
    }

    public function getRegisterCode(): ?string
    {
        return $this->registerCode;
    }

    public function setRegisterCode(?string $registerCode): self
    {
        $this->registerCode = $registerCode;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getUserDirectory(): ?string
    {
        return $this->userDirectory;
    }

    public function setUserDirectory(?string $userDirectory): self
    {
        $this->userDirectory = $userDirectory;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): self
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getFacebookAccessToken(): ?string
    {
        return $this->facebookAccessToken;
    }

    public function setFacebookAccessToken(?string $facebookAccessToken): self
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getGoogleAccessToken(): ?string
    {
        return $this->googleAccessToken;
    }

    public function setGoogleAccessToken(?string $googleAccessToken): self
    {
        $this->googleAccessToken = $googleAccessToken;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getReceiveNewsletter(): ?bool
    {
        return $this->receiveNewsletter;
    }

    public function setReceiveNewsletter(?bool $receiveNewsletter): self
    {
        $this->receiveNewsletter = $receiveNewsletter;

        return $this;
    }

    public function getReceiveTargetedEmailsFromPromotion(): ?bool
    {
        return $this->receiveTargetedEmailsFromPromotion;
    }

    public function setReceiveTargetedEmailsFromPromotion(?bool $receiveTargetedEmailsFromPromotion): self
    {
        $this->receiveTargetedEmailsFromPromotion = $receiveTargetedEmailsFromPromotion;

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

    /**
     * @return string
     */
    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    /**
     * @param string $apiToken
     * @return User
     */
    public function setApiToken(?string $apiToken): self
    {
        $this->apiToken = $apiToken;
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
            $order->setClient($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            // set the owning side to null (unless already changed)
            if ($order->getClient() === $this) {
                $order->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setClient($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getClient() === $this) {
                $transaction->setClient(null);
            }
        }

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        // set (or unset) the owning side of the relation if necessary
        $newClient = $wallet === null ? null : $this;
        if ($newClient !== $wallet->getClient()) {
            $wallet->setClient($newClient);
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
            $promotion->addClient($this);
        }

        return $this;
    }

    public function removePromotion(Promo $promotion): self
    {
        if ($this->promotions->contains($promotion)) {
            $this->promotions->removeElement($promotion);
            $promotion->removeClient($this);
        }

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
        return (string) $this->username;
    }

    /**
     * Get user full name
     *
     * @return string
     */
    public function getFullName()
    {
    	if (!empty($this->firstName) && !empty($this->lastName)){
		    return sprintf("%s %s", ucfirst($this->firstName) , ucfirst($this->lastName));
	    }
	    
	    return (string) $this->username;
    }
	
	/**
	 * Get user full name
	 *
	 * @return string
	 */
	public function getFormattedFullName()
	{
		if (!empty($this->getFirstName()) && !empty($this->getLastName())){
			return sprintf("%s.%s", ucfirst($this->getFirstName()) , ucfirst($this->getLastName()[0]));
		}
		
		return (string) $this->getUsername();
	}

    /**
     * {@inheritDoc}
     */
    public function setEmail($email):self
    {
    	if (!is_null($email)){
		    parent::setEmail($email);
		    $this->setUsername($email);
	    }
	    
        return $this;
    }


    /**
     * @return bool
     */
    public function isAcceptConditions()
    {
        return $this->acceptConditions;
    }

    /**
     * @param bool $acceptConditions
     *
     * @return self
     */
    public function setAcceptConditions($acceptConditions)
    {
        $this->acceptConditions = $acceptConditions;

        return $this;
    }
}
