<?php

namespace AppBundle\Controller\Author;

use AppBundle\Entity\Content;
use AppBundle\Form\ContentFormType;
use AppBundle\Helper\ContentHelper;
use AppBundle\Helper\LessonTreeMaker;
use AppBundle\Helper\UserHelper;
use AppBundle\Service\RstTransformer;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
//use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AuthorContentController
 * @package AppBundle\Controller\Author
 *
 * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
 */
class AuthorContentController extends Controller
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
     * AuthorContentController constructor. Load dependencies.
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
        ContentHelper $contentHelper)
    {
        $this->lessonTreeMaker = $lessonTreeMaker;
        $this->rstTransformer = $rstTransformer;
        $this->session = $session;
        $this->userHelper = $userHelper;
        $this->contentHelper = $contentHelper;
    }

    /**
     * Show content list for author.
     *
     * This controller has no route. It is called from ContentController when the current user has a higher security
     * authorization. This lets the two controllers be written in different psychological contexts.
     *
     * @param $contentType
     * @param ContainerInterface $container
     * @return Response
     * @throws \Exception
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     */
    public function authorListContentAction($contentType, ContainerInterface $container)
    {
        if (!in_array($contentType, ContentHelper::CONTENT_TYPES)) {
            throw new \Exception('authorListContentAction: bad content type: '.$contentType);
        }
        $authorOrBetter = $this->userHelper->isLoggedInUserAuthorOrBetter();
        //Another permission check.
        if ( ! $authorOrBetter ) {
            throw new \Exception('authorListContentAction: permission failure.');
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $container->get('doctrine.orm.entity_manager');
        /** @var \AppBundle\Repository\ContentRepository $contentRepo */
        $contentRepo = $em->getRepository('AppBundle:Content');
        //Second param says to run query for author or better role.
        $content = $contentRepo->findAllContentByTitle($contentType, true);
        $html = $container->get('twig')->render(
            'author/content/author_content_list.html.twig',
            [
                'contentType' => $contentType,
                'contentTypeDisplayName' => ContentHelper::CONTENT_TYPE_DISPLAY_NAMES[$contentType],
                'content' => $content,
            ]
        );
        return new Response($html);
    }

    /**
     * Show content.
     *
     * This controller has no route. It is called from ContentController when the current user has a higher security
     * authorization. This lets the two controllers be written in different psychological contexts.
     *
     * @param $contentType
     * @param $slug
     * @param ContainerInterface $container
     * @return Response
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     */
    public function authorShowContentAction($contentType, $slug, ContainerInterface $container)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $content = $em->getRepository('AppBundle:Content')
            ->findOneBy(['slug' => $slug, 'contentType' => $contentType]);
        if (!$content) {
            throw $this->createNotFoundException($contentType.' not found: '.$slug);
        }
        //Adjust title for availability.
        $content->changeTitleIfNotAvailable();
        //Adjust short title for availability.
        $content->changeShortMenuTreeTitleIfNotAvailable();
        //Gather the data to be rendered.
        $renderData = [];
        $this->contentHelper->prepareRenderableData($content, $renderData);
        $html = $container->get('twig')->render('author/content/author_content_show.html.twig', $renderData);
        return new Response($html);
    }

    /**
     * @Route("/author/{contentType}/new", name="author_content_new")
     * @param $contentType
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     */
    public function newContentAction($contentType, Request $request)
    {
        //Create a new object to get its default field values.
        $content = new Content();
        //Pass the content type to the form.
        $content->setContentType($contentType);
        //Pass new object to the form.
        $form = $this->createForm(ContentFormType::class, $content);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var \AppBundle\Entity\Content $content
             */
            $content = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success', $contentType . ' created.');

            return $this->redirectToRoute('content_show', [
                'contentType' => $contentType,
                'slug' => $content->getSlug(),
            ]);
        }
        return $this->render('author/content/author_content_new.html.twig', [
            'contentForm' => $form->createView(),
            'operation' => 'new',
            'contentType' => $contentType,
            //Where to go if the user cancels the operation.
            'cancel_destination' => $this->generateUrl('content_list', [
                'contentType' => $contentType,
            ])
        ]);
    }

    /**
     * @Route("/author/{contentType}/{id}/edit", name="author_content_edit")
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     * @param $contentType
     * @param $id
     * @param Request $request
     * @param Content $content
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editContentAction($contentType, $id, Request $request, Content $content)
    {
        $form = $this->createForm(ContentFormType::class, $content);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                /** @var \AppBundle\Entity\Content $content */
            $content = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();
            $this->addFlash(
                'success',
                ContentHelper::CONTENT_TYPE_DISPLAY_NAMES[$contentType].' updated!'
            );
            //Done. Show the content.
            return $this->redirectToRoute(
                'content_show',
                [
                    'contentType' => $contentType,
                    'slug' => $content->getSlug(),
                ]
            );
        }

        return $this->render('author/content/author_content_edit.html.twig', [
            'contentForm' => $form->createView(),
            'operation' => 'edit',
            'content_id' => $content->getId(),
            'contentType' => $contentType,
            //Define tabs for the file groups.
            'fileGroups' => [
                [
                    'group' => 'content_media',
                    'tabLabel' => 'Media',
                    'help' => 'Media for insertion into summary, body, and notes.',
                ],
                [
                    'group' => 'content_attached_file',
                    'tabLabel' => 'Public attachments',
                    'help' => 'Attached files shown to all users.',
                ],
                [
                    'group' => 'content_attached_file_hidden',
                    'tabLabel' => 'Hidden attachments',
                    'help' => 'Attached files shown to privileged users, e.g., exercise solutions.',
                ],
            ],
            //Where to go if the user cancels the operation.
            'cancel_destination' => $this->generateUrl('content_show', [
               'contentType' => $contentType, 'slug' => $content->getSlug(),
            ]),
        ]);
    }

    /**
     * Delete a content entity.
     *
     * @Route("/author/{contentType}/{id}/delete", name="author_content_delete")
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteContentAction($contentType, $id)
    {
        //Does the same thing as the annotation.
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return new Response('Not here.');
    }

    /**
     * @Route("/content/lesson/reorder", name="author_reorder_lessons")
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     * @return Response
     */
    public function reorderLessonsAction()
    {
        $lessonReorderTree = json_encode(
            $this->lessonTreeMaker->makeTreeDisplay(false, true)
        );
        return $this->render(
            'author/content/author_lessons_reorder.html.twig',
            [
                'lessonReorderTree' => $lessonReorderTree,
                'cancel_destination' => $this->generateUrl('content_list', [
                    'contentType' => ContentHelper::LESSON,
                ])
            ]
        );
    }

}
