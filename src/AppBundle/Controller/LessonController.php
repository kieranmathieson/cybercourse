<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Helper;
use AppBundle\Entity\Lesson;
use AppBundle\Form\LessonFormType;
use AppBundle\Helper\ContentTypes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;

class LessonController extends Controller
{
    /**
     * List all of the lessons by title.
     *
     * @Route("/lesson", name="lesson_list")
     */
    public function listLessonAction()
    {
        $lessons = $this->getDoctrine()
            ->getRepository('AppBundle:Content')
            ->findBy(
                [ 'contentType' => ContentTypes::LESSON, 'isAvailable' => true],
                [ 'title' => 'ASC' ]
            );

        return $this->render('lesson/lesson_list.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    /**
     * @Route("/lesson/{slug}", name="lesson_show")
     */
    public function showLessonAction($slug)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $lesson = $em->getRepository('AppBundle:Content')
            ->findOneBy([ 'contentType' => ContentTypes::LESSON, 'isAvailable' => true, 'slug' => $slug ]);
        if (!$lesson) {
            throw $this->createNotFoundException('Lesson not found');
        }
//
//        //Get the tags for the lesson.
//        $sql = "
//            SELECT keyword.id, keyword.text FROM keyword
//              INNER JOIN keyword_use ON keyword_use.keyword_id=keyword.id
//              WHERE entity_type=:lesson and keyword_use.entity_id = :lesson_id
//              ORDER BY keyword.text
//        ";
//        $params = [ 'lesson' => Helper::LESSON, 'lesson_id' => $lesson->getId() ];
//        $stmt = $em->getConnection()->prepare($sql);
//        $stmt->bindValue('lesson', Helper::LESSON);
//        $stmt->bindValue('lesson_id', $lesson->getId());
//        $stmt->execute();
//        $keywords = $stmt->fetchAll();


//        $markdownTransformer = $this->get('app.markdown_transformer');
//        $funFact = $markdownTransformer->parse($genus->getFunFact());
//
//        $this->get('logger')
//            ->info('Showing genus: '.$genusName);
//
//        $recentNotes = $em->getRepository('AppBundle:GenusNote')
//            ->findAllRecentNotesForGenus($genus);
//
        return $this->render('lesson/lesson_show.html.twig', array(
            'lesson' => $lesson,
//            'keywords' => $keywords,
        ));
    }

}
