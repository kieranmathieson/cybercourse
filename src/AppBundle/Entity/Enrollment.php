<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 1:36 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="enrollment")
 */
class Enrollment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Many enrollments have ons user.
     * @ManyToOne(targetEntity="User", inversedBy="enrollments")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * Many enrollments for one class.
     * @ManyToOne(targetEntity="AppBundle\Entity\Clss", inversedBy="enrollments")
     * @JoinColumn(name="class_id", referencedColumnName="id")
     */
    protected $clss;

    /**
     * Comma-separated list of roles. Can do a LIKE on them.
     *
     * @ORM\Column(type="string")
     */
    protected $roles;
}