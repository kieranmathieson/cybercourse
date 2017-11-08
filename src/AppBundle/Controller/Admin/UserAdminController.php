<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminEditUserFormType;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends Controller
{
    /**
     * List all of the users.
     *
     * @Route("/admin/user", name="admin_user_list")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function listUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var EntityRepository $userRepo */
        $userRepo = $em->getRepository('AppBundle:User');

        $users = $userRepo->createQueryBuilder('user')
            ->orderBy('user.username', 'ASC')
            ->getQuery()
            ->execute();

        return $this->render('admin/user/admin_list_user.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Show one user's deets.
     *
     * @Route("/admin/user/{id}", name="admin_user_show")
     * @Security("has_role('ROLE_ADMIN')")
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminShowUser(User $user) {
        return $this->render('admin/user/admin_show_user.html.twig', array(
            'user' => $user,
        ));

    }

    /**
     * Edit one user's deets.
     *
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminEditUser(Request $request, User $user, UserPasswordEncoderInterface $encoder) {
        $form = $this->createForm(AdminEditUserFormType::class, $user);

        // only handles data on POST
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \AppBundle\Entity\User $user */
            $user = $form->getData();
            //Has the user supplied a new password?
            $plainPw = trim($user->getPlainPassword());
            if ( strlen($plainPw) !== 0 ) {
                //Compare with password repeat.
                $plainPwRepeat = trim($form->get('plainPasswordRepeat')->getData());
                if ( $plainPw != $plainPwRepeat ) {
                    $this->addFlash('error', 'Sorry, new passwords must match.');
                    return $this->redirectToRoute('admin_user_edit', ['id' => $user->getId()]);
                }
                        $user->setPassword(
                    $encoder->encodePassword($user, $user->getPlainPassword())
                );
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User updated!');

            return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/admin_edit_user.html.twig', array(
            'form' => $form->createView(),
        ));

    }


}
