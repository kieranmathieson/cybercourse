<?php
/**
 * A file that has been uploaded by a user.
 *
 * User: kieran
 * Date: 11/4/2017
 * Time: 5:48 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="uploaded_file")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UploadedFileRepository")
 */
class UploadedFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="guid")
     */
    protected $uuid;

    /**
     * Many files have one user uploader.
     * @ManyToOne(targetEntity="User", inversedBy="files")
     */
    protected $uploadingUser;

    /**
     * URI path to the file.
     * e.g., /this/that/puppy.png
     *
     * @ORM\Column(type="string")
     */
    protected $uriPath;

    /**
     * Name and extension of the file.
     *
     * @ORM\Column(type="string")
     */
    protected $fileName;

    /**
     * Many files can be attached to many content objects.
     * @ManyToMany(targetEntity="AppBundle\Entity\Content", mappedBy="attachedPublicFiles")
     */
    protected $attachedTo;

    /**
     * Many files can be hidden attached to many content objects.
     * @ManyToMany(targetEntity="AppBundle\Entity\Content", mappedBy="attachedHiddenFiles")
     */
    protected $attachedToHidden;

    /**
     * UploadedFile constructor.
     * @param $attachedTo
     */
    public function __construct()
    {
        $this->attachedTo = new ArrayCollection();
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
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return User
     */
    public function getUploadingUser()
    {
        return $this->uploadingUser;
    }

    /**
     * @param User $uploadingUser
     */
    public function setUploadingUser($uploadingUser)
    {
        $this->uploadingUser = $uploadingUser;
    }

    /**
     * @return string
     */
    public function getUriPath()
    {
        return $this->uriPath;
    }

    /**
     * @param string $uriPath
     */
    public function setUriPath($uriPath)
    {
        $this->uriPath = $uriPath;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }


}