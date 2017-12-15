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
     * Many enrollments have one user.
     * @ManyToOne(targetEntity="User", inversedBy="enrollments")
     * @JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Many enrollments for one class.
     * @ManyToOne(targetEntity="AppBundle\Entity\Clss", inversedBy="enrollments")
     * @JoinColumn(name="class_id", referencedColumnName="id", nullable=false)
     */
    protected $clss;

    /**
     * Comma-separated list of roles. Can do a LIKE on them.
     *
     * @ORM\Column(type="string")
     */
    protected $roles;

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
     * @return mixed
     */
    public function getClss()
    {
        return $this->clss;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @param Clss $clss
     */
    public function setClss($clss)
    {
        $this->clss = $clss;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }



}