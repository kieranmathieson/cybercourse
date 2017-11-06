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
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_photo")
 */
class UserPhoto
{
    /**
     * @Id
     * @ORM\Column(type="integer")
     */
    protected $userId;

    /**
     * @Id
     * @ORM\Column(type="integer")
     */
    protected $fileId;

}