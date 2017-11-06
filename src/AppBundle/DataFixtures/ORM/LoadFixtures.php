<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Helper\Roles;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Doctrine\UserManager;
use Nelmio\Alice\Fixtures;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFixtures implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /** @var  UserManager */
    protected $userManager;

    public function load(ObjectManager $manager)
    {
//        $this->createSuperAdmin($this->userManager);
        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager);
        $this->userManager = $this->container->get('fos_user.user_manager');
        $this->encryptPasswords();
    }

//    protected function createSuperAdmin(UserManager $userManager) {
//        /**
//         * @var \AppBundle\Entity\User $user
//         */
//        $user = $userManager->createUser();
//        $user->setUsername('xena');
//        $user->setEmail('xena@example.com');
//        $user->setFirstName('Xena');
//        $user->setLastName('Warrior Princess');
//        $user->setAboutMe('Blah');
//        $user->setRoles([Roles::ROLE_SUPER_ADMIN]);
//        $user->setEmailCanonical('xena@example.com');
//        $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
//        // this method will encrypt the password with the default settings :)
//        $user->setPlainPassword('xena');
//        $user->setUpdatedAt(new \DateTime());
//        $userManager->updateUser($user);
//    }

    protected function encryptPasswords() {
        $users = $this->userManager->findUsers();
        /** @var \AppBundle\Entity\User $user */
        foreach ( $users as $user ) {
            $pw = $user->getPassword();
            $user->setPassword('');
            $user->setPlainPassword($pw);
            $this->userManager->updateUser($user);
        }
    }
}

