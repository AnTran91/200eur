<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\Intl\Intl;

/**
 * Country code filter
 *
 * @internal
 */
class CountryExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('country', [$this, 'doGetCountryName'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Get the Country Name form the code
     *
     * @param string $lang
     * @param string $locale
     *
     * @return string
     */
    public function doGetCountryName(?string $country, string $locale = "en"): ?string
    {
        return Intl::getRegionBundle()->getCountryName($country, $locale);
    }
}
