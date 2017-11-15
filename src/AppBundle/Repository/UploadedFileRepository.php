<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/11/2017
 * Time: 11:03 AM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\UploadedFile;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UploadedFileRepository extends EntityRepository
{
    /**
     * @param array $fileIds
     * @return UploadedFile[]
     */
    public function findUploadedFilesWithIds(array $fileIds)
    {
        return $this->createQueryBuilder('uploaded_file')
            ->andWhere('uploaded_file.id IN (:fileIds)')
            ->setParameter('fileIds', $fileIds)
            ->addOrderBy('uploaded_file.fileName', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function findUploadedFileWithUuid(string $uuidToFind) {
        return $this->createQueryBuilder('uploaded_file')
            ->andWhere('uploaded_file.uuid = :uuidToFind')
            ->setParameter('uuidToFind', $uuidToFind)
            ->getQuery()
            ->execute();
    }
}