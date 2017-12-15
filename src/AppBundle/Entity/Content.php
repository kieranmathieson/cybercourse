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
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Content
{
    /**
     * What to prepend to a title to show that the content is not available.
     */
    const NOT_AVAILABLE_MARKER = '(NA) ';

    /**
     * Content objects have multiple groups of uploads, like Attached files.
     * The groups are created when modeling (with Doctrine relationships) the content entity.
     * Each group is a virtual field for the user. The group name also serves as
     * the name of the SQL table relating uploaded files to their content items.
     */
    const UPLOAD_GROUPS = ['content_media', 'content_attached_file', 'content_attached_file_hidden'];

    /**
     * Constructor.
     */
    public function __construct()
    {
//        $this->isAvailable = true;
        $this->whenCreated = new \DateTime();
        $this->keywords = new ArrayCollection();
        $this->rubricItems = new ArrayCollection();
        $this->attachedPublicFiles = new ArrayCollection();
        $this->attachedHiddenFiles = new ArrayCollection();
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
     * Short title for a menu tree.
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    protected $shortMenuTreeTitle;

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
     * Median belonging to this content object.
     * @ManyToMany(targetEntity="AppBundle\Entity\UploadedFile", inversedBy="contentMedia")
     * @JoinTable(name="content_media")
     */
    protected $media;

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
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    protected $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    protected $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    protected $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Content")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Content", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

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
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $entityType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
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
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->whenUpdated= new \DateTime();
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
     * Change the title of the content, if it is not available.
     * Can't do this in get, because sometimes we don't want the
     * title to be adjusted, e.g., in a content report for admin.
     */
    public function changeTitleIfNotAvailable()
    {
        if ( ! $this->isAvailable() ) {
            $this->setTitle( self::NOT_AVAILABLE_MARKER . $this->getTitle() );
        }
    }

    /**
     * @return string
     */
    public function getShortMenuTreeTitle()
    {
        return $this->shortMenuTreeTitle;
    }

    /**
     * @param string $shortMenuTreeTitle
     */
    public function setShortMenuTreeTitle($shortMenuTreeTitle)
    {
        $this->shortMenuTreeTitle = $shortMenuTreeTitle;
    }

    /**
     * Change the short menu tree title of the content, if it is not available.
     * Can't do this in get, because sometimes we don't want the
     * title to be adjusted, e.g., in a content report for admin.
     */
    public function changeShortMenuTreeTitleIfNotAvailable()
    {
        if ( ! $this->isAvailable() ) {
            $this->setShortMenuTreeTitle( self::NOT_AVAILABLE_MARKER . $this->getShortMenuTreeTitle() );
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
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
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
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
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * @param boolean $isAvailable
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;
    }

    /**
     * @return string
     */
    public function getPatternCondition()
    {
        return $this->patternCondition;
    }

    /**
     * @param string $patternCondition
     */
    public function setPatternCondition($patternCondition)
    {
        $this->patternCondition = $patternCondition;
    }

    /**
     * @return string
     */
    public function getPatternAction()
    {
        return $this->patternAction;
    }

    /**
     * @param string $patternAction
     */
    public function setPatternAction($patternAction)
    {
        $this->patternAction = $patternAction;
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

    public function getRoot()
    {
        return $this->root;
    }

    public function setParent(Content $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

}