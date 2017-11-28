<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/28/2017
 * Time: 3:04 PM
 */

namespace Tests\AppBundle\Helper\UserActivityLogTest;

use AppBundle\Helper\UserActivityLogHelper;
use AppBundle\Helper\UserHelper;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class UserActivityLogTest extends WebTestCase
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
    function testLog1() {
        global $ip, $pathInfo, $user;
        $ip = '100.101.102.103';
        $pathInfo = 'dogs/are/best';
        $user = null;
        $fauxRequest = new FauxRequest();
        $fauxUserHelper = new FauxUserHelper();
        $request = $this->createMock(Request::class);
        $request->expects($this->any())
            ->method('getClientIp')
            ->willReturn($fauxRequest->getClientIp());
        $request->expects($this->any())
            ->method('getPathInfo')
            ->willReturn($fauxRequest->getPathInfo());
        $userHelper = $this->createMock(UserHelper::class);
        $userHelper->expects($this->any())
            ->method('getLoggedInUser')
            ->willReturn($fauxUserHelper->getLoggedInUser());
        $em = $this->serviceContainer->get('doctrine.orm.entity_manager');
        $logHelper = new UserActivityLogHelper($em, $userHelper);
        try {
            $logHelper->logEvent(UserActivityLogHelper::VIEW_LESSON, $request);
            $this->assertTrue(true, 'Saved log message 1');
        }
        catch( \Exception $e ) {
            echo $e->getMessage();
        }


    }
}