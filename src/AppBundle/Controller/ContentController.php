<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Keyword;
use AppBundle\Entity\User;
use AppBundle\Helper\ContentTypes;
use AppBundle\Helper\Roles;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ContentController extends SkillCourseController
{
    /**
     * Author menu page.
     *
     * @Route("/content", name="content_list")
     */
    public function listAllContentAction()
    {
        return new Response('content list');
    }

    /**
     * List all of the lessons by title.
     *
     * @Route("/lesson", name="lesson_list", defaults={"type" = "lesson"})
     */
    public function listLessonAction()
    {
        $contentType = ContentTypes::LESSON;
        $html = $this->listContentAction($contentType);

        return $html;
    }

    /**
     * List all of the exercises by title.
     *
     * @Route("/exercise", name="exercise_list", defaults={"type" = "exercise"})
     */
    public function listExerciseAction()
    {
        $contentType = ContentTypes::EXERCISE;
        $html = $this->listContentAction($contentType);

        return $html;
    }

    public function listContentAction($contentType)
    {
        if (!in_array($contentType, ContentTypes::CONTENT_TYPES)) {
            throw new \Exception('listContentAction: bad content type: '.$contentType);
        }
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        /** @var \AppBundle\Repository\ContentRepository $contentRepo */
        $contentRepo = $this->getDoctrine()
            ->getRepository('AppBundle:Content');
        $content = $contentRepo->findAllContentByTitle($contentType, $authorOrBetter);

        return $this->render(
            'content/list_content.html.twig',
            [
                'contentType' => $contentType,
                'contentTypeDisplayName' => ContentTypes::CONTENT_TYPE_DISPLAY_NAMES[$contentType],
                'authorOrBetter' => $authorOrBetter,
                'content' => $content,
            ]
        );

    }

    /**
     * Show content.
     *
     * @Route("/{contentType}/{slug}", name="content_show", requirements={"contentType" = "lesson|exercise|pattern|coreidea|sitepage"})
     */
    public function showContentAction($contentType, $slug)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Content')
            ->findOneBy(['slug' => $slug, 'contentType' => $contentType]);
        if (!$content) {
            throw $this->createNotFoundException($contentType.' not found: '.$slug);
        }

        return $this->render(
            'content/content_show.html.twig',
            [
                'contentType' => $contentType,
                'authorOrBetter' => $authorOrBetter,
                'slug' => $slug,
                'content' => $content,
            ]
        );
    }

    /**
     * Show content.
     *
     * @Route("/keyword/{id}", name="content_list_with_keyword")
     */
    public function showContentWithKeyword(Keyword $keyword)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        return $this->render(
            'keyword/list_keyworded_content.html.twig',
            [
                'authorOrBetter' => $authorOrBetter,
                'keyword' => $keyword,
            ]
        );
    }
}
