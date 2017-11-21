<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Content;
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

    /** @var  ObjectManager $objectManager */
    private $objectManager;
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
        $this->objectManager = $manager;
//        $this->createSuperAdmin($this->userManager);
        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager);
        $this->userManager = $this->container->get('fos_user.user_manager');
        $this->encryptPasswords();
        $this->treeContent();
    }

    public function treeContent() {
        $repository = $this->objectManager->getRepository('AppBundle:Content');
        $query = $repository->createQueryBuilder('c')
            ->where('c.contentType = :lesson')
            ->setParameter('lesson', 'lesson')
            ->orderBy('c.slug', 'ASC')
            ->getQuery();
        /** @var Content[] $lessons */
        $lessons = $query->getResult();
        /** @var Content $root */
        $root = $lessons[0];
        for ($lessonIndex = 1; $lessonIndex < 20; $lessonIndex += 4) {
            $lessons[$lessonIndex]->setParent($root);
            $lessons[$lessonIndex+1]->setParent($lessons[$lessonIndex]);
            $lessons[$lessonIndex+2]->setParent($lessons[$lessonIndex+1]);
            $lessons[$lessonIndex+3]->setParent($lessons[$lessonIndex+1]);
        }
        for ($lessonIndex = 0; $lessonIndex < 20; $lessonIndex ++) {
            $this->objectManager->persist($lessons[$lessonIndex]);
        }
        $this->objectManager->flush();
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

