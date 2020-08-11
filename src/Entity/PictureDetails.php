<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureDetailsRepository")
 * @Gedmo\Loggable
 * @ORM\HasLifecycleCallbacks()
 */
class PictureDetails implements \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime $created
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Versioned
     */
    private $updated;

    /**
     * @var int $price
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Gedmo\Versioned
     */
    private $price;

    /**
     * @var \App\Entity\Retouch One retouch relates to Many PictureDetails.
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Retouch", inversedBy="pictureRetouches")
     * @ORM\JoinColumn(nullable=false)
     * @Gedmo\Versioned
     */
    private $retouch;

    /**
     * @var \App\Entity\Picture One picture relates to Many PictureDetails.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Picture", inversedBy="pictureDetail")
     */
    private $picture;

    /**
     * @var \App\Entity\ParamCollection  One PictureDetails relates to one param.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\ParamCollection", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Gedmo\Versioned
     */
    private $param;

    /**
     * @var \App\Entity\Picture Many PictureDetails relates to one picture.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $returnedPicture;

    /**
     * @var \App\Entity\Picture Many PictureDetails relates to one gif picture.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $returnedGifPicture;

    /**
     * @var \App\Entity\Picture Many PictureDetails relates to one gif picture.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $returnedMP4Picture;

    /**
     * @var \App\Entity\FieldDetails one PictureDetails relates to Many FieldDetails.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FieldDetails", mappedBy="pictureDetail", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $fieldDetails;

    public function __construct()
    {
        $this->fieldDetails = new ArrayCollection();
    }

    /**
     * GETTERS & SETTERS
     */

    /**
     * Lifecycle Callbacks
     */

    /**
     * Set updatedAt
     *
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function setUpdatedAt()
    {
        if (is_null($this->created)) {
            $this->created = new \DateTime();
        }

        $this->updated = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;

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
	
    public function getParam(): ?ParamCollection
    {
        return $this->param;
    }

    public function setParam(?ParamCollection $param): self
    {
        $this->param = $param;

        return $this;
    }

    public function getReturnedPicture(): ?Picture
    {
        return $this->returnedPicture;
    }

    public function setReturnedPicture(?Picture $returnedPicture): self
    {
        $this->returnedPicture = $returnedPicture;

        return $this;
    }

    public function getReturnedGifPicture(): ?Picture
    {
        return $this->returnedGifPicture;
    }

    public function setReturnedGifPicture(?Picture $returnedGifPicture): self
    {
        $this->returnedGifPicture = $returnedGifPicture;

        return $this;
    }
    
    public function getReturnedMP4Picture(): ?Picture
    {
        return $this->returnedMP4Picture;
    }

    public function setReturnedMP4Picture(?Picture $returnedMP4Picture): self
    {
        $this->returnedMP4Picture = $returnedMP4Picture;

        return $this;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return Collection|FieldDetails[]
     */
    public function getFieldDetails(): Collection
    {
        return $this->fieldDetails;
    }

    public function addFieldDetail(FieldDetails $fieldDetail): self
    {
        if (!$this->fieldDetails->contains($fieldDetail)) {
            $this->fieldDetails[] = $fieldDetail;
            $fieldDetail->setPictureDetail($this);
        }

        return $this;
    }

    public function removeFieldDetail(FieldDetails $fieldDetail): self
    {
        if ($this->fieldDetails->contains($fieldDetail)) {
            $this->fieldDetails->removeElement($fieldDetail);
            // set the owning side to null (unless already changed)
            if ($fieldDetail->getPictureDetail() === $this) {
                $fieldDetail->setPictureDetail(null);
            }
        }

        return $this;
    }

    /**
     * CUSTOM METHOD
     */

    /**
     * if status is valid
     */
    public function isAwaitingForVerification(): bool
    {
        return !is_null($this->getReturnedPicture()) && $this->getReturnedPicture()->getStatus() === Picture::AWAITING_FOR_VERIFICATION;
    }
	
	/**
	 * if object is equal
	 *
	 * @param PictureDetails $obj
	 *
	 * @return bool
	 */
    public function equals(PictureDetails $obj)
    {
        return $this->price == $obj->getPrice() && $this->retouch->getId() == $obj->getRetouch()->getId();
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
          $this->price,
          $this->retouch,
          $this->param
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->price,
          $this->retouch,
          $this->param
        ) = unserialize($data);
    }
}
