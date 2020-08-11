<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WalletRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Wallet implements \Serializable
{
    /**
    * CLASS ATTRIBUTS
    */

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @Assert\NotBlank(groups= {"AdminUserCreation"})
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $currentAmount;

    /**
     * @var \DateTimeInterface
     *
     * @Assert\NotBlank()
     * @Assert\Date()
     *
     * @ORM\Column(type="datetime")
     */
    private $lastUpdate;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * One wallet has Many Transactions
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="wallet", fetch="EXTRA_LAZY")
     */
    private $transactions;

    /**
     * One wallet has One User
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="wallet", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $client;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function setUpdatedAt()
    {
        $this->lastUpdate = new \DateTime();
    }

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrentAmount()
    {
        return $this->currentAmount;
    }

    public function setCurrentAmount($currentAmount): self
    {
        $this->currentAmount = $currentAmount;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeInterface $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transactions): self
    {
        if (!$this->transactions->contains($transactions)) {
            $this->transactions[] = $transactions;
            $transactions->setWallet($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transactions): self
    {
        if ($this->transactions->contains($transactions)) {
            $this->transactions->removeElement($transactions);
            // set the owning side to null (unless already changed)
            if ($transactions->getWallet() === $this) {
                $transactions->setWallet(null);
            }
        }

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

    /**
     * CUSTOM METHOD
     */
	
	/**
	 * Decrease Amount
	 *
	 * @param $amount
	 * @return Wallet
	 */
    public function decreaseAmount($amount): self
    {
    	if ($amount > $this->currentAmount){
		    return $this;
	    }
	    
        $this->currentAmount -= $amount;

        return $this;
    }
	
	/**
	 * Increase Amount
	 *
	 * @param $amount
	 *
	 * @return Wallet
	 */
    public function increaseAmount($amount): self
    {
        $this->currentAmount += $amount;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->currentAmount;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->currentAmount,
          $this->lastUpdate,
          $this->transactions
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->currentAmount,
          $this->lastUpdate,
          $this->transactions
        ) = unserialize($data);
    }
}
