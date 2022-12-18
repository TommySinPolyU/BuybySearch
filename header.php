<?php 
/* 
It is a comment part of header.php:
It is a header file for all page
Just include this file at the beginning of the website
Modify This file To apply all change of header
*/
?>
<?php
// Open a Session for store the value as a SESSION Variable
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if($_COOKIE['language']==""){
	setcookie("language", "eng", time() + (86400 * 30), "/");
	header('Location: '.$_SERVER['REQUEST_URI']);
}
include('includes/userinfo.php');
include('includes/ac.php'); // including account function content for handling the function of account
// Language Change Handler
// Receive a form data from lan_form and change the $_COOKIE['language'].
// $_COOKIE['language'] is a identifier for the content language
if(isset($_POST['lan_form_submitted'])){
	if($_POST['changeLanguage']=="Eng")
		setcookie("language", "eng", time() + (86400 * 30), "/");
	else if($_POST['changeLanguage']=="中")
		setcookie("language", "zh-tw", time() + (86400 * 30), "/");
	header('Location: '.$_SERVER['REQUEST_URI']);
}
// Get Unread Notification Count From DB
if(isset($_SESSION['NickName'])){
	require_once('includes/connect.php');
	$Table_notification = table_notification;
	$SQL_getNotification_UnreadCount = $conn->prepare("SELECT * FROM $Table_notification WHERE To_NickName = ? AND Status = ?");
	$SQL_getNotification_UnreadCount->execute(array($_SESSION['NickName'],"Unread"));
	$unread_msg_count = $SQL_getNotification_UnreadCount->rowCount();
} else {
	$unread_msg_count = 0;
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="css/style.css" />
	<link rel="stylesheet" href="css/navbar.css" />
	<link rel="stylesheet" href="css/mobile_navbar.css" />
	<link rel="stylesheet" href="css/calendar.css" />
	<link rel="stylesheet" href="css/slidershow_style.css" />
	<link rel="stylesheet" href="css/mobile_style.css" />
	<script type="text/javascript"src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<style>
	.button {
		border: none;
		color: white;
		text-align: center;
		font-size: 16px;
		-webkit-transition-duration: 0.4s; /* Safari */
		transition-duration: 0.4s;
		cursor: pointer;
	}
	.btngray {
		background-color: white;
		color: black;	
	}
	.btngray:hover {
		background-color: #555555;
		color: white;
	}
	#addvalue_form input[type=text],#addvalue_form input[type=email] {
		width: 100%;
		box-sizing: border-box;
		border: 2px solid #ccc;
		border-radius: 3px;
		font-size: 16px;
		background-color: white;
		background-position: 10px 10px; 
		background-repeat: no-repeat;
		padding: 12px 20px 12px 40px;
		-webkit-transition: width 0.4s ease-in-out;
		transition: width 0.4s ease-in-out;
	}
	</style>
</head>
<body>
<div id="container">
<div class="popupmsg-container" id="login-msg-container"> 
	<p id="login-msg" style="white-space: pre-line;margin-top: 120px;">Please Enter Again!</p>
	<div class="popupmsg-option-single"  id="login-msg-option-container">
		<button id="login-msg-confirm">OK</a>
	</div>
</div>
<!-- Custom Popup Message Bar  
Tutorial Reference from https://www.w3schools.com/howto/howto_css_modals.asp and https://www.youtube.com/watch?v=m-HmbHeVKo4 -->
<div class="popupmsg-container" id="login-container"> 
	<div id="login_form_div">
	<div class="form_column">
		<button id="login_close"><img src="images/close.png" height="38" width="38"/></button></br></br>
	</div>
		<form class="register_form-container" id="login_form">
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="email">電郵地址</label></div>
			<input type="text" placeholder="輸入電郵地址" name="email" id="email" required>
		</br>	
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div class="form_label"><label for="email">Email</label></div>
			<input type="text" placeholder="Enter Email" name="email" id="email" required>
		</br>	
		<? endif; ?>
		</div>
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="pwd">密碼</label></div>
			<input type="password" placeholder="輸入登入用密碼" name="pwd" id="pwd" required>
		</br>	
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div class="form_label"><label for="pwd">Password</label></div>
			<input type="password" placeholder="Enter Password" name="pwd" id="pwd" required>
		</br>	
		<? endif; ?>
		</div>
		<div class="popupmsg-option-single">
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				<button type="submit" id="login-submit">登入</a>	
			<? elseif ($_COOKIE['language']=="eng") : ?>
				<button type="submit" id="login-submit">Login</a>
			<? endif; ?>
		</div>
		</form>
	</div>
