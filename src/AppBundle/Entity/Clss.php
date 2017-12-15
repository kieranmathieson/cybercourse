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
     * @OneToMany(targetEntity="AppBundle\Entity\Enrollment", mappedBy="clss", fetch="EXTRA_LAZY")
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return ArrayCollection
     */
    public function getEnrollments()
    {
        return $this->enrollments;
    }

    public function addEnrollment(Enrollment $enrollment) {
        if ($this->enrollments->contains($enrollment)) {
            //Already a member.
            return;
        }
        $this->enrollments[] = $enrollment;
        // not needed for persistence, just keeping both sides in sync
        $enrollment->setClss($this);
    }

    public function removeEnrollment(Enrollment $enrollment)
    {
        if (!$this->enrollments->contains($enrollment)) {
            return;
        }
        $this->enrollments->removeElement($enrollment);
        // not needed for persistence, just keeping both sides in sync
        //$user->removeEnrollment($this);
    }

}