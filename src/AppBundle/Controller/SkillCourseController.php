<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class SkillCourseController extends Controller
{
    /** @var EntityManagerInterface $entityManager */
    protected $entityManager;

    protected $userManager;

    protected $container;

    protected $lessonTree;

    public function __construct(ContainerInterface $container,
        EntityManagerInterface $entityManager, UserManagerInterface $userManager)
    {
//        parent::__construct();
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;

        //Make the lesson tree.
        //Load the logged in user.

        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        //Most users only see content that is marked available.
        $userIsAuthorOrBetter = false;
        if (!is_null($loggedInUser)) {
            $userIsAuthorOrBetter = $loggedInUser->isAuthorOrBetter();
        }
        $repo = $this->entityManager->getRepository('AppBundle:Content');
        $nodes = $repo->findLessonsForTree($userIsAuthorOrBetter);
        $tree = $repo->childrenHierarchy($nodes[0]);
        //$tree = $repo->buildTree($nodes);
        //Convert array to something we can pass as JSON to FancyTree.
        //If there is one root, the tree starts with the root's children at the top level.
        //If there is more than one root, each root has its own tree, with the root shown.
        $treeDisplay = []; //If there are no roots, the array will be MT.
        $treeDisplayOptions = [
//            'activeId' => 13,
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
        $this->lessonTree = $treeDisplay;
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
            //Set item key, so can refer to it in JS.
            $result['key'] = $node['id'];
            //Use the short menu tree title if it is set.
            $title = isset($node['shortMenuTreeTitle']) ? $node['shortMenuTreeTitle'] : $node['title'];
            if ( $makeLinks ) {
                //Turn the title into a link.
                $url = $this->generateUrl('content_show',
                    ['contentType'=>$node['contentType'], 'slug' => $node['slug']] );
                $title = '<a href="'.$url.'">'.$title.'</a>';
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
     * Renders a view, making the lesson tree available.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = [], Response $response = null){
        $parameters['lessonTree'] = json_encode( $this->lessonTree );
        $result = parent::render($view, $parameters, $response);
        return $result;
    }

}
