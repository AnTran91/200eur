<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldDetailsRepository")
 */
class FieldDetails
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PictureDetails", inversedBy="fieldDetails")
     */
    private $pictureDetail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Field")
     */
    private $field;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPictureDetail(): ?PictureDetails
    {
        return $this->pictureDetail;
    }

    public function setPictureDetail(?PictureDetails $pictureDetail): self
    {
        $this->pictureDetail = $pictureDetail;

        return $this;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function setField(?Field $field): self
    {
        $this->field = $field;

        return $this;
    }
}
