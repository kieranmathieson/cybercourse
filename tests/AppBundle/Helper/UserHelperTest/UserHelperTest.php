<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/25/2017
 * Time: 8:35 AM
 */

namespace Tests\AppBundle\Helper;


use AppBundle\Helper\LessonNavLinkHelper;
use AppBundle\entity\Content;
use AppBundle\Helper\UserHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tests\AppBundle\Helper\LessonNavHelperTest\FauxUser;
use Tests\AppBundle\Helper\UserHelperTest\FauxToken;

class UserHelperTest extends WebTestCase
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

    public function testAnonymousUser() {
        global $fauxUser;
        $fauxUser = 'anon.';

        $fauxToken = new FauxToken(); //Returns $fauxUser.
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new UserHelper($fauxTokenStorage);

        $this->assertFalse($helper->isLoggedInUserAuthorOrBetter(), 'Tested Anonymous user.');
    }

    public function testNullUser() {
        global $fauxUser;
        $fauxUser = null;

        $fauxToken = new FauxToken(); //Returns $fauxUser.
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new UserHelper($fauxTokenStorage);

        $this->assertFalse($helper->isLoggedInUserAuthorOrBetter(), 'Tested null user.');
    }

    public function testNonAuthorUser() {
        global $fauxUser, $isAuthorOrBetter;
        $fauxUser = new FauxUser(); //Set return for getToken.
        $isAuthorOrBetter = false; //Set return for user->isAuthorOrBetter.
        $fauxToken = new FauxToken();
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new UserHelper($fauxTokenStorage);

        $this->assertFalse($helper->isLoggedInUserAuthorOrBetter(), 'Tested non-author user.');
    }

    public function testAuthorUser() {
        global $fauxUser, $isAuthorOrBetter;
        $fauxUser = new FauxUser(); //Set return for getToken.
        $isAuthorOrBetter = true; //Set return for user->isAuthorOrBetter.
        $fauxToken = new FauxToken();
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new UserHelper($fauxTokenStorage);

        $this->assertFalse($helper->isLoggedInUserAuthorOrBetter(), 'Tested author user.');
    }

}