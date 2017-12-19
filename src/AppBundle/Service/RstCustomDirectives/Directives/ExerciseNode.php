<?php
/**
 * Renders a ReST exercise node.
 * User: kieran
 * Date: 12/19/2017
 * Time: 11:16 AM
 */

namespace AppBundle\Service\RstCustomDirectives\Directives;


use AppBundle\Entity\Content;
use AppBundle\Helper\UserHelper;
use AppBundle\Service\RstTransformer;
use Doctrine\ORM\EntityManager;
use Gregwar\RST\Nodes\Node;

class ExerciseNode extends Node
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var UserHelper $userHelper */
    protected $userHelper;

    /** @var Content $exercise */
    protected $exercise;

    /** @var RstTransformer $rstTransformer */
    protected $rstTransformer;

    /**
     * ExerciseNode constructor.
     * @param Content $exercise
     * @param EntityManager $entityManager
     * @param UserHelper $userHelper
     * @param RstTransformer $rstTransformer
     */
    public function __construct(
        Content $exercise,
        EntityManager $entityManager,
        UserHelper $userHelper,
        RstTransformer $rstTransformer)
    {
        parent::__construct();
        $this->exercise = $exercise;
        $this->entityManager = $entityManager;
        $this->userHelper = $userHelper;
        $this->rstTransformer = $rstTransformer;
    }


    public function render()
    {
        $result = '<div class="inserted-exercise">'
             . '<label>Exercise</label>';
        //Pass the body of the exercise through the transformer.
        $result .= $this->rstTransformer->transform($this->exercise->getBody());
        $result .= '</div>';
        return $result;
    }
}