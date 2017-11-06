<?php
/**
 * Relationship table for content objects embedded in other content objects.
 *
 * References are embedded in content with ReST tags.
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
 * @ORM\Table(name="embedded_content")
 */
class EmbeddedContent
{
    /**
     * @Id
     * @ORM\Column(type="integer")
     */
    protected $contentEmbeddedInId;

    /**
     * @Id
     * @ORM\Column(type="integer")
     */
    protected $embeddedContentId;

}