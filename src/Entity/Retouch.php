<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Translatable\Translatable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RetouchRepository")
 *
 * @UniqueEntity(fields={"retouchCode"}, groups={"retouchCreation"})
 */
class Retouch extends ApplicationType implements \Serializable, \JsonSerializable, Translatable
{
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
     * @Assert\NotBlank(groups={"retouchCreation"})
     * @Assert\Length(max = 200, groups={"retouchCreation"})
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @Assert\Length(max = 200, groups={"retouchCreation"})
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $helpLink;

    /**
     * @var string
     *
     * @Assert\Length(max = 5000, groups={"retouchCreation"})
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * HTML RENDER ATTRIBUTS
     */

    /**
     * @var string
     *
     * @Assert\Range(max = 1000, groups={"retouchCreation"})
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderNumber;

    /**
     * @var string
     *
     * @Assert\Length(max=200, groups={"retouchCreation"})
     * @Assert\Regex(pattern="/^[a-zA-Z0-9_-]+$/i", groups={"retouchCreation"} ,match=true)
     *
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $retouchCode;

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
     * One Retouch relates to Many PictureDetails.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PictureDetails", mappedBy="retouch", cascade={"persist", "remove", "refresh"}, fetch="EXTRA_LAZY")
     */
    private $pictureRetouches;

    /**
     * Many Retouch relates to Many FieldGroup.
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\FieldGroup", mappedBy="retouch", fetch="EXTRA_LAZY")
     */
    private $fieldGroups;

    /**
     * One Retouch relates to Many PhotoRetouchingPricing.
     *
     * @Assert\Valid(groups={"retouchCreation"})
     * @ORM\OneToMany(targetEntity="App\Entity\PhotoRetouchingPricing", mappedBy="retouch", cascade={"persist"}, orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $pricings;

    /**
     * One Retouch relates to Many PromoDetails.
     *
     * @ORM\OneToMany(targetEntity="App\Entity\PictureDiscountPerRetouch", mappedBy="retouch", fetch="EXTRA_LAZY")
     */
    private $promotionsPerRetouch;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PictureCounterPerRetouch", mappedBy="retouch")
     */
    private $pictureCounterPerRetouches;

    public function __construct()
    {
        $this->pictureRetouches = new ArrayCollection();
        $this->fieldGroups = new ArrayCollection();
        $this->pricings = new ArrayCollection();
        $this->promotionsPerRetouch = new ArrayCollection();
        $this->pictureCounterPerRetouches = new ArrayCollection();

        $this->appType = self::DEFAULT_APP_TYPE;
    }

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setOrderNumber($orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    public function setHelpLink($helpLink): self
    {
        $this->helpLink = $helpLink;
        return $this;
    }

    public function getHelpLink()
    {
        return $this->helpLink;
    }

    /**
     * @return Collection|PictureDetails[]
     */
    public function getPictureRetouches(): Collection
    {
        return $this->pictureRetouches;
    }

    public function addPictureRetouch(PictureDetails $pictureRetouch): self
    {
        if (!$this->pictureRetouches->contains($pictureRetouch)) {
            $this->pictureRetouches[] = $pictureRetouch;
            $pictureRetouch->setRetouch($this);
        }

        return $this;
    }

    public function removePictureRetouch(PictureDetails $pictureRetouch): self
    {
        if ($this->pictureRetouches->contains($pictureRetouch)) {
            $this->pictureRetouches->removeElement($pictureRetouch);
            // set the owning side to null (unless already changed)
            if ($pictureRetouch->getRetouch() === $this) {
                $pictureRetouch->setRetouch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FieldGroup[]
     */
    public function getFieldGroups(): Collection
    {
        return $this->fieldGroups;
    }

    public function addFieldGroup(FieldGroup $fieldGroup): self
    {
        if (!$this->fieldGroups->contains($fieldGroup)) {
            $this->fieldGroups[] = $fieldGroup;
            $fieldGroup->addRetouch($this);
        }

        return $this;
    }

    public function removeFieldGroup(FieldGroup $fieldGroup): self
    {
        if ($this->fieldGroups->contains($fieldGroup)) {
            $this->fieldGroups->removeElement($fieldGroup);
            $fieldGroup->removeRetouch($this);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|PhotoRetouchingPricing[]
     */
    public function getPricings(): Collection
    {
        return $this->pricings;
    }

    public function addPricing(PhotoRetouchingPricing $price): self
    {
        if (!$this->pricings->contains($price)) {
            $this->pricings[] = $price;
            $price->setRetouch($this);
        }

        return $this;
    }

    public function removePricing(PhotoRetouchingPricing $price): self
    {
        if ($this->pricings->contains($price)) {
            $this->pricings->removeElement($price);
            // set the owning side to null (unless already changed)
            if ($price->getRetouch() === $this) {
                $price->setRetouch(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PictureDiscountPerRetouch[]
     */
    public function getPromotionsPerRetouch(): Collection
    {
        return $this->promotionsPerRetouch;
    }

    public function addPromotionsPerRetouch(PictureDiscountPerRetouch $promotionsPerRetouch): self
    {
        if (!$this->promotionsPerRetouch->contains($promotionsPerRetouch)) {
            $this->promotionsPerRetouch[] = $promotionsPerRetouch;
            $promotionsPerRetouch->setRetouch($this);
        }

        return $this;
    }

    public function removePromotionsPerRetouch(PictureDiscountPerRetouch $promotionsPerRetouch): self
    {
        if ($this->promotionsPerRetouch->contains($promotionsPerRetouch)) {
            $this->promotionsPerRetouch->removeElement($promotionsPerRetouch);
            // set the owning side to null (unless already changed)
            if ($promotionsPerRetouch->getRetouch() === $this) {
                $promotionsPerRetouch->setRetouch(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getRetouchCode(): ?string
    {
        return $this->retouchCode;
    }

    /**
     * @param string $retouchCode
     * @return Retouch
     */
    public function setRetouchCode(?string $retouchCode): self
    {
        $this->retouchCode = $retouchCode;
        return $this;
    }

    /**
     * @return Collection|PictureCounterPerRetouch[]
     */
    public function getPictureCounterPerRetouches(): Collection
    {
        return $this->pictureCounterPerRetouches;
    }

    public function addPictureCounterPerRetouch(PictureCounterPerRetouch $pictureCounterPerRetouch): self
    {
        if (!$this->pictureCounterPerRetouches->contains($pictureCounterPerRetouch)) {
            $this->pictureCounterPerRetouches[] = $pictureCounterPerRetouch;
            $pictureCounterPerRetouch->setRetouch($this);
        }

        return $this;
    }

    public function removePictureCounterPerRetouch(PictureCounterPerRetouch $pictureCounterPerRetouch): self
    {
        if ($this->pictureCounterPerRetouches->contains($pictureCounterPerRetouch)) {
            $this->pictureCounterPerRetouches->removeElement($pictureCounterPerRetouch);
            // set the owning side to null (unless already changed)
            if ($pictureCounterPerRetouch->getRetouch() === $this) {
                $pictureCounterPerRetouch->setRetouch(null);
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
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
          "id" => $this->id,
          "title" => $this->getTitle(),
          "description" => $this->getDescription(),
          "orderNumber" => $this->getOrderNumber()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->title,
          $this->description,
          $this->orderNumber
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        list(
          $this->id,
          $this->title,
          $this->description,
          $this->orderNumber
        ) = unserialize($data);
    }
}
