<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Exercise;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\KeywordUse;
use AppBundle\Entity\Lesson;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper\UploadHandler;

use AppBundle\Entity\Helper;

class TestController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("/test/config1", name="test_config1")
     */
    public function configTest1Action()
    {
        return new Response('Poo ' . count(Helper::ENTITY_TYPES));
    }

    /**
     * @Route("/test/key1", name="test_key1")
     */
    public function configTestKey1Action()
    {
        $em = $this->getDoctrine()->getManager(); //->getRepository('AppBundle:Keyword');

        $keyword = new Keyword();
        $keyword->setText('Dogs');
        $keyword->setNotes('This is about dogs.');

        $em->persist($keyword);
        $em->flush();

        return new Response('Done. Id: ' . $keyword->getId());

    }

    /**
     * @Route("/test/key2", name="test_key2")
     */
    public function configTestKey2Action()
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $keywordRepo
         */
        $keywordRepo = $this->getDoctrine()->getRepository(Keyword::class);
        $keyword = $keywordRepo->findOneBy(['text' => 'Dogs']);
        /**
         * @var \Doctrine\ORM\QueryBuilder $qb
         */
        $qb = $keywordRepo->createQueryBuilder('k');
        $query = $qb
            ->where('k.text like :target')
            ->setParameter('target', 'dog%')
            ->getQuery();
        $keyword = $query->getSingleResult();

        return new Response('Text: ' . $keyword->getNotes());
    }

    /**
     * @Route("/test/key3", name="test_key3")
     */
    public function configTestKey3Action()
    {
        $em = $this->getDoctrine()->getManager();

        $lesson = new Lesson();
        $lesson->setTitle('Lesson 1');
        $lesson->setSlug('lesson1');
        $lesson->setSummary('This is lesson 1.');
        $lesson->setBody('This is the body of lesson 1.');
        $lesson->setIsAvailable(true);
        $lesson->setWhenCreated(new \DateTime());
        $lesson->setWhenUpdated(new \DateTime());
        $lesson->setParent(0);
        $em->persist($lesson);

        $exercise = new Exercise();
        $exercise->setTitle('Exercise 1');
        $exercise->setSlug('exercise1');
        $exercise->setSummary('This is exercise 1.');
        $exercise->setBody('This is the body of exercise 1.');
        $exercise->setIsAvailable(true);
        $exercise->setWhenCreated(new \DateTime());
        $exercise->setWhenUpdated(new \DateTime());
        $exercise->setSolution('This is the solution to exercise 1.');
        $em->persist($exercise);

        //Flush so that the objects are created, and have ids.
        $em->flush();

        $keywordRepo = $this->getDoctrine()->getRepository(Keyword::class);
        $dogs = $keywordRepo->findOneBy(['text' => 'Dogs']);

        $use1 = new KeywordUse();
        $use1->setEntityId($lesson->getId());
        $use1->setEntityType(Helper::LESSON);
        $use1->setKeywordId($dogs->getId());
        $em->persist($use1);

        $use2 = new KeywordUse();
        $use2->setEntityId($exercise->getId());
        $use2->setEntityType(Helper::EXERCISE);
        $use2->setKeywordId($dogs->getId());
        $em->persist($use2);

        $em->flush();

        return new Response('Saved them');
    }

    /**
     * @Route("/test/key4", name="test_key4")
     */
    public function configTestKey4Action()
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $keywordRepo
         */
        $keywordRepo = $this->getDoctrine()->getRepository(Keyword::class);
        $keyword = $keywordRepo->findOneBy(['text' => 'Dogs']);
        /**
         * @var \Doctrine\ORM\QueryBuilder $qb
         */
        $qb = $keywordRepo->createQueryBuilder('k');
        $query = $qb
            ->where('k.text like :target')
            ->setParameter('target', 'dog%')
            ->getQuery();
        $keyword = $query->getSingleResult();

        return new Response('Text: ' . $keyword->getNotes());
    }

    /**
     * @Route("/test/lesson1", name="test_lesson1")
     */
    public function configTestLesson1Action()
    {
        /**
         * @var \Doctrine\ORM\EntityRepository $lessonRepo
         */
        $lessonRepo = $this->getDoctrine()->getRepository(Lesson::class);
        /** @var Lesson $lesson */
        $lesson = $lessonRepo->findOneBy(['id' => 1]);

        $lesson->setIsAvailable(true);
        $lesson->setParent(666);
        $lesson->setSlug('slugggg');

        $em = $this->getDoctrine()->getManager();
        $em->persist($lesson);
        $em->flush();

        return new Response('ok');
    }

    /**
     * @Route("/test/rest-client1", name="test_rest_client1")
     */
    public function testRestClient1Action()
    {
        return $this->render('test/rest_client1.html.twig', [
//            'lessons' => $lessons,
        ]);

    }

    /**
     * @Route("/test/rest-server1", name="test_rest_server1")
     * @param Request $request
     * @return JsonResponse
     */
    public function testRestServer1Action(Request $request)
    {
        if ( $request->getMethod() == 'GET') {
            $dogs = [
                'Renata', 'Rosie',
            ];
            return new JsonResponse($dogs);
        }
    }

    /**
     * @Route("/test/rest-server2", name="test_rest_server2")
     * @param Request $request
     * @return JsonResponse
     */
    public function testRestServer2Action(Request $request)
    {
        try {
            if ($request->getMethod() == 'GET') {
                /** @var User|string $user */
                $user = $this->container->get('security.token_storage')->getToken()->getUser();
                if ($user instanceof \AppBundle\Entity\User) {
                    if (is_callable([$user, 'hasRole'])) {
                        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
                            $dogs = [
                                'Renata',
                                'Rosie',
                            ];

                            return new JsonResponse(
                                ['status' => 'ok', 'dogs' => $dogs]
                            );
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return new JsonResponse(
                ['status' => 'fail', 'message' => $e->getMessage() ]
            );
        }
        return new JsonResponse(
            ['status' => 'fail', 'message' => 'Access denied' ]
        );
    }


    /**
     * @Route("/test/upload-client1", name="test_upload_client1")
     */
    public function testUploadClient1Action()
    {
        return $this->render('test/upload_client1.hml.twig', [
//            'lessons' => $lessons,
        ]);

    }

    /**
     * @Route("/test/upload-server1", name="test_upload_server1")
     */
    public function testUploadServer1Action()
    {


        return new JsonResponse($result);
    }

    /**
     * @Route("/upload/submission/{rest}", name="uploaded_submissions", requirements={"rest"=".+"})
     * @Route("/upload/exercise/solution/{rest}", name="uploaded_solutions", requirements={"rest"=".+"})
     */
    public function testInterceptPrivateFile($rest)
    {
        $result = '<p>Asked for ' . $rest . '</p>';

        return new Response($result);
    }
}
