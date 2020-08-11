<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldGroupRepository")
 *
 * @UniqueEntity("labelText")
 */
class FieldGroup implements Translatable, \Serializable
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
     * @Assert\NotNull()
     * @Assert\Length(max = 255)
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255)
     */
    protected $labelText;

    /**
     * @Assert\Length(max = 255)
     */
    protected $labelTextEn;

    /**
     * HTML RENDER ATTRIBUTS
     */

    /**
     * @Assert\NotNull()
     * @Assert\Length(max = 50)
     *
     * @ORM\Column(type="string", length=50)
     */
    private $position;

    /**
     * @Assert\NotNull()
     * @Assert\Range(max = 999)
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="integer")
     */
    private $orderNumber;

    /**
     * TRANSLATION ATTRIBUTS
     */

    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     * and it is not necessary because globally locale can be set in listener
     */
    private $locale;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * One Field has Many FieldGroup.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Field", mappedBy="fieldGroup", orphanRemoval=true, cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\Valid()
     */
    private $fields;

    /**
     * Many Retouch has Many FieldGroup.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Retouch", inversedBy="fieldGroups")
     * @ORM\JoinColumn(nullable=true)
     */
    private $retouch;

    /**
     * GETTERS & SETTERS
     */

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->retouch = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabelText(): ?string
    {
        return $this->labelText;
    }

    public function setLabelText(string $labelText): self
    {
        $this->labelText = $labelText;

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
            $field->setFieldGroup($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getFieldGroup() === $this) {
                $field->setFieldGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Retouch[]
     */
    public function getRetouch(): Collection
    {
        return $this->retouch;
    }

    public function addRetouch(Retouch $retouch): self
    {
        if (!$this->retouch->contains($retouch)) {
            $this->retouch[] = $retouch;
        }

        return $this;
    }

    public function removeRetouch(Retouch $retouch): self
    {
        if ($this->retouch->contains($retouch)) {
            $this->retouch->removeElement($retouch);
        }

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(int $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getLabelTextEn(): ?string
    {
        return $this->labelTextEn;
    }

    public function setLabelTextEn(?string $labelTextEn): self
    {
        $this->labelTextEn = $labelTextEn;

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
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->labelText,
          $this->position,
          $this->orderNumber,
          $this->locale,
          $this->fields,
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
          $this->labelText,
          $this->position,
          $this->orderNumber,
          $this->locale,
          $this->fields,
          $this->retouch
        ) = unserialize($data);
    }
}
