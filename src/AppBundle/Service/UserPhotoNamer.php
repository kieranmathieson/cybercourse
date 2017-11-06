<?php
/**
 * File namer for user photos. They are named the same as the username.
 *
 * User: kieran
 * Date: 10/26/2017
 * Time: 9:42 AM
 */

namespace AppBundle\Service;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

class UserPhotoNamer implements NamerInterface
{

    /**
     * Creates a name for the file being uploaded.
     *
     * @param object $object The object the upload is attached to
     * @param PropertyMapping $mapping The mapping to use to manipulate the given object
     *
     * @return string The file name
     */
    public function name($object, PropertyMapping $mapping)
    {
        /**
         * @var \AppBundle\Entity\User $object
         */
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $mapping->getFile($object);
        $extension = $file->guessExtension();
        $fileName = $object->getUsername() . '.' . $extension;
        return $fileName;
    }
}