</div>
<div class="popupmsg-container" id="addvalue-container"> 
	<div id="addvalue_form_div">
	<div class="form_column">
		<button id="addvalue_close"><img src="images/close.png" height="38" width="38"/></button></br></br>
	</div>
	<br>
	<? if ($_COOKIE['language']=="zh-tw") : ?>
		<a>按以下按鈕進行充值<br>系統會進行人手核實，當核實完成後將會增加你的用戶點數。</a><br><br>
		<a style="color:red;">緊記在付款備註上留下你的登入帳號 (Email)</a><br><br>
		<button style="border:none;background-color:white;" id="paypal" onclick="window.open('https://www.paypal.me/buybusearchtest/')"><img width=50% src="images/paypal_icon.png"></button><br><br>
		<a style="color:red;">請注意：這只是一個示範，用作演示電子付款功能。<br>請勿支付任何款項</a>
	<? elseif ($_COOKIE['language']=="eng") : ?>
		<a> Press the following button to recharge <br> The system will perform manual verification.<br>After the verification is completed, your user points will be increased. </a><br><br>
		<a style="color:red;">Remember to leave your login account (Email) on the payment note</a><br><br>
		<button style="border:none;background-color:white;" id="paypal" onclick="window.open('https://www.paypal.me/buybusearchtest/')"><img width=50% src="images/paypal_icon.png"></button><br><br>
		<a style="color:red;">Please note: This is just a demonstration to demonstrate the electronic payment function.<br>Do not pay any money</a>
	<? endif; ?>
	</div>
	<br><br>
	<form class="register_form-container" id="addvalue_form">
		<div class="form_column">
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				<div class="form_label"><label for="add_TransactionID">請於下方輸入你已進行充值的交易ID</label></div>
				<input type="text" placeholder="輸入交易ID" name="add_TransactionID" id="add_TransactionID" required>
			</br>	
			<? elseif ($_COOKIE['language']=="eng") : ?>
				<div class="form_label"><label for="add_TransactionID">Please enter the transaction ID you have recharged below</label></div>
				<input type="text" placeholder="Enter TransactionID" name="add_TransactionID" id="add_TransactionID" required>
			</br>	
			<? endif; ?>
		</div>
		<div class="form_column">
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				<div class="form_label"><label for="add_PaypalAC">請於下方輸入你進行充值時所使用的Paypal帳號</label></div>
				<input type="email" placeholder="輸入Paypal帳號" name="add_PaypalAC" id="add_PaypalAC" required>
			</br>	
			<? elseif ($_COOKIE['language']=="eng") : ?>
				<div class="form_label"><label for="add_PaypalAC">Please enter the Paypal account you used when recharging below</label></div>
				<input type="email" placeholder="Enter Paypal Account" name="add_PaypalAC" id="add_PaypalAC" required>
			</br>	
			<? endif; ?>
		</div>
	<div class="form_column">
	<? if ($_COOKIE['language']=="zh-tw") : ?>
        <div class="form_label"><label for="receipt_img">請上傳交易證明: </label><br></div>
    <? elseif ($_COOKIE['language']=="eng") : ?>
        <div class="form_label"><label for="receipt_img">Please upload proof of transaction: </label><br></div>
    <? endif; ?>
	<input style="padding-left:10px" type="file" name="receipt_img[]" id="receipt_img" accept="image/png, image/jpeg" required><br><br>
	</div>
	<? if ($_COOKIE['language']=="zh-tw") : ?>
			<button class="green_btn" type="submit" id="submit_TransactionID">提交</button><br><br>
		</br>	
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<button class="green_btn" type="submit" id="submit_TransactionID">Submit</button><br><br>
		</br>	
		<? endif; ?>
	</form>
