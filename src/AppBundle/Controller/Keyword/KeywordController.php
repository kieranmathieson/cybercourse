<?php

namespace AppBundle\Controller\Keyword;

use AppBundle\Entity\Keyword;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
/**
 * Keyword controller.
 *
 * @Route("keyword")
 */
class KeywordController extends Controller
{
    /**
     * Lists all keyword entities.
     *
     * @Route("/", name="keyword_list")
     * @Method("GET")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository('AppBundle:Keyword')->findAll();
        //Authors can edit.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        return $this->render('keyword/keyword_list.html.twig', array(
            'keywords' => $keywords,
            'authorOrBetter' => $authorOrBetter,
        ));
    }

    /**
     * Creates a new keyword entity.
     *
     * @Route("/new", name="keyword_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_AUTHOR')")
     */
    public function newAction(Request $request)
    {
        $keyword = new Keyword();
        $newForm = $this->createForm('AppBundle\Form\KeywordFormType', $keyword);
        $newForm->handleRequest($request);
        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //handleRequest has updated the keyword object.
            $em->persist($keyword);
            $em->flush();
            return $this->redirectToRoute('keyword_show', array('id' => $keyword->getId()));
        }
        return $this->render('keyword/keyword_new.html.twig', array(
            'operation' => 'new',
            'form' => $newForm->createView(),
            //Where to go if the user cancels the operation.
            'cancel_destination' => $this->generateUrl('keyword_list')
        ));
    }

    /**
     * Displays a form to edit an existing keyword entity.
     *
     * @Route("/{id}/edit", name="keyword_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Keyword $keyword
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Security("has_role('ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_AUTHOR')")
     */
    public function editAction(Request $request, Keyword $keyword)
    {
//        $deleteForm = $this->createDeleteForm($keyword);
        $editForm = $this->createForm('AppBundle\Form\KeywordFormType', $keyword);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            //handleRequest has updated the keyword object.
            $em->persist($keyword);
            $em->flush();
            return $this->redirectToRoute('keyword_show', array('id' => $keyword->getId()));
        }
        return $this->render('keyword/keyword_edit.html.twig', array(
            'operation' => 'edit',
            'keyword' => $keyword,
            'form' => $editForm->createView(),
            //Where to go if the user cancels the operation.
            'cancel_destination' => $this->generateUrl('keyword_show', ['id' => $keyword->getId()])
//            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Showa a keyword entity, including what it is linked to.
     *
     * @Route("/{id}", name="keyword_show")
     * @Method("GET")
     * @param Keyword $keyword
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Keyword $keyword)
    {
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Authors and better can edit keywords.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        return $this->render('keyword/keyword_show.html.twig', array(
            'keyword' => $keyword,
            'authorOrBetter' => $authorOrBetter,
        ));
    }

    /**
     * Deletes a keyword entity.
     *
     * @Route("/{id}", name="keyword_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Keyword $keyword)
    {
        $form = $this->createDeleteForm($keyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($keyword);
            $em->flush();
        }

        return $this->redirectToRoute('keyword_index');
    }

    /**
     * Creates a form to delete a keyword entity.
     *
     * @param Keyword $keyword The keyword entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Keyword $keyword)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('keyword_delete', array('id' => $keyword->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
