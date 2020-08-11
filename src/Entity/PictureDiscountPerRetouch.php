<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureDiscountPerRetouchRepository")
 */
class PictureDiscountPerRetouch implements \Serializable
{
    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer
     *
     * @Assert\NotBlank()
     *
     * @Assert\Range(min = 0, max = 100)
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $reduction;

    /**
     * @var integer
     *
     * @Assert\Range(min = 1, max = 99999)
     * @Assert\Regex(pattern="/\d/" ,match=true)
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="decimal", precision=5, scale=0, nullable=true)
     */
    private $imageLimit;
	
	/**
	 * @var integer
	 *
	 * @Assert\Range(min = 1, max = 99999)
	 * @Assert\Regex(pattern="/\d/" ,match=true)
	 *
	 * @ORM\Column(type="decimal", precision=5, scale=0, nullable=true)
	 */
	private $imageLimitPerOrder;

    /**
     * @var integer
     *
     * @Assert\Range(min = 1, max = 99999)
     * @Assert\Regex(pattern="/\d/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=0, nullable=true)
     */
    private $imageLimitPerUser;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * @var Retouch
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Retouch", inversedBy="promotionsPerRetouch")
     *
     * @Assert\NotBlank()
     */
    private $retouch;

    /**
     * @var PictureDiscount
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PictureDiscount", inversedBy="promotionsPerRetouch")
     */
    private $pictureDiscount;

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReduction()
    {
        return $this->reduction;
    }

    public function setReduction($reduction): self
    {
        $this->reduction = $reduction;

        return $this;
    }
    
	/**
	 * @return int
	 */
	public function getImageLimitPerOrder(): ?int
	{
		return $this->imageLimitPerOrder;
	}
	
	/**
	 * @param int $imageLimitPerOrder
	 * @return PictureDiscountPerRetouch
	 */
	public function setImageLimitPerOrder(int $imageLimitPerOrder): self
	{
		$this->imageLimitPerOrder = $imageLimitPerOrder;
		
		return $this;
	}

    public function getImageLimit(): ?int
    {
        return $this->imageLimit;
    }

    public function setImageLimit(int $imageLimit): self
    {
        $this->imageLimit = $imageLimit;

        return $this;
    }

    public function getImageLimitPerUser()
    {
        return $this->imageLimitPerUser;
    }

    public function setImageLimitPerUser($imageLimitPerUser): self
    {
        $this->imageLimitPerUser = $imageLimitPerUser;

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

    public function getPictureDiscount(): ?PictureDiscount
    {
        return $this->pictureDiscount;
    }

    public function setPictureDiscount(?PictureDiscount $pictureDiscount): self
    {
        $this->pictureDiscount = $pictureDiscount;

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
        if ($this->imageLimit > 0 && $this->imageLimitPerUser > 0 && $this->imageLimit % $this->imageLimitPerUser != 0) {
            $context->buildViolation('promo.msg.image_limit')
                  ->atPath('imageLimitPerUser')
                  ->addViolation();
        }
	
	    if ($this->imageLimit > 0 && $this->imageLimitPerOrder > 0 && $this->imageLimit % $this->imageLimitPerOrder != 0) {
		    $context->buildViolation('promo.msg.image_limit')
			    ->atPath('imageLimitPerOrder')
			    ->addViolation();
	    }
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
          $this->id,
          $this->reduction,
          $this->imageLimit,
          $this->retouch
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->reduction,
          $this->imageLimit,
          $this->retouch
        ) = unserialize($data);
    }
}
