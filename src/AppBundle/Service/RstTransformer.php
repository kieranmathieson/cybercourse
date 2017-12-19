<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 10/18/2017
 * Time: 11:22 AM
 */

namespace AppBundle\Service;

//use Gregwar\RST\Directives\DogBlock;
use AppBundle\Helper\UserHelper;
use AppBundle\Service\RstCustomDirectives\Directives\Dude;
use AppBundle\Service\RstCustomDirectives\Directives\ExerciseDirective;
use Doctrine\ORM\EntityManager;
use Gregwar\RST\Parser;

class RstTransformer
{
    protected $parser;
    protected $dudeDirective;
    protected $exerciseDirective;

    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var UserHelper $userHelper */
    protected $userHelper;

    public function __construct(
        EntityManager $entityManager,
        UserHelper $userHelper)
    {
        $this->entityManager = $entityManager;
        $this->userHelper = $userHelper;
        $this->dudeDirective = new Dude();
        $this->exerciseDirective = new ExerciseDirective(
            $this->entityManager, $this->userHelper, $this);
        $this->parser = new Parser();
        $this->parser->registerDirective($this->dudeDirective);
        $this->parser->registerDirective($this->exerciseDirective);
    }

    public function transform($rstContent) {
        $transformedNodes = $this->parser->parse($rstContent);
        $transformedHtml = $transformedNodes->render();
        return $transformedHtml;
    }

}