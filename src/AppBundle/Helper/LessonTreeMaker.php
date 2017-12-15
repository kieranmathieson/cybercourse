<?php
/**
 * Makes a lesson tree as a set of nodes. Rendering is by JS widget FancyTree.
 *
 * User: kieran
 * Date: 11/23/2017
 * Time: 9:25 AM
 */
namespace AppBundle\Helper;

use AppBundle\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Entity\Content;

class LessonTreeMaker
{

    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    /** @var UserHelper $userHelper */
    protected $userHelper;

    /** @var RouterInterface  */
    protected $router;

    /** @var bool Make links from title. */
    protected $makeLinks = false;

    /** @var bool Expand all nodes on tree display. */
    protected $expandAll = false;

    /** @var bool Expand the active node. Requires an active id. */
    protected $expandActive = false;

    /** @var int Active node's id. */
    protected $activeId = 0;

    /** @var bool Add spans to titles for placing controls for editing. */
    protected $addControlSpans = false;

    /** @var bool Whether the user is an author or better. */
    protected $userIsAuthorOrBetter = false;

    /** @var array Data structure representing the lesson tree. */
    protected $lessonTree;

    /**
     * Constructor. Load some dependencies.
     *
     * @param EntityManagerInterface $entityManager
     * @param UserHelper $userHelper
     * @param RouterInterface $router
     */
    public function __construct(EntityManagerInterface $entityManager, UserHelper $userHelper, RouterInterface $router)
    {
        //Store service references.
        $this->entityManager = $entityManager;
        $this->userHelper = $userHelper;
        $this->router = $router;
    }


    /**
     * Make a lesson tree, using the current settings.
     * @return $this For chaining.
     */
    public function makeTree() {
        $this->userIsAuthorOrBetter = $this->userHelper->isLoggedInUserAuthorOrBetter();
        /** @var ContentRepository $repo */
        $repo = $this->entityManager->getRepository('AppBundle:Content');
        //Find the single root lesson. There must be exactly one.
        $rootNode = $repo->findRootLesson();
        //Use Doctrine tree method to make a tree.
        $tree = $repo->childrenHierarchy($rootNode);
        //Change the data structure into something FancyTree can use.
        $treeDisplay = $this->toDisplayArray($tree);
        $this->lessonTree = $treeDisplay;
        return $this;
    }

    /**
     * @param $nodes
     * @return array
     */
    public function toDisplayArray($nodes) {
        $results = [];
        foreach( $nodes as $node ) {
            //Unavailable nodes are visible only to some users.
            if ( $node['isAvailable'] || $this->userIsAuthorOrBetter ) {
                $result = [];
                //Set item key, so can refer to it in JS.
                $result['key'] = $node['id'];
                //Use the short menu tree title if it is set.
                $title = isset($node['shortMenuTreeTitle']) ? $node['shortMenuTreeTitle'] : $node['title'];
                //Add unavailable marker?
                if (! $node['isAvailable']) {
                    $title = Content::NOT_AVAILABLE_MARKER . $title;
                }
                if ($this->isMakeLinks()) {
                    //Turn the title into a link.
                    $url = $this->router->generate(
                        'content_show',
                        ['contentType' => $node['contentType'], 'slug' => $node['slug']]
                    );
                    $title = '<a href="'.$url.'">'.$title.'</a>';
                }
                $result['title'] = $title;
                if ($this->isExpandAll()) {
                    $result['expanded'] = true;
                }
                if ($this->IsExpandActive() && $node['id'] === $this->getActiveId()) {
                    $result['active'] = true;
                }
                if (count($node['__children']) > 0) {
                    $result['children'] = $this->toDisplayArray($node['__children']);
                }
                $results[] = $result;
            } //End available test.
        } //End nodes loop.
        return $results;
    }

    /**
     * Get flag showing whether to make links from title.
     *
     * @return bool
     */
    public function isMakeLinks(): bool
    {
        return $this->makeLinks;
    }

    /**
     * Set flag showing whether to make links from title.
     *
     * @param bool $makeLinks
     * @return LessonTreeMaker
     */
    public function setMakeLinks(bool $makeLinks): LessonTreeMaker
    {
        $this->makeLinks = $makeLinks;
        return $this;
    }

    /**
     * Flag showing whether to expand all nodes on tree display.
     *
     * @return bool
     */
    public function isExpandAll(): bool
    {
        return $this->expandAll;
    }

    /**
     * Flag showing whether to expand all nodes on tree display.

     * @param bool $expandAll
     * @return LessonTreeMaker
     */
    public function setExpandAll(bool $expandAll): LessonTreeMaker
    {
        $this->expandAll = $expandAll;
        return $this;
    }

    /**
     * Flag showing whether to expand the active node. Requires an active id.
     *
     * @return bool
     */
    public function isExpandActive(): bool
    {
        return $this->expandActive;
    }

    /**
     * Flag showing whether to expand the active node. Requires an active id.

     * @param bool $expandActive
     * @return LessonTreeMaker
     */
    public function setExpandActive(bool $expandActive): LessonTreeMaker
    {
        $this->expandActive = $expandActive;
        return $this;
    }

    /**
     * Active node's id. Used with setExpandActive().
     *
     * @return int
     */
    public function getActiveId(): int
    {
        return $this->activeId;
    }

    /**
     * Active node's id. Used with setExpandActive().

     * @param int $activeId
     * @return LessonTreeMaker
     */
    public function setActiveId(int $activeId): LessonTreeMaker
    {
        $this->activeId = $activeId;
        return $this;
    }

    /**
     * Flag showing whether to add spans to titles for placing controls for editing.
     *
     * @return bool
     */
    public function isAddControlSpans(): bool
    {
        return $this->addControlSpans;
    }

    /**
     * Flag showing whether to add spans to titles for placing controls for editing.
     *
     * @param bool $addControlSpans
     * @return LessonTreeMaker
     */
    public function setAddControlSpans(bool $addControlSpans): LessonTreeMaker
    {
        $this->addControlSpans = $addControlSpans;
        return $this;
    }


    /**
     * Get the lesson tree ready for JS, hopefully after it is populated.
     *
     * @return array Lesson tree for sending to FancyTree.
     */
    public function getLessonTree() {
        return $this->lessonTree;
    }

}