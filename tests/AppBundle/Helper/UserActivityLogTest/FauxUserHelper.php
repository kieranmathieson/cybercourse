<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/28/2017
 * Time: 4:06 PM
 */

namespace Tests\AppBundle\Helper\UserActivityLogTest;


class FauxUserHelper
{
    public function getLoggedInUser() {
        global $user;
        return $user;
    }
}