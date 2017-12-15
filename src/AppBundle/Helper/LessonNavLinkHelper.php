<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/22/2017
 * Time: 10:11 AM
 */

namespace AppBundle\Helper;


use AppBundle\Entity\Content;
use AppBundle\Entity\User;
use AppBundle\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LessonNavLinkHelper
{
    /** @var Content The item that is the focus of the investigation. Helping Symfony with its enquiries. */
    protected $contentItem = null;

    /** @var Content The lesson's parent in the lesson tree. */
    protected $parent = null;

    /** @var Content The lesson's left sibling in the lesson tree. */
    protected $leftSib = null;

    /** @var Content The lesson's right sibling in the lesson tree. */
    protected $rightSib = null;

    /** @var Content[] The lesson's children in the lesson tree. */
    protected $children = null;

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var ContentRepository $repository */
    protected $repository;

    /** @var User $loggedInUser */
    protected $loggedInUser;

    /**
     * LessonNavLinkHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @throws \Exception
     * @internal param string $parentSlug
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository('AppBundle:Content');
        $this->loggedInUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * Find the parent, sibs, and children of a lesson. What is found depends on permissions of current user.
     * Authors or better have unavailable pages included.
     *
     * @param Content $contentItem The lesson.
     * @throws \Exception $contentItem is not a lesson.
     */
    public function findFriends(Content $contentItem) {
        if ( $contentItem->getContentType() !== ContentHelper::LESSON ) {
            throw new \Exception('Not a lesson:' . $contentItem->getContentType());
        }
        $this->contentItem = $contentItem;
        $authorOrBetter = ( $this->loggedInUser && $this->loggedInUser !== 'anon.' && $this->loggedInUser->isAuthorOrBetter() );
        //Compute the left sibling, if there is one.
        $leftSibs = $this->repository->getPrevSiblings($contentItem);
        if ( count($leftSibs) > 0 ) {
            /** @var Content $leftSib */
            $leftSib = end($leftSibs);
            //Is it available?
            if ( $leftSib->isAvailable() || $authorOrBetter ) {
                $this->leftSib = $leftSib;
                $this->leftSib->changeShortMenuTreeTitleIfNotAvailable();
            }
        }
        //Compute the right sibling, if there is one.
        // todo: could be first lesson in next subtree.
        $rightSibs = $this->repository->getNextSiblings($contentItem);
        if ( count($rightSibs) > 0 ) {
            /** @var Content $rightSib */
            $rightSib = $rightSibs[0];
            //Is it available?
            if ( $rightSib->isAvailable() || $authorOrBetter ) {
                $this->rightSib = $rightSib;
                $this->rightSib->changeShortMenuTreeTitleIfNotAvailable();
            }
        }
        //Parent.
        $path = $this->repository->getPath($contentItem);
        if ( count($path) > 1 ) {
            /** @var Content $parent */
            $parent = $path[ count($path) - 2 ];
            //Can't go up to the root - it is not shown.
            $rootLesson = $this->repository->findRootLesson();
            if ( $rootLesson !== $parent ) {
                //Is it available, or is user special?
                if ($parent->isAvailable() || $authorOrBetter) {
                    $this->parent = $parent;
                    $this->parent->changeShortMenuTreeTitleIfNotAvailable();
                }
            }
        }
        //Children.
        /** @var Content $child */
        foreach ($this->repository->children($contentItem, true) as $child) {
            if ( $child->isAvailable() || $authorOrBetter ) {
                $child->changeShortMenuTreeTitleIfNotAvailable();
                $this->children[] = $child;
            }
        }
    }


    /**
     * @return Content|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Content|null
     */
    public function getLeftSib()
    {
        return $this->leftSib;
    }

    /**
     * @return Content|null
     */
    public function getRightSib()
    {
        return $this->rightSib;
    }

    /**
     * @return Content[]|null
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get all the links needed to make a lesson nav bar.
     *
     * @return array All the links.
     */
    public function getNavbarLinks() {
        $result = [
            'parent' => $this->getParent(),
            'left' => $this->getLeftSib(),
            'right' => $this->getRightSib(),
            'children' => $this->getChildren(),
        ];
        return $result;
    }

}