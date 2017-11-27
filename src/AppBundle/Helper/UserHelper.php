<?php
/**
 * Methods that help with users, but aren't part of the user class.
 *
 * User: kieran
 * Date: 11/23/2017
 * Time: 8:39 AM
 */

namespace AppBundle\Helper;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserHelper
{
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
        if ( is_callable($loggedInUser, 'isAuthorOrBetter') ) {
            return $loggedInUser->isAuthorOrBetter();
        }
        return false;
    }
}