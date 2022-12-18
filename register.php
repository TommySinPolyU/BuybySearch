<html>
<head><title>BuyBySearch - Register</title>
</head>
<body>
<?php include_once('header.php') ?>
<?php

?>
<!--  Begin of Content (Body)	-->
<div id="register_form_div">
	<form class="register_form-container" id="register_form">
		<input type="hidden" name="register_form_submitted" value="1"/>
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div style="background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>註冊帳號</h1></div>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div style="background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>Register</h1></div>
		<? endif; ?>
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<h3>帳號資料部份</h3>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<h3>Account Information</h3>
		<? endif; ?>
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="register_email">電郵地址</label></div>
			<input type="text" placeholder="輸入電郵地址" name="register_email" id="register_email" required			
			oninvalid="this.setCustomValidity('請輸入電郵地址')"
    		oninput="this.setCustomValidity('')"
			onfocusout="register_ac_check_Repetitive(this.value)">
			<div style="font-size:13px;padding-top:5px;">電郵地址為你登入所用的帳號 (建議使用Gmail)</div>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<label for="register_email"><b>Email</b></label>
			<input type="text" placeholder="Enter Email" name="register_email" id="register_email" required
			oninvalid="this.setCustomValidity('Please Enter Your Email')"
    		oninput="this.setCustomValidity('')"
			onfocusout="register_ac_check_Repetitive(this.value)">	
			<div style="font-size:13px;padding-top:5px;">Email is your login account (We recommend using Gmail)</div>
		<? endif; ?>
		<div id="emailcheck_confirm" style="font-size:13px;padding-top:5px;color:red;"></div>
		</div>
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="pwd">密碼</label></div>
			<input type="password" placeholder="輸入登入用密碼" name="register_pwd" id="register_pwd" required
			oninvalid="this.setCustomValidity('請輸入帳號密碼')"
			oninput="register_pwd_change(this.value)">
			<div id="pwdcheck">
			<div style="font-size:13px;padding-top:5px;">密碼必須符合以下條件: </div>
				<ui style="font-size:11px;list-style-type:none;">
					<li id="pwdcheck_length" style="color:red;">&emsp;&emsp;密碼長度需介乎8~20位元</li>
					<li id="pwdcheck_uppercase" style="color:red;">&emsp;&emsp;必須含有大寫字母</li>
					<li id="pwdcheck_lowercase" style="color:red;">&emsp;&emsp;必須含有小寫字母</li>
					<li id="pwdcheck_numbers" style="color:red;">&emsp;&emsp;必須含有數字</li>
					<li id="pwdcheck_sc" style="color:red;">&emsp;&emsp;必須含有至少一個括號內的特殊符號(!,*,%,-,+,/)</li>
				</ui>
			</div>
		</br>	
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div class="form_label"><label for="pwd">Password</label></div>
			<input type="password" placeholder="Enter Password" name="register_pwd" id="register_pwd" required
			oninvalid="this.setCustomValidity('Please Enter Account Password')"
			oninput="register_pwd_change(this.value)">
			<div id="pwdcheck">
				<div style="font-size:13px;padding-top:5px;">The password must meet the following conditions: </div>
				<ui style="font-size:11px;list-style-type:none;">
					<li id="pwdcheck_length" style="color:red;">&emsp;&emsp;Password length must be between 8 and 20 digits</li>
					<li id="pwdcheck_uppercase" style="color:red;">&emsp;&emsp;Must contain uppercase letters</li>
					<li id="pwdcheck_lowercase" style="color:red;">&emsp;&emsp;Must contain lowercase letters</li>
					<li id="pwdcheck_numbers" style="color:red;">&emsp;&emsp;Must contain numbers</li>
					<li id="pwdcheck_sc" style="color:red;">&emsp;&emsp;Must contain at least one special symbol in parentheses(!,*,%,-,+,/)</li>
				</ui>
			</div>
		</br>	
		<? endif; ?>
		</div>
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="confirmpwd">確認密碼</label></div>
			<input type="password" placeholder="再次輸入登入用密碼" name="confirmpwd" id="confirmpwd" disabled required
			oninvalid="this.setCustomValidity('請再次輸入與上欄相同的密碼')"
			oninput="register_confirmpwd_change(this.value)">
			<div id="pwdcheck_confirm" style="font-size:13px;padding-top:5px;color:red;">&emsp;</div>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div class="form_label"><label for="confirmpwd">Confirmation Password</label></div>
			<input type="password" placeholder="Confirmation Password" name="confirmpwd" id="confirmpwd" disabled required
			oninvalid="this.setCustomValidity('Please Enter Account Password Again')"
			oninput="register_confirmpwd_change(this.value)">
			<div id="pwdcheck_confirm" style="font-size:13px;padding-top:5px;color:red;">&emsp;</div>
		<? endif; ?>
		</div>
		<hr class="style-two" />
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<h3>用戶檔案部份</h3>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<h3>User Profile</h3>
		<? endif; ?>
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="nickname">暱稱</label></div>
			<input type="text" placeholder="輸入暱稱" name="nickname" required
			oninvalid="this.setCustomValidity('請輸入你的用戶名稱')"
    		oninput="this.setCustomValidity('')"
			onfocusout="register_nickname_check(this.value)">	
			<div style="font-size:13px;padding-top:5px;">此暱稱將會公開顯示於用戶檔案中<br><a style="color:red;">注意：暱稱無法修改，請慎重考慮你的暱稱</a></div>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div class="form_label"><label for="nickname">Nickname</label></div>
			<input type="text" placeholder="Enter Nickname" name="nickname" required
			oninvalid="this.setCustomValidity('Please Enter Your Nickname')"
    		oninput="this.setCustomValidity('')"
			onfocusout="register_nickname_check(this.value)">
			<div style="font-size:13px;padding-top:5px;">This nickname will be publicly displayed in the profile.<br><a style="color:red;">Note that the nickname cannot be modified, please consider your nickname carefully</a></div>
		<? endif; ?>
		<div id="nicknamecheck_confirm" style="font-size:13px;padding-top:5px;color:red;"></div>
		</div>
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="gender">性別</label></div>
			<select name="gender" required>
				<option value="M">男</option>
				<option value="F">女</option>
				<option value="Other">其他</option>
			</select>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div class="form_label"><label for="gender">Gender</label></div>
			<select name="gender" required>
				<option value="M">Male</option>
				<option value="F">Female</option>
				<option value="Other">Other</option>
			</select>
		<? endif; ?>
		</div>
		<div class="form_column">
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<div class="form_label"><label for="birthdate">出生日期</label></div>
			<input type="date" name="birthdate" id="birthdate" required>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<div class="form_label"><label for="birthdate">Birth Date</label></div>
			<input type="date" name="birthdate" id="birthdate" required>
		<? endif; ?></br></br>
		</div>
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			<button type="submit" id="register_form_submit"><b>提交</b></button>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<button type="submit" id="register_form_submit"><b>Submit</b></button>
		<? endif; ?>
  </form>
