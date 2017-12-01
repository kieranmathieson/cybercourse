<?php
/**
 * Keyword associated with content.
 * User: kieran
 * Date: 10/30/2017
 * Time: 10:21 AM
 */

namespace AppBundle\Entity;


use AppBundle\Helper\KeywordHelper;
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
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $notes;

    /**
     * Many keywords have many content entities.
     * @ManyToMany(targetEntity="Content", mappedBy="keywords")
     */
    protected $contentEntities;

    /** @var KeywordHelper  */
    protected $keywordHelper;

    /**
     * Keyword constructor.
     */
    public function __construct(KeywordHelper $keywordHelper)
    {
        $this->contentEntities = new ArrayCollection();
        $this->keywordHelper = $keywordHelper;
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
     * @param mixed string
     */
    public function setTitle($title)
    {
//        if ( ! $this->keywordHelper->validateTitleFormat($title) ) {
//            throw new \Exception('Invalid keyword format: ' . htmlspecialchars($title)  );
//        }
        $this->title = $title;
    }

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

    public function getContentEntities()
    {
        return $this->contentEntities;
    }

    /**
     * @param mixed $contentEntities
     */
    public function setContentEntities($contentEntities)
    {
        $this->contentEntities = $contentEntities;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

}