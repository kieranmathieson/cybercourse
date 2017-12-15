<?php
/**
 * Methods that help with users, but aren't part of the user class.
 *
 * User: kieran
 * Date: 11/23/2017
 * Time: 8:39 AM
 */

namespace AppBundle\Helper;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserHelper
{
    const ANONYMOUS_USER_NAME = 'anon.';

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    /**
     * SkillCourseController constructor. Builds a lesson tree.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        //Store service references.
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Is there a logged in user, who is an author or admin?
     *
     * @return bool True if logged in user is author or better.
     */
    public function isLoggedInUserAuthorOrBetter() {
        /** @var \AppBundle\Entity\User|string|null $loggedInUser */
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        if ( is_null($loggedInUser) || ! $loggedInUser ) {
            return false;
        }
        if ( $loggedInUser === 'anon.' ) {
            return false;
        }
        if ( $loggedInUser instanceof User ) {
            return $loggedInUser->isAuthorOrBetter();
        }
        return false;
    }

    /**
     * Get the logged in user.
     * Null if there isn't one.
     *
     * @return User|null
     */
    public function getLoggedInUser() {
        /** @var \AppBundle\Entity\User|string|null $loggedInUser */
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        if ( ! $loggedInUser instanceof User ) {
            $loggedInUser = null;
        }
        return $loggedInUser;
    }

    /**
     * Get the id of the logged in user.
     * anon. for anonymous, null, or integer.
     *
     * @return integer|string|null
     */
    public function getLoggedInUserId() {
        /** @var \AppBundle\Entity\User|string|null $loggedInUser */
        $loggedInUser = $this->tokenStorage->getToken()->getUser();
        return $loggedInUser->getId();
    }

}