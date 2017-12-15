<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/4/2017
 * Time: 5:25 PM
 */

namespace AppBundle\Helper;


use Symfony\Component\HttpFoundation\Request;

class FileUploader
{
    /** @var array File extensions that are allowed for upload. */
    protected $allowedExtensions;

    /** @var integer File size limit. */
    protected $sizeLimit;

    /** @var string Path to the chunks directory. */
    protected $chunksDir;

    /** @var string Destination for uploads. */
    protected $destinationDir;

    /** @var FileHelper $fileHelper */
    protected $fileHelper;

    /** @var ConfigHelper $configHelper */
    protected $configHelper;

    /** @var UserHelper $userHelper */
    protected $userHelper;

    public function __construct(
            FileHelper $fileHelper, ConfigHelper $configHelper, UserHelper $userHelper
    )
    {
        $this->fileHelper = $fileHelper;
        $this->configHelper = $configHelper;
        $this->userHelper = $userHelper;
        //Set defaults.
        $this->sizeLimit = null;
        $this->destinationDir = null;
        $this->chunksDir = $this->fileHelper->getFileChunksUploadDirectory();
        //Compute the allowed extensions for uploads.
        $this->computeDefaultAllowedExtensions();
    }

    /**
     * Compute file extensions that are allowed.
     * @throws \Exception
     */
    protected function computeDefaultAllowedExtensions() {
        //Compute allowed extensions from config, based on user role.
        //Get the extensions allowed for all users.
        $config = $this->configHelper->getCourseConfig();
        if ( ! isset($config['allowed_upload_file_extentions']['all_users']) ) {
            throw new \Exception('Missing upload file extensions in course config file');
        }
        $customExtensions = $config['allowed_upload_file_extentions']['all_users'];
        //Adjust if the current user is privileged.
        if ( $this->userHelper->isLoggedInUserAuthorOrBetter() ) {
            if ( ! isset($config['allowed_upload_file_extentions']['privileged_users_extra']) ) {
                throw new \Exception('Missing upload file extensions for privileged users in course config file');
            }
            $extraExtensions = $config['allowed_upload_file_extentions']['privileged_users_extra'];
            $customExtensions = array_merge($customExtensions, $extraExtensions);
        }
        //Check if there is an 'all' in the array. If so, all files are allowed.
        $all = in_array('all', $customExtensions);
        if ( $all ) {
            //All are allowed. Indicated by an MT array.
            $this->setAllowedExtensions([]);
        }
        else {
            //Don't have 'all' permission.
            $this->setAllowedExtensions( $customExtensions );
        }
    }


    public function uploadFile(Request $request) {
        //Need a dest dir first.
        if ( ! $this->getDestinationDir() ) {
            throw new \Exception('Upload file dest no set.');
        }
        //Make sure the dirs exist.
        $this->fileHelper->createFilePath($this->getDestinationDir());
        $this->fileHelper->createFilePath($this->getChunksDir());
        $uploader = new UploadHandler($this->getChunksDir());
        // Specify the list of valid extensions.
        // All files types allowed by default.
        if ( count($this->getAllowedExtensions()) > 0 ) {
            $uploader->allowedExtensions = $this->getAllowedExtensions();
        }
        // Specify max file size in bytes.
        if ( $this->getSizeLimit() ) {
            $uploader->sizeLimit = $this->getSizeLimit();
        }
        // Specify the input name set in the javascript.
        $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
        $method = $request->getMethod();
        if ($method == "POST") {
            // ???????? header("Content-Type: text/plain");
            // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
            // For example: /myserver/handlers/endpoint.php?done
            if (isset($_GET["done"])) {
                $result = $uploader->combineChunks($this->getDestinationDir());
            }
            // Handles upload requests
            else {
                // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
                $result = $uploader->handleUpload($this->getDestinationDir());
                // To return a name used for uploaded file you can use the following line.
                $result["uploadName"] = $uploader->getUploadName();
            }
            //echo json_encode($result);
        }
// for delete file requests
        else if ($method == "DELETE") {
            $result = $uploader->handleDelete($this->getDestinationDir());
            //echo json_encode($result);
        }
        else {
            $result['s'] = 'fail';
//            return new JsonResponse()
            //header("HTTP/1.0 405 Method Not Allowed");
        }
        return $result;
    }


    // This will retrieve the "intended" request method.  Normally, this is the
// actual method of the request.  Sometimes, though, the intended request method
// must be hidden in the parameters of the request.  For example, when attempting to
// delete a file using a POST request. In that case, "DELETE" will be sent along with
// the request in a "_method" parameter.
    protected function get_request_method() {
        global $HTTP_RAW_POST_DATA;
        if(isset($HTTP_RAW_POST_DATA)) {
            parse_str($HTTP_RAW_POST_DATA, $_POST);
        }
        if (isset($_POST["_method"]) && $_POST["_method"] != null) {
            return $_POST["_method"];
        }
        return $_SERVER["REQUEST_METHOD"];
    }

    /**
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    /**
     * @param array $allowedExtensions
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * @return mixed
     */
    public function getSizeLimit()
    {
        return $this->sizeLimit;
    }

    /**
     * @param mixed $sizeLimit
     */
    public function setSizeLimit($sizeLimit)
    {
        $this->sizeLimit = $sizeLimit;
    }

    /**
     * @return string
     */
    public function getDestinationDir()
    {
        return $this->destinationDir;
    }

    /**
     * @param string $destinationDir
     */
    public function setDestinationDir($destinationDir)
    {
        $this->destinationDir = $destinationDir;
    }

    /**
     * @return string
     */
    public function getChunksDir(): string
    {
        return $this->chunksDir;
    }

    /**
     * @param string $chunksDir
     */
    public function setChunksDir($chunksDir)
    {
        $this->chunksDir = $chunksDir;
    }


}