</div>
<!--  Begin of Header of Website (Top Menu Bar)	-->
<ul class="navbar" id="topnav">
	<li>
		<a class="logo" href="index.php">
			<img src="images/minilogo.png">
		</a>
	</li>
	<div class="logomenu">
		<li><a href="javascript:void(0);" class="leftmenu-icon" onclick="leftmenu()"><img src="images/menu-icon.png" height="38" width="38"/></a></li>
	</div>

	<div id="usermenu" class="usermenu">
	<li><button class="usermenu-close-icon" onclick="usermenu()"><img src="images/close.png" height="38" width="38"/></button></li>
	
	<li class="navbaritem-right usermenu-item">
	<div id="menu-userdata">
		<b style="font-size:16px;" id="usermenu_nickname"></b>
		<b style="font-size:12px;" id="usermenu_email"></b>
	</div>
		<table>
			<tr>
				<td width=100%><b style="padding-left:5px;font-size:12px;color:white;" id="usermenu_coins"></b></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width=50%><button style="width:100%;color:white;background-color:black;" id="usermenu_addcoins"></button></td>
				<td width=50%><button style="width:100%;color:white;background-color:black;" id="check_TransactionRecord">
				<? if ($_COOKIE['language']=="zh-tw") : ?>  增值記錄 
				<? elseif ($_COOKIE['language']=="eng") : ?> Rechange Records 
				<? endif; ?>
				</button></td>
			</tr>
		</table>

			<? if ($_COOKIE['language']=="zh-tw") : ?>
				<a class="navbar-text-right" id="loginbtn" href="javascript:void(0);">登入</a>
			<? elseif ($_COOKIE['language']=="eng") : ?>
				<a class="navbar-text-right" id="loginbtn" href="javascript:void(0);">Login</a>
			<? endif; ?>
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				<a class="navbar-text-right" id="regbtn" href="register.php">註冊</a>
			<? elseif ($_COOKIE['language']=="eng") : ?>
				<a class="navbar-text-right" id="regbtn" href="register.php">Register</a>
			<? endif; ?>
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				<a class="navbar-text-right" id="mypostbtn" href="mypost.php">我的發文</a>
			<? elseif ($_COOKIE['language']=="eng") : ?>
				<a class="navbar-text-right" id="mypostbtn" href="mypost.php">My&nbsp;Post</a>
			<? endif; ?>
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				<a class="navbar-text-right" id="postpagebtn" href="post.php">發佈新東西</a>
			<? elseif ($_COOKIE['language']=="eng") : ?>
				<a class="navbar-text-right" id="postpagebtn" href="post.php">Post&nbsp;New&nbsp;Thing</a>
			<? endif; ?>
			<form style="margin:0px" id="logout_form">
				<? if ($_COOKIE['language']=="zh-tw") : ?>
					<button style="background-color:black;border:none;" type="submit" class="navbar-text-right" id="logoutbtn">登出</button>
				<? elseif ($_COOKIE['language']=="eng") : ?>
					<button style="background-color:black;border:none;" type="submit" class="navbar-text-right" id="logoutbtn">Logout</button>
				<? endif; ?>
			</form>
		</li>
	</div>
	
	<li style="float:right;"><a href="javascript:void(0);" class="usermenu-icon" onclick="usermenu()"><img src="images/user_bgwhite.png"/></a></li>
	<li style="float:right;"><a id="cart-icon" href="cart.php"><img src="images/cart_white.png"/></a></li>
	<li style="float:right;">
	<div id="notification_icon" style="position: relative;text-align: center;color: white;">
		<a id="notify-icon" href="notification.php">
			<img src="images/notification.png"/>
			<div style="background:black;color:white;opacity:0.75;position: absolute;bottom: 0px;right: -4px;"><?php echo $unread_msg_count ?></div>
		</a>
	</div></li>
	<li class="lanregion">
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				地區: 
			<? elseif ($_COOKIE['language']=="eng") : ?>
				Region: 
			<? endif; ?> <?php echo $country ?>
			<form style="margin:0px" action="" method="post">
				<input type="hidden" name="lan_form_submitted" value="1" />
				<input type="submit" name="changeLanguage" class="button btngray" value="Eng" />
				<input type="submit" name="changeLanguage" class="button btngray" value="中" />
			</form>
	</li>
		<div class="leftmenu" id="leftmenu">
		<li><button class="leftmenu-close-icon" id="leftmenu-close-icon" style="display:none;" onclick="leftmenu()"><img src="images/close.png" height="38" width="38"/></button></li>
			<li class="navbaritem leftmenu-item">
				<a class="navbar-text" href="about-us.php"> 
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						關於我們
					<? elseif ($_COOKIE['language']=="eng") : ?>
						Abort&nbsp;Us
					<? endif; ?>
				</a>
			</li>
			<li class="navbaritem leftmenu-item">
				<a class="navbar-text" onclick="submit_search_type('Service')"> 
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						服務
					<? elseif ($_COOKIE['language']=="eng") : ?>
						Service
					<? endif; ?>
				</a>
			</li>
			<li class="navbaritem leftmenu-item">
				<a class="navbar-text" onclick="submit_search_type('Product')"> 
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						產品
					<? elseif ($_COOKIE['language']=="eng") : ?>
						Product
					<? endif; ?>
				</a>
			</li>
			<li class="navbaritem leftmenu-item">
				<a class="navbar-text" onclick="submit_search_type('Information')"> 
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						資訊
					<? elseif ($_COOKIE['language']=="eng") : ?>
						Information
					<? endif; ?>
				</a>
			</li></br>

	</div>
