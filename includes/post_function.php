<?php
// A Connection Setting Parts at below are Refer To https://www.w3schools.com/php/php_mysql_connect.asp
// All DB Connection and preparation of the SQL are Refer To https://www.w3schools.com/php/php_mysql_prepared_statements.asp
if (isset($_POST['process'])) {
	switch($_POST['process']){
		case "Posting":
			echo create_post($_POST['post_type'],
			$_POST['post_title'],
			$_POST['post_desc'],
			$_POST['post_currency'],
			$_POST['post_price']);
		break;
		case "RemovingPics":
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			setcookie("Posting_ID", $_POST['post_PostBy_PostID'], time() + (86400 * 30), "/");
			$upload_location = dirname( dirname(__FILE__) )."\\"."user_upload_images\\".$_POST['post_PostBy_PostID'].'_'.$_SESSION['NickName'].'_'.$_SESSION['UID'];
			echo remove_post_pics($upload_location);
		break;
		case "UpdatingPost":
			echo edit_post($_POST['post_id'],
			$_POST['post_type'],
			$_POST['post_desc'],
			$_POST['post_currency'],
			$_POST['post_price']);
		break;
		case "UpdatingPreview":
			echo update_preview($_POST['post_id'],
			$_POST['post_preview']);
		break;
		case "RemovingPost":
			echo remove_post($_POST['post_id']);
		break;
		case "ExtendPost":
			echo ExtendPost($_POST['post_id']);
		break;
		case "ConfirmBuyer":
			echo ConfirmBuyer($_POST['post_id'],
			$_POST['post_selectedbuyer']);
		break;
		case "BuyerConfirm_Settlement":
			echo BuyerConfirm_Settlement($_POST['post_id']);
		break;
		case "GetPostDetails":
			echo GetPostDetails($_POST['PostID']);
		break;
		
	}
}

function GetPostDetails($Postid){
	require_once('connect.php');
	$Table_post = table_post;
	$details_array = array();
	$getPostDetails = $conn->prepare("SELECT * FROM $Table_post WHERE ID = ?");
	$getPostDetails->execute(array($Postid));
	while($row = $getPostDetails->fetch()) {
		$details_array[] = $row['ID'];
		$details_array[] = $row['Type'];
		$details_array[] = $row['Title'];
		$details_array[] = $row['Description'];
		$details_array[] = $row['Currency'];
		$details_array[] = $row['Price'];
		$details_array[] = $row['PostBy'];
		$details_array[] = $row['PostBy_UID'];
	}
	print_r(implode(",",$details_array));
}

