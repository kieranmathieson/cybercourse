<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Keyword;

use AppBundle\Entity\User;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
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

        return new Response('Done. Id: '.$keyword->getId());

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

        return new Response('Text: '.$keyword->getNotes());
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

        return new Response('Text: '.$keyword->getNotes());
    }


    /**
     * @Route("/test/rest-client1", name="test_rest_client1")
     */
    public function testRestClient1Action()
    {
        return $this->render(
            'test/rest_client1.html.twig',
            [
//            'lessons' => $lessons,
            ]
        );

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
                ['status' => 'fail', 'message' => $e->getMessage()]
            );
        }

        return new JsonResponse(
            ['status' => 'fail', 'message' => 'Access denied']
        );
    }


    /**
     * @Route("/test/upload-client1", name="test_upload_client1")
     */
    public function testUploadClient1Action()
    {
        return $this->render(
            'test/upload_client1.hml.twig',
            [
//            'lessons' => $lessons,
            ]
        );

    }



    /**
     * @Route("/upload/submission/{rest}", name="uploaded_submissions", requirements={"rest"=".+"})
     * @Route("/upload/exercise/solution/{rest}", name="uploaded_solutions", requirements={"rest"=".+"})
     */
    public function testInterceptPrivateFile($rest)
    {
        $result = '<p>Asked for '.$rest.'</p>';

        return new Response($result);
    }

    /**
     * @Route("/test/path1", name="test_path1")
     */
    public function testPath1()
    {
        $filePathToUploads = $this->get_absolute_path(
            $this->get('kernel')->getRootDir().'/../web'
            .$this->container->getParameter('app.base_uploads_uri')
        );
//        $filePathToUploads = 'FOFO';
        $result = '<p>Asked for DOGAS '.$filePathToUploads.'</p>';

        $result .= '<p>'.$this->container->getParameter('app.base_uploads_uri').'</p>';

        return new Response($result);
    }

    protected function get_absolute_path($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }



    /**
     * @Route("/test/info", name="test_info")
     */
    public function testInfo()
    {

        $result = phpinfo();

        return new Response($result);
    }

    /**
     * @Route("/test/log1", name="test_log1")
     */
    public function testLog1()
    {
        $this->container->get('monolog.logger.user_activity')->info('something happened', [
            'foo' => 'bar'
        ]);

        return new Response('Log dog');
    }

    /**
     * @Route("/test/log2", name="test_log2")
     */
    public function testLog2()
    {
        $em = $this->getDoctrine()->getManager();
        $activityLogRepo = $em->getRepository('AppBundle:UserActivityLog');
        $activityLogItem = $activityLogRepo->createQueryBuilder('user_activity_log')
            ->andWhere('user_activity_log.id = :id')
            ->setParameter('id', 1)
            ->getQuery()
            ->getOneOrNullResult();

        return new Response('Log dog 2');
    }

    /**
     * @Route("/test/log3", name="test_log3")
     */
    public function testLog3()
    {
        $this->container->get('monolog.logger.security')->info(
            'something bad happened', [
            'evil_doer' => 'baz'
        ]);

        return new Response('Log dog 3');
    }

}
