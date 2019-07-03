<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

use \Firebase\JWT\JWT;

include_once 'config/database.php';
include_once 'objects/file.php';

$database = new Database();
$db = $database->getConnection();

$file = new File($db);
$data = json_decode(file_get_contents("php://input"));

$jwt = isset($data->jwt) ? $data->jwt : "";

if ($jwt) {
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        /*if (!empty($_FILES['file_name']['name'])) {
    $uploadedFile = '';
    if (!empty($_FILES["file_name"]["type"])) {
        $fileName = time() . '_' . $_FILES['file_name']['name'];
        $valid_extensions = array("jpeg", "jpg", "png", "gif", "pdf", "html", "txt", "ppt", "doc", "xls");
        $temporary = explode(".", $_FILES["file_name"]["name"]);
        $file_extension = end($temporary);
        if ((($_FILES["hard_file_name"]["type"] == "image/png") || ($_FILES["file_name"]["type"] == "image/jpg") 
        || ($_FILES["file_name"]["type"] == "image/jpeg") || ($_FILES["file_name"]["type"] == "application/pdf")) 
        && in_array($file_extension, $valid_extensions)) {
            $sourcePath = $_FILES['file_name']['tmp_name'];
            $targetPath = "uploads/" . $fileName;
            if (move_uploaded_file($sourcePath, $targetPath)) {
                $uploadedFile = $fileName;
            }
        }
    }*/

        $file->email = $data->email;
        $file->file_name = $data->file_name;
        if (!isset($data->access))
            $file->access = 0;
        else if ($data->access == "on")
            $file->access = 1;

        if (
            !empty($file->email) &&
            !empty($file->file_name) &&
            $file->add()
        ) {
            $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => array(
                    "id" => $file->id,
                    "email" => $file->email,
                    "file_name" => $file->file_name,
                    "access" => $file->access
                )
            );
            $jwt = JWT::encode($token, $key);
            http_response_code(200);
            echo json_encode(
                array(
                    "message" => "File was uploaded.",
                    "jwt" => $jwt
                )
            );
        } else {

            http_response_code(400);
            echo json_encode(array("message" => "Unable to upload file."));
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
} else {
    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
}
