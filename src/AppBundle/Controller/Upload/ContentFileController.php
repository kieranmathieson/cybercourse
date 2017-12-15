<?php

namespace AppBundle\Controller\Upload;

use AppBundle\Entity\UploadedFile;
use AppBundle\Entity\User;
use AppBundle\Helper\ConfigHelper;
use AppBundle\Helper\FileHelper;
use AppBundle\Helper\FileUploader;
//use DirectoryIterator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentFileController extends Controller
{
    /** @var FileHelper $fileHelper */
    protected $fileHelper;

    /** @var  ConfigHelper $configHelper */
    protected $configHelper;

    /** @var FileUploader $fileUploader */
    protected $fileUploader;

    /**
     * ContentFileController constructor.
     * @param FileHelper $fileHelper
     * @param ConfigHelper $configHelper
     * @param FileUploader $fileUploader
     */
    public function __construct(FileHelper $fileHelper, ConfigHelper $configHelper,
        FileUploader $fileUploader)
    {
        $this->fileHelper = $fileHelper;
        $this->configHelper = $configHelper;
        $this->fileUploader = $fileUploader;
    }


    /**
     * REST endpoint to upload a file for use with a content entity.
     * Also handles requests for file lists.
     *
     * @Route("/rest/content-file-upload/{contentId}/{groupName}", name="rest_content_upload")
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     * @param $contentId
     * @param $groupName
     * @param Request $request
     * @return JsonResponse
     * @throws \HttpRequestMethodException
     */
    public function fileUploadHandler($contentId, $groupName, Request $request)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        if ( is_null($loggedInUser) || $loggedInUser == 'anon.' || ! $loggedInUser ) {
            throw new AccessDeniedException('Attempt to upload by unauthorized user.');
            //Todo Log this.
        }
        if ( $request->getMethod() === 'GET' ) {
            //Request for file list.
            if ( $contentId === 'new' ) {
                //New content element. No files yet.
                $result = [];
            }
            else {
                $result = $this->getFileList( $contentId, $groupName );
            }
            return new JsonResponse($result);
        }
        if ( $request->getMethod() === 'POST' ) {
            //Check user's permission to upload.
            $this->checkUploadDeletePermission($loggedInUser);
            //Where do uploads go?
            $this->fileUploader->setDestinationDir(
                $this->fileHelper->getContentUploadDirectory($contentId)
            );
            $result = $this->fileUploader->uploadFile($request);
            //Did it work?
            if ( isset($result['success']) && $result['success'] ) {
                //Success!
                $uuid = $result['uuid'];
                /** @var string $fileNameAndExt The file name and extension, e.g., rosie.jpg. */
                $fileNameAndExt = $result['uploadName'];
                /** @var string $fileFullUri The full URI to the file. */
                $fileFullUri = $this->fileHelper->getContentUri($contentId, $uuid, $fileNameAndExt);
                /** @var string $fileFullFilePath The full file path to the file, on the server.  */
                $fileFullFilePath = $this->fileHelper->getContentFilePath($contentId, $uuid, $fileNameAndExt);
                //Is it an image file?
                $fileExtension = $this->fileHelper->getFileExtension($fileNameAndExt);
                if ( $this->fileHelper->isImageExtension($fileExtension) ) {
                    //Fit the image to maximum dimensions.
                    $this->fileHelper->fitToMaximumDimensions($fileFullFilePath);
                    //Make a thumbnail.
                    $this->fileHelper->makeThumbnail($fileFullFilePath);
                }
                //Make a database record for the file.
                $uploadedFile = new UploadedFile();
                $uploadedFile->setFileName($fileNameAndExt);
                $uploadedFile->setUuid($uuid);
                $uploadedFile->setUriPath($fileFullUri);
                $uploadedFile->setUploadingUser($loggedInUser);
                $em = $this->getDoctrine()->getManager();
                $em->persist($uploadedFile);
                //Need to flush to get file id.
                $em->flush();
                //Record in the appropriate relationship table.
                $relationshipTableName = $groupName;
                $sql = "INSERT INTO $relationshipTableName (content_id, uploaded_file_id)
                    VALUES (:contentId, :uploadedFileId);";
                $params = [
//                    'relationshipTable' => $relationshipTableName,
                    'contentId' => $contentId,
                    'uploadedFileId' => $uploadedFile->getId(),
                ];
                /** @var EntityManagerInterface $em */
                $em = $this->getDoctrine()->getManager();
                $stmt = $em->getConnection()->prepare($sql);
                $stmt->execute($params);
                $em->flush();
            }
            else {
                //Uploader did not succeed.
                //Todo: something here
                $r=6;
            }
            return new JsonResponse($result);
        } //End post.

        throw new \HttpRequestMethodException('Unsupported method.');
    }

    protected function getFileList($contentId, $groupName)
    {
        $em = $this->getDoctrine()->getManager();
//        //Find the dir with the files.
//        $uploadDir = $fileHelper->getContentUploadDirectory($contentId);
//        $result = [];
//        //Loop over the dirs in the dir. The name of each dir is a UUID.
//        foreach (new DirectoryIterator($uploadDir) as $itemInfo) {
//            if ( $itemInfo->isDir() ) {
//                //Dir name should be a UUID.
//                $dirName = $itemInfo->getFilename();
//                if ( ! $fileHelper->isUuid4($dirName) ) {
//                    //Todo: Still need an exception strateegery.
//                    throw new \Exception('Expected UUID level 4 as dir name for content upload.');
//                }
//
//            }
//        }
        $uploadedFiles =
            $em->getRepository('AppBundle:Content')->findUploadsForContentWithId($contentId, $groupName);
        $result = [];
        foreach ($uploadedFiles as $uploadedFile) {
            $fileName = $uploadedFile['file_name'];
            $uuid = $uploadedFile['uuid'];
            $deleteFilePoint = $this->generateUrl(
                'rest_content_file_delete',
                [
                    'contentId' => $contentId,
                    'groupName' => $groupName,
                    'uuid' => $uuid,
                ]
            );
            $thumbnailUrl = $this->fileHelper->getContentThumbnailUri($contentId, $uuid, $fileName);
            //Compute image size.
            $filePath = $this->fileHelper->getContentFilePath($contentId, $uuid, $fileName);
            $imageSize = filesize($filePath);
            if ( $imageSize === FALSE ) {
                throw new \Exception('Could not get file size: ' . $filePath);
            }
            $fileData = [
                'name' => $fileName,
                'uuid' => $uuid,
                'size' => $imageSize,
                'deleteFilePoint' => $deleteFilePoint,
                'thumbnailUrl' => $thumbnailUrl,
            ];
            $result[] = $fileData;
        }
        return $result;
    }

    /**
     * REST endpoint to delete a content file.
     *
     * @Route("/rest/content-file-delete/{contentId}/{groupName}/{uuid}", name="rest_content_file_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_AUTHOR') or has_role('ROLE_ADMIN') or has_role('ROLE_SUPER_ADMIN')")
     * @param Request $request
     * @param $uuid
     * @param FileHelper $fileHelper
     * @return JsonResponse
     * @throws \Exception
     */
    public function fileDeleteHandler($contentId, $groupName, $uuid, Request $request, FileHelper $fileHelper)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        if ( is_null($loggedInUser) || $loggedInUser == 'anon.' || ! $loggedInUser ) {
            throw new AccessDeniedException('Only logged in user can access user photos.');
        }
        //Check user's permission to delete.
        $this->checkUploadDeletePermission($loggedInUser);
        //Fetch file info from the DB.
        $em = $this->getDoctrine()->getManager();
        /** @var UploadedFile[] $fileRecords */
        $fileRecords = $em->getRepository('AppBundle:UploadedFile')->findUploadedFileWithUuid($uuid);
        //Sanity check.
        if ( count($fileRecords) !== 1 ) {
            throw new \Exception('Unexpected number of files:' . count($fileRecords));
        }
        $photo = $fileRecords[0];
        $fileId = $photo->getId();
        $fileName = $photo->getFileName();
        $fileUuid = $photo->getUuid();
        if ( $fileUuid !== $uuid ) {
            throw new \Exception('UUIDs dinna match.');
        }
        //Delete from uploaded_files
        $em->remove($photo);
        //Delete from user_photos.
        $result = $em->getRepository('AppBundle:UserPhoto')->deletePhotoRecordWithFileId($fileId);
        if ( ! $result ) {
            throw new \Exception('user photo table delete fail.');
        }
        $em->flush();
        //Erase the thumbnail.
