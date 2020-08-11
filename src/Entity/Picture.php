<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 */
class Picture implements \JsonSerializable, \Serializable
{
    /**
     * CLASS CONSTANT
     */
    public const AWAITING_FOR_VERIFICATION = 'picture.status.awaiting_for_verification';
    public const VALIDATED = 'picture.status.completed';
    public const REFUSED = 'picture.status.refused';

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
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pictureName;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $picturePath;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $paintedPicturePath;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $watermarkedPicturePath;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $paintedPicturePathThumb;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $picturePathThumb;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $pictureDirectory;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentary;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * @var Order
     *
     * Many Pictures relates to one OrderCreation.
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="pictures", cascade={"all"})
     */
    private $order;

    /**
     * @var PictureDetails
     *
     * One Picture relates to many PictureDetails.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PictureDetails", mappedBy="picture", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     * @Assert\Valid()
     */
    private $pictureDetail;

    public function __construct()
    {
        $this->pictureDetail = new ArrayCollection();
    }

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(?string $pictureName): self
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    public function getPaintedPicturePath(): ?string
    {
        return $this->paintedPicturePath;
    }

    public function setPaintedPicturePath(?string $picturePath): self
    {
        $this->paintedPicturePath = $picturePath;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(?string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getPaintedPicturePathThumb(): ?string
    {
        return $this->paintedPicturePathThumb;
    }

    public function setPaintedPicturePathThumb(?string $picturePathThumb): self
    {
        $this->paintedPicturePathThumb = $picturePathThumb;

        return $this;
    }

    public function getPicturePathThumb(): ?string
    {
        return $this->picturePathThumb;
    }

    public function setPicturePathThumb(?string $picturePathThumb): self
    {
        $this->picturePathThumb = $picturePathThumb;

        return $this;
    }

    public function getWatermarkedPicturePath(): ?string
    {
        return $this->watermarkedPicturePath;
    }

    public function setWatermarkedPicturePath(?string $picturePath): self
    {
        $this->watermarkedPicturePath = $picturePath;

        return $this;
    }

    public function getPictureDirectory(): ?string
    {
        return $this->pictureDirectory;
    }

    public function setPictureDirectory(?string $pictureDirectory): self
    {
        $this->pictureDirectory = $pictureDirectory;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCommentary(): ?string
    {
        return $this->commentary;
    }

    public function setCommentary(?string $commentary): self
    {
        $this->commentary = $commentary;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return Collection|PictureDetails[]
     */
    public function getPictureDetail(): Collection
    {
        return $this->pictureDetail;
    }

    public function addPictureDetail(PictureDetails $pictureDetail): self
    {
        if (!$this->pictureDetail->contains($pictureDetail)) {
            $this->pictureDetail[] = $pictureDetail;
            $pictureDetail->setPicture($this);
        }

        return $this;
    }

    public function removePictureDetail(PictureDetails $pictureDetail): self
    {
        if ($this->pictureDetail->contains($pictureDetail)) {
            $this->pictureDetail->removeElement($pictureDetail);
            // set the owning side to null (unless already changed)
            if ($pictureDetail->getPicture() === $this) {
                $pictureDetail->setPicture(null);
            }
        }

        return $this;
    }

    /**
     * If status is valid
     */
    public function isAwaitingForVerification(): bool
    {
        return $this->getStatus() === self::AWAITING_FOR_VERIFICATION;
    }

    public function getPictureDetailNumber()
    {
        return $this->pictureDetail->count();
    }

    public function getPictureTotalPrice()
    {
        return array_sum($this->pictureDetail->map(function (PictureDetails $pictureDetail){ return $pictureDetail->getPrice();})->toArray());
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        return [
	        'pictureName' => $this->pictureName,
	        'picturePath' => $this->picturePath,
	        'picturePathThumb' => $this->picturePathThumb,
	        'status' => $this->status,
	        'pictureDirectory' => $this->pictureDirectory
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->pictureName,
          $this->picturePath,
          $this->picturePathThumb,
          $this->pictureDirectory,
          $this->status,
          $this->commentary,
          $this->pictureDetail
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->pictureName,
          $this->picturePath,
          $this->picturePathThumb,
          $this->pictureDirectory,
          $this->status,
          $this->commentary,
          $this->pictureDetail
        ) = unserialize($data);
    }
}
