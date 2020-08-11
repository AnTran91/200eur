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
use Twig\TwigFunction;
use Symfony\Component\Intl\Intl;

/**
 * Language code filter
 *
 * @internal
 */
class LanguageExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('language', [$this, 'doGetLanguageName'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Get the Language Name form the code
     *
     * @param string $lang
     * @param string $locale
     *
     * @return string
     */
    public function doGetLanguageName(?string $lang, string $locale = "en"): ?string
    {
        return Intl::getLanguageBundle()->getLanguageName($lang, null, $locale);
    }
}
