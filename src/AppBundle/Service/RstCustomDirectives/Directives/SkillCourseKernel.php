<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 10/19/2017
 * Time: 10:35 AM
 */

namespace AppBundle\Service\RstCustomDirectives\Directives;
use Gregwar\RST\HTML\Kernel;

class SkillCourseKernel extends Kernel
{
    public function getDirectives() {
        $directives = parent::getDirectives();
        $directives[] = new Dude();
        return $directives;
    }

//    /**
//     * Get the name of the kernel
//     */
//    function getName()
//    {
//        //return 'cycokernel';
//    }
}
