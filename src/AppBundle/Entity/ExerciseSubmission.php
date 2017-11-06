<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 11:02 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="exercise_submission")
 */
class ExerciseSubmission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Many submissions to one exercise.
     * @ManyToOne(targetEntity="AppBundle\Entity\Content", inversedBy="exerciseSubmissions")
     * @JoinColumn(name="exercise_id", referencedColumnName="id")
     */
    protected $exercise;

    /**
     * Many submissions from one user.
     * @ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="exerciseSubmissions")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $version;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $whenCreated;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $whenUpdated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $whenAssessed;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $feedbackMessage;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $complete;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $whenFeedbackViewedByStudent;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * Responses grader chose, serialized array.
     */
    protected $rubricItemResponses;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param integer $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return \DateTime
     */
    public function getWhenCreated()
    {
        return $this->whenCreated;
    }

    /**
     * @param \DateTime $whenCreated
     */
    public function setWhenCreated($whenCreated)
    {
        $this->whenCreated = $whenCreated;
    }

    /**
     * @return \DateTime
     */
    public function getWhenUpdated()
    {
        return $this->whenUpdated;
    }

    /**
     * @param \DateTime $whenUpdated
     */
    public function setWhenUpdated($whenUpdated)
    {
        $this->whenUpdated = $whenUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getWhenAssessed()
    {
        return $this->whenAssessed;
    }

    /**
     * @param \DateTime $whenAssessed
     */
    public function setWhenAssessed($whenAssessed)
    {
        $this->whenAssessed = $whenAssessed;
    }

    /**
     * @return string
     */
    public function getFeedbackMessage()
    {
        return $this->feedbackMessage;
    }

    /**
     * @param string $feedbackMessage
     */
    public function setFeedbackMessage($feedbackMessage)
    {
        $this->feedbackMessage = $feedbackMessage;
    }

    /**
     * @return boolean
     */
    public function getComplete()
    {
        return $this->complete;
    }

    /**
     * @param boolean $complete
     */
    public function setComplete($complete)
    {
        $this->complete = $complete;
    }

    /**
     * @return \DateTime
     */
    public function getWhenFeedbackViewedByStudent()
    {
        return $this->whenFeedbackViewedByStudent;
    }

    /**
     * @param \DateTime $whenFeedbackViewedByStudent
     */
    public function setFeedbackViewedByStudent($whenFeedbackViewedByStudent)
    {
        $this->whenFeedbackViewedByStudent = $whenFeedbackViewedByStudent;
    }


}