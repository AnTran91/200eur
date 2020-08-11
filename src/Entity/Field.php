<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldRepository")
 *
 * @UniqueEntity("name")
 */
class Field implements Translatable, \Serializable
{
    /**
     * VALIDATION ATTRIBUTS
     */

    /**
     * @var array
     */
    private static $_choiceOptions = ['choice', 'radio', 'checkbox'];
    private static $_renovationOptions = ['image_renovation'];

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
     *
     * @Assert\NotNull()
     * @Assert\Length(max = 200)
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_-]+$/i" ,match=true)
     *
     * @ORM\Column(type="string", length=200, unique=true)
     */
    protected $name;

    /**
     * @Assert\Length(max = 255)
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $labelText;

    /**
     * @Assert\Length(max = 255)
     */
    protected $labelTextEn;

    /**
     * @Assert\Length(max = 200)
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $emptyData;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $mapped;

    /**
     * @Assert\Range(max = 99999)
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    protected $price;

    /**
     * @Assert\Length(max = 200)
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    protected $addThePriceWhenValueEqualsTo;

    /**
     * @Assert\NotNull()
     * @Assert\Length(max = 200)
     *
     * @ORM\Column(type="string", length=200)
     */
    protected $fieldType;

    /**
     * HTML RENDER ATTRIBUTS
     */

    /**
     * @Assert\NotNull()
     * @Assert\Range(max = 999)
     * @Assert\Regex(pattern="/^\d+(\.\d+)?/" ,match=true)
     *
     * @ORM\Column(type="integer")
     */
    protected $orderNumber;

    /**
     * @Assert\Length(max = 150)
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $HTMLClass;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $disabled;

    /**
     * MAPPED ATTRIBUTS
     */

    /**
     * Many Field has One Fields.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Field", inversedBy="disabledFields")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $disabledOn;

    /**
     * One Field has Many Fields.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Field", mappedBy="disabledOn", orphanRemoval=false, cascade={"all"})
     */
    protected $disabledFields;

    /**
     * Many FieldGroup has one Field.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\FieldGroup", inversedBy="fields", cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     */
    protected $fieldGroup;

    /**
     * Many FieldChoices has Many Field.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\FieldChoices", inversedBy="fields", orphanRemoval=true, cascade={"all"})
     * @ORM\JoinColumn(nullable=true)
     *
     * @Assert\Valid()
     */
    protected $choices;

    /**
     * Many FieldRenovationType has Many Field.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\FieldRenovationType", inversedBy="fields", orphanRemoval=true, cascade={"all"})
     *
     * @Assert\Valid()
     */
    protected $renovations;

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
        $this->emptyData = true;
        $this->disabledFields = new ArrayCollection();
        $this->choices = new ArrayCollection();
        $this->renovations = new ArrayCollection();
    }

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLabelText(): ?string
    {
        return $this->labelText;
    }

    public function setLabelText(?string $labelText): self
    {
        $this->labelText = $labelText;

        return $this;
    }

    public function getEmptyData(): ?string
    {
        return $this->emptyData;
    }

    public function setEmptyData(?string $emptyData): self
    {
        $this->emptyData = $emptyData;

        return $this;
    }

    public function getMapped(): ?bool
    {
        return $this->mapped;
    }

    public function setMapped(bool $mapped): self
    {
        $this->mapped = $mapped;

        return $this;
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

    public function getAddThePriceWhenValueEqualsTo(): ?string
    {
        return $this->addThePriceWhenValueEqualsTo;
    }

    public function setAddThePriceWhenValueEqualsTo(?string $addThePriceWhenValueEqualsTo): self
    {
        $this->addThePriceWhenValueEqualsTo = $addThePriceWhenValueEqualsTo;

        return $this;
    }

    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    public function setFieldType(?string $fieldType): self
    {
        $this->fieldType = $fieldType;

        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?int $orderNumber): self
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getHTMLClass(): ?string
    {
        return $this->HTMLClass;
    }

    public function setHTMLClass(?string $HTMLClass): self
    {
        $this->HTMLClass = $HTMLClass;

        return $this;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function getDisabledOn(): ?self
    {
        return $this->disabledOn;
    }

    public function setDisabledOn(?self $disabledOn): self
    {
        $this->disabledOn = $disabledOn;

        return $this;
    }

    /**
     * @return Collection|Field[]
     */
    public function getDisabledFields(): Collection
    {
        return $this->disabledFields;
    }

    public function addDisabledField(Field $disabledField): self
    {
        if (!$this->disabledFields->contains($disabledField)) {
            $this->disabledFields[] = $disabledField;
            $disabledField->setDisabledOn($this);
        }

        return $this;
    }

    public function removeDisabledField(Field $disabledField): self
    {
        if ($this->disabledFields->contains($disabledField)) {
            $this->disabledFields->removeElement($disabledField);
            // set the owning side to null (unless already changed)
            if ($disabledField->getDisabledOn() === $this) {
                $disabledField->setDisabledOn(null);
            }
        }

        return $this;
    }

    public function getFieldGroup(): ?FieldGroup
    {
        return $this->fieldGroup;
    }

    public function setFieldGroup(?FieldGroup $fieldGroup): self
    {
        $this->fieldGroup = $fieldGroup;

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
     * @return Collection|FieldChoices[]
     */
    public function getChoices(): Collection
    {
        return $this->choices;
    }

    public function addChoice(FieldChoices $choice): self
    {
        if (!$this->choices->contains($choice)) {
            $this->choices[] = $choice;
        }

        return $this;
    }

    public function removeChoice(FieldChoices $choice): self
    {
        if ($this->choices->contains($choice)) {
            $this->choices->removeElement($choice);
        }

        return $this;
    }

    /**
     * @return Collection|FieldRenovationType[]
     */
    public function getRenovations(): Collection
    {
        return $this->renovations;
    }

    public function addRenovation(FieldRenovationType $renovation): self
    {
        if (!$this->renovations->contains($renovation)) {
            $this->renovations[] = $renovation;
        }

        return $this;
    }

    public function removeRenovation(FieldRenovationType $renovation): self
    {
        if ($this->renovations->contains($renovation)) {
            $this->renovations->removeElement($renovation);
        }

        return $this;
    }

    /**
     * VALIDATION
     */
	
	/**
	 * @Assert\Callback
	 *
	 * @param ExecutionContextInterface $context
	 * @param $payload
	 */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if (in_array($this->getFieldType(), self::$_choiceOptions) && $this->getChoices()->isEmpty()) {
            $context->buildViolation('You need to add at least one choice.')
                   ->atPath('fieldType')
                   ->addViolation();
        }

        if (in_array($this->getFieldType(), self::$_renovationOptions) && $this->getRenovations()->isEmpty()) {
            $context->buildViolation('You need to add at least one renovation option.')
                   ->atPath('fieldType')
                   ->addViolation();
        }
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->name,
          $this->labelText,
          $this->emptyData,
          $this->mapped,
          $this->price,
          $this->addThePriceWhenValueEqualsTo,
          $this->fieldType,
          $this->orderNumber,
          $this->HTMLClass,
          $this->disabled,
          $this->disabledOn,
          $this->disabledFields,
          $this->fieldGroup,
          $this->choices,
          $this->renovations,
          $this->locale
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->name,
          $this->labelText,
          $this->emptyData,
          $this->mapped,
          $this->price,
          $this->addThePriceWhenValueEqualsTo,
          $this->fieldType,
          $this->orderNumber,
          $this->HTMLClass,
          $this->disabled,
          $this->disabledOn,
          $this->disabledFields,
          $this->fieldGroup,
          $this->choices,
          $this->renovations,
          $this->locale
        ) = unserialize($data);
    }
}
