<?php

/*
 * This file is part of the Emmobilier project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * User granted
 *
 * @internal
 */
class UserGranted extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_granted_user', [$this, 'isGranted'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Is granted user
     *
     * @param null|string   $role
     * @param User          $user
     * @return bool
     */
    public function isGranted(?string $role, User $user): bool
    {
        return $user->hasRole($role);
    }
}
