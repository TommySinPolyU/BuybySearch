<?php
// Upload Files Function Refer To https://makitweb.com/multiple-files-upload-at-once-with-php/
require_once('connect.php');
$Table_trans = table_trans;

if (session_status() == PHP_SESSION_NONE) {
	session_start();
  }
  // Count total files
$countfiles = count($_FILES['files']['name']);

// Upload directory
$datenow = date("Y_m_d");
$upload_location = "../"."transaction_receipt"."/".$datenow;

if (!file_exists($upload_location)) {
    mkdir($upload_location, 755, true);
}
if (file_exists($upload_location)) {
  chmod($upload_location, 0755);
}

// To store uploaded files path
$files_arr = array();
$files_sql_arr = array();
// Loop all files
for($index = 0;$index < $countfiles;$index++){

   // File name
   $filename = $_FILES['files']['name'][$index];
   // Get extension
   $ext = pathinfo($filename, PATHINFO_EXTENSION);

   // Valid image extension
   $valid_ext = array("png","jpeg","jpg");

   // Check extension
   if(in_array($ext, $valid_ext)){
    
    // File path

    $filename_to_server = $_SESSION['UID']."_".$_SESSION['TransID'].'.'.$ext;
   $path = "../"."transaction_receipt"."/".$datenow.'/'.$filename_to_server;
   $store_sql_path = $filename_to_server;

     // Upload file
     if(move_uploaded_file($_FILES['files']['tmp_name'][$index],$path)){
        $files_arr[] = $path;
        $files_sql_arr[] = $store_sql_path;
        chmod($path, 0755);
     }
   }

}
$columns = implode(", ",$files_sql_arr);
$insert_imglocation_sql = $conn->prepare("UPDATE $Table_trans SET receipt_img = ? WHERE Transaction_ID = ?");
$insert_imglocation_sql->execute(array($columns, $_SESSION['TransID']));
echo json_encode($files_arr);
unset($_SESSION['TransID']);
die;


?>