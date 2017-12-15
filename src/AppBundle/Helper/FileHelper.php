<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/12/2017
 * Time: 12:43 PM
 */

namespace AppBundle\Helper;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Validator\Constraints\Uuid;

class FileHelper
{
    //Subdir added to uploads root to get to photos dir.
    const USER_PHOTO_DIR = 'user';
    //Subdir added to uploads root to get to contents upload dir.
    const CONTENT_DIR = 'content';
    //Subdir added to uploads root to get to chunks dir.
    const CHUNK_DIR = 'chunk';
    //Attach to end of file name to make a thumbnail name.
    const THUMBNAIL_EXTRA = '_thumb';
    //File extensions for image files.
    const IMAGE_FILE_EXTENSIONS = ['png', 'jpg', 'gif'];
    //FontAwesome icon codes to use for various file types.
    //Separate values in key be spaces, to allow easy matching in loop.
    //Todo: move to data file to make it easier to chamge.
    const FILE_TYPE_ICONS = [
        ' xlsx xlsm ' => 'file-excel',
        ' doc docx ' => 'file-word',
        ' ppt pptx ' => 'file-powerpoint',
        ' txt ' => 'file-alt',
        ' pdf ' => 'file-pdf',
        ' zip gz ' => 'file-archive',
        ' exe ' => 'file-code',
    ];
    //Icon for other file types.
    const GENERIC_FILE_OPTION = 'file';
    //Web path (URLish) to file type icons.
    const FILE_ICON_PATH = '/images/fontawesome/';
    //Extensions for those files.
    const FILE_ICON_EXTENSION = 'svg';
    //Regex for testing for UUID level 4.
    //See https://stackoverflow.com/questions/19989481/how-to-determine-if-a-string-is-a-valid-v4-uuid
    const UUID4_REGEX = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

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
        $ext = strtolower($ext);
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
     * Get the file path to the app root.
     *
     * @return string Path.
     */
    public function getAppRootFilePath() {
        $result = $this->normalizePath($this->container->get('kernel')->getRootDir());
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

    /**
     * Get URI for the dir with all uploads.
     *
     * @return mixed|string
     */
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

    /**
     * Get the dir for photos for a user.
     * @param integer $userId User id.
     * @return string Path.
     */
    public function getUserPhotoUploadDirectory($userId) {
        $result = $this->getUploadRootFilePath() . self::USER_PHOTO_DIR . '/'. $userId . '/';
        return $result;
    }

    /** Get the dir for uploads for a content object.
     * @param integer $contentId Content id.
     * @return string Path.
     */
    public function getContentUploadDirectory($contentId) {
        $result = $this->getUploadRootFilePath() . self::CONTENT_DIR . '/'. $contentId . '/';
        return $result;
    }

    /**
     * Get the URI for photos for a user.
     * @param integer $userId User id.
     * @return string URI.
     */
    public function getUserPhotoUploadUri($userId) {
        $result = $this->getUploadRootUri() . self::USER_PHOTO_DIR . '/'. $userId . '/';
        return $result;
    }

    /**
     * Get the URI for content.
     * @param integer $contentId Content id.
     * @return string URI.
     */
    public function getContentUploadUri($contentId) {
        $result = $this->getUploadRootUri() . self::CONTENT_DIR . '/'. $contentId . '/';
        return $result;
    }

    public function getFileChunksUploadDirectory() {
        $result = $this->getUploadRootFilePath() . self::CHUNK_DIR . '/';
        return $result;
    }

    /**
     * Find the name of a thumbnail, given an image file name.
     *
     * @param $fileName
     * @return string
     */
    public function convertImageFileNameToThumbnailName($fileName) {
        $fileNameWoExtension = $this->getFileNameWithoutExtension($fileName);
        $fileExtension = $this->getFileExtension($fileName);
        $result = $fileNameWoExtension . self::THUMBNAIL_EXTRA . '.' . $fileExtension;
        return $result;
    }

    /**
     * Compute the file path for a user photo.
     *
     * @param integer $userId User id.
     * @param string $imageUuid File UUID, computed by uploader.
     * @param string $fileName File name with extension.
     * @return string Path.
     */
    public function getUserPhotoFilePath($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadDirectory($userId). $imageUuid .'/' . $fileName;
        return $result;
    }

    /**
     * Compute the file path for a content upload.
     *
     * @param integer $contentId Content id.
     * @param string $imageUuid File UUID, computed by uploader.
     * @param string $fileName File name with extension.
     * @return string Path.
     */
    public function getContentFilePath($contentId, $imageUuid, $fileName) {
        $result = $this->getContentUploadDirectory($contentId). $imageUuid .'/' . $fileName;
        return $result;
    }

    /**
     * Compute the URI for a user photo.
     *
     * @param integer $userId User id.
     * @param string $imageUuid Uuid computed by uploader.
     * @param string $fileName File name with extension.
     * @return string URI.
     */
    public function getUserPhotoUri($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadUri($userId) . $imageUuid .'/' . $fileName;
        return $result;
    }

    /**
     * Compute the URI for a content upload.
     *
     * @param integer $contentId content id.
     * @param string $imageUuid Uuid computed by uploader.
     * @param string $fileName File name with extension.
     * @return string URI.
     */
    public function getContentUri($contentId, $imageUuid, $fileName) {
        $result = $this->getContentUploadUri($contentId) . $imageUuid .'/' . $fileName;
        return $result;
    }

    /**
     * Compute the file path to a user photo thumbnail.
     *
     * @param integer $userId User id.
     * @param string $imageUuid Uuid computed by uploader.
     * @param string $fileName File name with extension.
     * @return string Path.
     */
    public function getUserPhotoThumbnailFilePath($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadDirectory($userId). $imageUuid
            . '/' . $this->convertImageFileNameToThumbnailName($fileName);
        return $result;
    }

    /**
     * Compute the file path to a content upload thumbnail.
     *
     * @param integer $contentId Content id.
     * @param string $imageUuid Uuid computed by uploader.
     * @param string $fileName File name with extension.
     * @return string Path.
     */
    public function getContentThumbnailFilePath($contentId, $imageUuid, $fileName) {
        $result = $this->getContentUploadDirectory($contentId) . $imageUuid
            . '/' . $this->convertImageFileNameToThumbnailName($fileName);
        return $result;
    }

    /**
     * Compute the URI of the thumbnail for a user photo.
     *
     * @param integer $userId User id.
     * @param string $imageUuid Uuid from uploader.
     * @param string $fileName File name with extension.
     * @return string URI.
     */
    public function getUserPhotoThumbnailUri($userId, $imageUuid, $fileName) {
        $result = $this->getUserPhotoUploadUri($userId). $imageUuid
            . '/' . $this->convertImageFileNameToThumbnailName($fileName);
        return $result;
    }

    /**
     * Compute the URI of the thumbnail for a content upload.
     * It will be a custom thumbnail for an image file, or a
     * generic thumbnail for other files.
     *
     * @param integer $contentId Content id.
     * @param string $imageUuid Uuid from uploader.
     * @param string $fileName File name with extension.
     * @return string URI.
     */
    public function getContentThumbnailUri($contentId, $imageUuid, $fileName) {
        $extension = $this->getFileExtension($fileName);
        if ( in_array($extension, self::IMAGE_FILE_EXTENSIONS) ) {
            //Image file - it should have a generated thumbnail.
            $result = $this->getContentUploadUri($contentId) . $imageUuid
                . '/' . $this->convertImageFileNameToThumbnailName($fileName);
        }
        else {
            //It's a non-image file. Use an icon.
            $iconNameToUse = self::GENERIC_FILE_OPTION;
            foreach (self::FILE_TYPE_ICONS as $extensions => $iconName) {
                if ( strstr($extensions, $extension) ) {
                    $iconNameToUse = $iconName;
                    break;
                }
            }
            $result = self::FILE_ICON_PATH . $iconNameToUse . '.' . self::FILE_ICON_EXTENSION;
        }
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

    /**
     * Test whether a value is a valid UUID level 4.
     *
     * @param string Value to test.
     * @return bool Resuult.
     */
    public function isUuid4($value) {
        $valid = ( preg_match(self::UUID4_REGEX, $value) === 1 );
        return $valid;
    }


    public function isImageExtension(string $ext) {
        $isImage = in_array( strtolower($ext), self::IMAGE_FILE_EXTENSIONS );
        return $isImage;
    }
}