<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Form\AdminEditUserFormType;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
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

        return $this->render('admin/user/admin_user_list.html.twig', [
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
        //Get the file ids of the user's photos.
        $userPhotoFileIds = [];
        /** @var \AppBundle\Entity\UserPhoto $userPhotoRecord */
        foreach ( $user->getPhotos() as $userPhotoRecord ) {
            $fileId = $userPhotoRecord->getUploadedFileId();
            $userPhotoFileIds[] = $fileId;
        }
        //Load the uploaded files for the file ids.
        $userPhotoUploadedFiles = [];
        if ( count($userPhotoFileIds) > 0 ) {
            $em = $this->getDoctrine()->getManager();
            $userPhotoUploadedFiles =
                $em->getRepository('AppBundle:UploadedFile')->findUploadedFilesWithIds($userPhotoFileIds);
        }
        return $this->render('admin/user/admin_user_show.html.twig', [
            'user' => $user,
            'userPhotoUploadedFiles' => $userPhotoUploadedFiles,
        ]);
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
            //Flag to track whether errors found.
            $errorsFound = false;
            /** @var \AppBundle\Entity\User $user */
            $user = $form->getData();
            //Has the user supplied a new password?
            $plainPw = trim($user->getPlainPassword());
            $plainPwRepeat = trim($form->get('plainPasswordRepeat')->getData());
            if ( strlen($plainPw) !== 0 || strlen($plainPwRepeat) !== 0 ) {
                //Compare with password repeat.
                if ( $plainPw !== $plainPwRepeat ) {
                    //Don't match. Add messages to the form and the fields.
                    $form->addError(new FormError('Sorry, the new passwords must match.'));
                    $form->get('plainPassword')->addError(new FormError('Sorry, the new password must be typed exactly the same in both fields.'));
                    $form->get('plainPasswordRepeat')->addError(new FormError('Sorry, the new password must be typed exactly the same in both fields.'));
                    $errorsFound = true;
                }
                else {
                    $user->setPassword($encoder->encodePassword($user, $user->getPlainPassword()));
                }
            }
            if ( ! $errorsFound ) {
                //Save all the things.
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                //Add message to queue for user, redirect to show new data.
                $this->addFlash('success', 'User updated.');

                return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
            }
        }

        return $this->render('admin/user/admin_user_edit.html.twig', [
            'form' => $form->createView(),
            'max_num_photos' => $this->container->getParameter('app.user_photo_max_files'),
            'max_photo_file_size' => $this->container->getParameter('app.user_photo_max_file_size'),
        ]);

    }

    /**
     * Edit one user's deets.
     *
     * @Route("/admin/user/add", name="admin_user_add")
     * @Security("has_role('ROLE_ADMIN')")
     * @param Request $request
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function adminAddUser(Request $request, User $user, UserPasswordEncoderInterface $encoder) {
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

            $this->addFlash('success', 'User added.');

            return $this->redirectToRoute('admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/admin_user_edit.html.twig', array(
            'form' => $form->createView(),
        ));

    }



}
