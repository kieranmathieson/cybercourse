<?php
/**
 * Relationship table for content objects that are related (relevant to) other content objects.
 *
 * Links are shown after the content's entity's body, in a See Also set of links.
 *
 * User: kieran
 * Date: 11/5/2017
 * Time: 8:47 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Id;

/**
 * @ORM\Entity
 * @ORM\Table(name="related_content")
 */
class RelatedContent
{
    /**
     * @Id
     * @ORM\Column(type="integer")
     */
    protected $contentRelatedToId;

    /**
     * @Id
     * @ORM\Column(type="integer")
     */
    protected $relatedContentId;

}