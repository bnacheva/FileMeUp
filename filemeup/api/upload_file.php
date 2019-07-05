<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/database.php';
include_once 'objects/file.php';

$database = new Database();
$db = $database->getConnection();

$file = new File($db);

if (!empty($_FILES['file_name']['name'])) {
    $uploadedFile = '';
    if (!empty($_FILES["file_name"]["type"])) {
        $fileName = $_FILES['file_name']['name'];
        $valid_extensions = array("jpeg", "jpg", "png", "gif", "pdf", "html", "txt", "ppt", "doc", "xls");
        $temporary = explode(".", $_FILES["file_name"]["name"]);
        $file_extension = end($temporary);
        if ((($_FILES["file_name"]["type"] == "image/png") || ($_FILES["file_name"]["type"] == "image/jpg")
                || ($_FILES["file_name"]["type"] == "image/jpeg") || ($_FILES["file_name"]["type"] == "application/pdf"))
            && in_array($file_extension, $valid_extensions) && !file_exists("uploads/" . $fileName)
        ) {
            $sourcePath = $_FILES['file_name']['tmp_name'];
            $targetPath = "uploads/" . $fileName;
            if (move_uploaded_file($sourcePath, $targetPath)) {
                $uploadedFile = $fileName;
            }
        }
    }

    $file->email = $_POST['email'];
    $file->file_name = $uploadedFile;
    if ($_POST['access'] == "Off")
        $file->access = 0;
    else if ($_POST['access'] == "On")
        $file->access = 1;
    if (
        !empty($file->email) &&
        !empty($file->file_name) &&
        $file->add()
    ) {        
        http_response_code(200);
        echo json_encode(array("message" => "File was uploaded."));
    }
}

else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to upload file."));
}
