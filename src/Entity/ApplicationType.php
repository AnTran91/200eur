<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class ApplicationType
{
    // order type
    public const EMMOBILIER_TYPE = "application.type.emmobilier";
    public const IMMOSQUARE_TYPE = "application.type.immosquare";

    public const DEFAULT_APP_TYPE = "application.type.emmobilier";

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $appType;

    /**
     * @return string
     */
    public function getAppType()
    {
        return $this->appType;
    }

    /**
     * @param string $appType
     * @return ApplicationType
     */
    public function setAppType(string $appType): self
    {
        $this->appType = $appType;
        return $this;
    }
}