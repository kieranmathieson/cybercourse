<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 10/18/2017
 * Time: 11:22 AM
 */

namespace AppBundle\Service;

//use Gregwar\RST\Directives\DogBlock;
use AppBundle\Service\RstCustomDirectives\Directives\Dude;
use Gregwar\RST\Parser;

class RstTransformer
{
    private $parser;
    private $dudeDirective;

    public function __construct()
    {
        $this->dudeDirective = new Dude();
        $this->parser = new Parser();
        $this->parser->registerDirective($this->dudeDirective);
    }

    public function transform($rstContent) {
        $transformedHtml = $this->parser->parse($rstContent);
        return $transformedHtml;
    }

}