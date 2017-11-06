<?php

// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @Vich\Uploadable
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
        $this->files = new ArrayCollection();
        $this->exerciseSubmissions = new ArrayCollection();
        $this->enrollments = new ArrayCollection();
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
     * @return string
     */

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
     * One user has many files.
     * @OneToMany(targetEntity="AppBundle\Entity\UploadedFile", mappedBy="uploadingUser")
     *
     * There is a separate table for user photos.
     */
    protected $files;

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

    //------------------------ Image file ----------------------
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="imageName", size="imageSize")
     *
     * @var File|\Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $imageFile;

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return User
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    //------------------------ Image name field ----------------------
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    protected $imageName;
    /**
     * @param string $imageName
     *
     * @return User
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName()
    {
        return $this->imageName;
    }



    //------------------------ Image size field ----------------------
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    protected $imageSize;
    /**
     * @param integer $imageSize
     *
     * @return User
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getImageSize()
    {
        return $this->imageSize;
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


}