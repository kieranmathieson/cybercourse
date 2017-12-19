<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 12/19/2017
 * Time: 9:31 AM
 */

namespace AppBundle\Service\RstCustomDirectives\Directives;

use AppBundle\Entity\Content;
use AppBundle\Helper\ContentHelper;
use AppBundle\Helper\UserHelper;
use AppBundle\Service\RstTransformer;
use Doctrine\ORM\EntityManager;
use Gregwar\RST\Directive;
use Gregwar\RST\HTML\Nodes\ParagraphNode;
use Gregwar\RST\Parser;

class ExerciseDirective extends Directive
{
    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var UserHelper $userHelper */
    protected $userHelper;

    /** @var RstTransformer $rstTransformer */
    protected $rstTransformer;

    /**
     * ExerciseDirective constructor.
     * @param EntityManager $entityManager
     * @param UserHelper $userHelper
     * @param RstTransformer $rstTransformer
     */
    public function __construct(
        EntityManager $entityManager,
        UserHelper $userHelper,
        RstTransformer $rstTransformer
)
    {
//        parent::construct();
        $this->entityManager = $entityManager;
        $this->userHelper = $userHelper;
        $this->rstTransformer = $rstTransformer;
    }

    /**
     * Get the directive name
     */
    public function getName()
    {
        return 'exercise';
    }

    /**
     * This can be overloaded to write a directive that just create one node for the
     * document.
     *
     * .. exercise:: slug
     *
     * @param Parser $parser the calling parser
     * @param string $variable The variable name of the directive
     * @param string $slug The slug of the exercise.
     * @param array $options
     * @return $node
     * @internal param The $options array of options for this directive
     */
    public function processNode(Parser $parser, $variable, $slug, array $options)
    {
        $errorMessage = '';
        /** @var Content $exercise */
        $exercise = $this->entityManager->getRepository('AppBundle:Content')
            ->findOneBy(['slug' => $slug, 'contentType' => ContentHelper::EXERCISE]);
        if ( ! $exercise) {
            $errorMessage = 'Could not find exercise with slug: ' . htmlspecialchars($slug);
        }
        else {
            //Check availability.
            if ( ! $exercise->isAvailable() && ! $this->userHelper->isLoggedInUserAuthorOrBetter()) {
                $errorMessage = 'Exercise not available: ' . htmlspecialchars($slug);
            }
        }
        //Create the output node.
        if ( $errorMessage ) {
            $node = new ParagraphNode($errorMessage);
        }
        else {
            $node = new ExerciseNode(
                $exercise,
                $this->entityManager,
                $this->userHelper,
                $this->rstTransformer
            );
        }
        return $node;
    }

}