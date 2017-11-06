<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class KeywordController extends Controller
{
    /**
     * List all of the keywords.
     *
     * @Route("/keyword", name="keyword_list")
     */
    public function listKeywordsAction()
    {
        $keywords = $this->getDoctrine()
            ->getRepository('AppBundle:Keyword')
            ->findAll();
        return $this->render('keyword/keyword_list.html.twig', [
                'keywords' => $keywords,
            ]
        );
    }

    /**
     * @Route("/keyword/{id}", name="keyword_show")
     */
    public function showKeywordAction($id)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $keyword = $em->getRepository('AppBundle:Keyword')
            ->find($id);
        if (!$keyword) {
            throw $this->createNotFoundException('Keyword not found: '.$id);
        }
        return $this->render('keyword/keyword_show.html.twig', array(
            'keyword' => $keyword,
        ));
    }
}
