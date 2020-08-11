<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Price filter
 *
 * @internal
 */
class FiltersExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('price', array($this, 'priceFilter')),
            new TwigFilter('array_search', array($this, 'arraySearch')),
            new TwigFilter('array_column', array($this, 'arrayColumn')),
        );
    }

    /**
     * Set price filter
     *
     * @param string $lang
     * @param string $locale
     *
     * @return string
     */
    public function priceFilter(?float $number, int $decimals = 2, string $decPoint = '.', string $thousandsSep = ','): string
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        $price = $price." €";

        return $price;
    }

    /**
     * search value in the array
     *
     * @param string $value
     * @param array  $array
     *
     * @return string
     */
    public function arraySearch(?string $value, array $array): string
    {
        return array_search($value, $array);
    }

    /**
     * get array of column
     *
     * @param array  $array
     * @param string $value
     *
     * @return string
     */
    public function arrayColumn(array $array, ?string $value): array
    {
        return array_column($array, $value);
    }
}
