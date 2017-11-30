<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NotImplementedController extends Controller
{
    /**
     * Tell the user something is not implemented.
     *
     * @Route("/not-implemented-yet", name="not_implemented_yet")
     */
    public function showNotImplemented()
    {
        return $this->render('helper/show_message.html.twig', [
            'header' => 'Not implemented',
            'message' => 'Not implemented yet.'
        ]);
    }
}
