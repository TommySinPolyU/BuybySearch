<?php
// A Connection Setting Parts at below are Refer To https://www.w3schools.com/php/php_mysql_connect.asp
// All DB Connection and preparation of the SQL are Refer To https://www.w3schools.com/php/php_mysql_prepared_statements.asp
if (isset($_POST['process'])) {
	switch($_POST['process']){
		case "Send_NewMsg":
			echo send_new_msg($_POST['post_id'],
			$_POST['trading_msg']);
		break;
		case "Reply_Msg":
			echo reply_msg($_POST['reply_msg_id'],
			$_POST['trading_msg']);
		break;
		case "Read_Msg":
			echo Read_Msg($_POST['post_id']);
		break;
		case "SystemMsg_PostConfirm_Buyer":
			echo SystemMsg_PostConfirm_Buyer($_POST['reply_msg_id'],
			$_POST['trading_msg']);
		break;
		case "SystemMsg_PostConfirm_Seller":
			echo SystemMsg_PostConfirm_Seller($_POST['reply_msg_id'],
			$_POST['trading_msg']);
		break;
		case "SystemMsg_BuyerConfirm_Settlement":
			echo SystemMsg_BuyerConfirm_Settlement($_POST['postid'],
			$_POST['buyer_nn'],
			$_POST['seller_nn']);
		break;	
	}
}

function send_new_msg($postid, $msg){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	$Table_post = table_post;
	$Table_notification = table_notification;
	$datetimenow = (new DateTime(date("Y-m-d H:i:s")))->format('Y-m-d H:i:s');
	$getPosyBy = $conn->prepare("SELECT PostBy FROM $Table_post WHERE ID = ?");
	$getPosyBy->execute(array($postid));
	while($row = $getPosyBy->fetch()) {
		$Result_PostBy = $row['PostBy'];
	}
	// Convert newline which is type by "Enter" from readable format to coding format: Refer To https://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
	$changed_msg = preg_replace( "/\r|\n/", "", nl2br($msg) ); 
	$msg_insert_sql = $conn->prepare("INSERT INTO $Table_notification(PostID, From_NickName, To_NickName, Message, Reply_MsgID, Send_Date, Status) VALUES(?, ?, ?, ?, ?, ?, ?)");
	$msg_insert_sql->execute(array($postid, $_SESSION['NickName'], $Result_PostBy, $changed_msg, NULL, $datetimenow, "Unread"));
	switch($_COOKIE['language']){
		case "eng":
			print_r("Sent Successfully");
		break;
		case "zh-tw":
			print_r("已成功發送");
		break;
	}
}

function reply_msg($reply_msg_id, $msg){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	$Table_notification = table_notification;
	$datetimenow = (new DateTime(date("Y-m-d H:i:s")))->format('Y-m-d H:i:s');
	$getreply_msg_data = $conn->prepare("SELECT * FROM $Table_notification WHERE Msg_ID = ?");
	$getreply_msg_data->execute(array($reply_msg_id));
	while($row = $getreply_msg_data->fetch()) {
		$Result_PostID = $row['PostID'];
		$Result_From_NickName = $row['From_NickName'];
		$Result_To_NickName = $row['To_NickName'];
	}
	// Convert newline which is type by "Enter" from readable format to coding format: Refer To https://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
	$changed_msg = preg_replace( "/\r|\n/", "", nl2br($msg) ); 
	$msg_insert_sql = $conn->prepare("INSERT INTO $Table_notification(PostID, From_NickName, To_NickName, Message, Reply_MsgID, Send_Date, Status) VALUES(?, ?, ?, ?, ?, ?, ?)");
	$msg_insert_sql->execute(array($Result_PostID, $Result_To_NickName, $Result_From_NickName, $changed_msg, $reply_msg_id, $datetimenow, "Unread"));
	switch($_COOKIE['language']){
		case "eng":
			print_r("Sent Successfully");
		break;
		case "zh-tw":
			print_r("已成功發送");
		break;
	}
}

