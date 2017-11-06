<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 10/18/2017
 * Time: 3:04 PM
 */
//namespace Gregwar\RST\Directives;

namespace AppBundle\Service\RstCustomDirectives\Directives;

//use Gregwar\RST\Directive;
//use Gregwar\RST\Nodes\DogNode;
use Gregwar\RST\Parser;
use Gregwar\RST\SubDirective;
use Gregwar\RST\Nodes\WrapperNode;

class Dude extends SubDirective //Directive
{

    public function getName()
    {
        return 'dude';
    }

//    public function process(Parser $parser, $node, $variable, $data, array $options)
//    {
//        if ($node) {
//            $kernel = $parser->getKernel();
//            if ($node instanceof DogNode) {
//                $node->setLanguage(trim($data));
//            }
//            if ($variable) {
//                $environment = $parser->getEnvironment();
//                $environment->setVariable($variable, $node);
//            } else {
//                $document = $parser->getDocument();
//                $document->addNode($node);
//            }
//        }
//    }
//    public function wantCode()
//    {
//        return false;
//    }

    public function processSub(Parser $parser, $document, $variable, $class, array $options)
    {
        $start = '<div';
        if ($class) {
            $start .= ' class="'.$class.'"';
        }
        $start .= '><p>DUDE!</p>';

        return new WrapperNode($document, $start, '</div>');
    }
}