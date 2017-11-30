<?php

namespace AppBundle\Controller\Upload;

use AppBundle\Entity\UploadedFile;
use AppBundle\Entity\User;
use AppBundle\Entity\UserPhoto;
use AppBundle\Helper\FileHelper;
use AppBundle\Helper\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserPhotoFileController extends Controller
{

    /**
     * REST endpoint to upload a user photo.
     *
     * @Route("/rest/user-photo-uploader/{id}", name="rest_user_photo_uploader")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @param User $userPhotoIsFor
     * @param FileHelper $fileHelper
     * @return JsonResponse
     * @throws \HttpRequestMethodException
     */
    public function photoUploadHandler(Request $request, User $userPhotoIsFor, FileHelper $fileHelper)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        if ( is_null($loggedInUser) || $loggedInUser == 'anon.' || ! $loggedInUser ) {
            throw new AccessDeniedException('Only logged in user can access user photos.');
        }
        if ( $request->getMethod() === 'GET' ) {
            //Request for file list.
            $result = $this->getFileList( $request, $userPhotoIsFor, $loggedInUser );
            return new JsonResponse($result);
        }
        if ( $request->getMethod() === 'POST' ) {
            //Check user's permission to upload.
            $this->checkUploadDeletePermission($userPhotoIsFor, $loggedInUser);
            $uploader = new FileUploader(
                $fileHelper->getUserPhotoUploadDirectory($userPhotoIsFor->getId()),
                $fileHelper->getFileChunksUploadDirectory(),
                $fileHelper
            );
            //Todo: set file extensions
            $result = $uploader->uploadFile($request);
            //Did it work?
            if ($result['success']) {
                //Success!
                $uuid = $result['uuid'];
                /** @var string $fileNameAndExt The file name and extension, e.g., rosie.jpg. */
                $fileNameAndExt = $result['uploadName'];
                /** @var string $fileFullUri The full URI to the file. */
                $fileFullUri = $fileHelper->getUserPhotoUri(
                    $userPhotoIsFor->getId(),
                    $uuid,
                    $fileNameAndExt
                );
                /** @var string $fileFullFilePath The full file path to the file, on the server.  */
                $fileFullFilePath = $fileHelper->getUserPhotoFilePath(
                    $userPhotoIsFor->getId(),
                    $uuid,
                    $fileNameAndExt
                );
                //Fit the image to maximum dimensions.
                $fileHelper->fitToMaximumDimensions($fileFullFilePath);
                //Make a thumbnail.
                $fileHelper->makeThumbnail($fileFullFilePath);
                //Make a database record
                $uploadedFile = new UploadedFile();
                $uploadedFile->setFileName($fileNameAndExt);
                $uploadedFile->setUuid($uuid);
                $uploadedFile->setUriPath($fileFullUri);
                $uploadedFile->setUploadingUser($loggedInUser);
                $em = $this->getDoctrine()->getManager();
                $em->persist($uploadedFile);
                //Need to flush to get file id.
                $em->flush();
                //Record in the relationship table.
                $userPhotoEntity = new UserPhoto();
                $userPhotoEntity->setUser($userPhotoIsFor);
                $userPhotoEntity->setUploadedFileId($uploadedFile->getId());
                $em->persist($userPhotoEntity);
                $em->flush();
            }

            return new JsonResponse($result);
        } //End post.

        throw new \HttpRequestMethodException('Unsupported method.');
    }

    protected function getFileList(Request $request, User $userPhotoIsFor, User $loggedInUser)
    {
        $fileHelper = $this->get('app.file_helper');
        $em = $this->getDoctrine()->getManager();
        $userPhotoUploadedFiles =
            $em->getRepository('AppBundle:UserPhoto')->fileUploadedPhotosForUser($userPhotoIsFor);
        $result = [];
        foreach ($userPhotoUploadedFiles as $userPhotoUploadedFile) {
//            $fileId = $userPhotoUploadedFile['id'];
            $fileName = $userPhotoUploadedFile['file_name'];
            $uuid = $userPhotoUploadedFile['uuid'];
//            $uriPath = $userPhotoUploadedFile['uri_path'];
            $deleteFilePoint = $this->generateUrl(
                'rest_user_photo_uploader',
                ['id' => $userPhotoIsFor->getId()]
            );
            $thumbnailUrl = $fileHelper->getUserPhotoThumbnailUri($userPhotoIsFor->getId(), $uuid, $fileName);
            //Compute image size.
            $imageFilePath = $fileHelper->getUserPhotoFilePath($userPhotoIsFor->getId(), $uuid, $fileName);
            $imageSize = filesize($imageFilePath);
            if ( $imageSize === FALSE ) {
                throw new \Exception('Could not get file size: ' . $imageFilePath);
            }
            $photoData = [
                'name' => $fileName,
                'uuid' => $uuid,
                'size' => $imageSize,
                'deleteFilePoint' => $deleteFilePoint,
                'thumbnailUrl' => $thumbnailUrl,
            ];
            $result[] = $photoData;
        }
        return $result;
    }

    /**
     * REST endpoint to delete a user photo.
     *
     * @Route("/rest/user-photo-delete/{id}/{uuid}", name="rest_user_photo_delete")
     * @Security("has_role('ROLE_USER')")
     * @Method("DELETE")
     * @param Request $request
     * @param User $userPhotoIsFor
     * @param $uuid
     * @param FileHelper $fileHelper
     * @return JsonResponse
     * @throws \Exception
     */
    public function photoDeleteHandler(Request $request, User $userPhotoIsFor, $uuid, FileHelper $fileHelper)
    {
        //Load the logged in user.
        /** @var User|string $loggedInUser */
        $loggedInUser = $this->getUser();
        if ( is_null($loggedInUser) || $loggedInUser == 'anon.' || ! $loggedInUser ) {
            throw new AccessDeniedException('Only logged in user can access user photos.');
        }
        //Check user's permission to delete.
        $this->checkUploadDeletePermission($userPhotoIsFor, $loggedInUser);
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
        $userId = $userPhotoIsFor->getId();
        $thumbnailFilePath = $fileHelper->getUserPhotoThumbnailFilePath($userId, $fileUuid, $fileName);
        if ( ! file_exists($thumbnailFilePath) ) {
            //Todo log warning.
        }
        else {
            unlink($thumbnailFilePath);
        }
        //Erase the image.
        $photoFilePath = $fileHelper->getUserPhotoFilePath($userId, $fileUuid, $fileName);
        if ( ! file_exists($photoFilePath) ) {
            //Todo log warning.
        }
        else {
            unlink($photoFilePath);
        }
        //Erase the UUID directory.
        $dir = $fileHelper->getUserPhotoUploadDirectory($userId) . $uuid;
        if ( ! file_exists($dir) ) {
            //Todo log warning.
        }
        else {
            rmdir($dir);
        }
        //Return result.
        //Defaults to 200.
        return new JsonResponse();
    }

    protected function checkUploadDeletePermission(User $userPhotoIsFor, User $loggedInUser)
    {
        //Check user's permission to upload.
        $uploadOk = false;
        if ($userPhotoIsFor->getId() == $loggedInUser->getId()) {
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
