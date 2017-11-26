<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/25/2017
 * Time: 8:46 AM
 */

namespace Tests\AppBundle\Helper\UserHelperTest;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class FauxTokenStorage implements TokenStorageInterface
{

    /**
     * Returns the current security token.
     *
     * @return TokenInterface|null A TokenInterface instance or null if no authentication information is available
     */
    public function getToken()
    {
        return new FauxToken();
    }

    /**
     * Sets the authentication token.
     *
     * @param TokenInterface $token A TokenInterface token, or null if no further authentication information should be stored
     */
    public function setToken(TokenInterface $token = null)
    {
        // TODO: Implement setToken() method.
    }
}