</div>
<div id="mainbody">
<div id="cover"></div>
<div class="popupmsg-container" style="width: 400px;height: 180px; margin-top: -90px;margin-left: -200px;" id="register-msg-container"> 
	<p id="register-msg" style="white-space: pre-line">Please Enter Again!</p>
	<div class="popupmsg-option-single" id="register-option-container">
		<a id="register-msg-confirm" href="javascript:void(0);">OK</a>
	</div>
</div>
</div>
<!--  End of Content (Body)	-->

<?php include_once('footer.php') ?>
<!-- NavBar Script - Tommy -->
<script>
document.getElementById('search_again_container').style.display="none";
var register_pwd_vaild = false;
var register_ac_vaild = false;
var email_format_vaild = false;
var nickname_vaild = false;
/* 
|register-msg-confirm:|
Close Msg Box by click confirm button.
*/
$('#register-msg-confirm').each(function(){
    $(this).click(function(){ 
        $('#cover').fadeOut('slow');
		$('#register-msg-container').fadeOut('slow');
    });
});
/* 
|register_pwd_change:|
Check The Password is / is not match each password condition.
Active when user type any character at Password Box.
*/
function register_pwd_change(change){
		register_confirmpwd_change(document.getElementById("confirmpwd").value);
		var pwd_regex_length = /^.{8,20}$/g;
		var pwd_regex_lower= /^(?=.*[a-z]).{1,}$/g;
		var pwd_regex_upper= /^(?=.*[A-Z]).{1,}$/g;
		var pwd_regex_digit= /^(?=.*\d).{1,}$/g;
		var pwd_regex_sc= /^(?=.*[\!\*\%\-\+\/]).{1,}$/g;
		var pwd_vaildation_length = change.toString().match(pwd_regex_length);
		var pwd_vaildation_lower = change.toString().match(pwd_regex_lower);
		var pwd_vaildation_upper = change.toString().match(pwd_regex_upper);
		var pwd_vaildation_digit = change.toString().match(pwd_regex_digit);
		var pwd_vaildation_sc = change.toString().match(pwd_regex_sc);
		document.getElementById("pwd").setCustomValidity('');
		if (pwd_vaildation_length){
			document.getElementById("pwdcheck_length").style.color = "#63B64A";
		} else {
			document.getElementById("pwdcheck_length").style.color = "#FF0000";
		}
		if (pwd_vaildation_lower){
			document.getElementById("pwdcheck_lowercase").style.color = "#63B64A";
		} else {
			document.getElementById("pwdcheck_lowercase").style.color = "#FF0000";
		}
		if (pwd_vaildation_upper){
			document.getElementById("pwdcheck_uppercase").style.color = "#63B64A";
		} else {
			document.getElementById("pwdcheck_uppercase").style.color = "#FF0000";
		}
		if (pwd_vaildation_digit){
			document.getElementById("pwdcheck_numbers").style.color = "#63B64A";
		} else {
			document.getElementById("pwdcheck_numbers").style.color = "#FF0000";
		}
		if (pwd_vaildation_sc){
			document.getElementById("pwdcheck_sc").style.color = "#63B64A";
		} else {
			document.getElementById("pwdcheck_sc").style.color = "#FF0000";
		}
		if(pwd_vaildation_length&&pwd_vaildation_lower&&pwd_vaildation_upper&&pwd_vaildation_digit&&pwd_vaildation_sc){
			document.getElementById("confirmpwd").disabled = false;
			register_pwd_vaild = true;
		} else {
			document.getElementById("confirmpwd").disabled = true;
			register_pwd_vaild = false;
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				document.getElementById("pwdcheck_confirm").innerHTML = "密碼未能符合所有條件，請重新輸入";
			<? elseif ($_COOKIE['language']=="eng") : ?>
				document.getElementById("pwdcheck_confirm").innerHTML = "The password did not meet all the conditions, please re-enter";
			<? endif; ?>
		}
	if(!change){
		document.getElementById("pwdcheck_confirm").innerHTML = "";
		document.getElementById("pwdcheck_confirm").style.color = "#FF0000";;
	}
}
/* 
|register_confirmpwd_change:|
Check The Confirmation Password is / is not same as Password.
Active when user type any character at Confimation Password Box.
*/
function register_confirmpwd_change(change){
	var pwd = document.getElementById("register_pwd").value;
	document.getElementById("pwdcheck_confirm").innerHTML=pwd;
	if(pwd){
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			document.getElementById("pwdcheck_confirm").innerHTML = "請再次輸入與上欄相同的密碼";
		<? elseif ($_COOKIE['language']=="eng") : ?>
			document.getElementById("pwdcheck_confirm").innerHTML = "Please enter the same password as above.";
		<? endif; ?>
		var confirmpwd = document.getElementById("confirmpwd").value;
		document.getElementById("confirmpwd").setCustomValidity('');
		if (pwd===confirmpwd){
			document.getElementById("pwdcheck_confirm").style.color = "#63B64A";
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				document.getElementById("pwdcheck_confirm").innerHTML = "密碼檢查已通過";
			<? elseif ($_COOKIE['language']=="eng") : ?>
				document.getElementById("pwdcheck_confirm").innerHTML = "Password check passed.";
			<? endif; ?>
		} else {
			document.getElementById("pwdcheck_confirm").style.color = "#FF0000";
		}
	} else {
		document.getElementById("pwdcheck_confirm").style.color = "#FF0000";
	}
}
/* 
|register_ac_check_Repetitive:|
Check Email has or has not been register.
Active when User Enter the email and out focus from Email Input Field.
*/
function register_ac_check_Repetitive(email){
	var email_regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	email_format_vaild = email_regex.test(email);
	if(!email_format_vaild){
		register_ac_vaild=false;
		document.getElementById("emailcheck_confirm").style.color = "#FF0000";
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			document.getElementById("emailcheck_confirm").innerHTML = "電郵格式錯誤，請重新輸入";
		<? elseif ($_COOKIE['language']=="eng") : ?>
			document.getElementById("emailcheck_confirm").innerHTML = "Email is not vaild, please re-enter";
		<? endif; ?>
	} else {
		$.ajax({
			url: 'includes/ac.php',
			type: 'POST',
			data: {register_email:email, process:"Register_CheckEmail"},
			success: function(reg_checkac_result) {
				if(reg_checkac_result==="true"){
					register_ac_vaild=false;
					document.getElementById("emailcheck_confirm").style.color = "#FF0000";
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						document.getElementById("emailcheck_confirm").innerHTML = "此電郵已被註冊，請輸入另一電郵";
					<? elseif ($_COOKIE['language']=="eng") : ?>
						document.getElementById("emailcheck_confirm").innerHTML = "This email has already been registered,\nplease enter another email.";
					<? endif; ?>
				} else {
					register_ac_vaild=true;
					document.getElementById("emailcheck_confirm").style.color = "#63B64A";
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						document.getElementById("emailcheck_confirm").innerHTML = "此電郵未被註冊，您可以使用此電郵進行登記";
					<? elseif ($_COOKIE['language']=="eng") : ?>
						document.getElementById("emailcheck_confirm").innerHTML = "This email has not been registered.\nYou can use this email to register.";
					<? endif; ?>
				}
			}
		});
	}
}
function register_nickname_check(NickName){
	var nickname_length_regax= /^.{4,16}$/g;
	var spec_char_regex_sc= /^(?=.*[\!\*\%\-\+\/ ]).{1,}$/g;
	nickname_length_vaild = nickname_length_regax.test(NickName);
	nickname_spec_char_contains = spec_char_regex_sc.test(NickName);
	if(nickname_length_vaild && !nickname_spec_char_contains){
		$.ajax({
			url: 'includes/ac.php',
			type: 'POST',
			data: {register_nName:NickName, process:"Register_CheckNickname"},
			success: function(reg_checknName_result) {
				if(reg_checknName_result==="true"){
					nickname_vaild = false;
					document.getElementById("nicknamecheck_confirm").style.color = "#FF0000";
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						document.getElementById("nicknamecheck_confirm").innerHTML = "此暱稱已被使用，請重新輸入";
					<? elseif ($_COOKIE['language']=="eng") : ?>
						document.getElementById("nicknamecheck_confirm").innerHTML = "This nickname is already used, please re-enter。";
					<? endif; ?>
				} else {
					nickname_vaild = true;
					document.getElementById("nicknamecheck_confirm").style.color = "#63B64A";
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						document.getElementById("nicknamecheck_confirm").innerHTML = "此暱稱未被使用，你可以使用此暱稱";
					<? elseif ($_COOKIE['language']=="eng") : ?>
						document.getElementById("nicknamecheck_confirm").innerHTML = "This nickname is not used.\nYou can use this nickname.";
					<? endif; ?>
				}
			}
		});
	} else {
		nickname_vaild = false;
		document.getElementById("nicknamecheck_confirm").style.color = "#FF0000";
		document.getElementById("nicknamecheck_confirm").innerHTML = "";
		if(!nickname_length_vaild){
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				document.getElementById("nicknamecheck_confirm").innerHTML += "暱稱必須介乎 4 ~ 16 個字<br>"
			<? elseif ($_COOKIE['language']=="eng") : ?>
				document.getElementById("nicknamecheck_confirm").innerHTML += "Nickname must be between 4 and 16 characters<br>";
			<? endif; ?>
		}
		if(nickname_spec_char_contains){
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				document.getElementById("nicknamecheck_confirm").innerHTML += "暱稱不能含有特殊字元及空格 (!,*,%,-,+,/)"
			<? elseif ($_COOKIE['language']=="eng") : ?>
				document.getElementById("nicknamecheck_confirm").innerHTML += "Nickname cannot contain special characters and space (!, *,%,-, +, /)";
			<? endif; ?>
		}
	}
}
/* 
|register_form submit:|
Handle the submition of register form
*/
$('#register_form').submit(function () {
	var result = { };
	$.each($('form').serializeArray(), function() {
    	result[this.name] = this.value;
	});
	if (register_pwd_vaild){
		if(result.register_pwd!=result.confirmpwd){
			<? if ($_COOKIE['language']=="zh-tw") : ?>
				$('#register-msg').text("密碼與確認密碼不相符\n請重新輸入");
			<? elseif ($_COOKIE['language']=="eng") : ?>
				$('#register-msg').text("The password does not match the confirmation password.\nPlease re-enter");
			<? endif; ?>
		} else {
			if(register_ac_vaild===true){
				if(nickname_vaild){
					document.getElementById("register-option-container").style.display="none";
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						$('#register-msg').text("請稍等\n正在進行註冊程序");
					<? elseif ($_COOKIE['language']=="eng") : ?>
						$('#register-msg').text("Please wait\nThe registration process is in progress");
					<? endif; ?>
					$.ajax({
						url: 'includes/ac.php',
						type: 'POST',
						data: 
						{
							process:"Register_Success",
							register_email:result.register_email,
							register_pw:result.register_pwd,
							register_nName:result.nickname,
							register_gender:result.gender,
							register_birthdate:result.birthdate
						},
						success: function(reg_checkac_result) {	
							document.getElementById("register-option-container").style.display="block";
							$('#register-msg').text(""+reg_checkac_result);
							document.getElementById("register_form").reset();
							register_pwd_vaild = false;
							register_ac_vaild = false;
							email_format_vaild = false;
							nickname_vaild = false;
							register_pwd_change("");
							register_ac_check_Repetitive("");
							document.getElementById("emailcheck_confirm").innerHTML = "";
						}
					});
				} else {
					<? if ($_COOKIE['language']=="zh-tw") : ?>
						$('#register-msg').text("暱稱未能符合條件，請重新輸入");
					<? elseif ($_COOKIE['language']=="eng") : ?>
						$('#register-msg').text("Nickname is not vaild\nplease re-enter");
					<? endif; ?>
				}
			} else if(!email_format_vaild){
				<? if ($_COOKIE['language']=="zh-tw") : ?>
					$('#register-msg').text("電郵格式錯誤，請重新輸入");
				<? elseif ($_COOKIE['language']=="eng") : ?>
					$('#register-msg').text("Email is not vaild\nplease re-enter");
				<? endif; ?>
			} else {
				<? if ($_COOKIE['language']=="zh-tw") : ?>
					$('#register-msg').text("此電郵已被註冊，請輸入另一電郵");
				<? elseif ($_COOKIE['language']=="eng") : ?>
					$('#register-msg').text("This email has already been registered\nPlease enter another email");
				<? endif; ?>
			}	
		}
	}
	else{
		<? if ($_COOKIE['language']=="zh-tw") : ?>
			$('#register-msg').text("密碼未能符合所有條件，請重新輸入");
		<? elseif ($_COOKIE['language']=="eng") : ?>
			$('#register-msg').text("The password did not meet all the conditions\nPlease re-enter");
		<? endif; ?>
	}
	$('#cover').fadeIn('slow');
	$('#register-msg-container').fadeIn('slow');
 return false;
});
</script>
<!-- NavBar Script - Tommy -->



</body>
</html>