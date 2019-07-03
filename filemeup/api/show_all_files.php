<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once 'config/database.php';
include_once 'objects/file.php';
    
$database = new Database();
$db = $database->getConnection();
    
$file = new File($db);

$stmt = $file->show_all_files();
$num = $stmt->rowCount();

if ($num > 0) {
    $files_arr = array();
    $files_arr["records"] = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $file_item = array(
            "email" => $email,
            "file_name" => $file_name
        );
        array_push($files_arr["records"], $file_item);
    }
    http_response_code(200);
    echo json_encode($files_arr, JSON_UNESCAPED_UNICODE);
}
else {
    http_response_code(404);
    echo json_encode(
        array("error" => "No files found.")
    );
}
?>