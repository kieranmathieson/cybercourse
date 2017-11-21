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
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class ContentRepository extends NestedTreeRepository
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
    }

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


}