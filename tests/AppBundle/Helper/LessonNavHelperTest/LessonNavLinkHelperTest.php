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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tests\AppBundle\Helper\LessonNavHelperTest\FauxEntityManager;
use Tests\AppBundle\Helper\LessonNavHelperTest\FauxRepository;
use Tests\AppBundle\Helper\LessonNavHelperTest\FauxToken;
use Tests\AppBundle\Helper\LessonNavHelperTest\FauxTokenStorage;
use Tests\AppBundle\Helper\LessonNavHelperTest\FauxUser;

class LessonNavLinkHelperTest extends WebTestCase
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

    public function testBadContentType() {
        $fauxRepository = new FauxRepository();
        $fauxToken = new FauxToken();

        $fauxEntityManager = $this->createMock(EntityManagerInterface::class);
        $fauxEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($fauxRepository);
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new LessonNavLinkHelper($fauxEntityManager, $fauxTokenStorage);
        $fauxContent = new Content();
        $exceptionThrown = false;
        $firstChars = 'dog';
        try {
            $helper->findFriends($fauxContent);
        }
        catch (\Exception $e) {
            $exceptionThrown = true;
            $firstChars = substr($e->getMessage(), 0,12);
        }
        $this->assertTrue($exceptionThrown && ($firstChars == 'Not a lesson'), 'Expected bad type exception thrown.');
    }

    public function testFindFriends1() {
        $fauxContentLeft = new Content();
        $fauxContentLeft->setTitle('left');
        $fauxContentLeft->setIsAvailable(true);

        $fauxContentRight = new Content();
        $fauxContentRight->setTitle('right');
        $fauxContentRight->setIsAvailable(true);

        $fauxContentParent = new Content();
        $fauxContentParent->setTitle('parent');
        $fauxContentParent->setIsAvailable(true);

        $fauxContentRoot = new Content();
        $fauxContentRoot->setTitle('root');
        $fauxContentRoot->setIsAvailable(true);

        $fauxContentChild1 = new Content();
        $fauxContentChild1->setTitle('child1');
        $fauxContentChild1->setIsAvailable(true);

        $fauxContentChild2 = new Content();
        $fauxContentChild2->setTitle('child2');
        $fauxContentChild2->setIsAvailable(true);

        $fauxLesson = new Content();
        $fauxLesson->setContentType('lesson');

        global $prevSibs, $nextSibs, $parent, $root, $kids;
        $prevSibs = [$fauxContentLeft];
        $nextSibs = [$fauxContentRight];
        $parent = $fauxContentParent;
        $root = $fauxContentRoot;
        $kids = [$fauxContentChild1, $fauxContentChild2];
        $fauxRepository = new FauxRepository();

        global $fauxUser;
        $fauxUser = 'anon.';
        $fauxToken = new FauxToken();

        $fauxEntityManager = $this->createMock(EntityManagerInterface::class);
        $fauxEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($fauxRepository);
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new LessonNavLinkHelper($fauxEntityManager, $fauxTokenStorage);
        $helper->findFriends($fauxLesson);

        $this->assertEquals($helper->getLeftSib(), $fauxContentLeft, 'Expected left lesson.');
        $this->assertEquals($helper->getLeftSib()->getTitle(), 'left', 'Expected left lesson title.');

        $this->assertEquals($helper->getRightSib(), $fauxContentRight, 'Expected right lesson.');
        $this->assertEquals($helper->getRightSib()->getTitle(), 'right', 'Expected right lesson title.');

        $this->assertEquals($helper->getParent(), $fauxContentParent, 'Expected parent lesson.');
        $this->assertEquals($helper->getParent()->getTitle(), 'parent', 'Expected parent lesson title.');

        $this->assertEquals(count($helper->getChildren()), 2, 'Expected two child lessons.');
        $this->assertEquals($helper->getChildren()[0], $fauxContentChild1, 'Expected first child lesson.');
        $this->assertEquals($helper->getChildren()[1], $fauxContentChild2, 'Expected second child lesson.');
        $this->assertEquals($helper->getChildren()[0]->getTitle(), 'child1', 'Expected first child lesson title.');
        $this->assertEquals($helper->getChildren()[1]->getTitle(), 'child2', 'Expected second child lesson title.');
    }

    public function testFindFriends2() {
        $fauxContentLeft = new Content();
        $fauxContentLeft->setTitle('left');
        $fauxContentLeft->setIsAvailable(true);

        $fauxContentRight = new Content();
        $fauxContentRight->setTitle('right');
        $fauxContentRight->setIsAvailable(false); //Not available

        $fauxContentParent = new Content();
        $fauxContentParent->setTitle('parent');
        $fauxContentParent->setIsAvailable(true);

        $fauxContentRoot = new Content();
        $fauxContentRoot->setTitle('root');
        $fauxContentRoot->setIsAvailable(true);

        $fauxContentChild1 = new Content();
        $fauxContentChild1->setTitle('child1');
        $fauxContentChild1->setIsAvailable(false); //Not available

        $fauxContentChild2 = new Content();
        $fauxContentChild2->setTitle('child2');
        $fauxContentChild2->setIsAvailable(true);

        $fauxLesson = new Content();
        $fauxLesson->setContentType('lesson');

        global $prevSibs, $nextSibs, $parent, $root, $kids;
        $prevSibs = [$fauxContentLeft];
        $nextSibs = [$fauxContentRight];
        $parent = $fauxContentParent;
        $root = $fauxContentRoot;
        $kids = [$fauxContentChild1, $fauxContentChild2];
        $fauxRepository = new FauxRepository();

        global $fauxUser;
        $fauxUser = 'anon.';
        $fauxToken = new FauxToken();

        $fauxEntityManager = $this->createMock(EntityManagerInterface::class);
        $fauxEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($fauxRepository);
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new LessonNavLinkHelper($fauxEntityManager, $fauxTokenStorage);
        $helper->findFriends($fauxLesson);

        $this->assertEquals($helper->getLeftSib(), $fauxContentLeft, 'Expected left lesson.');
        $this->assertEquals($helper->getLeftSib()->getTitle(), 'left', 'Expected left lesson title.');

        $this->assertNull($helper->getRightSib(), 'Expected no right lesson.');

        $this->assertEquals($helper->getParent(), $fauxContentParent, 'Expected parent lesson.');
        $this->assertEquals($helper->getParent()->getTitle(), 'parent', 'Expected parent lesson title.');

        $this->assertEquals(count($helper->getChildren()), 1, 'Expected one child lessons.');
        $this->assertEquals($helper->getChildren()[0], $fauxContentChild2, 'Expected first child lesson.');
        $this->assertEquals($helper->getChildren()[0]->getTitle(), 'child2', 'Expected first child lesson title.');
    }

    public function testFindFriends3() {
        //Like friends 2, but user is author.
        $fauxContentLeft = new Content();
        $fauxContentLeft->setTitle('left');
        $fauxContentLeft->setIsAvailable(true);

        $fauxContentRight = new Content();
        $fauxContentRight->setTitle('right');
        $fauxContentRight->setIsAvailable(false); //Not available

        $fauxContentParent = new Content();
        $fauxContentParent->setTitle('parent');
        $fauxContentParent->setIsAvailable(true);

        $fauxContentRoot = new Content();
        $fauxContentRoot->setTitle('root');
        $fauxContentRoot->setIsAvailable(true);

        $fauxContentChild1 = new Content();
        $fauxContentChild1->setTitle('child1');
        $fauxContentChild1->setIsAvailable(false); //Not available

        $fauxContentChild2 = new Content();
        $fauxContentChild2->setTitle('child2');
        $fauxContentChild2->setIsAvailable(true);

        $fauxLesson = new Content();
        $fauxLesson->setContentType('lesson');

        global $prevSibs, $nextSibs, $parent, $root, $kids;
        $prevSibs = [$fauxContentLeft];
        $nextSibs = [$fauxContentRight];
        $parent = $fauxContentParent;
        $root = $fauxContentRoot;
        $kids = [$fauxContentChild1, $fauxContentChild2];
        $fauxRepository = new FauxRepository();

        global $fauxUser, $isAuthorOrBetter;
        $isAuthorOrBetter = true; //Author
        $fauxUser = new FauxUser();
        $fauxToken = new FauxToken();

        $fauxEntityManager = $this->createMock(EntityManagerInterface::class);
        $fauxEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($fauxRepository);
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new LessonNavLinkHelper($fauxEntityManager, $fauxTokenStorage);
        $helper->findFriends($fauxLesson);

        $this->assertEquals($helper->getLeftSib(), $fauxContentLeft, 'Expected left lesson.');
        $this->assertEquals($helper->getLeftSib()->getTitle(), 'left', 'Expected left lesson title.');

        $this->assertEquals($helper->getRightSib(), $fauxContentRight, 'Expected right lesson.');
        $this->assertEquals($helper->getRightSib()->getTitle(), 'right', 'Expected right lesson title.');

        $this->assertEquals($helper->getParent(), $fauxContentParent, 'Expected parent lesson.');
        $this->assertEquals($helper->getParent()->getTitle(), 'parent', 'Expected parent lesson title.');

        $this->assertEquals(count($helper->getChildren()), 2, 'Expected two child lessons.');
        $this->assertEquals($helper->getChildren()[0], $fauxContentChild1, 'Expected first child lesson.');
        $this->assertEquals($helper->getChildren()[1], $fauxContentChild2, 'Expected second child lesson.');
        $this->assertEquals($helper->getChildren()[0]->getTitle(), 'child1', 'Expected first child lesson title.');
        $this->assertEquals($helper->getChildren()[1]->getTitle(), 'child2', 'Expected second child lesson title.');
    }

    public function testFindFriends4() {
        //User is logged in, but not an author.
        $fauxContentLeft = new Content();
        $fauxContentLeft->setTitle('left');
        $fauxContentLeft->setIsAvailable(true);

        $fauxContentRight = new Content();
        $fauxContentRight->setTitle('right');
        $fauxContentRight->setIsAvailable(false); //Not available

        $fauxContentParent = new Content();
        $fauxContentParent->setTitle('parent');
        $fauxContentParent->setIsAvailable(true);

        $fauxContentRoot = new Content();
        $fauxContentRoot->setTitle('root');
        $fauxContentRoot->setIsAvailable(true);

        $fauxContentChild1 = new Content();
        $fauxContentChild1->setTitle('child1');
        $fauxContentChild1->setIsAvailable(false); //Not available

        $fauxContentChild2 = new Content();
        $fauxContentChild2->setTitle('child2');
        $fauxContentChild2->setIsAvailable(true);

        $fauxLesson = new Content();
        $fauxLesson->setContentType('lesson');

        global $prevSibs, $nextSibs, $parent, $root, $kids;
        $prevSibs = [$fauxContentLeft];
        $nextSibs = [$fauxContentRight];
        $parent = $fauxContentParent;
        $root = $fauxContentRoot;
        $kids = [$fauxContentChild1, $fauxContentChild2];
        $fauxRepository = new FauxRepository();

        global $fauxUser, $isAuthorOrBetter;
        $isAuthorOrBetter = false; //Not an author
        $fauxUser = new FauxUser();
        $fauxToken = new FauxToken();

        $fauxEntityManager = $this->createMock(EntityManagerInterface::class);
        $fauxEntityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($fauxRepository);
        $fauxTokenStorage = $this->createMock(TokenStorageInterface::class);
        $fauxTokenStorage->expects($this->any())
            ->method('getToken')
            ->willReturn($fauxToken);

        $helper = new LessonNavLinkHelper($fauxEntityManager, $fauxTokenStorage);
        $helper->findFriends($fauxLesson);

        $this->assertEquals($helper->getLeftSib(), $fauxContentLeft, 'Expected left lesson.');
        $this->assertEquals($helper->getLeftSib()->getTitle(), 'left', 'Expected left lesson title.');

        $this->assertNull($helper->getRightSib(), 'Expected no right lesson.');

        $this->assertEquals($helper->getParent(), $fauxContentParent, 'Expected parent lesson.');
        $this->assertEquals($helper->getParent()->getTitle(), 'parent', 'Expected parent lesson title.');

        $this->assertEquals(count($helper->getChildren()), 1, 'Expected one child lessons.');
        $this->assertEquals($helper->getChildren()[0], $fauxContentChild2, 'Expected first child lesson.');
        $this->assertEquals($helper->getChildren()[0]->getTitle(), 'child2', 'Expected first child lesson title.');
    }

}