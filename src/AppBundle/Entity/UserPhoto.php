<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 8:47 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_photo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserPhotoRepository")
 */
class UserPhoto
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Many user photos belong to one user.
     * @ManyToOne(targetEntity="User", inversedBy="photos")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer")
     */
    protected $uploadedFileId;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUploadedFileId()
    {
        return $this->uploadedFileId;
    }

    /**
     * @param mixed $uploadedFileId
     */
    public function setUploadedFileId($uploadedFileId)
    {
        $this->uploadedFileId = $uploadedFileId;
    }

}