</ul>
</br></br>
<hr class="style-two" />
<!-- Search Bar -->
<div id="search_again_container" style="display:block;">
          <form style=" text-align: center;width: 100%;" id="searchbar" method="POST" action="search_result.php">
              <input type="hidden" name="search_request_send" value="1"/>
              <? if ($_COOKIE['language']=="zh-tw") : ?>
                    <select id="search_type" name="search_type" onchange="searchtext_name_change(this.value)">
                      <option value="">請選擇搜尋方式</option>
                      <option value="SearchBy_ID">Post ID</option>
                      <option value="SearchBy_Type">分類</option>
                      <option value="SearchBy_UID">用戶編號</option>
                      <option value="SearchBy_Keywords">關鍵詞</option>
                    </select>
                    <input type="text" id="search_text" name="search" placeholder="尋找.." required>
                    <select id="searchby_type_selection" name="searchby_type_selection">
                      <option value="">請選擇類型</option>
                      <option value="Product">產品</option>
                      <option value="Information">資訊</option>
                      <option value="Service">服務</option>
                    </select>
                    <button type="submit">搜尋</button>
              <? elseif ($_COOKIE['language']=="eng") : ?> 
                    <select id="search_type" name="search_type" onchange="searchtext_name_change(this.value)">
                      <option value="">Search By</option>
                      <option value="SearchBy_ID">Post ID</option>
                      <option value="SearchBy_Type">Type</option>
                      <option value="SearchBy_UID">User ID</option>
                      <option value="SearchBy_Keywords">Keywords</option>
                    </select>
                    <input type="text" id="search_text" name="search" placeholder="Search.." required>
                    <select id="searchby_type_selection" name="searchby_type_selection">
                      <option value="">Select Category</option>
                      <option value="Product">Product</option>
                      <option value="Information">Infomation</option>
                      <option value="Service">Service</option>
                    </select>
                    <button type="submit">Search</button>
              <? endif; ?>
          </form>    
</div>
<!--  End of Header of Website (Top Menu Bar)	-->
<script>
// NavBar Script - Tommy
document.getElementById('searchby_type_selection').style.display="none";

