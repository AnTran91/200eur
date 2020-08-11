<?php

namespace App\Security\Providers;

use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\EmailUserProvider;

class ResidenceProvider extends EmailUserProvider
{
    /**
     * @var array
     */
    private $residenceRoles;

    /**
     * EmmobilierProvider constructor.
     *
     * @param UserManagerInterface  $userManager
     * @param array                 $residenceRoles
     */
    public function __construct(UserManagerInterface $userManager, array $residenceRoles)
    {
        parent::__construct($userManager);
        $this->residenceRoles = $residenceRoles;
    }

    /**
     * {@inheritdoc}
     */
    protected function findUser($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!is_null($user)){
            foreach ($this->residenceRoles as $role)
            {
                if ($user->hasRole($role)) {
                    return $user;
                }
            }
        }

        return null;
    }
}