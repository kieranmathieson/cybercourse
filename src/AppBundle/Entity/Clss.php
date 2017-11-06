<?php
/**
 * A group of users taking a course at the same time.
 *
 * User: kieran
 * Date: 11/5/2017
 * Time: 1:33 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="clss")
 */
class Clss
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $whenStarts;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $whenEnds;

    /**
     * @ORM\Column(type="string")
     */
    protected $notes;

    /**
     * One class has many enrollments.
     * @OneToMany(targetEntity="AppBundle\Entity\Enrollment", mappedBy="clss")
     */
    protected $enrollments;

    /**
     * Clss constructor.
     */
    public function __construct()
    {
        $this->enrollments = new ArrayCollection();
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getWhenStarts()
    {
        return $this->whenStarts;
    }

    /**
     * @param \DateTime $whenStarts
     */
    public function setWhenStarts(\DateTime $whenStarts)
    {
        $this->whenStarts = $whenStarts;
    }

    /**
     * @return \DateTime
     */
    public function getWhenEnds()
    {
        return $this->whenEnds;
    }

    /**
     * @param \DateTime $whenEnds
     */
    public function setWhenEnds(\DateTime $whenEnds)
    {
        $this->whenEnds = $whenEnds;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }


}