function create_post($type, $title, $desc, $currency_type, $price){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	$Table_post = table_post;
	$datetimenow = (new DateTime(date("Y-m-d H:i:s")))->format('Y-m-d H:i:s');
	$datetimeadd = (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval('P15D'))->format('Y-m-d H:i:s');
	// Convert newline which is type by "Enter" from readable format to coding format: Refer To https://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
	$changed_desc = preg_replace( "/\r|\n/", "", nl2br($desc) ); 
		$post_insert_sql = $conn->prepare("INSERT INTO $Table_post(Type, Title, Description, Currency, Price, PostBy, PostBy_UID, PostDate, ExpireDate) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$post_insert_sql->execute(array($type, $title, $changed_desc, $currency_type, $price, $_SESSION['NickName'],$_SESSION['UID'], $datetimenow, $datetimeadd));
		$getPostID = $conn->prepare("SELECT ID FROM $Table_post WHERE PostDate = ? AND PostBy_UID = ? AND Type = ?");
		$getPostID->execute(array($datetimenow,$_SESSION['UID'],$type));
		while($row = $getPostID->fetch()) {
			$Result_PostID = $row['ID'];
		}
		setcookie("Posting_ID", $Result_PostID, time() + (86400 * 30), "/");

		switch($_COOKIE['language']){
			case "eng":
				print_r("Post Successfully!");
			break;
			case "zh-tw":
				print_r("發佈成功");
			break;
		}	
}

function edit_post($ID, $new_type, $new_desc, $new_currency_type, $new_price){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	$Table_post = table_post;
	$datetimenow = date("Y-m-d H:i:s");
	// Convert newline which is type by "Enter" from readable format to coding format: Refer To https://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
	$changed_desc = preg_replace( "/\r|\n/", "", nl2br($new_desc) );
	$post_update_sql = $conn->prepare("UPDATE $Table_post SET Type=?, Description=?, Currency=?, Price=?, LastModifyDate=? WHERE ID = ?");
	$post_update_sql->execute(array($new_type,$changed_desc,$new_currency_type,$new_price, $datetimenow, $ID));
	switch($_COOKIE['language']){
		case "eng":
			print_r("Modified");
		break;
		case "zh-tw":
			print_r("修改完成");
		break;
	}	
}

function update_preview($ID, $new_preview_pic){
	require_once('connect.php');
	$Table_post = table_post;
	$post_update_sql = $conn->prepare("UPDATE $Table_post SET preview_img=? WHERE ID = ?");
	$post_update_sql->execute(array($new_preview_pic,$ID));
	switch($_COOKIE['language']){
		case "eng":
			print_r("Modified and change the preview image successfully");
		break;
		case "zh-tw":
			print_r("修改完成及成功變更預覽圖片");
		break;
	}	
}

function ExtendPost($ID){
	require_once('connect.php');
	$datetimeadd = (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval('P7D'))->format('Y-m-d H:i:s');
	$Table_post = table_post;
	$post_update_sql = $conn->prepare("UPDATE $Table_post SET ExpireDate=? WHERE ID = ?");
	$post_update_sql->execute(array($datetimeadd,$ID));
	switch($_COOKIE['language']){
		case "eng":
			print_r("Expiry Date has been extended to ".$datetimeadd);
		break;
		case "zh-tw":
			print_r("發文持續時間已延長至 ".$datetimeadd);
		break;
	}	
}

function remove_post_pics($path){
	if (file_exists($path)) {
		delete_files($path);
	}
}

function remove_post($id){
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	require_once('connect.php');
	$Table_post = table_post;
	$post_delete_sql = $conn->prepare("DELETE FROM $Table_post WHERE ID = ?");
	$post_delete_sql->execute(array($id));
	$pics_location = dirname( dirname(__FILE__) )."\\"."user_upload_images\\".$id.'_'.$_SESSION['NickName'].'_'.$_SESSION['UID'];
	remove_post_pics($pics_location);
	switch($_COOKIE['language']){
		case "eng":
			print_r("Post Deleted");
		break;
		case "zh-tw":
			print_r("發文已刪除");
		break;
	}	
}

function ConfirmBuyer($postid, $finalbuyer){
	require_once('connect.php');
	$Table_post = table_post;
	$post_update_sql = $conn->prepare("UPDATE $Table_post SET Post_Status=?,Selected_Buyer=? WHERE ID = ?");
	$post_update_sql->execute(array("Waiting for confirmation",$finalbuyer,$postid));
	switch($_COOKIE['language']){
		case "zh-tw":
			print_r("發文狀態已成功變更至「等待確定」\n請等待對方進行交收確定。");
		break;
		case "eng":
			print_r("The posting status has been successfully changed to 'waiting for confirmation'\nPlease wait for the buyer to confirm the delivery.");
		break;
	}	
}

function BuyerConfirm_Settlement($postid){
	require_once('connect.php');
	$Table_post = table_post;
	$post_update_sql = $conn->prepare("UPDATE $Table_post SET Post_Status=? WHERE ID = ?");
	$post_update_sql->execute(array("Transaction complete",$postid));
	switch($_COOKIE['language']){
		case "zh-tw":
			print_r("確認交收成功，\n確認訊息已發至你和發文者的訊息中心");
		break;
		case "eng":
			print_r("Confirmation of successful Settlement,\nConfirmation has been sent to your and the author’s notification center");
		break;
	}	
}


/* 
 * php delete function that deals with directories recursively
 * Refer To https://paulund.co.uk/php-delete-directory-and-files-in-directory and
 * https://stackoverflow.com/questions/24144045/rmdir-no-such-file-directory-error-although-directory-exist
 */
function delete_files($target) {
	if(!is_link($target) && is_dir($target))
    {
        // it's a directory; recursively delete everything in it
        $files = array_diff( scandir($target), array('.', '..') );
        foreach($files as $file) {
            delete_files("$target/$file");
        }
        rmdir($target);
    }
    else
    {
        // probably a normal file or a symlink; either way, just unlink() it
        unlink($target);
    }
  }
?>