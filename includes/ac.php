<?php
// A Connection Setting Parts at below are Refer To https://www.w3schools.com/php/php_mysql_connect.asp
// All DB Connection and preparation of the SQL are Refer To https://www.w3schools.com/php/php_mysql_prepared_statements.asp
if (isset($_POST['process'])) {
	switch($_POST['process']){
		case "Register_Success":
			echo create_ac($_POST['register_email'],
			$_POST['register_pw'],
			$_POST['register_nName'],
			$_POST['register_gender'],
			$_POST['register_birthdate']);
		break;
		case "Register_CheckEmail":
			echo check_ac($_POST['register_email']);
		break;
		case "Register_CheckNickname":
			echo check_nickname($_POST['register_nName']);
		break;
		case "Login_Checking":
			echo login_ac($_POST['login_email'],$_POST['login_pw']);
		break;
		case "Logout":
			session_start();
			unset($_SESSION['UID']);
			unset($_SESSION['email']);
			unset($_SESSION['NickName']);
			unset($_SESSION['Gender']);
			unset($_SESSION['Birthdate']);
			unset($_SESSION['Country']);
			unset($_SESSION['Coins']);
		break;
		case "Reduce_Coins":
			session_start();
			reduce_coins($_SESSION['UID'], $_POST['Coin_Cost']);
		break;
		/*
		case "Add_Coins":
			session_start();
			add_coins($_SESSION['UID'], $_POST['Coin_Add']);
		break;
		*/
		case "Update_Coins":
			session_start();
			update_coins($_SESSION['UID']);
		break;
		case "Send_TransactionID":
			session_start();
			send_paypal_transaction($_POST['Trans_ID'],
			$_POST['Trans_Email']);
		break;
	}
}

function send_paypal_transaction($transactionID, $Trans_Email){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
	}
	$Table_trans = table_trans;
	$datetimenow = date("Y-m-d H:i:s");
	$stmt_result = $conn->prepare("SELECT * FROM $Table_trans WHERE UID = ? AND Status = ?");
	$stmt_result->execute(array($_SESSION['UID'],"Processing"));
	$stmt_result_count = $stmt_result->rowCount();
	if($stmt_result_count < 5){
		$stmt = $conn->prepare("INSERT INTO $Table_trans VALUES(?,?,?,?,?,?)");
		$stmt->execute(array($_SESSION['UID'], $Trans_Email, $transactionID, $datetimenow, "Processing", ""));
		$_SESSION['TransID'] = $transactionID;
		print_r("Success");
	} else {
		print_r("Fail");
	}
}

function update_coins($uid){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
	}
	$Table_ac = table_ac;
	$stmt = $conn->prepare("SELECT Coins FROM $Table_ac WHERE UID = ?");
	$stmt->execute(array($uid));
	while($row = $stmt->fetch()) {
		$_SESSION['Coins'] = $row['Coins'];
	}
	print_r($_SESSION['Coins']);
}

//Removed Function, function for testing only
/*
function add_coins($uid, $value){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
	}
	$Balance = $_SESSION['Coins'] + $value;
	$Table_ac = table_ac;
	$stmt = $conn->prepare("UPDATE $Table_ac SET Coins=? WHERE UID = ?");
	$stmt->execute(array($Balance,$uid));
	update_coins($uid);
}
*/
function reduce_coins($uid, $Cost){
	require_once('connect.php');
	if (session_status() == PHP_SESSION_NONE) {
        session_start();
	}
	$Balance = $_SESSION['Coins'] - $Cost;
	if($Balance >= 0){
		$Table_ac = table_ac;
		$stmt = $conn->prepare("UPDATE $Table_ac SET Coins=? WHERE UID = ?");
		$stmt->execute(array($Balance,$uid));
		print_r("true");
	} else {
		switch($_COOKIE['language']){
			case "eng":
				print_r("Insufficient user points");
			break;
			case "zh-tw":
				print_r("用戶點數不足");
			break;
		}	
	}
}

