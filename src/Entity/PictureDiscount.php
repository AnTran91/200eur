<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureDiscountRepository")
 */
class PictureDiscount extends Promo  implements \Serializable
{
    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $hasNumberOfUse;

    /**
     * @var integer
     *
     * @Assert\Range(min = 1, max = 99999)
     * @Assert\Regex(pattern="/\d/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=0, nullable=true)
     */
    protected $useLimit;

    /**
     * @var integer
     *
     * @Assert\Range(min = 1, max = 99999)
     * @Assert\Regex(pattern="/\d/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=0, nullable=true)
     */
    protected $useLimitPerUser;

   /**
    * @var ArrayCollection
    *
    * One Promo has Many Orders.
    *
    * @ORM\OneToMany(targetEntity="App\Entity\PictureDiscountPerRetouch", mappedBy="pictureDiscount", cascade={"remove", "persist"}, orphanRemoval=true)
    * @ORM\OrderBy({"id" = "ASC"})
    *
    * @Assert\Valid()
    */
    protected $promotionsPerRetouch;

    /**
     * GETTERS & SETTERS
     */

    public function __construct()
    {
        parent::__construct();

        $this->hasNumberOfUse = true;
        $this->promotionsPerRetouch = new ArrayCollection();
    }

    public function getHasNumberOfUse(): ?bool
    {
        return $this->hasNumberOfUse;
    }

    public function setHasNumberOfUse(bool $hasNumberOfUse): self
    {
        $this->hasNumberOfUse = $hasNumberOfUse;

        return $this;
    }

    public function getUseLimit()
    {
        return $this->useLimit;
    }

    public function setUseLimit($useLimit): self
    {
        $this->useLimit = $useLimit;

        return $this;
    }

    public function getUseLimitPerUser()
    {
        return $this->useLimitPerUser;
    }

    public function setUseLimitPerUser($useLimitPerUser): self
    {
        $this->useLimitPerUser = $useLimitPerUser;

        return $this;
    }

    /**
     * @return Collection|PictureDiscountPerRetouch[]
     */
    public function getPromotionsPerRetouch(): Collection
    {
        return $this->promotionsPerRetouch;
    }

    public function addPromotionsPerRetouch(PictureDiscountPerRetouch $promotionsPerRetouch): self
    {
        if (!$this->promotionsPerRetouch->contains($promotionsPerRetouch)) {
            $this->promotionsPerRetouch[] = $promotionsPerRetouch;
            $promotionsPerRetouch->setPictureDiscount($this);
        }

        return $this;
    }

    public function removePromotionsPerRetouch(PictureDiscountPerRetouch $promotionsPerRetouch): self
    {
        if ($this->promotionsPerRetouch->contains($promotionsPerRetouch)) {
            $this->promotionsPerRetouch->removeElement($promotionsPerRetouch);
            // set the owning side to null (unless already changed)
            if ($promotionsPerRetouch->getPictureDiscount() === $this) {
                $promotionsPerRetouch->setPictureDiscount(null);
            }
        }

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     * @param array $payload
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->promoType == self::ASSIGN_TO_SPECIFIC_CUSTOMERS && $this->clients->isEmpty()) {
            $context->buildViolation('This value should not be blank.')
                  ->atPath('clients')
                  ->addViolation();
        }

        if ($this->useLimit > 0 && $this->useLimitPerUser > 0 && $this->useLimit % $this->useLimitPerUser != 0) {
            $context->buildViolation('promo.msg.use_limit')
                  ->atPath('useLimit')
                  ->addViolation();
        }

        $retouchArray = $this->promotionsPerRetouch->map(function (PictureDiscountPerRetouch $pictureDiscountPerRetouch){
          return $pictureDiscountPerRetouch->getRetouch()->getId();
        })->toArray();

        if (count($retouchArray) !== count(array_unique($retouchArray))) {
            $context->buildViolation('promo.msg.retouch')
                  ->atPath('promotionsPerRetouch')
                  ->addViolation();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return [
          $this->id,
          $this->promoCode,
          $this->startDate,
          $this->endDate,
          $this->expired,
          $this->promoType,
          $this->orders,
          $this->clients,
          $this->hasNumberOfUse,
          $this->useLimit,
          $this->useLimitPerUser,
          $this->promotionsPerRetouch
        ];
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
          $this->clients,
          $this->hasNumberOfUse,
          $this->useLimit,
          $this->useLimitPerUser,
          $this->promotionsPerRetouch
        ) = unserialize($data);
    }
}
