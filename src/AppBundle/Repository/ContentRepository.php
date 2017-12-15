<?php
/**
 * Queries for the Content entity.
 *
 * User: kieran
 * Date: 11/11/2017
 * Time: 11:03 AM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Content;
use AppBundle\Helper\ContentHelper;
use AppBundle\Helper\ContentTypes;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use PHPUnit\Runner\Exception;

class ContentRepository extends NestedTreeRepository
{

    /**
     * Query to find all content of a type. If the query is for a user who is author or better,
     * content available flag is ignored.
     *
     * @param string $contentType Content type to list, or all.
     * @param boolean $authorOrBetter True if query is for a user who is an author or better.
     * @return Content[] Results.
     */
    public function findAllContentByTitle($contentType, $authorOrBetter) {
        $qb = $this->createQueryBuilder('content');
        //Constrain content type, if query not for all content.
        if ( $contentType !== ContentHelper::ALL ) {
            $qb
                ->andWhere('content.contentType = :contentType')
                ->setParameter('contentType', $contentType);
        }
        //If query not for author or better, add available flag constraint.
        if ( ! $authorOrBetter ) {
            $qb->andWhere('content.isAvailable = :isAvailable')
                ->setParameter('isAvailable', true);
        }
        $query = $qb
            ->addOrderBy('content.title', 'ASC')
            ->getQuery();
        return $query->execute();
    }

    /**
     * Query to find lessons, sorted by lvl (tree level). Used to make lesson tree.
     *
     * @param boolean $userIsAuthorOrBetter If so, show unavailable lessons.
     * @return Content[] The lessons.
     */
    public function findLessonsForTree($userIsAuthorOrBetter) {
        $qb = $this->createQueryBuilder('content')
            ->andWhere('content.contentType = :contentType')
            ->setParameter('contentType', 'lesson');
        if ( ! $userIsAuthorOrBetter ) {
            $qb->andWhere('content.isAvailable = :isAvailable')
                ->setParameter('isAvailable', true);
        }
        $query = $qb
            ->orderBy('content.lvl')
            ->getQuery();
        return $query->execute();
    }

    /**
     * Query to find root lesson for tree. There is always one root.
     * @return Content The lesson.
     * @throws \Exception
     */
    public function findRootLesson() {
        $qb = $this->createQueryBuilder('content')
            ->andWhere('content.contentType = :contentType')
            ->setParameter('contentType', ContentHelper::LESSON)
            ->andWhere('content.parent is NULL');
        $query = $qb->getQuery();
        $result = $query->execute();
        if ( count($result) !== 1 ) {
            throw new \Exception('Lesson tree should have one root.');
        }
        return $result[0];
    }

    /**
     * Find uploaded files for content with given id, from a given upload group.
     *
     * @param integer $contentId Content id
     * @param string $groupName Upload file group, like content_attached_file.
     * @return array Results, MT is none.
     * @throws \Exception Something broke.
     */
    public function findUploadsForContentWithId($contentId, $groupName) {
        //Check that the group is known.
        if ( ! in_array($groupName, Content::UPLOAD_GROUPS) ) {
            //Todo Exception organization.
            throw new \Exception('Unknown upload group: ' . $groupName);
        }
        $sql = "SELECT uploaded_file.* from ".$groupName."
                  INNER JOIN uploaded_file ON ".$groupName.".uploaded_file_id = uploaded_file.id
                  WHERE content_id=:contentId
                  ORDER BY uploaded_file.file_name
        ";
        $params = [ 'contentId' => $contentId ];
//        $params = [ 'group' => $groupName, 'contentId' => $contentId ];
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        return $results;
    }

}