/* 
Window Onload by using $(document).ready
This Part is handle all initialization of page load.
*/

$( document ).ready(function() {
	<? if (!isset($_SESSION['email'])) : ?>
	// No Any User Logged-in, Display the guest content at user menu. and hide all content for logged-in user.
		document.getElementById("regbtn").style.display="block";
		document.getElementById("loginbtn").style.display="block";
		document.getElementById("logoutbtn").style.display="none";
		document.getElementById("postpagebtn").style.display="none";
		document.getElementById("mypostbtn").style.display="none";
		document.getElementById("cart-icon").style.display="none";
		document.getElementById("notify-icon").style.display="none";
		document.getElementById("usermenu_addcoins").style.display="none";
		document.getElementById("usermenu_coins").style.display="none";
		document.getElementById("check_TransactionRecord").style.display="none";
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			document.getElementById("usermenu_nickname").innerHTML="遊客";
			document.getElementById("usermenu_email").innerHTML="請登入你的帳號\n以使用購物服務";
		<? elseif ($_COOKIE['language']=="eng") : ?>
			document.getElementById("usermenu_nickname").innerHTML="Guest";
			document.getElementById("usermenu_email").innerHTML="Please login to your account\nto use the shopping service";
		<? endif; ?>
	<? else : ?>
	// When User is login already, Display the logged-in user content at user menu. and hide all content for guest.
		document.getElementById("regbtn").style.display="none";
		document.getElementById("loginbtn").style.display="none";
		document.getElementById("logoutbtn").style.display="block";
		document.getElementById("postpagebtn").style.display="block";
		document.getElementById("mypostbtn").style.display="block";
		document.getElementById("cart-icon").style.display="block";
		document.getElementById("notify-icon").style.display="block";
		document.getElementById("usermenu_nickname").innerHTML="<?php Print($_SESSION['NickName']); ?>";
		document.getElementById("usermenu_email").innerHTML="<?php Print($_SESSION['email']); ?>";
		document.getElementById("usermenu_addcoins").style.display="";
		document.getElementById("usermenu_coins").style.display="";
		document.getElementById("check_TransactionRecord").style.display="";
		// Get User Points from server
		$.ajax({
			url: 'includes/ac.php',
			type: 'POST',
			data: 
			{
				process:"Update_Coins"
			},
			success: function(result) {	
				<? if ($_COOKIE['language']=="zh-tw") : ?>
					document.getElementById("usermenu_coins").innerHTML="用戶點數: "+result;
					document.getElementById("usermenu_addcoins").innerHTML="增值";
				<? elseif ($_COOKIE['language']=="eng") : ?>
					document.getElementById("usermenu_coins").innerHTML="User Points: "+result;
					document.getElementById("usermenu_addcoins").innerHTML="Recharge";
				<? endif; ?>
			}
		});
	<? endif; ?>
});
var isMobile = window.matchMedia("only screen and (max-width: 1000px)").matches;
var activebar_right = false;
var activebar_left = false;
// Auto Check if browser resolution / screen size changed.
// Convert the page style between mobile and PC / High Resolution Device
$(window).resize(function(){
	isMobile = window.matchMedia("only screen and (max-width: 1000px)").matches;
    if (!isMobile) {
		var y = document.getElementsByClassName("leftmenu-item");
		for (i = 0; i < y.length; i++) {
			y[i].style.display = "block";
		}
		if(activebar_left){
			activebar_left=false;
			document.getElementById("cover").style.display="none";
		}
		document.getElementById("leftmenu").style.width = '100%';
		document.getElementById("mainbody").style.marginLeft = "0";
		document.getElementById("mainbody").style.marginRight = "0";
		document.getElementById("leftmenu-close-icon").style.display="none";
    } else if (isMobile && !activebar_left) {
		document.getElementById("leftmenu").style.width = '0';
		document.getElementById("leftmenu-close-icon").style.display="none";
	} 
	if(!isMobile && activebar_right){
		document.getElementById("mainbody").style.marginRight = "180px";
		document.getElementById("cover").style.display="block";
	}
});

