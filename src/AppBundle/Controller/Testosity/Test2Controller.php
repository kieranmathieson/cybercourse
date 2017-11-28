<?php

namespace AppBundle\Controller\Testosity;

use AppBundle\Entity\Category;
use AppBundle\Helper\UserActivityLogHelper;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Test2Controller extends Controller
{

    protected $em;
    protected $activityLogger;

    /**
     * Test2Controller constructor.
     */
    public function __construct(EntityManagerInterface $em,
            UserActivityLogHelper $activityLogger) {
        $this->em = $em;
        $this->activityLogger = $activityLogger;
    }


    /**
     * @Route("/test2/t1", name="test2_t1")
     */
    public function t1()
    {
        $food = new Category();
        $food->setTitle('Food');

        $fruits = new Category();
        $fruits->setTitle('Fruits');
        $fruits->setParent($food);

        $vegetables = new Category();
        $vegetables->setTitle('Vegetables');
        $vegetables->setParent($food);

        $carrots = new Category();
        $carrots->setTitle('Carrots');
        $carrots->setParent($vegetables);

        $this->em->persist($food);
        $this->em->persist($fruits);
        $this->em->persist($vegetables);
        $this->em->persist($carrots);
        $this->em->flush();
        return new Response('moo');
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/test2/t2", name="test2_t2")
     */
    public function t2()
    {
        $repo = $this->em->getRepository('AppBundle:Category');
        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* false: load all children, true: only direct */
            array(
                'decorate' => true,
                'representationField' => 'slug',
                'html' => true
            )
        );
        return new Response('<h1>moo Tree</h1>' . $htmlTree);
    }

    /**
     * @Route("/test2/t3", name="test2_t3")
     */
    public function t3()
    {
        $repo = $this->em->getRepository('AppBundle:Category');
        $fruits = $repo->findOneBy(['title' => 'Fruits']);
        $apples = new Category();
        $apples->setTitle('Apples');
        $apples->setParent($fruits);
        $this->em->persist($fruits);
        $this->em->persist($apples);
        $this->em->flush();
        return new Response('moooooo');
    }

    /**
     * @Route("/test2/t4", name="test2_t4")
     */
    public function t4()
    {
        $toPersist = [];
        $repo = $this->em->getRepository('AppBundle:Category');
        $fruits = $repo->findOneBy(['title' => 'Fruits']);
        $toPersist[] = $fruits;

        $grapes = new Category();
        $grapes->setTitle('Grapes');
        $grapes->setParent($fruits);
        $toPersist[] = $grapes;

        $ape = new Category();
        $ape->setTitle('Grape ape');
        $ape->setParent($grapes);
        $toPersist[] = $ape;

        $shot = new Category();
        $shot->setTitle('Grape shot');
        $shot->setParent($grapes);
        $toPersist[] = $shot;

        $jello = new Category();
        $jello->setTitle('Jello');
        $jello->setParent($shot);
        $toPersist[] = $jello;

        $melons = new Category();
        $melons->setTitle('Melons');
        $melons->setParent($fruits);
        $toPersist[] = $melons;

        $rockMelons = new Category();
        $rockMelons->setTitle('Rockmelons');
        $rockMelons->setParent($melons);
        $toPersist[] = $rockMelons;

        $waterMelons = new Category();
        $waterMelons->setTitle('Watermelons');
        $waterMelons->setParent($melons);
        $toPersist[] = $waterMelons;

        foreach ($toPersist as $item) {
            $this->em->persist($item);
        }
        $this->em->flush();
        return new Response('WOOOOOF!');
    }

    /**
     * @Route("/test2/t5", name="test2_t5")
     */
    public function t5()
    {
        $repo = $this->em->getRepository('AppBundle:Category');
        $melons = $repo->findOneBy(['title' => 'Melons']);
        $collies = new Category();
        $collies->setTitle('Collies');
        $collies->setParent($melons);
        $this->em->persist($melons);
        $this->em->persist($collies);
        $this->em->flush();
        return new Response('Collies added.');
    }

    /**
     * @Route("/test2/t6", name="test2_t6")
     */
    public function t6()
    {
        $repo = $this->em->getRepository('AppBundle:Category');
        $fruits = $repo->findOneBy(['title' => 'Fruits']);
        $melons = $repo->findOneBy(['title' => 'Melons']);
        $repo->persistAsFirstChildOf($melons, $fruits);
        $this->em->flush();
        return new Response('Moooooved');

    }

    /**
     * @Route("/test2/t7", name="test2_t7")
     */
    public function t7()
    {
        $repo = $this->em->getRepository('AppBundle:Category');
        $fruits = $repo->findOneBy(['title' => 'Fruits']);
        $melons = $repo->findOneBy(['title' => 'Melons']);
        $repo->persistAsFirstChildOf($melons, $fruits);
        $this->em->flush();
        return new Response('Moooooved');

    }

    /**
     * @Route("/test2/t8", name="test2_t8")
     */
    public function t8(Request $request) {
        $this->activityLogger->logEvent(UserActivityLogHelper::VIEW_EXERCISE, $request);
        return new Response('logged');
    }

    /**
     * @Route("/test2/t9", name="test2_t9")
     */
    public function t9(Request $request) {
        $this->activityLogger->logEvent(
            UserActivityLogHelper::VIEW_EXERCISE,
            $request,
            ['best' => 'dogs']);
        return new Response('log dog');
    }

}
