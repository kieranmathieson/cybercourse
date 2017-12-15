<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * Show a list of user profiles.
     * @Security("has_role('ROLE_USER')")
     * @Route("/user", name="user_list")
     */
    public function listUserAction()
    {
    }

    /**
     * @Route("/user/{id}", name="user_show")
     */
    public function showUserAction(User $user)
    {
        return $this->render('', array('name' => $name));
    }

}