function SystemMsg_PostConfirm_Buyer($reply_msg_id, $msg){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	$Table_notification = table_notification;
	$datetimenow = (new DateTime(date("Y-m-d H:i:s")))->format('Y-m-d H:i:s');
	$getreply_msg_data = $conn->prepare("SELECT * FROM $Table_notification WHERE Msg_ID = ?");
	$getreply_msg_data->execute(array($reply_msg_id));
	while($row = $getreply_msg_data->fetch()) {
		$Result_PostID = $row['PostID'];
		$Result_To_BuyerNickName = $row['From_NickName'];
	}
	$Result_From_NickName = "BuyBySearch Notification System / 系統訊息";
	// Convert newline which is type by "Enter" from readable format to coding format: Refer To https://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
	$changed_msg = preg_replace( "/\r|\n/", "", nl2br($msg) ); 
	$msg_insert_sql = $conn->prepare("INSERT INTO $Table_notification(PostID, From_NickName, To_NickName, Message, Reply_MsgID, Send_Date, Status, System_Reply_PostID) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
	$msg_insert_sql->execute(array(-1, $Result_From_NickName, $Result_To_BuyerNickName, $changed_msg, $reply_msg_id, $datetimenow, "Unread", $Result_PostID));
}

function SystemMsg_PostConfirm_Seller($reply_msg_id, $msg){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	$Table_notification = table_notification;
	$datetimenow = (new DateTime(date("Y-m-d H:i:s")))->format('Y-m-d H:i:s');
	$getreply_msg_data = $conn->prepare("SELECT * FROM $Table_notification WHERE Msg_ID = ?");
	$getreply_msg_data->execute(array($reply_msg_id));
	while($row = $getreply_msg_data->fetch()) {
		$Result_PostID = $row['PostID'];
		$Result_To_SellerNickName = $row['From_NickName'];
	}
	$Result_From_NickName = "BuyBySearch Notification System / 系統訊息";
	// Convert newline which is type by "Enter" from readable format to coding format: Refer To https://stackoverflow.com/questions/10757671/how-to-remove-line-breaks-no-characters-from-the-string
	$changed_msg = preg_replace( "/\r|\n/", "", nl2br($msg) ); 
	$msg_insert_sql = $conn->prepare("INSERT INTO $Table_notification(PostID, From_NickName, To_NickName, Message, Reply_MsgID, Send_Date, Status, System_Reply_PostID) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
	$msg_insert_sql->execute(array(-1, $Result_From_NickName, $Result_To_SellerNickName, $changed_msg, $reply_msg_id, $datetimenow, "Unread", $Result_PostID));
}

function SystemMsg_BuyerConfirm_Settlement($postid, $buyer, $seller){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
	}

	$Table_post = table_post;
	$Table_notification = table_notification;

	$datetimenow = (new DateTime(date("Y-m-d H:i:s")))->format('Y-m-d H:i:s');

	$getPostTitle = $conn->prepare("SELECT * FROM $Table_post WHERE ID = ?");
	$getPostTitle->execute(array($postid));
	while($row = $getPostTitle->fetch()) {
		$Result_PostTitle = $row['Title'];	
	}

	$Result_From_NickName = "BuyBySearch Notification System / 系統訊息";

	$SQL_getLatest_Buyer = $conn->prepare("SELECT Msg_ID,PostID,To_NickName FROM $Table_notification where (Msg_ID,System_Reply_PostID, Send_Date) in (select MAX(Msg_ID) AS LatestMsgID,System_Reply_PostID, max(Send_Date) as DATE from $Table_notification WHERE System_Reply_PostID = ? group by To_NickName) AND From_NickName=? AND To_NickName=?");
    $SQL_getLatest_Buyer->execute(array($postid,$Result_From_NickName,$buyer));
    while($Latest = $SQL_getLatest_Buyer->fetch()) {
      $buyerLaster_MsgID = $Latest['Msg_ID'];
    }
    
    $SQL_getLatest_Seller = $conn->prepare("SELECT Msg_ID,PostID,To_NickName FROM $Table_notification where (Msg_ID,System_Reply_PostID, Send_Date) in (select MAX(Msg_ID) AS LatestMsgID,System_Reply_PostID, max(Send_Date) as DATE from $Table_notification WHERE System_Reply_PostID = ? group by To_NickName) AND From_NickName=? AND To_NickName=?");
    $SQL_getLatest_Seller->execute(array($postid,$Result_From_NickName,$seller));
    while($Latest = $SQL_getLatest_Seller->fetch()) {
      $SellerLaster_MsgID = $Latest['Msg_ID'];
    }


	$system_msg_ToBuyer = "親愛的 ".$buyer."<br />你已經確認了對 「".$Result_PostTitle."」 的交收<br />"."感謝你使用本網站的功能"."<br />歡迎您再次透過本站搜尋您有興趣的東西<br /><br />BuyBySearch 訊息管理系統<br />----------------------------------------------<br />Dear ".$buyer."<br />You have confirmed the settlement of '".$Result_PostTitle."'<br />Thank you for using the features of this site"."<br />You are welcome to search anythings you are interested again through this site<br /><br />BuyBySearch Notification System"; 
	$system_msg_ToSeller = "親愛的 ".$seller."<br />".$buyer."已經確認了對 「".$Result_PostTitle."」 的交收<br />"."感謝你使用本網站的功能"."<br />歡迎您再次透過本站發佈你想售賣 / 宣傳的東西<br /><br />BuyBySearch 訊息管理系統<br />----------------------------------------------<br />Dear ".$seller."<br />".$buyer." have confirmed the settlement of '".$Result_PostTitle."'<br />Thank you for using the features of this site"."<br />You are welcome to post again through this site what you want to sell or promote<br /><br />BuyBySearch Notification System";  	
	
	$system_msg_tobuyer_sqlInsert = $conn->prepare("INSERT INTO $Table_notification(PostID, From_NickName, To_NickName, Message, Reply_MsgID, Send_Date, Status, System_Reply_PostID) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
	$system_msg_tobuyer_sqlInsert->execute(array(-1, $Result_From_NickName, $buyer, $system_msg_ToBuyer, $buyerLaster_MsgID, $datetimenow, "Unread", $postid));

	$system_msg_toseller_sqlInsert = $conn->prepare("INSERT INTO $Table_notification(PostID, From_NickName, To_NickName, Message, Reply_MsgID, Send_Date, Status, System_Reply_PostID) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
	$system_msg_toseller_sqlInsert->execute(array(-1, $Result_From_NickName, $seller, $system_msg_ToSeller, $SellerLaster_MsgID, $datetimenow, "Unread", $postid));
	
	switch($_COOKIE['language']){
		case "eng":
			print_r("The settlement has been successfully confirmed,\nif you have any questions about the transaction, please contact the author");
		break;
		case "zh-tw":
			print_r("已成功確認交收，\n如有任何關於是次交易的問題請自行聯絡發文者");
		break;
	}
}

function Read_Msg($post_id){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
	$Table_notification = table_notification;
	$getreply_msg_data = $conn->prepare("UPDATE $Table_notification SET Status = ? WHERE PostID = ? AND To_NickName = ?");
	$getreply_msg_data->execute(array("Have Read",$post_id, $_SESSION['NickName']));

}

?>