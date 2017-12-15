<?php

namespace AppBundle\Controller\Content;

use AppBundle\Controller\Author\AuthorContentController;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\User;
use AppBundle\Helper\ContentHelper;
use AppBundle\Helper\LessonTreeMaker;
use AppBundle\Helper\UserHelper;
use AppBundle\Service\RstTransformer;
use AppBundle\Twig\Extension\LastLesson;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends Controller
{
    /** @var LessonTreeMaker $lessonTreeMaker */
    protected $lessonTreeMaker;

    /** @var RstTransformer $rstTransformer */
    protected $rstTransformer;

    /** @var SessionInterface $session */
    protected $session;

    /** @var UserHelper $userHelper */
    protected $userHelper;

    /** @var ContentHelper $contentHelper */
    protected $contentHelper;

    /**
     * ContentController constructor. Load dependencies.
     *
     * @param LessonTreeMaker $lessonTreeMaker Service that can make a lesson tree.
     * @param RstTransformer $rstTransformer
     * @param SessionInterface $session
     * @param UserHelper $userHelper
     * @param ContentHelper $contentHelper
     */
    public function __construct(
        LessonTreeMaker $lessonTreeMaker,
        RstTransformer $rstTransformer,
        SessionInterface $session,
        UserHelper $userHelper,
        ContentHelper $contentHelper
    )
    {
        $this->lessonTreeMaker = $lessonTreeMaker;
        $this->rstTransformer = $rstTransformer;
        $this->session = $session;
        $this->userHelper = $userHelper;
        $this->contentHelper = $contentHelper;
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
        if (!in_array($contentType, ContentHelper::CONTENT_TYPES)) {
            throw new \Exception('listContentAction: bad content type: '.$contentType);
        }
        //If an author, use a more capable controller.
        //This approach lets the controllers be written separately, with different security contexts in mind.
        if ( $this->userHelper->isLoggedInUserAuthorOrBetter() ) {
            //This doesn't seem like the right way to do this.
            //Could change the author controller so that its constructor uses ->get('service') to init everything,
            //rather than passing the services in.
            $authorController = new AuthorContentController(
                $this->lessonTreeMaker, $this->rstTransformer, $this->session, $this->userHelper, $this->contentHelper
            );
            return $authorController->authorListContentAction($contentType, $this->container);
        }
        /** @var \AppBundle\Repository\ContentRepository $contentRepo */
        $contentRepo = $this->getDoctrine()->getRepository('AppBundle:Content');
        $content = $contentRepo->findAllContentByTitle($contentType, false);
        return $this->render(
            'content/content_list.html.twig',
            [
                'contentType' => $contentType,
                'contentTypeDisplayName' => ContentHelper::CONTENT_TYPE_DISPLAY_NAMES[$contentType],
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
        //If an author, use a more capable controller.
        //This approach lets the controllers be written separately, with different security contexts in mind.
        if ( $this->userHelper->isLoggedInUserAuthorOrBetter() ) {
            //This doesn't seem like the right way to do this.
            $authorController = new AuthorContentController(
                $this->lessonTreeMaker, $this->rstTransformer, $this->session, $this->userHelper, $this->contentHelper
            );
            return $authorController->authorShowContentAction($contentType, $slug, $this->container);
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Content')
            ->findOneBy(['slug' => $slug, 'contentType' => $contentType]);
        if (!$content) {
            throw $this->createNotFoundException($contentType.' not found: '.$slug);
        }
        //Check availability.
        if ( ! $content->isAvailable() ) {
            throw $this->createNotFoundException($contentType.' not found: '.$slug);
        }
        //Gather the data to be rendered.
        $renderData = [];
        $this->contentHelper->prepareRenderableData($content, $renderData);
        //Render.
        return $this->render('content/content_show.html.twig', $renderData );
    }

}
