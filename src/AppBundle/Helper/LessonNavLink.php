<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 12/17/2017
 * Time: 10:55 AM
 */

namespace AppBundle\Helper;


class LessonNavLink
{
    /** @var int $lessonId Id of the lesson in the database. */
    protected $lessonId;

    /** @var string $slug Slug (URL component) of the lesson. */
    protected $slug;

    /** @var string $title Title of the lesson. */
    protected $title;

    /** @var string $shortMenuTitle Used instead of the full title in menus. */
    protected $shortMenuTitle;

    /**
     * LessonNavLink constructor.
     * @param int $lessonId
     * @param string $slug
     * @param string $title
     * @param string $shortMenuTitle
     */
    public function __construct($lessonId, $slug, $title, $shortMenuTitle)
    {
        $this->lessonId = $lessonId;
        $this->slug = $slug;
        $this->title = $title;
        $this->shortMenuTitle = $shortMenuTitle;
    }

    /**
     * @return int
     */
    public function getLessonId(): int
    {
        return $this->lessonId;
    }

    /**
     * @param int $lessonId
     */
    public function setLessonId(int $lessonId)
    {
        $this->lessonId = $lessonId;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getShortMenuTitle(): string
    {
        return $this->shortMenuTitle;
    }

    /**
     * @param string $shortMenuTitle
     */
    public function setShortMenuTitle(string $shortMenuTitle)
    {
        $this->shortMenuTitle = $shortMenuTitle;
    }



}