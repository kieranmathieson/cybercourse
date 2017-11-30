<?php

namespace AppBundle\Controller\Content;

use AppBundle\Entity\Keyword;
use AppBundle\Entity\User;
use AppBundle\Helper\ContentTypes;
use AppBundle\Helper\LessonTreeMaker;
use AppBundle\Service\RstTransformer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends Controller
{
    /** @var LessonTreeMaker $lessonTreeMaker */
    protected $lessonTreeMaker;

    /** @var RstTransformer $rstTransformer */
    protected $rstTransformer;

    /**
     * ContentController constructor. Load dependencies.
     *
     * @param LessonTreeMaker $lessonTreeMaker Service that can make a lesson tree.
     */
    public function __construct(LessonTreeMaker $lessonTreeMaker, RstTransformer $rstTransformer)
    {
        $this->lessonTreeMaker = $lessonTreeMaker;
        $this->rstTransformer = $rstTransformer;
    }

    /**
     * Show content list.
     *
     * @Route("/{contentType}", name="content_list", requirements={"contentType" = "all|lesson|exercise|pattern|coreidea|sitepage"})
     * @param $contentType
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */

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
        //Make a lesson tree, with no current lesson.
        //Todo: Expand to show most recent lesson?
        $lessonTree = json_encode( $this->lessonTreeMaker
                ->setMakeLinks(true)
                ->makeTree()->getLessonTree()
        );
        return $this->render(
            'content/content_list.html.twig',
            [
                'lessonTree' => $lessonTree,
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
     * @param $contentType
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
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
        //Gather the data to be rendered.
        $renderData = [
            'contentType' => $contentType,
            //Flag that is true when a single lesson is being shown. In that case, it makes sense to activate
            //The current lesson in the lesson tree.
            'showingLesson' => ( $contentType === ContentTypes::LESSON ),
            'authorOrBetter' => $authorOrBetter,
            'slug' => $slug,
            'content' => $content,
        ];
        if ( $contentType === ContentTypes::LESSON ) {
            //Add lesson nav deets.
            $lessonNavLinkHelper = $this->get('app.lesson_nav_link_helper');
            $lessonNavLinkHelper->findFriends($content);
            $renderData['lessonNavLinks'] = [
                'parent' => $lessonNavLinkHelper->getParent(),
                'left' => $lessonNavLinkHelper->getLeftSib(),
                'right' => $lessonNavLinkHelper->getRightSib(),
                'children' => $lessonNavLinkHelper->getChildren(),
             ];
        }
        //Make a lesson tree, showing the current lesson as active.
        $lessonTree = json_encode( $this->lessonTreeMaker
            ->setMakeLinks(true)
            ->setExpandActive(true)
            ->setActiveId($content->getId())
            ->makeTree()->getLessonTree()
        );
        $renderData['lessonTree'] = $lessonTree;
        //ReST transform.
        $renderData['renderableBody'] = $this->rstTransformer->transform($content->getBody());
        return $this->render('content/content_show.html.twig', $renderData );
    }

    /**
     * Show content.
     *
     * @Route("/keyword/{id}", name="content_list_with_keyword")
     * @param Keyword $keyword
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listContentWithKeyword(Keyword $keyword)
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

    /**
     * @Route("/content/lesson/reorder", name="reorder_lessons")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_SUPER_ADMIN', 'ROLE_AUTHOR')")
     */
    public function reorderLessonsAction()
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser) && $loggedInUser !== 'anon.') {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        //Check user has permission.
        if ( ! $authorOrBetter ) {
            throw new NotFoundHttpException();
        }
        $repo = $this->entityManager->getRepository('AppBundle:Content');
        //Todo: just get the root node(s).
        $nodes = $repo->findLessonsForTree($authorOrBetter);
        //Todo: multiple roots.
        $tree = $repo->childrenHierarchy($nodes[0]);
        //Convert array to something we can pass as JSON to FancyTree.
        //If there is one root, the tree starts with the root's children at the top level.
        //If there is more than one root, each root has its own tree, with the root shown.
        $treeDisplayOptions = [
            'makeLinks' => false,
            'expandAll' => true,
            'expandActive' => false,
            'checkBoxes' => true,
            'markUnavailable' => true,
            'stripUnavailable' => false,
        ];
        $treeDisplay = []; //If there are no roots, the array will be MT.
        if ( count($tree) == 1 ) {
            //There is just one root.
            $treeDisplay = $this->toDisplayArray($tree[0]['__children'], $treeDisplayOptions);
        }
        elseif ( count($tree) > 1 ) {
            //There is more than one root.
            $treeDisplay = $this->toDisplayArray($tree, $treeDisplayOptions);
        }
        return $this->render(
            'content/reorder_lessons.html.twig',
            [
                'authorOrBetter' => $authorOrBetter,
                'lessonReorderTree' => json_encode($treeDisplay),
            ]
        );
    }
}
