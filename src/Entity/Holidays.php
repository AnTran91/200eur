<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Utils\Tools;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HolidaysRepository")
 */
class Holidays implements \Serializable, \JsonSerializable
{
    /**
     * CLASS ATTRIBUTS
     */

    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max = 255)
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date")
     *
     * @Assert\Expression("value and this.getStartDate() and value >= this.getStartDate()")
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private $endDate;

    /**
     * GETTERS & SETTERS
     */

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * OVERRIDING
     */

    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public function jsonSerialize()
    {
        return [
          "id" => $this->id,
          "title" => $this->title,
          "startDate" => $this->startDate->format('d-m-Y'),
          "endDate" => $this->endDate->format('d-m-Y'),
          "Days" => Tools::dateInterval($this->startDate, $this->endDate->modify('+ 1day'))
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
          $this->id,
          $this->title,
          $this->startDate,
          $this->endDate
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
          $this->startDate,
          $this->endDate
        ) = unserialize($data);
    }
}
