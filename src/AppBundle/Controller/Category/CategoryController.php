<?php

namespace AppBundle\Controller\Category;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    protected $em;

    /**
     * Test2Controller constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/cat", name="cat_list")
     */
    public function indexAction()
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        $repo = $this->em->getRepository('AppBundle:Category');
        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* false: load all children, true: only direct */
            array(
                'decorate' => true,
                'representationField' => 'slug',
                'html' => true,
                'nodeDecorator' => function($node) {
                    return '<a href="/cat/'.$node['id'].'">'.$node['title'].'</a>';
                }
            )
        );

        return $this->render(
            'category/list_categories.html.twig',
            [
                'authorOrBetter' => $authorOrBetter,
                'tree' => $htmlTree,
            ]
        );
    }

    /**
     * @Route("/catjs", name="cat_list_js")
     */
    public function indexActionJs()
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        $repo = $this->em->getRepository('AppBundle:Category');
        $tree = $repo->childrenHierarchy();
        //Convert array to something we can pass as JSON to FancyTree.
        //If there is one root, the tree starts with the root's children at the top level.
        //If there is more than one root, each root has its own tree, with the root shown.
        $treeDisplay = []; //If there are no roots, the array will be MT.
        $treeDisplayOptions = [
            'activeId' => 13,
            'makeLinks' => true,
            'expandAll' => false,
            'expandActive' => true,
        ];
        if ( count($tree) == 1 ) {
            //There is just one root.
            $treeDisplay = $this->toDisplayArray($tree[0]['__children'], $treeDisplayOptions);
        }
        elseif ( count($tree) > 1 ) {
            //There is more than one root.
            $treeDisplay = $this->toDisplayArray($tree, $treeDisplayOptions);
        }
        return $this->render(
            'category/list_categories_js.html.twig',
            [
                'authorOrBetter' => $authorOrBetter,
                'tree' => json_encode($treeDisplay),
            ]
        );
    }

    /**
     * @param $nodes
     * @param array $treeDisplayOptions Display options.
     * @return array
     */
    public function toDisplayArray($nodes, array $treeDisplayOptions=[]) {
        $results = [];
        //Decode options.
        $activeId = isset($treeDisplayOptions['activeId']) ? $treeDisplayOptions['activeId'] : 0;
        $makeLinks = isset($treeDisplayOptions['makeLinks']) ? $treeDisplayOptions['makeLinks'] : false;
        $expandAll = isset($treeDisplayOptions['expandAll']) ? $treeDisplayOptions['expandAll'] : false;
        $expandActive = isset($treeDisplayOptions['expandActive']) ? $treeDisplayOptions['expandActive'] : false;
        foreach( $nodes as $node ){
            $result = [];
            $url = $this->generateUrl('cat_show', ['id'=>$node['id']] );
            if ( $makeLinks ) {
                $title = '<a href="'.$url.'">'.$node['title'].'</a>';
            }
            else {
                $title = $node['title'];
            }
            $result['title'] = $title;
            if ( $expandAll ) {
                $result['expanded'] = true;
            }
            if ( $expandActive && $node['id'] === $activeId ) {
                $result['active'] = true;
            }
            if ( count($node['__children']) > 0 ) {
                $result['children'] = $this->toDisplayArray($node['__children'], $treeDisplayOptions);
            }
            $results[] = $result;
        }
        return $results;
    }

    /**
     * @Route("/catjs/reorder", name="cat_reorder_js")
     */
    public function reorderActionJs()
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }


        $repo = $this->em->getRepository('AppBundle:Category');
        $stuff = $repo->findAvailabilityStatus('lesson');
        $tree = $repo->childrenHierarchy();
        //Convert array to something we can pass as JSON to FancyTree.
        //If there is one root, the tree starts with the root's children at the top level.
        //If there is more than one root, each root has its own tree, with the root shown.
        $treeDisplayOptions = [
            'makeLinks' => false,
            'expandAll' => true,
        ];
        $treeDisplay = []; //If there are no roots, the array will be MT.
        if ( count($tree) == 1 ) {
            //There is just one root.
            $treeDisplay = $this->toDisplayArray($tree[0]['__children'], $treeDisplayOptions);
        }
        elseif ( count($tree) > 1 ) {
            //There is more than one root.
            $treeDisplay = $this->toDisplayArray($tree, $treeDisplayOptions);
        }
        return $this->render(
            'category/reorder_categories_js.html.twig',
            [
                'authorOrBetter' => $authorOrBetter,
                'tree' => json_encode($treeDisplay),
            ]
        );
    }

    /**
     * @Route("/cat/{id}", name="cat_show")
     */
    public function showAction(Category $cat)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $authorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $authorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        $repo = $this->em->getRepository('AppBundle:Category');

        $prevSib = null;
        $prevSibs = $repo->getPrevSiblings($cat);
        if ( count($prevSibs) > 0 ) {
            $prevSib = end($prevSibs);
        }

        $nextSib = null;
        $nextSibs = $repo->getNextSiblings($cat);
        if ( count($nextSibs) > 0 ) {
            $nextSib = $nextSibs[0];
        }

        $parent = null;
        $path = $repo->getPath($cat);
        if ( count($path) > 1 ) {
            $parent = $path[ count($path) - 2 ];
        }

        $children = null;
        $childrenList = $repo->children($cat);
        if ( count($childrenList) > 0 ) {
            $children = $childrenList;
        }

        return $this->render(
            'category/show_category.html.twig',
            [
                'authorOrBetter' => $authorOrBetter,
                'parent' => $parent,
                'prevSib' => $prevSib,
                'nextSib' => $nextSib,
                'children' => $children,
                'category' => $cat,
            ]
        );
    }
}