/* 
LeftMenu
Controller of left menu
This Function will called when user click on the left menu button
(Only For Mobile Version)
*/

function leftmenu() {
	var x = document.getElementById("leftmenu");
	var y = document.getElementsByClassName("leftmenu-item");
	if(activebar_right){
		usermenu();
	}
	if (x.className === "leftmenu") {
		if(activebar_left===false){
			for (i = 0; i < y.length; i++) {
				y[i].style.display = "none";
			}
			activebar_left=true;
			document.getElementById("leftmenu").style.width = "180px";
			document.getElementById("mainbody").style.marginLeft = "180px";
			document.getElementById("cover").style.display="block";	
			setTimeout(function (){
				for (i = 0; i < y.length; i++) {
					y[i].style.display = "block";
				}
				document.getElementById("leftmenu-close-icon").style.display="block";
			}, 400);
		} else {
				for (i = 0; i < y.length; i++) {
					y[i].style.display = "none";
					document.getElementById("leftmenu-close-icon").style.display="none";
				}
				activebar_left=false;
				document.getElementById("cover").style.display="none";
				document.getElementById("leftmenu").style.width = "0";
				document.getElementById("mainbody").style.marginLeft = "0";

		}
	} 
}

/* 
UserMenu
Controller of user menu
This Function will called when user click on the user menu button at the top-right of webpage
*/

function usermenu() {
  var x = document.getElementById("usermenu");
  var y = document.getElementsByClassName("usermenu-item");
  if(activebar_left){
		leftmenu();
	}
	if (x.className === "usermenu") {
		if(activebar_right===false){
			for (i = 0; i < y.length; i++) {
  				y[i].style.display = "none";
			}
			activebar_right=true;
			document.getElementById("usermenu").style.width = "180px";
			if (isMobile) {
				document.getElementById("mainbody").style.marginRight = "180px";
			}
			setTimeout(function (){
			for (i = 0; i < y.length; i++) {
				y[i].style.display = "block";
			}
			}, 400);
			document.getElementById("cover").style.display="block";
		} else {
			for (i = 0; i < y.length; i++) {
  				y[i].style.display = "none";
			}
			activebar_right=false;
			document.getElementById("cover").style.display="none";
			document.getElementById("usermenu").style.width = "0";
			document.getElementById("mainbody").style.marginRight = "0";
  		}
  } 
}

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    topbtn.style.display = "block";
  } else {
    topbtn.style.display = "none";
  }
}

$('#loginbtn').each(function(){
    $(this).click(function(){ 
		$('#msg-cover').fadeIn('slow');
		$('#login-container').fadeIn('slow');
    });
});

$('#usermenu_addcoins').each(function(){
    $(this).click(function(){ 
		$('#msg-cover').fadeIn('slow');
		$('#addvalue-container').fadeIn('slow');
    });
});

/*Custom Popup Message Bar  
Tutorial Reference from https://www.w3schools.com/howto/howto_css_modals.asp and https://www.youtube.com/watch?v=m-HmbHeVKo4 */
$('#login-msg-confirm').each(function(){
    $(this).click(function(){ 
		$('#msg-cover').fadeOut('slow');
		$('#login-container').fadeOut('slow');
		$('#login-msg-container').fadeOut('slow');
    });
});

$('#check_TransactionRecord').each(function(){
    $(this).click(function(){ 
		location.href = 'check_addvalue_records.php';
    });
});

$('#login_close').each(function(){
    $(this).click(function(){ 
		$('#msg-cover').fadeOut('slow');
		$('#login-container').fadeOut('slow');
		$('#login-msg-container').fadeOut('slow');
    });
});

$('#addvalue_close').each(function(){
    $(this).click(function(){ 
		$('#msg-cover').fadeOut('slow');
		$('#addvalue-container').fadeOut('slow');
		$('#addvalue-msg-container').fadeOut('slow');
    });
});

