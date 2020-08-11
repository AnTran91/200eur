<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use FOS\UserBundle\FOSUserEvents;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\RouterInterface;
use App\Handlers\FileHandler;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;
use App\Entity\Wallet;

/**
 * @see \FOS\UserBundle\Event\UserEvent class.
 * @see \FOS\UserBundle\Event\FormEvent class.
 * @see \FOS\UserBundle\Event\FilterUserResponseEvent class.
 * @see \FOS\UserBundle\Event\GetResponseUserEvent class.
 */
final class UserEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var FileHandler
     */
    private $dirHandler;

    /**
     * @var array
     */
    private $emmobilierRoles;

    /**
     * Constructor
     *
     * @param \App\Handlers\FileHandler $uploader
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param array $emmobilierRoles
     */
    public function __construct(FileHandler $uploader, EntityManagerInterface $em, RouterInterface $router, array $emmobilierRoles)
    {
        $this->dirHandler = $uploader;
        $this->em = $em;
        $this->router = $router;
        $this->emmobilierRoles = $emmobilierRoles;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
          FOSUserEvents::USER_CREATED => 'onCreateUser',
          FOSUserEvents::PROFILE_EDIT_COMPLETED => 'onEditProfile',
          FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
          FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationConfirmed'
        ];
    }

    public function onRegistrationConfirmed(FilterUserResponseEvent $event)
    {
        /** @var RedirectResponse $response */
        $response = $event->getResponse();
        $response->setTargetUrl($this->router->generate('order_list'));
    }

    /**
     * The RESETTING_RESET_INITIALIZE event occurs when the resetting process is initialized.
     *
     * @param FormEvent $event
     */
    public function onRegistrationSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();

        $user->setFirstName($user->getBillingAddress()->getFirstName());
        $user->setLastName($user->getBillingAddress()->getLastName());
        $user->setLanguage($event->getRequest()->getLocale());

        $this->onCreateUserWallet($user);
        $this->onCreateUserDir($user);
        $this->onSetUserRole($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * The USER_CREATED event occurs when the user is created with UserManipulator.
     *
     * @param UserEvent $event
     */
    public function onCreateUser(UserEvent $event)
    {
    	/** @var User $user */
        $user = $event->getUser();

        $this->onCreateUserWallet($user);
        $this->onCreateUserDir($user);
        $this->onSetUserRole($user);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * The USER_CREATED event occurs when the user is created with UserManipulator.
     *
     * @param FilterUserResponseEvent $event
     */
    public function onEditProfile(FilterUserResponseEvent $event)
    {
    	/** @var User $user */
        $user = $event->getUser();
        $request = $event->getRequest();

        if (!empty($user->getLanguage())) {
            $request->getSession()->set('_locale', $user->getLanguage());
        }
    }

    /**
     * Create wallet after the user creation.
     *
     * @param User $user
     */
    private function onCreateUserWallet(User &$user): void
    {
        $wallet = (new Wallet())
                ->setClient($user)
                ->setLastUpdate(new \DateTime('now'))
                ->setCurrentAmount(0);

        $user->setWallet($wallet);
    }

    /**
     * Create unique dir after the user creation.
     *
     * @param User $user
     */
    private function onCreateUserDir(User &$user): void
    {
        if (!empty($user->getFirstName())){
            $username = $user->getFirstName();
        }else{
            list($username) = explode('@', $user->getEmail());
        }

        do {
            $userUniqueDir = $this->dirHandler->getUserUniqueDir($username);
        } while (!is_null($this->em->getRepository(User::class)->findOneBy(['userDirectory' => $userUniqueDir])));

        $user->setUserDirectory($userUniqueDir);
    }

    private function onSetUserRole(User &$user)
    {
        foreach ($this->emmobilierRoles as $role){
            $user->addRole($role);
        }
    }
}
