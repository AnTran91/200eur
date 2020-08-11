<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldRenovationTypeRepository")
 */
class FieldRenovationType  implements Translatable, \Serializable, \JsonSerializable
{
    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @Assert\NotNull()
     * @Assert\Length(max = 255)
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    protected $typeName;

    /**
     * @Assert\Length(max = 255)
     */
    protected $typeNameEn;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * Many FieldRenovationChoices Has One FieldRenovationType
     *
     * @ORM\OneToMany(targetEntity="App\Entity\FieldRenovationChoices", mappedBy="type", cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\Valid()
     */
    protected $fieldRenovationChoices;

    /**
     * Many Field Has Many FieldRenovationType
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Field", mappedBy="renovations")
     */
    protected $fields;

    /**
     * TRANSLATION ATTRIBUTS
     */

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    protected $locale;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->fieldRenovationChoices = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

    public function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;

        return $this;
    }

    /**
     * @return Collection|FieldRenovationChoices[]
     */
    public function getFieldRenovationChoices(): Collection
    {
        return $this->fieldRenovationChoices;
    }

    public function addFieldPictureChoice(FieldRenovationChoices $fieldPictureChoice): self
    {
        if (!$this->fieldRenovationChoices->contains($fieldPictureChoice)) {
            $this->fieldRenovationChoices[] = $fieldPictureChoice;
            $fieldPictureChoice->setType($this);
        }

        return $this;
    }

    public function removeFieldPictureChoice(FieldRenovationChoices $fieldPictureChoice): self
    {
        if ($this->fieldRenovationChoices->contains($fieldPictureChoice)) {
            $this->fieldRenovationChoices->removeElement($fieldPictureChoice);
            // set the owning side to null (unless already changed)
            if ($fieldPictureChoice->getType() === $this) {
                $fieldPictureChoice->setType(null);
            }
        }

        return $this;
    }

    public function getTypeNameEn(): ?string
    {
        return $this->typeNameEn;
    }

    public function setTypeNameEn(?string $typeNameEn): self
    {
        $this->typeNameEn = $typeNameEn;

        return $this;
    }

    /**
     * @return Collection|Field[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->addRenovation($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            $field->removeRenovation($this);
        }

        return $this;
    }



    public function addFieldRenovationChoice(FieldRenovationChoices $fieldRenovationChoice): self
    {
        if (!$this->fieldRenovationChoices->contains($fieldRenovationChoice)) {
            $this->fieldRenovationChoices[] = $fieldRenovationChoice;
            $fieldRenovationChoice->setType($this);
        }

        return $this;
    }

    public function removeFieldRenovationChoice(FieldRenovationChoices $fieldRenovationChoice): self
    {
        if ($this->fieldRenovationChoices->contains($fieldRenovationChoice)) {
            $this->fieldRenovationChoices->removeElement($fieldRenovationChoice);
            // set the owning side to null (unless already changed)
            if ($fieldRenovationChoice->getType() === $this) {
                $fieldRenovationChoice->setType(null);
            }
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
        return (string) $this->typeName;
    }

    /**
     * OVERRIDING
     */

    public function jsonSerialize()
    {
        return [
        'typeName' => $this->typeName
      ];
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->typeName
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->typeName
        ) = unserialize($data);
    }
}
