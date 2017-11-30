<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use AppBundle\Helper\Roles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    // Todo: check that user names are unique.
    public function __construct()
    {
        parent::__construct();
        $this->files = new ArrayCollection();
        $this->exerciseSubmissions = new ArrayCollection();
        $this->enrollments = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->activityLogEntries = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $aboutMe;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     *
     * Default is to share deets.
     */
    protected $shareDeetsWithClass = true;

    /**
     * One user has many submissions.
     * @OneToMany(targetEntity="AppBundle\Entity\ExerciseSubmission", mappedBy="user")
     */
    protected $exerciseSubmissions;

    /**
     * One user has many enrollments.
     * @OneToMany(targetEntity="AppBundle\Entity\Enrollment", mappedBy="user")
     */
    protected $enrollments;

    /**
     * One user has many uploaded files.
     *
     * This is not the photos for the user. This is just the backside of an M:1 showing the user
     * show uploaded each file.
     *
     * @OneToMany(targetEntity="AppBundle\Entity\UploadedFile", mappedBy="uploadingUser")
     */
    protected $files;

    /**
     * Photos for the user. Another relationship to UploadedFile, handled indirectly through
     * UserPhoto. One user can have many UserPhoto objects.
     *
     * @OneToMany(targetEntity="AppBundle\Entity\UserPhoto", mappedBy="user")
     */
    protected $photos;

    /**
     * One user has many activity log entries.
     * @OneToMany(targetEntity="AppBundle\Entity\UserActivityLog", mappedBy="user")
     */
    protected $activityLogEntries;


    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getAboutMe()
    {
        return $this->aboutMe;
    }

    /**
     * @param string $aboutMe
     */
    public function setAboutMe($aboutMe)
    {
        $this->aboutMe = $aboutMe;
    }


    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return boolean
     */
    public function getShareDeetsWithClass()
    {
        return $this->shareDeetsWithClass;
    }

    /**
     * @param boolean $shareDeetsWithClass
     */
    public function setShareDeetsWithClass($shareDeetsWithClass)
    {
        $this->shareDeetsWithClass = $shareDeetsWithClass;
    }

    /**
     * @return ArrayCollection|UploadedFile[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return ArrayCollection|UserPhoto[]
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * Is the user an author or better?
     *
     * @return bool True if the user has the author role or better.
     */
    public function isAuthorOrBetter()
    {
        $authorOrBetter = false;
        $userRoles = $this->getRoles();
        $authorOrBetter = (
            in_array(Roles::ROLE_AUTHOR, $userRoles)
            || in_array(Roles::ROLE_ADMIN, $userRoles)
            || in_array(Roles::ROLE_SUPER_ADMIN, $userRoles)
        );
        return $authorOrBetter;
    }

    public function getActivityLogEntries() {
        return $this->activityLogEntries;
    }


}