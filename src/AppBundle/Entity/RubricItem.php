<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 10:25 AM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @ORM\Entity
 * @ORM\Table(name="rubric_item")
 */
class RubricItem
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $notes;

    /**
     * Many rubric items have many exercises
     * @ManyToMany(targetEntity="AppBundle\Entity\Content", mappedBy="rubricItems")
     */
    protected $exercises;

    /**
     * One rubric item has many responses.
     * @OneToMany(targetEntity="AppBundle\Entity\RubricItemResponse", mappedBy="rubricItem")
     */
    protected $responses;



    /**
     * RubricItem constructor.
     */
    public function __construct()
    {
        $this->exercises = new ArrayCollection();
    }

    /**
     * @return mixed
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
     * @param string $title
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
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }


}