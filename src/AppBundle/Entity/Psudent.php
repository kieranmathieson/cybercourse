<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 10:05 AM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="psudent")
 */
class Psudent
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;

    /**
     * One psudent has many poses.
     * @OneToMany(targetEntity="AppBundle\Entity\PsudentPose", mappedBy="psudent")
     */
    protected $poses;

    /**
     * Psudent constructor.
     */
    public function __construct()
    {
        $this->poses = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }
}