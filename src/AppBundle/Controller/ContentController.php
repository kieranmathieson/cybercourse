<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends Controller
{
    /**
     * Author menu page.
     *
     * @Route("/content", name="content_menu")
     */
    public function indexAction()
    {
        return new Response('content menu');
        return $this->render('', array('name' => $name));
    }
}
