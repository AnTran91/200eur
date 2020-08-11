<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigExtension
 */
class SystemPayFormExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('systempayForm', array($this, 'systempayForm')),
        );
    }

    /**
     * @param array $fields
     * @param string $formName
     *
     * @return string
     */
    public function systempayForm(array $fields): string
    {
        $inputs = '';
        foreach ($fields as $field => $value) {
            $inputs .= sprintf('<input type="hidden" name="%s" value="%s">', $field, $value);
        }
        return $inputs;
    }
}
