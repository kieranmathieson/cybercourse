<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/11/2017
 * Time: 11:03 AM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Log;
use Doctrine\ORM\EntityRepository;

class LogRepository extends EntityRepository
{
        public function findActivitiesForUser($userId) {
        return $this->createQueryBuilder('log')
            ->andWhere('uploaded_file.uuid = :uuidToFind')
            ->setParameter('uuidToFind', $userId)
            ->getQuery()
            ->execute();
    }
}