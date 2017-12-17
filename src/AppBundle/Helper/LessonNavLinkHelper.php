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
    /** @var LessonNavLink The item that is the focus of the investigation. Helping Symfony with its enquiries. */
    protected $contentLessonNavLink = null;

    /** @var LessonNavLink The slug of the lesson's parent in the lesson tree. */
    protected $parentLessonNavLink = null;

    /** @var LessonNavLink The slug of the lesson's left sibling in the lesson tree. */
    protected $leftSibLessonNavLink = null;

    /** @var LessonNavLink The slug of the lesson's right sibling in the lesson tree. */
    protected $rightSibLessonNavLink = null;

    /** @var LessonNavLink[] The slugs of the lesson's children in the lesson tree. */
    protected $childrenLessonNavLinks = [];

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var ContentRepository $repository */
    protected $repository;

    /** @var User $loggedInUser */
    protected $loggedInUser;

    /** @var LessonTreeMaker $lessonTreeMaker */
    protected $lessonTreeMaker;

    /**
     * LessonNavLinkHelper constructor.
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @param LessonTreeMaker $lessonTreeMaker
     * @internal param string $parentSlug
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        LessonTreeMaker $lessonTreeMaker
    )
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository('AppBundle:Content');
        $this->loggedInUser = $tokenStorage->getToken()->getUser();
        $this->lessonTreeMaker = $lessonTreeMaker;
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
        /** @var array $lessonTree
         * This is not an array of Content objects, but an array of array elements, one for each Content object.
         */
        $lessonTree = $this->lessonTreeMaker->getLessonTree();
        $node = null; //The node for the content in the tree.
        $parent = null; //The node's parent.
        $idToFind = $contentItem->getId();
        $userIsAuthorOrBetter = ( $this->loggedInUser && $this->loggedInUser !== 'anon.' && $this->loggedInUser->isAuthorOrBetter() );
        //Is the content unavailable? Shouldn't happen.
        if ( ! $contentItem->isAvailable() && ! $userIsAuthorOrBetter ) {
            throw new \Exception('Tried to findFriends of unavailable item.');
        }
        //Root lesson is special.
        /** @var Content $root */
        $root = $contentItem->getRoot();
        if ( $contentItem->getId() === $root->getId() ) {
            $parent = null;
            $leftSib = null;
            $rightSib = null;
            //Compute the right sib - only one that could exist.
            //Pick up the first one that is available.
            foreach( $lessonTree as $lesson ) {
                if ( $lesson['isAvailable'] || $userIsAuthorOrBetter ) {
                    $rightSib = $lesson;
                    break;
                }
            }
            //Package the nav link data for sending around.
            $this->contentLessonNavLink = new LessonNavLink(
                $contentItem->getId(),
                $contentItem->getSlug(),
                $contentItem->getTitle(),
                $contentItem->getShortMenuTreeTitle()
            );
            foreach( $lessonTree as $lesson ) {
                if ( $lesson['isAvailable'] || $userIsAuthorOrBetter ) {
                    $this->childrenLessonNavLinks[] = new LessonNavLink(
                        $lesson['id'], $lesson['slug'], $lesson['title'], $lesson['shortMenuTreeTitle']
                    );
                }
            }
        }
        else {
            //Not the root lesson.
            //Loop over top nodes, stop when find the target.
            //Top of lessonTree is an array of nodes.
            foreach ($lessonTree as $topNode) {
                //Only check available nodes.
                if ($topNode['isAvailable'] || $userIsAuthorOrBetter) {
                    if ($topNode['id'] === $idToFind) {
                        $node = $topNode;
                        //Leave $parent as null.
                        break;
                    }
                    $result = $this->findContentInTree($topNode, $idToFind, $userIsAuthorOrBetter);
                    if (!is_null($result)) {
                        list($node, $parent) = $result;
                        break;
                    }
                }
            }
            if (is_null($node)) {
                //Didn't find the node.
                throw new \Exception('findFriends could not find node: '.$idToFind);
            }
            //Compute friends.
            //Find the sibs.
            list($leftSib, $rightSib) = $this->findSibs(
                $node,
                is_null($parent) ? $lessonTree : $parent['__children']
            );
            //Package the nav link data for sending around.
            $this->contentLessonNavLink = new LessonNavLink(
                $node['id'], $node['slug'], $node['title'], $node['shortMenuTreeTitle']
            );
            if ( ! is_null($parent) ) {
                $this->parentLessonNavLink = new LessonNavLink(
                    $parent['id'], $parent['slug'], $parent['title'], $parent['shortMenuTreeTitle']
                );
            }
            if ( ! is_null($leftSib) ) {
                $this->leftSibLessonNavLink = new LessonNavLink(
                    $leftSib['id'], $leftSib['slug'], $leftSib['title'], $leftSib['shortMenuTreeTitle']
                );
            }
            if ( ! is_null($rightSib) ) {
                $this->rightSibLessonNavLink = new LessonNavLink(
                    $rightSib['id'], $rightSib['slug'], $rightSib['title'], $rightSib['shortMenuTreeTitle']
                );
            }
            foreach( $node['__children'] as $child ) {
                $this->childrenLessonNavLinks[] = new LessonNavLink(
                    $child['id'], $child['slug'], $child['title'], $child['shortMenuTreeTitle']
                );
            }
        }
    }

    public function findContentInTree(array $node, int $contentId, bool $userIsAuthorOrBetter) {
        //If node not available, skip it.
        if ( $node['isAvailable'] || $userIsAuthorOrBetter ) {
            if (count($node['__children']) > 0) {
                foreach ($node['__children'] as $child) {
                    //If child not available, skip it.
                    if ($child['isAvailable'] || $userIsAuthorOrBetter) {
                        if ($child['id'] === $contentId) {
                            return [$child, $node];
                        }
                        $result = $this->findContentInTree($child, $contentId, $userIsAuthorOrBetter);
                        if ( ! is_null($result) ) {
                            return $result;
                        }
                    }
                }
            }
        }
    }

    /**
     * Node is somewhere in sibsList.
     * @param array $node The node being sought.
     * @param array $sibsList Array of nodes that includes the node we are looking for.
     * @return array Left and right sibs.
     * @throws \Exception
     */
    protected function findSibs($node, $sibsList){
        //Find the node in the top level.
        $leftSib = null;
        $rightSib = null;
        for ( $i = 0; $i < count($sibsList); $i++ ) {
            if ( $sibsList[$i]['id'] === $node['id'] ) {
                //Found the node.
                //If this is the first node, there is no left sib, so leave it as null.
                if ( $i > 0 ) {
                    //Not the first node, it is the left sib.
                    $leftSib = $sibsList[$i - 1];
                }
                //If there are children, use the first child as the right sib. That is the natural reading order.
                if ( count($node['__children']) > 0 ) {
                    $rightSib = $node['__children'][0];
                }
                else {
                    //Kidless.
                    //Leave right sib null if the node is the last one.
                    if (($i + 1) < count($sibsList)) {
                        $rightSib = $sibsList[$i + 1];
                    }
                }
                break;
            }
        }
        return [$leftSib, $rightSib];
    }


    /**
     * Get all of the slugs needed to make a lesson nav bar.
     *
     * @return array All if the links.
     */
    public function getLessonNavbarSlugs() {
        $result = [
            'contentLessonNavLink' => $this->contentLessonNavLink,
            'parentLessonNavLink' => $this->parentLessonNavLink,
            'leftLessonNavLink' => $this->leftSibLessonNavLink,
            'rightLessonNavLink' => $this->rightSibLessonNavLink,
            'childrenLessonNavLinks' => $this->childrenLessonNavLinks,
        ];
        return $result;
    }

}