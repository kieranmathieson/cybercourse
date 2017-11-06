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

/**
 * @ORM\Entity
 * @ORM\Table(name="uploaded_file")
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
    protected $guid;

    /**
     * Many files have one user uploader.
     * @ManyToOne(targetEntity="User", inversedBy="files")
     */
    protected $uploadingUser;

    /**
     * @ORM\Column(type="string")
     */
    protected $uri;

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
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    /**
     * @return integer
     */
    public function getUploadingUser()
    {
        return $this->uploadingUser;
    }

    /**
     * @param integer $uploadingUser
     */
    public function setUploadingUser($uploadingUser)
    {
        $this->uploadingUser = $uploadingUser;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }


}