<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/13/2017
 * Time: 1:44 PM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class UserPhotoRepository extends EntityRepository
{
    public function fileUploadedPhotosForUser(User $user) {
        $sql = "SELECT uploaded_file.* from user_photo
                  INNER JOIN uploaded_file ON user_photo.uploaded_file_id = uploaded_file.id
                  WHERE user_id=:userId
                  ORDER BY uploaded_file.file_name
        ";
        $params = [ 'userId' => $user->getId() ];
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
//        $stmt->bindValue('userId', $user->getId());
//        $stmt->bindValue('lesson_id', $lesson->getId());
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        return $results;
    }

    /**
     * Delete row from the user_photo table that has a given id
     * for an uploaded file.
     *
     * @param int $fileId Id of the uploaded file to erase.
     * @return bool True if success.
     */
    public function deletePhotoRecordWithFileId($fileId) {
        $sql = "DELETE FROM user_photo WHERE uploaded_file_id = :fileId";
        $params = [ 'fileId' => $fileId ];
        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $result = $stmt->execute($params);
        return $result;
    }

}

