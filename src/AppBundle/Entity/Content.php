<?php
/**
 * Content types.
 *
 * Used for lessons, exercises, patterns, core ideas, and site pages. Most fields are used by all. A few fields
 * are specific to content types.
 *
 * User: kieran
 * Date: 11/5/2017
 * Time: 7:10 AM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Helper\ContentTypes;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="content")
 */
class Content
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->isAvailable = true;
        $this->keywords = new ArrayCollection();
        $this->rubricItems = new ArrayCollection();
        $this->attachedPublicFiles = new ArrayCollection();
        $this->exerciseSubmissions = new ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    protected $contentType;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * Many content entities have many keywords.
     * @ManyToMany(targetEntity="Keyword", inversedBy="contentEntities")
     * @JoinTable(name="content_keyword")
     */
    protected $keywords;

    /**
     * Slug to use for URL.
     *
     * For new entity, slug is generated, but does not change when the title changes.
     *
     * That's what the custom URL field is for.
     *
     * TODO: Put old changed URLs somewhere for redirects, or something.
     *
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(type="string", length=128, unique=true, nullable=false)
     */
    protected $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * Pattern content only.
     */
    protected $patternCondition;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * Pattern content only.
     */
    protected $patternAction;

    /**
     * Many content objects have many attached files.
     * @ManyToMany(targetEntity="AppBundle\Entity\UploadedFile", inversedBy="attachedTo")
     * @JoinTable(name="content_attached_file")
     */
    protected $attachedPublicFiles;

    /**
     * Many content objects have many hidden attached files.
     * @ManyToMany(targetEntity="AppBundle\Entity\UploadedFile", inversedBy="attachedToHidden")
     * @JoinTable(name="content_attached_file_hidden")
     */
    protected $attachedHiddenFiles;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isAvailable;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $whenCreated;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $whenUpdated;

    /**
     * Many exercises have many rubric items.
     * @ManyToMany(targetEntity="AppBundle\Entity\RubricItem", inversedBy="exercises")
     * @JoinTable(name="exercise_rubric_item")
     */
    protected $rubricItems;

    /**
     * One exercise has many submissions.
     * @OneToMany(targetEntity="AppBundle\Entity\ExerciseSubmission", mappedBy="exercise")
     */
    protected $exerciseSubmissions;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->contentType;
    }

    /**
     * @param mixed $entityType
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
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
     * @return ArrayCollection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param mixed $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * @param mixed $isAvailable
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }


}