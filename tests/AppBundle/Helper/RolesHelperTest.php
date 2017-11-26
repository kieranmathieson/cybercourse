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

class RolesHelperTest extends WebTestCase
{
    /** @var  Container */
    protected $serviceContainer;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        //get the DI container
        $this->serviceContainer = $kernel->getContainer();
    }

    public function testContentTypes() {
        $roles = $this->serviceContainer->get('app.roles');
        $this->assertEquals($roles::ROLE_STUDENT, 'ROLE_STUDENT', 'Test student role');
        $this->assertEquals($roles::ROLE_INSTRUCTOR, 'ROLE_INSTRUCTOR', 'Test instructor role');
        $this->assertEquals($roles::ROLE_AUTHOR, 'ROLE_AUTHOR', 'Test author role');
    }

    public function testContentTypesList() {
        $roles = $this->serviceContainer->get('app.roles');
        $this->assertTrue(in_array($roles::ROLE_STUDENT, $roles::ROLES), 'Student role in list');
        $this->assertTrue(in_array($roles::ROLE_INSTRUCTOR, $roles::ROLES), 'Instructor role in list');
        $this->assertTrue(in_array($roles::ROLE_AUTHOR, $roles::ROLES), 'Author role in list');
    }

    public function testDisplayNames() {
        $roles = $this->serviceContainer->get('app.roles');
        $this->assertEquals($roles::ROLE_LABELS[$roles::ROLE_STUDENT][$roles::ROLE_LABEL_STANDARD], 'Student', 'Test student standard label');
        $this->assertEquals($roles::ROLE_LABELS[$roles::ROLE_INSTRUCTOR][$roles::ROLE_LABEL_STANDARD], 'Instructor', 'Test instructor standard label');
        $this->assertEquals($roles::ROLE_LABELS[$roles::ROLE_AUTHOR][$roles::ROLE_LABEL_SHORT], 'Authr', 'Test author short label');
    }
}