/* 
|logout_form_submit|
Handle Logout Part
It will send the Logout order to server and clear all user data which once stored by $_SESSION
*/

$('#logout_form').submit(function () {
	var result = { };
	$.each($('form').serializeArray(), function() {
    	result[this.name] = this.value;
	});
	$.ajax({
		url: 'includes/ac.php',
		type: 'POST',
		data: {process:"Logout"},
		success: function(data) {
			location.href = 'index.php';
		}
	});
 return false;
});

$('#addvalue_form').submit(function () {
	var result = { };
	$.each($('form').serializeArray(), function() {
    	result[this.name] = this.value;
	});
	$('#addvalue_close').click();
	$.ajax({
		url: 'includes/ac.php',
		type: 'POST',
		data: {
			process:"Send_TransactionID",
			Trans_ID:result.add_TransactionID,
			Trans_Email:result.add_PaypalAC
			},
		success: function(result) {
			if(result == "Success"){
				document.getElementById("login-msg-option-container").style.display="";
				<? if ($_COOKIE['language']=="zh-tw") : ?>
					$('#login-msg').text("已成功提交資料，請等待我們進行確認，需時大約15至30分鐘\n若過時請聯絡我們");
				<? elseif ($_COOKIE['language']=="eng") : ?>
					$('#login-msg').text("Successfully Submitted, Please wait for us to confirm, it will take about 15-30 minutes.\nIf it is out of time, please contact us");
				<? endif; ?>
			} else if (result == "Fail") {
				document.getElementById("login-msg-option-container").style.display="";
				<? if ($_COOKIE['language']=="zh-tw") : ?>
					$('#login-msg').text("提交失敗！\n\n此帳號累積未處理申請已達 5 項，請耐心等侯\n系統將會盡快完成你所提交的申請");
				<? elseif ($_COOKIE['language']=="eng") : ?>
					$('#login-msg').text("Submission Failed!\n\nThis account has accumulated 5 outstanding applications, please wait patiently\nThe system will complete your application as soon as possible");
				<? endif; ?>
			}
			upload_payment_image();
		}
	});
 return false;
});

function upload_payment_image(){
  var form_data = new FormData();
  // Read selected files
  var totalfiles = document.getElementById('receipt_img').files.length;
  for (var index = 0; index < totalfiles; index++) {
    form_data.append("files[]", document.getElementById('receipt_img').files[index]);
  }
  //AJAX upload pics
        $.ajax({
        url: 'includes/transaction_upload.php', 
        type: 'post',
        data: form_data,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {
			$('#login-msg-container').fadeIn('slow');
			$('#login-container').fadeOut('slow');
      }
    });
}

/* 
|login_form_submit|
Handle The Submition of Login Form
It will send the data to server for Verification of User Account and Password.
If Login Successful, The User Data will saved in $_SESSION Variables
*/

$('#login_form').submit(function () {
	var result = { };
	$.each($('form').serializeArray(), function() {
    	result[this.name] = this.value;
	});
	$.ajax({
		url: 'includes/ac.php',
		type: 'POST',
		data: {login_email:result.email, login_pw:result.pwd, process:"Login_Checking"},
		success: function(login_vaild) {
			if(login_vaild=="true"){
				document.getElementById("login-msg-option-container").style.display="none";
				<? if ($_COOKIE['language']=="zh-tw") : ?>
					$('#login-msg').text("登入成功");
				<? elseif ($_COOKIE['language']=="eng") : ?>
					$('#login-msg').text("Login Success");
				<? endif; ?>
				window.setTimeout(function(){location.href = 'index.php';},1000)
			}
			else{
				document.getElementById("login-msg-option-container").style.display="block";
				document.getElementById("login-msg").innerHTML = login_vaild;
			}
			document.getElementById("login_form").reset();
		}
	});
	$('#login-msg-container').fadeIn('slow');
	$('#login-container').fadeOut('slow');
 return false;
});
// NavBar Script - Tommy
</script>




</body>
</html>