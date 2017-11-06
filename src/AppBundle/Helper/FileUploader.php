<?php
/**
 * Created by PhpStorm.
 * User: kieran
 * Date: 11/4/2017
 * Time: 5:25 PM
 */

namespace AppBundle\Helper;


class FileUploader
{
    public function __construct()
    {
        $this->allowedExtensions = ['jpg', 'gif', 'png'];
    }

    protected $allowedExtensions;
    protected $sizeLimit;
    protected $destinationDir;
    public function uploadFile() {
        $uploader = new UploadHandler();
        // Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $uploader->allowedExtensions = array('jpg', 'gif', 'png'); // all files types allowed by default
// Specify max file size in bytes.
        $uploader->sizeLimit = null;
// Specify the input name set in the javascript.
        $uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
        $uploader->chunksFolder = "chunks";
        $method = $this->get_request_method();

        if ($method == "POST") {
            // ???????? header("Content-Type: text/plain");
            // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
            // For example: /myserver/handlers/endpoint.php?done
            if (isset($_GET["done"])) {
                $result = $uploader->combineChunks("users");
            }
            // Handles upload requests
            else {
                // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
                $result = $uploader->handleUpload("users");
                // To return a name used for uploaded file you can use the following line.
                $result["uploadName"] = $uploader->getUploadName();
            }
            //echo json_encode($result);
        }
// for delete file requests
        else if ($method == "DELETE") {
            $result = $uploader->handleDelete("users");
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

}