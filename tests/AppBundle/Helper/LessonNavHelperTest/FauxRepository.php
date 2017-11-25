<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/25/2017
 * Time: 8:43 AM
 */

namespace Tests\AppBundle\Helper\LessonNavHelperTest;


class FauxRepository
{
    public function getPrevSiblings($contentItem) {
        global $prevSibs;
        return $prevSibs;
    }

    public function getNextSiblings($contentItem) {
        global $nextSibs;
        return $nextSibs;
    }

    public function getPath($contentItem) {
        global $parent, $root;
        return [$root, $parent, $contentItem];
    }

    public function children($contentItem) {
        global $kids;
        return $kids;
    }


}