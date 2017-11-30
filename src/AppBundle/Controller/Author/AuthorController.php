<?php

namespace AppBundle\Controller\Author;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AuthorController extends Controller
{
    /**
     * Author menu page.
     *
     * @Route("/author", name="author_menu")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_AUTHOR')")
     */
    public function indexAction()
    {
        return new Response('Author menu');
    }
}
