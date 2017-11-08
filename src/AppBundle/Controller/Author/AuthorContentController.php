<?php

namespace AppBundle\Controller\Author;

use AppBundle\Entity\Content;
use AppBundle\Form\ContentFormType;
use AppBundle\Helper\ContentTypes;
use AppBundle\Helper\Roles;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AuthorContentController
 * @package AppBundle\Controller\Author
 *
 * @Security("has_role('ROLE_ADMIN', 'ROLE_AUTHOR')")
 */
class AuthorContentController extends Controller
{
    /**
     * List all of the lessons by title.
     *
     * @Route("/author/lesson", name="lesson_list")
     */
    public function authorListLessonAction()
    {
        $contentType = ContentTypes::LESSON;
        $html = $this->authorListContentAction($contentType);
        return $html;
    }
    public function authorListContentAction($contentType) {
        $hasAuthorRole = $this->container->get('security.authorization_checker')
            ->isGranted(Roles::ROLE_AUTHOR);
        if ( ! $hasAuthorRole ) {
            # todo: log potential attack.
            throw new AccessDeniedException('Authoring access denied.');
        }
        if ( ! in_array($contentType, ContentTypes::CONTENT_TYPES) ) {
            throw new Exception('authorListContentAction: bad content type: ' . $contentType);
        }
        $content = $this->getDoctrine()
            ->getRepository('AppBundle:Content')
            ->findBy(
                [ 'contentType' => $contentType, 'isAvailable' => true ],
                [ 'title' => 'ASC' ]
            );

        return $this->render('author/content/author_list_content.html.twig', [
            'content' => $content,
        ]);

    }

    /**
     * @Route("/author/lesson/new", name="lesson_new")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_AUTHOR')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newContentAction(Request $request)
    {
        //Create a new object to get its default field values.
        $content = new Content();
        //Pass new object to the form.
        $form = $this->createForm(ContentFormType::class, $content);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var \AppBundle\Entity\Content $content
             */
            $content = $form->getData();
            $content->setWhenCreated(new \DateTime());#Todo: use real date/time
            $content->setWhenUpdated(new \DateTime());#Todo: use real date/time
            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success', 'Lesson created!');

            return $this->redirectToRoute('lesson_list');
        }

        return $this->render('author/content/author_new_content.html.twig', [
            'lessonForm' => $form->createView(),
            'operation' => 'new',
        ]);
    }

    /**
     * @Route("/author/lesson/{id}/edit", name="lesson_edit")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_AUTHOR')")
     * @param Request $request
     * @param Content $content
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editContentAction(Request $request, Content $content)
    {
        $form = $this->createForm(ContentFormType::class, $content);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \AppBundle\Entity\Content $content */
            $content = $form->getData();

//            $x = $form->get('isAvailable');
//            $v = $x->getViewData();

//            $lesson->setIsAvailable( ! is_null( $form->get('isAvailable')->getViewData() ) );
//            $lesson->setTitle('DOGS');

//            $lesson->setIsAvailable( false );

            $content->setWhenUpdated(new \DateTime()); #Todo: use real date/time.


            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success', 'Lesson updated!');

            return $this->redirectToRoute('lesson_list');
        }

        return $this->render('author/content/author_edit_content.html.twig', [
            'lessonForm' => $form->createView(),
            'operation' => 'edit',
            'lesson_id' => $content->getId(),
        ]);
    }

    /**
     * Delete a lesson.
     *
     * @Route("/author/lesson/{id}/delete", name="lesson_delete")
     * @Security("has_role('ROLE_ADMIN', 'ROLE_AUTHOR')")
     */
    public function deleteContentAction() //$id)
    {
        //Does the same thing as the annotation.
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return new Response('Not here.');
    }
}
