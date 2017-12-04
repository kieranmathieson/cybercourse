<?php

namespace AppBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LastLesson extends \Twig_Extension
{

    const LAST_LESSON_SESSION_KEY = 'lastLessonId';

    private $session;

    /**
     * @param SessionInterface $container
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_last_lesson_id', [$this, 'getLastLessonId'])
        ];
    }

    /**
     *
     */
    public function getLastLessonId() {
        $lastLessonId = 0;
        if ( ! is_null($this->session->get(self::LAST_LESSON_SESSION_KEY)) ) {
            $lastLessonId = $this->session->get(self::LAST_LESSON_SESSION_KEY);
        }
        return $lastLessonId;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'last_lesson';
    }
}
