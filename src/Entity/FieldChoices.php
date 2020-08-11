<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldChoicesRepository")
 */
class FieldChoices implements Translatable, \Serializable
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
    protected $choiceLabel;

    /**
     * @Assert\Length(max = 255)
     */
    protected $choiceLabelEn;

    /**
     *
     * @Assert\NotNull()
     * @Assert\Length(max = 255)
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $choiceValue;

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

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * Many Field has Many FieldChoices.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Field", mappedBy="choices")
     */
    protected $fields;

    /**
     * GETTERS & SETTERS
     */

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getChoiceValue(): ?string
    {
        return $this->choiceValue;
    }

    public function setChoiceValue(string $choiceValue): self
    {
        $this->choiceValue = $choiceValue;

        return $this;
    }

    public function getChoiceLabel(): ?string
    {
        return $this->choiceLabel;
    }

    public function setChoiceLabel(string $choiceLabel): self
    {
        $this->choiceLabel = $choiceLabel;

        return $this;
    }

    public function getChoiceLabelEn(): ?string
    {
        return $this->choiceLabelEn;
    }

    public function setChoiceLabelEn(?string $choiceLabelEn): self
    {
        $this->choiceLabelEn = $choiceLabelEn;

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
            $field->addChoice($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            $field->removeChoice($this);
        }

        return $this;
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return (string) $this->choiceLabel;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->choiceLabel,
          $this->choiceValue
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->choiceLabel,
          $this->choiceValue
        ) = unserialize($data);
    }
}
