<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once 'config/database.php';
include_once 'objects/file.php';
include_once 'get_my_files.php';

$all_data = $data;
    
$database = new Database();
$db = $database->getConnection();
    
$file = new File($db);
    
$stmt = $file->show_my_files($all_data['email']);
$num = $stmt->rowCount();

if ($num > 0) {
    $my_files_arr = array();
    $my_files_arr["files"] = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $file_item = array(
            "email" => $email,
            "file_name" => $file_name
        );
        array_push($my_files_arr["files"], $file_item);
    }
    http_response_code(200);
    echo json_encode($my_files_arr, JSON_UNESCAPED_UNICODE);
}
else {
    http_response_code(404);
    echo json_encode(
        array("error" => "No files found.")
    );
}
?>
