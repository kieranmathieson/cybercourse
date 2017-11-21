<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/19/2017
 * Time: 9:04 AM
 */

namespace AppBundle\Repository;


use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
//    public function findAvailabilityStatus($contentType='all') {
//        $qb = $this->createQueryBuilder('content');
//        if ( $contentType !== 'all' ) {
//            $qb->andWhere('content.contentType = :contentType')
//                ->setParameter('contentType', $contentType);
//        }
//        $query = $qb->getQuery();
//        $result = $query->execute();
//        return $result;
//    }
}


