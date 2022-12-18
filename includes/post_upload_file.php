<?php
// Upload Files Function Refer To https://makitweb.com/multiple-files-upload-at-once-with-php/
require_once('connect.php');
$Table_post = table_post;

if (session_status() == PHP_SESSION_NONE) {
	session_start();
  }
  // Count total files
$countfiles = count($_FILES['files']['name']);

// Upload directory
$img_folder_name = $_COOKIE['Posting_ID'].'_'.$_SESSION['NickName'].'_'.$_SESSION['UID'];
$upload_location = "../"."user_upload_images/".$img_folder_name;

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
    $datenow = date("Y-m-d");
    $filename_to_server = $datenow."_post_img_".$index.'.'.$ext;
   $path = "../"."user_upload_images/".$img_folder_name.'/'.$filename_to_server;
   
   $store_sql_path = $filename_to_server;
   if($filename==$_POST['preview_img']){
    $preview_img_update_sql = $conn->prepare("UPDATE $Table_post SET preview_img = ? WHERE ID = ?");
    $preview_img_update_sql->execute(array($store_sql_path, $_COOKIE['Posting_ID']));
  }
     // Upload file
     if(move_uploaded_file($_FILES['files']['tmp_name'][$index],$path)){
        $files_arr[] = $path;
        $files_sql_arr[] = $store_sql_path;
        chmod($path, 0755);
     }
   }

}
$columns = implode(", ",$files_sql_arr);
$post_insert_imglocation_sql = $conn->prepare("UPDATE $Table_post SET img_files = ?, img_folder = ? WHERE ID = ?");
$post_insert_imglocation_sql->execute(array($columns, $img_folder_name, $_COOKIE['Posting_ID']));
echo json_encode($files_arr);

setcookie("Posting_ID", "", time() - 3600);
die;


?>