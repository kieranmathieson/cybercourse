<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/25/2017
 * Time: 8:57 AM
 */

namespace Tests\AppBundle\Helper\LessonNavHelperTest;


class FauxUser
{
    public function isAuthorOrBetter() {
        global $isAuthorOrBetter;
        return $isAuthorOrBetter;
    }
}