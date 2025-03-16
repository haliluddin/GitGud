<?php
header('Content-Type: application/json');

if(isset($_FILES['productimage'])){
    $targetDir = "tmp/";
    if(!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $file = $_FILES['productimage'];
    $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if(!in_array($imageFileType, ["jpg", "jpeg", "png"])){
        echo json_encode(['success' => false, 'error' => 'Invalid file type.']);
        exit;
    }
    if($file['size'] > 500 * 1024){
        echo json_encode(['success' => false, 'error' => 'File is too large.']);
        exit;
    }
    $filename = uniqid() . "." . $imageFileType;
    $targetFile = $targetDir . $filename;
    if(move_uploaded_file($file['tmp_name'], $targetFile)){
        echo json_encode(['success' => true, 'filePath' => $targetFile]);
        exit;
    }
}
echo json_encode(['success' => false, 'error' => 'File upload failed.']);
