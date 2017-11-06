<?php
/**
 * Keyword associated with content.
 * User: kieran
 * Date: 10/30/2017
 * Time: 10:21 AM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="keyword")
 */
class Keyword
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * Many keywords have many content entities.
     * @ManyToMany(targetEntity="Content", mappedBy="keywords")
     */
    protected $contentEntities;

    /**
     * Keyword constructor.
     */
    public function __construct()
    {
        $this->contentEntities = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $text;

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed string
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $notes;

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed string
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

}