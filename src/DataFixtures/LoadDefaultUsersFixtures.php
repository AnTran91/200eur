<?php

namespace App\DataFixtures;

use App\Entity\Wallet;
use App\Handlers\FileHandler;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Yaml\Yaml;

class LoadDefaultUsersFixtures extends Fixture
{
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
     * @param array $emmobilierRoles
     */
    public function __construct(FileHandler $uploader, array $emmobilierRoles)
    {
        $this->dirHandler = $uploader;
        $this->emmobilierRoles = $emmobilierRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getUsersData() as $oldUser){

            $isUserExist = $manager->getRepository(User::class)->findOneBy(['email' => $oldUser['email']]);

            if (!is_null($isUserExist)){

	            print "Importing: Username = {$isUserExist->getUsername()} ....... Already exist" . PHP_EOL;
                continue;
            }

            $user = new User();
            $user->setUsername($oldUser['username']);
            $user->setEmail($oldUser['email']);

            $user->setFirstName($oldUser['nom'] ?? null);
            $user->setLastName($oldUser['prenom'] ?? null);

            $user->setLanguage($oldUser['langue'] ?? null);

            $user->getBillingAddress()->setAddress($oldUser['Adresse'] ?? null);
            $user->getBillingAddress()->setCountry($oldUser['pays'] ?? null);
            $user->getBillingAddress()->setCity($oldUser['ville'] ?? null);
            $user->getBillingAddress()->setZipCode($oldUser['cp'] ?? null);
            $user->getBillingAddress()->setSecondaryAddress($oldUser['adresse2'] ?? null);
            $user->getBillingAddress()->setCompany($oldUser['compagnie'] ?? null);
            $user->getBillingAddress()->setCorporateName($oldUser['raisonSociale'] ?? null);
            $user->getBillingAddress()->setTVA($oldUser['numeroTva'] ?? null);
            $user->getBillingAddress()->setPhone($oldUser['telephone'] ?? null);

            $user->setPassword($oldUser['password']);
            $user->setSalt($oldUser['salt']);

            $user->setEnabled($oldUser['enabled']);

            $this->onCreateUserWallet($user);
            $this->onCreateUserDir($user, $manager);
            $this->onSetUserRole($user);

            $manager->persist($user);

	        print "Importing: Username = {$user->getUsername()} ....... Done" . PHP_EOL;
        }

        $manager->flush();
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
     * @param ObjectManager $em
     */
    private function onCreateUserDir(User &$user, ObjectManager $em): void
    {
        if (!empty($user->getFirstName())){
            $username = $user->getFirstName();
        }else{
            list($username) = explode('@', $user->getEmail());
        }

        do {
            $userUniqueDir = $this->dirHandler->getUserUniqueDir($username);
        } while (!is_null($em->getRepository(User::class)->findOneBy(['userDirectory' => $userUniqueDir])));

        $user->setUserDirectory($userUniqueDir);
    }

    private function onSetUserRole(User &$user)
    {
        foreach ($this->emmobilierRoles as $role){
            $user->addRole($role);
        }
    }

    /**
     * Get Users.
     *
     * @return array The users data.
     */
    protected static function getUsersData(): array
    {
        return Yaml::parseFile(__DIR__ . '/Resource/Data/users.yaml');
    }
}
