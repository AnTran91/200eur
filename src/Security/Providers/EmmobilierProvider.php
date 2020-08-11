<?php

namespace App\Security\Providers;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\EmailUserProvider;

class EmmobilierProvider  extends EmailUserProvider
{
    /**
     * @var array
     */
    private $emmobilierRoles;

    /**
     * EmmobilierProvider constructor.
     * @param UserManagerInterface  $userManager
     * @param array                 $emmobilierRoles
     */
    public function __construct(UserManagerInterface $userManager, array $emmobilierRoles)
    {
        parent::__construct($userManager);
        $this->emmobilierRoles = $emmobilierRoles;
    }

    /**
     * {@inheritdoc}
     */
    protected function findUser($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!is_null($user)) {
            foreach ($this->emmobilierRoles as $role) {
                if ($user->hasRole($role)) {
                    return $user;
                }
            }
        }

        return null;
    }
}