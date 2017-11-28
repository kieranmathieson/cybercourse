<?php

namespace AppBundle\Controller\Testosity;

use AppBundle\Service\RstCustomDirectives\Directives\SkillCourseKernel;
use AppBundle\Service\RstCustomDirectives\Directives\Dude;
use Gregwar\RST\Parser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Service\RstTransformer;

use Gregwar\RST\Environment;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $r=6;
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/r", name="rsttest")
     */
    public function restTestAction(Request $request, RstTransformer $rstTransformer)
    {
//        $rstTransformer = $this->get(RstTransformer::class);

// RST document
        $rst = ' 
Hello world
===========

What is it?
----------
This is a **RST** document!

Where can I get it?
-------------------
You can get it on the `GitHub page <https://github.com/Gregwar/RST>`_
';

// Parse it
        $parsedHtml = $rstTransformer->transform($rst);
        // replace this example code with whatever you need
        return $this->render(':rst:rsttest.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'parsedHtml' => $parsedHtml,
        ]);
    }


    /**
     * @Route("/r2", name="rsttest2")
     */
    public function restTest2Action(Request $request, RstTransformer $rstTransformer)
    {
//        $rstTransformer = $this->get(RstTransformer::class);

// RST document
        $rst = ' 
This is a **RST** document! About to div it.

.. div:: indent

    This is inside a div.

    .. div:: indent
    
        This is a nested div.
            
        .. div:: indent
    
            This is inside a nested div.

.. div:: indent

    This is a div after nested divs.

This is predude.

.. dude:: indent
    :scale: 50
    :weight: 68
    
    This is the first dude.
    
    .. dude:: indent
        :scale: 50
        :weight: 68

        This is your inner dude.

.. dude:: indent
    :scale: 50
    :weight: 68

    This is another dude.
        
This is post dude. AYE!

';

//        $dudeDirective = new Dude();
//        $parser = new Parser();
//        $parser->registerDirective($dudeDirective);

        $parser = new Parser(null, new CycoKernel());

// Parse it
        $document = $parser->parse($rst);
        //$document = $rstTransformer->transform($rst);
        // replace this example code with whatever you need
        return $this->render(':rst:rsttest.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'parsedHtml' => $document,
        ]);
    }


}
