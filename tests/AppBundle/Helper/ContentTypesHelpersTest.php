<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/24/2017
 * Time: 9:52 AM
 */

namespace Tests\AppBundle\Helper;


use AppBundle\Helper\ContentTypes;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;

class ContentTypesHelpersTest extends WebTestCase
{
    /** @var ContentTypes */
    protected $ct;
    /** @var  Container */
    protected $serviceContainer;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        //get the DI container
        $this->serviceContainer = $kernel->getContainer();
        //Get the helper
        $this->ct = $this->serviceContainer->get('app.content_types');
    }

    public function testContentTypes() {
        $this->assertEquals($this->ct::LESSON, 'lesson', 'Test lesson');
        $this->assertEquals($this->ct::EXERCISE, 'exercise', 'Test exercise');
        $this->assertEquals($this->ct::CORE_IDEA, 'coreidea', 'Test core idea');
    }

    public function testContentTypesList() {
        $this->assertTrue(in_array($this->ct::LESSON, $this->ct::CONTENT_TYPES), 'Lesson in list');
        $this->assertTrue(in_array($this->ct::EXERCISE, $this->ct::CONTENT_TYPES), 'Exercise in list');
        $this->assertTrue(in_array($this->ct::CORE_IDEA, $this->ct::CONTENT_TYPES), 'Core idea in list');
    }

    public function testDisplayNames() {
        $this->assertEquals($this->ct::CONTENT_TYPE_DISPLAY_NAMES[$this->ct::LESSON], 'Lesson', 'Test lesson display name');
        $this->assertEquals($this->ct::CONTENT_TYPE_DISPLAY_NAMES[$this->ct::EXERCISE], 'Exercise', 'Test exercise display name');
        $this->assertEquals($this->ct::CONTENT_TYPE_DISPLAY_NAMES[$this->ct::CORE_IDEA], 'Core idea', 'Test core idea display name');
    }
}