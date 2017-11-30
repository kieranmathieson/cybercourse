<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function indexAction()
    {
        return $this->render('admin/admin.html.twig');
    }
}
