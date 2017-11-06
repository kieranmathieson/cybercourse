<?php
/**
 * Psudent poses.
 *
 * User: kieran
 * Date: 11/5/2017
 * Time: 10:10 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="psudent_pose")
 */
class PsudentPose
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Many poses belong to one psudent.
     * @ManyToOne(targetEntity="AppBundle\Entity\Psudent", inversedBy="poses")
     * @JoinColumn(name="psudent_id", referencedColumnName="id")
     */
    protected $psudent;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $poseName;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $fileName;

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
    public function getPoseName()
    {
        return $this->poseName;
    }

    /**
     * @param string $poseName
     */
    public function setPoseName($poseName)
    {
        $this->poseName = $poseName;
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