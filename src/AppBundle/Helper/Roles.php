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

    const ROLE_LABEL_STANDARD = 'standard';
    const ROLE_LABEL_SHORT = 'short';
    const ROLE_DESCRIPTION = 'description';

    const ROLE_LABELS = [
        Roles::ROLE_USER => [
            'standard' => 'User',
            'short' => 'User',
            'description' => 'Anyone who can log into the system.',
        ],
        Roles::ROLE_STUDENT => [
            'standard' => 'Student',
            'short' => 'Sdnt',
            'description' => 'A user taking a class.',
        ],
        Roles::ROLE_GRADER => [
            'standard' => 'Grader',
            'short' => 'Grdr',
            'description' => 'A user assessing student\'s submissions.',
        ],
        Roles::ROLE_INSTRUCTOR => [
            'standard' => 'Instructor',
            'short' => 'Instr',
            'description' => 'A user running a class.',
        ],
        Roles::ROLE_AUTHOR => [
            'standard' => 'Author',
            'short' => 'Authr',
            'description' => 'A user creating and editing content.',
        ],
        Roles::ROLE_ADMIN => [
            'standard' => 'Administrator',
            'short' => 'Admn',
            'description' => 'A user who can administer anything except other administrators.',
        ],
        Roles::ROLE_SUPER_ADMIN => [
            'standard' => 'Super administrator',
            'short' => 'Supr',
            'description' => 'A user who can do anything. There is only one.',
        ],
    ];
}
