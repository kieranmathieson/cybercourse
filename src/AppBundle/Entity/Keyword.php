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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="keyword")
 * @UniqueEntity("title")
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
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 50,
     *      minMessage = "Keywords must be at least {{ limit }} characters long",
     *      maxMessage = "Keywords cannot be longer than {{ limit }} characters"
     * )
     * @ORM\Column(type="string", nullable=false, unique=true, length=50)
     *
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

    /**
     * Keyword constructor.
     */
    public function __construct()
    {
        $this->contentEntities = new ArrayCollection();
//        $this->keywordHelper = $keywordHelper;
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