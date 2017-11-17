<?php
/**
 * Queries for the Content entity.
 *
 * User: kieran
 * Date: 11/11/2017
 * Time: 11:03 AM
 */

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Constraint\IsTrue;

class ContentRepository extends EntityRepository
{
    public function findAllContentByTitle($contentType, $authorOrBetter) {
        $qb = $this->createQueryBuilder('content')
            ->andWhere('content.contentType = :contentType');
        if ( ! $authorOrBetter ) {
            $qb->andWhere('content.isAvailable = :isAvailable')
                ->setParameter('isAvailable', true);
        }
        $query = $qb
            ->setParameter('contentType', $contentType)
            ->addOrderBy('content.title', 'ASC')
            ->getQuery();

        return $query->execute();
//        $sql = "SELECT * from content
//              WHERE content_type=:contentType ";
//        if ( $limitToIsAvailable ) {
//            $sql .= " AND is_available = 1 ";
//        }
//        $sql .= " ORDER BY title ASC;";
//        $queryParams = [ 'contentType' => $contentType ];
//        $em = $this->getEntityManager();
//        $stmt = $em->getConnection()->prepare($sql);
//        $stmt->execute($queryParams);
//        $results = $stmt->fetchAll();
//        return $results;
    }
}