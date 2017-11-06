<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/5/2017
 * Time: 10:52 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @ORM\Entity
 * @ORM\Table(name="rubric_item_response")
 */
class RubricItemResponse
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
     * Many responses belong to one item.
     * @ManyToOne(targetEntity="AppBundle\Entity\RubricItem", inversedBy="responses")
     * @JoinColumn(name="rubric_item_id", referencedColumnName="id")
     */
    protected $rubricItem;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $completesRubricItem;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return boolean
     */
    public function getCompletesRubricItem()
    {
        return $this->completesRubricItem;
    }

    /**
     * @param boolean $completesRubricItem
     */
    public function setCompletesRubricItem($completesRubricItem)
    {
        $this->completesRubricItem = $completesRubricItem;
    }


}