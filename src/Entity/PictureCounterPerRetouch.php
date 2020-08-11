<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureCounterPerRetouchRepository")
 */
class PictureCounterPerRetouch
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 1, max = 99999)
     * @Assert\Regex(pattern="/\d/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=0, nullable=false)
     */
    private $imageCounterLimit;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 1, max = 99999)
     * @Assert\Regex(pattern="/\d/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=0, nullable=false)
     */
    private $imageCounterLimitWithReduction;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 1, max = 100)
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=false)
     */
    private $imageCounterReduction;

    /**
     * @var PictureCounter
     *
     * Each PictureCounter relates can relate to (have) many PictureCounterPerRetouch objects.
     * Each PictureCounterPerRetouch relates to (has) one PictureCounter
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\PictureCounter", inversedBy="promotionsPerRetouch")
     */
    private $pictureCounter;

    /**
     * @var Retouch
     *
     * Each PictureCounterPerRetouch relates to (has) one Retouch.
     * Each Retouch can relate/has to (have) many PictureCounterPerRetouch objects
     *
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Retouch", inversedBy="pictureCounterPerRetouches")
     */
    private $retouch;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageCounterLimit()
    {
        return $this->imageCounterLimit;
    }

    public function setImageCounterLimit($imageCounterLimit): self
    {
        $this->imageCounterLimit = $imageCounterLimit;

        return $this;
    }

    public function getImageCounterLimitWithReduction()
    {
        return $this->imageCounterLimitWithReduction;
    }

    public function setImageCounterLimitWithReduction($imageCounterLimitWithReduction): self
    {
        $this->imageCounterLimitWithReduction = $imageCounterLimitWithReduction;

        return $this;
    }

    public function getImageCounterReduction()
    {
        return $this->imageCounterReduction;
    }

    public function setImageCounterReduction($imageCounterReduction): self
    {
        $this->imageCounterReduction = $imageCounterReduction;

        return $this;
    }

    public function getPictureCounter(): ?PictureCounter
    {
        return $this->pictureCounter;
    }

    public function setPictureCounter(?PictureCounter $pictureCounter): self
    {
        $this->pictureCounter = $pictureCounter;

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
}
