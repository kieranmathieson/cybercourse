<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelperController extends Controller
{
    /**
     * Tell the user something is not implemented.
     *
     * @Route("/not-implemented-yet", name="not_implemented_yet")
     */
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