function check_ac($email){
	require_once('connect.php');
	$Table_ac = table_ac;
	$stmt = $conn->prepare("SELECT Email FROM $Table_ac WHERE Email = ?");
	$stmt->execute(array($email));
	$count = $stmt->rowCount();
	if($count>0){
		return "true";
	} else 
		return "false";
}
function check_nickname($nickname){
	require_once('connect.php');
	$Table_userinfo = table_userinfo;
	$stmt = $conn->prepare("SELECT NickName FROM $Table_userinfo WHERE NickName = ?");
	$stmt->execute(array($nickname));
	$count = $stmt->rowCount();
	if($count>0){
		return "true";
	} else 
		return "false";
}
function create_ac($email, $password, $nName, $gender, $birthdate){
	require_once('connect.php');
	require_once('userinfo.php');
	$Table_ac = table_ac;
	$Table_userinfo = table_userinfo;
	$datetimenow = date("Y-m-d H:i:s");

	$stmt = $conn->prepare("SELECT Email FROM $Table_ac WHERE Email = ?");
	$stmt->execute(array($email));
	$count = $stmt->rowCount();

	if($count==0){ // if There are not same email address in Account Table --> continue register
		$hashedPW = password_hash($password, PASSWORD_DEFAULT);
		$ac_sql = $conn->prepare("INSERT INTO $Table_ac(Email, Password, RegisterDate) VALUES(?, ?, ?)");
		$ac_sql->execute(array($email, $hashedPW, $datetimenow));

		$SQL_getUID = $conn->prepare("SELECT UID FROM $Table_ac WHERE Email = ?");
		$SQL_getUID->execute(array($email));
		while($row = $SQL_getUID->fetch()) {
			$Result_UID = $row['UID'];
		}
		$info_sql = $conn->prepare("INSERT INTO $Table_userinfo(UID , NickName, Gender, Birthdate, Country) VALUES(?, ?, ?, ?, ?)");
		$info_sql->execute(array($Result_UID, $nName, $gender, $birthdate, $country));
		switch($_COOKIE['language']){
			case "eng":
				print_r("Account Register Successfully");
			break;
			case "zh-tw":
				print_r("帳號註冊成功");
			break;
		}
	} else {
		switch($_COOKIE['language']){
			case "eng":
				print_r("This email has already been registered,\nplease enter another email");
			break;
			case "zh-tw":
				print_r("此電郵已被註冊，\n請輸入另一電郵");
			break;
		}
	}	
}

function login_ac($email, $pw){
	require_once('connect.php');
	session_start();
	$Table_ac = table_ac;
	$Table_userinfo = table_userinfo;
	$SQL_getPW_Hash = $conn->prepare("SELECT * FROM $Table_ac WHERE Email = ?");
	$SQL_getPW_Hash->execute(array($email));
	$count = $SQL_getPW_Hash->rowCount();
	if($count>0){
		while($row = $SQL_getPW_Hash->fetch()) {
			$Result_PW_Hash = $row['Password'];
			$Result_UID = $row['UID'];
		}
		if(password_verify($pw,$Result_PW_Hash)){
			$SQL_getACData = $conn->prepare("SELECT * FROM $Table_userinfo WHERE UID = ?");
			$SQL_getACData->execute(array($Result_UID));
			while($row = $SQL_getACData->fetch()) {
				$Result_Nickname = $row['NickName'];
				$Result_Gender = $row['Gender'];
				$Result_BD = $row['Birthdate'];
				$Result_Country = $row['Country'];
			}
			$_SESSION['UID']=$Result_UID;
			$_SESSION['email']=$email;
			$_SESSION['NickName']=$Result_Nickname;
			$_SESSION['Gender']=$Result_Gender;
			$_SESSION['Birthdate']=$Result_BD;
			$_SESSION['Country']=$Result_Country;
			//print_r($_SESSION['email']);
			//print_r($_SESSION['NickName']);
			//print_r($_SESSION['Gender']);
			//print_r($_SESSION['Birthdate']);
			//print_r($_SESSION['Country']);
			print_r("true");
		} else {
			switch($_COOKIE['language']){
				case "eng":
					print_r("Incorrect password");
				break;
				case "zh-tw":
					print_r("密碼錯誤");
				break;
		}
	}
}
else {
	switch($_COOKIE['language']){
		case "eng":
			print_r("Incorrect email");
		break;
		case "zh-tw":
			print_r("電子郵件錯誤");
		break;
	}
}
}

?>