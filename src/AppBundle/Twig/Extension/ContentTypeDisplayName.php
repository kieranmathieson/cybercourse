<?php

namespace AppBundle\Twig\Extension;

use AppBundle\Entity\User;
use AppBundle\Helper\ContentTypes;
use AppBundle\Helper\Roles;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentTypeDisplayName extends \Twig_Extension
{

    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('content_type_display_name', [$this, 'getContentTypeDisplayName'])
        ];
    }

    /**
     * Return the display name of a content type.
     *
     * @param User $user The user.
     * @return string The list, for display.
     */
    public function getContentTypeDisplayName($contentType) {
        $result = 'EVIL MUTANT!';
        if ( in_array($contentType, ContentTypes::CONTENT_TYPES) ) {
            $result = ContentTypes::CONTENT_TYPE_DISPLAY_NAMES[ ContentTypes::CONTENT_TYPES[$contentType] ];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'role_display';
    }
}
