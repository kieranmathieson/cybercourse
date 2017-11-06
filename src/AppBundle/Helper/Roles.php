<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 7:30 AM
 */

namespace AppBundle\Helper;

// Todo: Read from config.yml?
class Roles
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_STUDENT = 'ROLE_STUDENT';
    const ROLE_GRADER = 'ROLE_GRADER';
    const ROLE_INSTRUCTOR = 'ROLE_INSTRUCTOR';
    const ROLE_AUTHOR = 'ROLE_AUTHOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    const ROLES = [
        Roles::ROLE_USER,
        Roles::ROLE_STUDENT,
        Roles::ROLE_GRADER,
        Roles::ROLE_INSTRUCTOR,
        Roles::ROLE_AUTHOR,
        Roles::ROLE_ADMIN,
        Roles::ROLE_SUPER_ADMIN,

    ];
}
