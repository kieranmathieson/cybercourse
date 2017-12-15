<?php

namespace AppBundle\Controller\Keyword;

use AppBundle\Entity\Keyword;
use AppBundle\Entity\User;
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
    public function showKeywordAction(Keyword $keyword)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        //Because of Doctrine relationship magic, the links to content are already loaded.
        return $this->render(
            'keyword/keyworded_content_list.html.twig',
            [
                'authorOrBetter' => $authorOrBetter,
                'keyword' => $keyword,
            ]
        );
    }

}
