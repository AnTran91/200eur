<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureCounterRepository")
 */
class PictureCounter extends Promo implements \Serializable
{
    /**
     * CLASS ATTRIBUTS
     */

    /**
     * Each PictureCounter relates can relate to (have) many PictureCounterPerRetouch objects.
     * Each PictureCounterPerRetouch relates to (has) one PictureCounter
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PictureCounterPerRetouch", mappedBy="pictureCounter", cascade={"remove", "persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @Assert\Valid()
     */
    protected $promotionsPerRetouch;

    public function __construct()
    {
        parent::__construct();
        $this->promotionsPerRetouch = new ArrayCollection();
    }

    /**
     * GETTERS & SETTERS
     */

     /**
      * @return Collection|PictureCounterPerRetouch[]
      */
     public function getPromotionsPerRetouch(): Collection
     {
         return $this->promotionsPerRetouch;
     }

     public function addPromotionsPerRetouch(PictureCounterPerRetouch $promotionsPerRetouch): self
     {
         if (!$this->promotionsPerRetouch->contains($promotionsPerRetouch)) {
             $this->promotionsPerRetouch[] = $promotionsPerRetouch;
             $promotionsPerRetouch->setPictureCounter($this);
         }

         return $this;
     }

     public function removePromotionsPerRetouch(PictureCounterPerRetouch $promotionsPerRetouch): self
     {
         if ($this->promotionsPerRetouch->contains($promotionsPerRetouch)) {
             $this->promotionsPerRetouch->removeElement($promotionsPerRetouch);
             // set the owning side to null (unless already changed)
             if ($promotionsPerRetouch->getPictureCounter() === $this) {
                 $promotionsPerRetouch->setPictureCounter(null);
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

         $retouchArray = $this->promotionsPerRetouch->map(function (PictureCounterPerRetouch $pictureDiscountPerRetouch){
           return $pictureDiscountPerRetouch->getRetouch()->getId();
         })->toArray();

         if (count($retouchArray) !== count(array_unique($retouchArray))) {
             $context->buildViolation('promo.msg.retouch')
                   ->atPath('promotionsPerRetouch')
                   ->addViolation();
         }
     }
}
