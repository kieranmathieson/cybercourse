<?php
/**
 * Helper for content types.
 *
 * User: kieran
 * Date: 11/5/2017
 * Time: 7:30 AM
 */

namespace AppBundle\Helper;

use AppBundle\Entity\Content;
use AppBundle\Twig\Extension\LastLesson;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Service\RstTransformer;

class ContentHelper
{
    const ALL = 'all';
    const LESSON = 'lesson';
    const EXERCISE = 'exercise';
    const PATTERN = 'pattern';
    const CORE_IDEA = 'coreidea';
    const SITE_PAGE = 'sitepage';

    const CONTENT_TYPES = [
        self::ALL => 'all',
        self::LESSON => 'lesson',
        self::EXERCISE => 'exercise',
        self::PATTERN => 'pattern',
        self::CORE_IDEA => 'coreidea',
        self::SITE_PAGE => 'sitepage',
    ];

    const CONTENT_TYPE_DISPLAY_NAMES = [
        self::ALL => 'All',
        self::LESSON => 'Lesson',
        self::EXERCISE => 'Exercise',
        self::PATTERN => 'Pattern',
        self::CORE_IDEA => 'Core idea',
        self::SITE_PAGE => 'Site page',
    ];

    /** @var LessonNavLinkHelper $lessonNavLinkHelper */
    protected $lessonNavLinkHelper;

    /** @var SessionInterface $session */
    protected $session;

    /** @var RstTransformer $rstTransformer */
    protected $rstTransformer;

    /**
     * ContentHelper constructor.
     * @param LessonNavLinkHelper $lessonNavLinkHelper
     * @param SessionInterface $session
     * @param RstTransformer $rstTransformer
     */
    public function __construct(
        LessonNavLinkHelper $lessonNavLinkHelper,
        SessionInterface $session,
        RstTransformer $rstTransformer
    )
    {
        $this->lessonNavLinkHelper = $lessonNavLinkHelper;
        $this->session = $session;
        $this->rstTransformer = $rstTransformer;
    }

    /**
     * Add data to array, ready for template to render.
     *
     * @param Content $content Content to be rendered.
     * @param array $renderData Array to prepare.
     */
    public function prepareRenderableData(Content $content, array &$renderData)
    {
        $renderData ['contentType'] = $content->getContentType();
        $renderData ['slug'] = $content->getSlug();
        $renderData ['content'] = $content;
        if ( $content->getContentType() === self::LESSON ) {
            //Add lesson nav deets.
            //findFriends() updates refs in the helper object, for getNavBarLinks to use.
            $this->lessonNavLinkHelper->findFriends($content);
            $renderData['lessonNavLinks'] = $this->lessonNavLinkHelper->getLessonNavbarSlugs();
            //In the session, record this as the last lesson shown.
            //It's used by a Twig extension to show the last session accessed.
            $this->session->set( LastLesson::LAST_LESSON_SESSION_KEY, $content->getId() );
        }
        //ReST transform.
        $renderData['renderableBody'] = $this->rstTransformer->transform($content->getBody());
    }
}
