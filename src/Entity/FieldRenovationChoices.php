<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldRenovationChoicesRepository")
 */
class FieldRenovationChoices implements \Serializable, \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Length(max = 1500)
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $picturePath;

    /**
     * @var string
     *
     * @Assert\NotNull()
     * @Assert\Length(max = 255)
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $uuid;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * Many FieldRenovationChoices Has One FieldRenovationType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\FieldRenovationType", inversedBy="fieldRenovationChoices", cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $type;

    public function getId()
    {
        return $this->id;
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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getType(): ?FieldRenovationType
    {
        return $this->type;
    }

    public function setType(?FieldRenovationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * OVERRIDING
     */

    public function jsonSerialize()
    {
        return [
          'picturePath' => $this->picturePath
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->picturePath
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->picturePath
        ) = unserialize($data);
    }
}