//        $userId = $userPhotoIsFor->getId();
//        $thumbnailFilePath = $fileHelper->getUserPhotoThumbnailFilePath($userId, $fileUuid, $fileName);
//        if ( ! file_exists($thumbnailFilePath) ) {
//            //Todo log warning.
//        }
//        else {
//            unlink($thumbnailFilePath);
//        }
        //Erase the image.
//        $photoFilePath = $fileHelper->getUserPhotoFilePath($userId, $fileUuid, $fileName);
//        if ( ! file_exists($photoFilePath) ) {
//            //Todo log warning.
//        }
//        else {
//            unlink($photoFilePath);
//        }
//        //Erase the UUID directory.
//        $dir = $fileHelper->getUserPhotoUploadDirectory($userId) . $uuid;
//        if ( ! file_exists($dir) ) {
//            //Todo log warning.
//        }
//        else {
//            rmdir($dir);
//        }
        //Return result.
        //Defaults to 200.
        return new JsonResponse();
    }

    protected function checkUploadDeletePermission(User $loggedInUser)
    {
        //Check user's permission to upload.
        $uploadOk = false;
        if ($loggedInUser->hasRole('ROLE_AUTHOR')) {
            $uploadOk = true;
        }
        if ($loggedInUser->hasRole('ROLE_ADMIN')) {
            $uploadOk = true;
        }
        if ($loggedInUser->hasRole('ROLE_SUPER_ADMIN')) {
            $uploadOk = true;
        }
        //Permission to upload?
        if (!$uploadOk) {
            //No - fail.
            //Wrong exception on purpose, so URL guessing probes won't know they found a good URL.
            throw new NotFoundHttpException();
        }
    }

}
