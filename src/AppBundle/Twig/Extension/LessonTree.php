<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Helper\LessonTreeMaker;

class LessonTree extends \Twig_Extension
{

    protected $lessonTreeMaker;

    public function __construct(LessonTreeMaker $lessonTreeMaker)
    {
        $this->lessonTreeMaker = $lessonTreeMaker;
    }


    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_lesson_tree', [$this, 'getLessonTree'])
        ];
    }

    public function getLessonTree() {
        //Make a lesson tree, showing a lesson as active.
        //The active lesson (which one is) is set on the client.
        $lessonTree = json_encode( $this->lessonTreeMaker
            ->setMakeLinks(true)
            ->setExpandActive(true)
            ->makeTree()->getLessonTree()
        );
        return $lessonTree;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lesson_tree';
    }
}
