<?php

namespace App\Security\Providers;


use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\EmailUserProvider;

class AdminProvider extends EmailUserProvider
{
    /**
     * @var array
     */
    private $adminRoles;

    /**
     * EmmobilierProvider constructor.
     * @param UserManagerInterface  $userManager
     * @param array                 $adminRoles
     */
    public function __construct(UserManagerInterface $userManager, array $adminRoles)
    {
        parent::__construct($userManager);
        $this->adminRoles = $adminRoles;
    }

    /**
     * {@inheritdoc}
     */
    protected function findUser($username)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($username);

        if (!is_null($user)){
            foreach ($this->adminRoles as $role)
            {
                if ($user->hasRole($role)) {
                    return $user;
                }
            }
        }

        return null;
    }
}