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
    protected $allowedExtensions;
    protected $sizeLimit;
    protected $chunksDir;
    protected $destinationDir;
    protected $fileHelper;
    public function __construct($destinationDir, $chunksDir, FileHelper $fileHelper)
    {
        //Set defaults.
        $this->allowedExtensions = ['jpg', 'gif', 'png']; //TOdo get from config.yml.
        $this->sizeLimit = null;
        $this->destinationDir = $destinationDir;
        $this->chunksDir = $chunksDir;
        $this->fileHelper = $fileHelper;
        //Make sure the dirs exist.
        $this->fileHelper->createFilePath($this->destinationDir);
        $this->fileHelper->createFilePath($this->chunksDir);
    }

    public function uploadFile(Request $request) {
        $uploader = new UploadHandler($this->getChunksDir());
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = array('jpg', 'gif', 'png'); // all files types allowed by default
        // Specify max file size in bytes.
        $uploader->sizeLimit = null;
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
    public function setAllowedExtensions(array $allowedExtensions)
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