<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/12/2017
 * Time: 12:43 PM
 */

namespace AppBundle\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class FileHelper
{
    //Subdir added to uploads root to get to photos dir.
    const USER_PHOTO_DIR = 'user';
    //Subdir added to uploads root to get to chunks dir.
    const CHUNK_DIR = 'chunk';
    //Attach to end of file name to make a thumbnail name.
    const THUMBNAIL_EXTRA = '_thumb';

    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Recursively create a long directory path
     * https://stackoverflow.com/questions/2303372/create-a-folder-if-it-doesnt-already-exist
     * @param $path
     * @return bool
     */
    public function createFilePath($path) {
        if (is_dir($path)) return true;
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = $this->createFilePath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }

    /**
     * Like realpath(), but works when dirs don't exist.
     *
     * Works for URI and file paths.
     *
     * http://php.net/manual/en/function.realpath.php
     *
     * @param $path
     * @return string
     */
    public function normalizePath($path)
    {
        $path = str_replace(array('/', '\\'), '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $result = implode('/', $absolutes);
        //Replace windows path seps with Linux.
        $result = str_replace('\\', '/', $result);
        return $result;
    }

    /**
     * Get a file's extension.
     *
     * @param string $fileName
     * @return string Extension, MT string if none.
     */
    public function getFileExtension($fileName) {
        $pathInfo = pathinfo($fileName);
        $ext = isset($pathInfo['extension']) ? $pathInfo['extension'] : '';
        return $ext;
    }

    /**
     * Get a filename w/o extension.
     *
     * @param string $fileName
     * @return string Name w/o extension.
     */
    public function getFileNameWithoutExtension($fileName) {
        $pathInfo = pathinfo($fileName);
        $name = $pathInfo['filename'];
        return $name;
    }

    /**
     * Get a file thumbnail's URI.
     *
     * @param string $fileName
     * @return string Name w/o extension.
     */
//    public function convertFileNameToThumbnailname($fileUri) {
//        $pathInfo = pathinfo($fileUri);
//        $result = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb' . '.' . $pathInfo['extension'];
//        // todo: move _thumb into constant.
//        return $result;
//    }

    /**
     * Get the file path to the Web root.
     *
     * @return string Path.
     */
    public function getWebRootFilePath() {
        $result = $this->normalizePath($this->container->get('kernel')->getRootDir().'/../web');
        $result = $this->addFinalSlash($result);
        return $result;
    }

    /**
     * Get the file path to the upload root.
     *
     * @return string Path.
     */
    public function getUploadRootFilePath() {
        $result = $this->getWebRootFilePath();
        $result .= $this->container->getParameter('app.base_uploads_uri');
        $result = $this->normalizePath($result);
        $result = $this->addFinalSlash($result);
        $result = $this->removeSlashDuplicates($result);
        return $result;
    }

    public function removeSlashDuplicates($path) {
        $toReplace = '//';
        while ( FALSE !== strstr($path, $toReplace)) {
            $path = str_replace($toReplace, '/', $path);
        }
        return $path;
    }

    public function getUploadRootUri() {
        $result = '/' . $this->container->getParameter('app.base_uploads_uri') . '/';
        $result = $this->removeSlashDuplicates($result);
        return $result;
    }


    /**
     * Make sure the last char of a string is the path separator.
     *
     * @param string $path String to check.
     * @return string Result.
     */
    public function addFinalSlash($path) {
        $lastChar = substr($path, -1);
        if ( $lastChar !== '/'  ) {
            $path .= '/';
        }
        return $path;
    }

    public function getUserPhotoUploadDirectory($userId) {
        $result = $this->getUploadRootFilePath() . self::USER_PHOTO_DIR . '/'. $userId . '/';
        return $result;
    }

    public function getUserPhotoUploadUri($userId) {
        $result = $this->getUploadRootUri() . self::USER_PHOTO_DIR . '/'. $userId . '/';
        return $result;
    }

    public function getFileChunksUploadDirectory() {
        $result = $this->getUploadRootFilePath() . self::CHUNK_DIR . '/';
        return $result;
    }

//    public function getUserPhotoUuidUploadDirectory($userId, $uuid) {
//        $result = $this->getUserPhotoUploadDirectory($userId). $uuid .'/';
//        return $result;
//    }

    public function convertFileNameToThumbnailname($fileName) {
        $fileNameWoExtension = $this->getFileNameWithoutExtension($fileName);
        $fileExtension = $this->getFileExtension($fileName);
        $result = $fileNameWoExtension . self::THUMBNAIL_EXTRA . '.' . $fileExtension;
        return $result;
    }

    public function getUserPhotoFilePath($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadDirectory($userId). $imageUuid .'/' . $fileName;
        return $result;
    }

    public function getUserPhotoUri($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadUri($userId). $imageUuid .'/' . $fileName;
        return $result;
    }

    public function getUserPhotoThumbnailFilePath($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadDirectory($userId). $imageUuid
            . '/' . $this->convertFileNameToThumbnailname($fileName);
        return $result;
    }
    public function getUserPhotoThumbnailUri($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadUri($userId). $imageUuid
            . '/' . $this->convertFileNameToThumbnailname($fileName);
        return $result;
    }

    public function makeThumbnail( $imageFilePath )
    {
        $pathInfo = pathinfo($imageFilePath);
        $thumbnailFilePath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'_thumb'.'.'.$pathInfo['extension'];
        //Get the max thumbnail size in X and Y.
        $maxSize = $this->container->getParameter('app.thumbnails_max_size');
        $thumbNailImage = new \claviska\SimpleImage();
        $thumbNailImage->fromFile($imageFilePath);
        $thumbNailImage->bestFit($maxSize, $maxSize);
        $thumbNailImage->toFile($thumbnailFilePath);
    }

    /**
     * If an image is too wide or tall, resize to maximum dimensions.
     *
     * @param string $imageFilePath The path to the image.
     */
    public function fitToMaximumDimensions( $imageFilePath )
    {
        //Get the max image size in X and Y.
        $maxSize = $this->container->getParameter('app.user_photo_max_width_height');
        $image = new \claviska\SimpleImage();
        $image->fromFile($imageFilePath);
        $image->bestFit($maxSize, $maxSize);
        $image->toFile($imageFilePath);
    }

}