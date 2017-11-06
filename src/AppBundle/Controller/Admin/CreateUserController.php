<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CreateUserController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/create-user1", name="create_user1")
     */
    public function createUser1Controller() {
        $email = 'xena@example.com';
        $password = 'xena';

        $userManager = $this->get('fos_user.user_manager');
        /**
         * @var \AppBundle\Entity\User $user
         */
        $user = $userManager->createUser();
        $user->setUsername('xena');
        $user->setEmail($email);
        $user->setFirstName('Xena');
        $user->setLastName('Warrior Princess');
        $user->setAboutMe('Blah');
        $user->setRoles([User::ROLE_SUPER_ADMIN]);
        $user->setEmailCanonical($email);
        $user->setEnabled(1); // enable the user or enable it later with a confirmation token in the email
        // this method will encrypt the password with the default settings :)
        $user->setPlainPassword($password);
        $user->setUpdatedAt(new \DateTime());
        $userManager->updateUser($user);

        return $this->render('dev/dev.html.twig', [
            'output' => 'Created user Xena',
        ]);

    }

    /**
     * @Route("/u2", name="u2")
     */
    public function checkHasRoleController() {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername('xena');
        $isSuper = $user->hasRole(User::ROLE_SUPER_ADMIN);
        if ( $isSuper ) {
            $message = 'Super!';
        }
        else {
            $message = 'normal';
        }
        return new Response($message);
    }
}
