<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="announcement")
 */
class Announcement
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text", length=2000, nullable=true)
     */
    private $body;

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
     * @var boolean
     * 
     * @ORM\Column(type="boolean")
     */
    private $enabled;


    /**
     * Get the value of title
     *
     * @return  string
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
     *
     * @return  self
     */ 
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of body
     *
     * @return  string
     */ 
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @param  string  $body
     *
     * @return  self
     */ 
    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of startDate
     *
     * @return  \DateTime
     */ 
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set the value of startDate
     *
     * @param  \DateTime  $startDate
     *
     * @return  self
     */ 
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the value of endDate
     *
     * @return  \DateTime
     */ 
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set the value of endDate
     *
     * @param  \DateTime  $endDate
     *
     * @return  self
     */ 
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get the value of enabled
     *
     * @return  boolean
     */ 
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set the value of enabled
     *
     * @param  boolean  $enabled
     *
     * @return  self
     */ 
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return (string) $this->title;
    }
}
