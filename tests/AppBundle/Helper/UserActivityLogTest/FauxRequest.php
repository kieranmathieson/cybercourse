<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/28/2017
 * Time: 3:11 PM
 */

namespace Tests\AppBundle\Helper\UserActivityLogTest;


class FauxRequest
{
    public function getClientIp() {
        global $ip;
        return $ip;
    }

    public function getPathInfo() {
        global $pathInfo;
        return $pathInfo;
    }
}