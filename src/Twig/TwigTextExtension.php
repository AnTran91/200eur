<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigTextExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('truncate', [$this, 'twigTruncateFilter'], array('needs_environment' => true)),
            new TwigFilter('wordwrap', [$this, 'twigWordwrapFilter'], array('needs_environment' => true)),

            new TwigFilter('html', [$this, 'convertHTMLFilter'], array('needs_environment' => true)),
            new TwigFilter('strip_tags', [$this, 'stripTagsFilter'], array('needs_environment' => true)),
        );
    }

    /**
     * Convert all applicable characters to HTML entities
     *
     * @param \Twig_Environment $env
     * @param string $string
     * @return string
     */
    public function convertHTMLFilter(\Twig_Environment $env, ?string $string): string
    {
        return htmlentities ( $string, ENT_QUOTES, $env->getCharset() , true);
    }

    /**
     * Strip HTML and PHP tags from a string
     *
     * @param \Twig_Environment $env
     * @param string $string
     * @param string $allowableTags
     *
     * @return string
     */
    public function stripTagsFilter(\Twig_Environment $env, ?string $string, string $allowableTags = ""): string
    {
        return strip_tags($string, $allowableTags);
    }

    /**
     * @param \Twig_Environment $env
     * @param $value
     * @param int $length
     * @param bool $preserve
     * @param string $separator
     * @return string
     */
    public function twigTruncateFilter(\Twig_Environment $env, $value, $length = 30, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $env->getCharset()) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, $env->getCharset()))) {
                    return $value;
                }

                $length = $breakpoint;
            }

            return rtrim(mb_substr($value, 0, $length, $env->getCharset())) . $separator;
        }

        return $value;
    }

    /**
     * @param \Twig_Environment $env
     * @param $value
     * @param int $length
     * @param string $separator
     * @param bool $preserve
     * @return string
     */
    public function twigWordwrapFilter(\Twig_Environment $env, $value, $length = 80, $separator = "\n", $preserve = false)
    {
        $sentences = array();

        $previous = mb_regex_encoding();
        mb_regex_encoding($env->getCharset());

        $pieces = mb_split($separator, $value);
        mb_regex_encoding($previous);

        foreach ($pieces as $piece) {
            while (!$preserve && mb_strlen($piece, $env->getCharset()) > $length) {
                $sentences[] = mb_substr($piece, 0, $length, $env->getCharset());
                $piece = mb_substr($piece, $length, 2048, $env->getCharset());
            }

            $sentences[] = $piece;
        }

        return implode($separator, $sentences);
    }
}