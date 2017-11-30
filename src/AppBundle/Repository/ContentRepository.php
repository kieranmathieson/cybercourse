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
use AppBundle\Helper\ContentTypes;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

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
        if ( $contentType !== ContentTypes::ALL ) {
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
            ->setParameter('contentType', ContentTypes::LESSON)
            ->andWhere('content.parent is NULL');
        $query = $qb->getQuery();
        $result = $query->execute();
        if ( count($result) !== 1 ) {
            throw new \Exception('Lesson tree should have one root.');
        }
        return $result[0];
    }

}