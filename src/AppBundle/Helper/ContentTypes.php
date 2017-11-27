<?php
/**
 * Helper for content types.
 *
 * User: kieran
 * Date: 11/5/2017
 * Time: 7:30 AM
 */

namespace AppBundle\Helper;

class ContentTypes
{
    const ALL = 'all';
    const LESSON = 'lesson';
    const EXERCISE = 'exercise';
    const PATTERN = 'pattern';
    const CORE_IDEA = 'coreidea';
    const SITE_PAGE = 'sitepage';

    const CONTENT_TYPES = [
        ContentTypes::ALL => 'all',
        ContentTypes::LESSON => 'lesson',
        ContentTypes::EXERCISE => 'exercise',
        ContentTypes::PATTERN => 'pattern',
        ContentTypes::CORE_IDEA => 'coreidea',
        ContentTypes::SITE_PAGE => 'sitepage',
    ];

    const CONTENT_TYPE_DISPLAY_NAMES = [
        ContentTypes::ALL => 'All',
        ContentTypes::LESSON => 'Lesson',
        ContentTypes::EXERCISE => 'Exercise',
        ContentTypes::PATTERN => 'Pattern',
        ContentTypes::CORE_IDEA => 'Core idea',
        ContentTypes::SITE_PAGE => 'Site page',
    ];
}
