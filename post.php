<!-- This is a template for new page, only contain the header (top menu) and footer-->
<?php
include('includes/connect.php');
$isediting = 0;
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
if(!isset($_SESSION['UID'])){
  header('Location: index.php');
  die();
}

if(isset($_GET['PostID'])){
  if(!verify_user()){
    header('Location: index.php');
    die();
  } else {
    setcookie("Posting_ID", $_GET['PostID'], time() + (86400 * 30), "/");
    $isediting = 1;
    $Table_post = table_post;
    $SQL_getPost = $conn->prepare("SELECT * FROM $Table_post WHERE ID = ?");
    $SQL_getPost->execute(array($_GET['PostID']));
    $count = $SQL_getPost->rowCount();
    if($count > 0) {
      while($row = $SQL_getPost->fetch()) {
        $post_Type = $row['Type'];
        $post_Title = $row['Title'];
        $post_Desc = $row['Description'];
        $post_Currency = $row['Currency'];
        $post_Price = $row['Price'];
        $post_img_folder = $row['img_folder'];
        $post_img = $row['img_files'];
        $post_pre_img = $row['preview_img'];
        $post_Status = $row['Post_Status'];
        $datetime_expire = new DateTime($row['ExpireDate']);
        $datetime_post = new DateTime($row['PostDate']);
      }
    }
    $datetime_now = new DateTime(date("Y-m-d H:i:s"));
    $datediff_expire = $datetime_expire->diff($datetime_now);
    $datediff_post = $datetime_post->diff($datetime_now);
    $isexpired = ($datetime_now>$datetime_expire);
    $post_img_arr = explode(", ",$post_img);
    
  }
}


function verify_user(){
  // Verification of author UID and current logged-in UID
  // if they are the same, return true, otherwise return false
  global $conn;
  $Table_post = table_post;
  $SQL_getPostBy_UID = $conn->prepare("SELECT PostBy_UID FROM $Table_post WHERE ID = ?");
  $SQL_getPostBy_UID->execute(array($_GET['PostID']));
  $count = $SQL_getPostBy_UID->rowCount();
  if($count > 0) {
    while($row = $SQL_getPostBy_UID->fetch()) {
      $post_UID = $row['PostBy_UID'];
    }
    if($_SESSION['UID']!=$post_UID)
        return false;
    else 
        return true;
  } 
  else 
    return false;
}
?>
<html>
<head>
	<title> BuybySearch </title>
	<style>
		.postform input[type=text] {
		width: 130px;
		box-sizing: border-box;
		border: 2px solid #ccc;
		border-radius: 3px;
		font-size: 16px;
		background-color: white;
		/*background-image: url('search.png');*/
		background-position: 10px 10px; 
		background-repeat: no-repeat;
		padding: 12px 20px 12px 40px;
		-webkit-transition: width 0.4s ease-in-out;
		transition: width 0.4s ease-in-out;
	}

	.postform input[type=text]:focus {
		width: 40%;
	}
	.postform input[type=text], select {
    width: 100%;
		padding: 12px 20px;
		margin: 8px 0;
		display: inline-block;
		border: 1px solid #ccc;
		border-radius: 4px;
		box-sizing: border-box;
	}

/* Style the submit button */
	.postform #post_form_submit{
		width: 100%;
		background-color: #4CAF50;
		color: white;
		padding: 14px 20px;
		margin: 8px 0;
		border: none;
		border-radius: 4px;
		cursor: pointer;
	}
/* Style the cancel button */
  #post_form_cancel {
		width: 100%;
		background-color: Red;
		color: white;
		padding: 14px 20px;
		margin: 8px 0;
		border: none;
		border-radius: 4px;
		cursor: pointer;
	}

/* Add a background color to the submit button on mouse-over */
 .postform input[type=submit]:hover {
		background-color: #45a049;
  }
  #image_reupload_btn a,#image_reupload_btn_cancel a {
    padding:5px;
    border-style: solid;
  }
	</style>
</head>
<body>
<!--  Begin of Header of Website (Top Menu Bar)	-->
<?php include_once('header.php') ?>
<!--  End of Header of Website (Top Menu Bar)	-->

<!--  Begin of Content (Body)	-->
<div id="mainbody">
  <div id="post_form_div">
      <form class="postform" id="postform" enctype='multipart/form-data'>
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div style="text-align:center;;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1 id="Post_Form_Title">發佈</h1></div>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1 id="Post_Form_Title">Post</h1></div>
          <? endif; ?>
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div style="text-align:center;;background-color:#000;color:#fff;padding-top:3px;padding-bottom:3px;"><h3 id="Post_Form_Tips">新發文將會於15天後過期<br>過期後將不能瀏覽</h3></div>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:3px;padding-bottom:3px;"><h3 id="Post_Form_Tips">New posts will expire in 15 days<br>Can't browse after expiration</h3></div>
          <? endif; ?>
          <div class="form_column">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div class="form_label"><label for="post_type">分類:</label></div>
            <select id="post_type" name="post_type">
              <option value="Product">產品</option>
              <option value="Service">服務</option>
              <option value="Information">資訊</option>
            </select><br><br>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div class="form_label"><label for="post_type">Type:</label></div>
            <select id="post_type" name="post_type">
              <option value="Product">Product</option>
              <option value="Service">Service</option>
              <option value="Information">Information</option>
            </select><br><br>
          <? endif; ?>
          </div>
        
        <div class="form_column">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div class="form_label"><label for="post_Title">標題 (長度必須介乎 4 ~ 40 個字):</label><br></div>
            <input type="text" id="post_Title" name="post_Title" placeholder="標題 (長度必須介乎 4 ~ 40 個字)" required minlength="4" maxlength="40"><br><br>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div class="form_label"><label for="post_Title">Title (Length must be between 4 and 40 characters):</label><br></div>
            <input type="text" id="post_Title" name="post_Title" placeholder="Title (Length must be between 4 and 40 characters)" required minlength="4" maxlength="40"><br><br>
          <? endif; ?>
        </div>

        <div class="form_column">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div class="form_label"><label for="post_Description">描述 (長度必須少於 250 個字):</label><br></div>
            <textarea id="post_Description" name="post_Description" placeholder="描述 (長度必須少於 250 個字)" required minlength="20" maxlength="250" form="postform"></textarea>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div class="form_label"><label for="post_Description">Description (Length must be less than 250 characters):</label><br></div>
            <textarea id="post_Description" name="post_Description" placeholder="Description (Length must be less than 250 characters)" required minlength="20" maxlength="250" form="postform"></textarea>
          <? endif; ?>
        </div>
        
        <div class="form_column">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div class="form_label"><label for="post_price">價格:</label><br></div>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div class="form_label"><label for="post_price">Price:</label><br></div>
          <? endif; ?>
          <? if ($_COOKIE['language']=="zh-tw") : ?>
              <select id="post_currency_type" name="post_currency_type">
                <option value="HKD">港元 (HKD)</option>
              </select>
              <input type="number" id="post_price" name="post_price" placeholder="價格.. (eg 10)" min="0" step="1" oninput="validity.valid||(value='0');" required><br><br>
          <? elseif ($_COOKIE['language']=="eng") : ?>
              <select id="post_currency_type" name="post_currency_type">
                <option value="HKD">港元 (HKD)</option>
              </select>
              <input type="number" id="post_price" name="post_price" placeholder="price..(eg 10)" min="0" step="1" oninput="validity.valid||(value='0');" required><br><br>
          <? endif; ?>
        </div>

        <? if ($isediting) : ?>
        <div class="form_column" id="change_preview_img">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div id="preview_old__tips_label" class="form_label" style="padding-left:10px">請於下方選擇一張圖片作為預覽縮圖</label><br></div>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div id="preview_old__tips_label" class="form_label" style="padding-left:10px">Please select a picture below as a preview</label><br></div>
          <? endif; ?>
          <?php
            if($isediting) {
              for($i = 0; $i < sizeof($post_img_arr); $i++){
                $path = 'http://'.$_SERVER['SERVER_NAME'].'/'.'user_upload_images'.'/'.$post_img_folder.'/'.$post_img_arr[$i];
                if($post_pre_img==$post_img_arr[$i])
                  echo "<input type='radio' name='preview_photo_old' value=".$post_img_arr[$i]." checked required><img width=10% height=10% src=".$path."><br>";
                else
                  echo "<input type='radio' name='preview_photo_old' value=".$post_img_arr[$i]." required><img width=10% height=10% src=".$path."><br>";
              }
            }
          ?>
        </div>
        <? endif; ?>
        <div class="form_column" id="image_reupload_btn">
        <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div class="form_label"><label for="post_form_reupload_pics">如需重新上傳圖片，請按以下按鈕<br></label><br></div>
            <a onclick="reupload_confirm()" name="post_form_reupload_pics" id="post_form_reupload_pics"><b>按此重新上傳圖片</b></a>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div class="form_label"><label for="post_form_reupload_pics">If you need to re-upload the picture, please click the button below <br></label><br></div>
            <a onclick="reupload_confirm()" name="post_form_reupload_pics" id="post_form_reupload_pics"><b>Click Here To Re-upload Images</b></a>
          <? endif; ?>
        </div>

        <div class="form_column" id="images-uploader">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div class="form_label"><label for="post_pic">有關圖片: (每個檔案不多於2MB)</label><br></div>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div class="form_label"><label for="post_pic">Your image for product: (No More Than 2MB/file)</label><br></div>
          <? endif; ?>
          <br>
          <input style="padding-left:10px" type="file" name="post_pic[]" id="post_pic" accept="image/png, image/jpeg" onchange="showFilesName()" multiple required><br><br>
        </div>
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div id="preview_tips_label" class="form_label" style="padding-left:10px">請於下方選擇一張圖片作為預覽縮圖</label><br></div>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div id="preview_tips_label" class="form_label" style="padding-left:10px">Please select a picture below as a preview</label><br></div>
          <? endif; ?>
        <div style="padding-left:10px" id="files_list"> </div><br>
        <div class="form_column" id="image_reupload_btn_cancel">
        <? if ($_COOKIE['language']=="zh-tw") : ?>
            <a onclick="reupload_cancel()" name="post_form_reupload_pics" id="post_form_reupload_pics_cancel"><b>取消上傳</b></a>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <a onclick="reupload_cancel()" name="post_form_reupload_pics" id="post_form_reupload_pics_cancel"><b>Cancel Re-Upload</b></a>
          <? endif; ?>
        </div>
        <br>
        <div class="form_column">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <button type="submit" id="post_form_submit"><b>提交</b></button>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <button type="submit" id="post_form_submit"><b>Submit</b></button>
          <? endif; ?>
        </div>
      </form>
      <div class="form_column">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <button onclick="window.history.back();" id="post_form_cancel"><b>取消</b></button>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <button onclick="window.history.back();" id="post_form_cancel"><b>Cancel</b></button>
          <? endif; ?>
      </div>
    </div>
    <? if ($isediting==1) : ?>
      <? if ($post_Status=="Transaction complete" || $post_Status=="Waiting for confirmation") : ?>
        <div style="text-align:center;;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1 id="Post_Form_Title"><?php (isset($post_Title)) ? Print($post_Title) : Print("Error: Can't Get A Title") ?></h1></div>
        <? if ($_COOKIE['language']=="zh-tw") : ?>
          <div style="text-align:center;color:#000;padding-top:10px;padding-bottom:10px;line-height:1.1;"><h1 id="Post_Form_Title">本發文已經無法再編輯<br>因其狀態已設為<b style="color:red;">「等待確定」或「交易已完成」</b></h1></div>
        <? elseif ($_COOKIE['language']=="eng") : ?>
          <div style="text-align:center;color:#000;padding-top:10px;padding-bottom:10px;line-height:1.1;"><h1 id="Post_Form_Title">This post cannot edit because its status is set to <br><b style="color:red;">"waiting for confirmation" or "transaction completed"</b></h1></div>
        <? endif; ?>
        <div class="form_column">
            <? if ($_COOKIE['language']=="zh-tw") : ?>
              <button onclick="window.location.href='mypost.php'" id="post_form_cancel"><b>回到「我的發文」</b></button>
            <? elseif ($_COOKIE['language']=="eng") : ?>
              <button onclick="window.location.href='mypost.php'" id="post_form_cancel"><b>Return To MyPost</b></button>
            <? endif; ?>
        </div>
      <? elseif($isexpired) : ?>
        <div style="text-align:center;;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1 id="Post_Form_Title"><?php (isset($post_Title)) ? Print($post_Title) : Print("Error: Can't Get A Title") ?></h1></div>
        <? if ($_COOKIE['language']=="zh-tw") : ?>
          <div style="text-align:center;color:#000;padding-top:10px;padding-bottom:10px;line-height:1.1;"><h1 id="Post_Form_Title">本發文已經無法再編輯，因其<b style="color:red;">發文時間已過<br>請返回「我的發文」使用用戶點數延長發文時間</b></h1></div>
        <? elseif ($_COOKIE['language']=="eng") : ?>
          <div style="text-align:center;color:#000;padding-top:10px;padding-bottom:10px;line-height:1.1;"><h1 id="Post_Form_Title">This post cannot edit Because its <b style = "color: red;"> posting time has passed<br>Please return to "My Post" and use user points to extend the posting time</b></h1></div>
        <? endif; ?>
        <div class="form_column">
            <? if ($_COOKIE['language']=="zh-tw") : ?>
              <button onclick="window.location.href='mypost.php'" id="post_form_cancel"><b>回到「我的發文」</b></button>
            <? elseif ($_COOKIE['language']=="eng") : ?>
              <button onclick="window.location.href='mypost.php'" id="post_form_cancel"><b>Return To MyPost</b></button>
            <? endif; ?>
        </div>
      <? endif; ?>
    <? endif; ?>
</div>
<div id="cover"></div>
<div class="popupmsg-container" style="width: 400px;height: 180px; margin-top: -90px;margin-left: -200px;" id="post-msg-container"> 
	<p id="post-msg" style="white-space: pre-line">Please Enter Again!</p>
	<div class="popupmsg-option-single" id="post-option-container">
		<a id="post-msg-confirm" href="javascript:void(0);">OK</a>
	</div>
</div>
<br><br>
<!--  End of Content (Body)	-->

<!--  Begin of Footer of Website (Bottom Bar)	-->
<?php include_once('footer.php') ?>
<!--  End of Footer of Website (Bottom Bar)	-->

<script>
document.getElementById('search_again_container').style.display="none";
var totalsize=0;
var files_count=0;
var php_get_edit_postID = <?php (isset($_GET['PostID'])) ? Print($_GET['PostID']) : Print(0) ?>;
var isreupload = false;
var isediting = <?php (isset($isediting)) ? Print($isediting) : Print(0) ?>;
document.getElementById('preview_tips_label').style.display="none";
document.getElementById('image_reupload_btn').style.display = "none";
document.getElementById('image_reupload_btn_cancel').style.display = "none";

if(php_get_edit_postID!=0){
  <? if ($_COOKIE['language']=="zh-tw") : ?>
    document.getElementById('Post_Form_Title').innerHTML="修改發文";
  <? elseif ($_COOKIE['language']=="eng") : ?>
    document.getElementById('Post_Form_Title').innerHTML="Editing Post";
  <? endif; ?>
  document.getElementById('Post_Form_Tips').style.display = "none";
  document.getElementById('image_reupload_btn').style.display = "";
  document.getElementById('images-uploader').style.display = "none";
  document.getElementById('post_Title').readOnly = true;
  document.getElementById('post_Title').style.backgroundColor = "#D3D3D3";
  document.getElementById('post_Title').value = "<?php (isset($post_Title)) ? Print($post_Title) : Print("Error: Can't Get A Title") ?>";
  document.getElementById('post_type').value = "<?php (isset($post_Type)) ? Print($post_Type) : Print("Error") ?>";
  var desc_text = "<?php (isset($post_Desc)) ? Print($post_Desc) : Print("Error: Can't Get A Desc") ?>";
  // Replace <br> to \n, Refer To https://stackoverflow.com/questions/5959415/jquery-javascript-regex-replace-br-with-n
  document.getElementById('post_Description').value = desc_text.replace(/<br\s*[\/]?>/gi, "\n");
  document.getElementById('post_currency_type').value = "<?php (isset($post_Currency)) ? Print($post_Currency) : Print("Error") ?>";
  document.getElementById('post_price').value = "<?php (isset($post_Price)) ? Print($post_Price) : Print("Error") ?>";
  document.getElementById('post_pic').required = false;
  <? if((isset($post_Title))) : ?>
    <? if ($post_Status=="Transaction complete" || $post_Status=="Waiting for confirmation") : ?>
      document.getElementById('post_form_div').innerHTML="";
    <? endif; ?>
  <? endif; ?>
} else {

}

function reupload_confirm(){
  isreupload=true;
  document.getElementById('change_preview_img').style.display = "none";
  document.getElementById('images-uploader').style.display = "";
  document.getElementById('image_reupload_btn').style.display = "none";
  document.getElementById('image_reupload_btn_cancel').style.display = "";
  document.getElementById('post_pic').required = true;
  var pics_old_array = document.getElementsByName("preview_photo_old");
  pics_old_array.forEach(element => element.required = false);
}

function reupload_cancel(){
  isreupload=false;
  document.getElementById('change_preview_img').style.display = "";
  document.getElementById('images-uploader').style.display = "none";
  document.getElementById('image_reupload_btn').style.display = "";
  document.getElementById('image_reupload_btn_cancel').style.display = "none";
  document.getElementById('post_pic').required = false;
  var pics_old_array = document.getElementsByName("preview_photo_old");
  pics_old_array.forEach(element => element.required = true);
  $("#post_pic").val('');
  showFilesName ();
}



function checksize(size) {
  return size < 2048000;
  // return true when file size more than 2MB.
}
/* 
|post-msg-confirm:|
Close Msg Box by click confirm button.
*/
$('#post-msg-confirm').each(function(){
    $(this).click(function(){ 
        $('#cover').fadeOut('slow');
		$('#post-msg-container').fadeOut('slow');
    });
});

var selected_preview_img = "";
//Function Call when Post Form is submitted
$('#postform').submit(function () {
  var result = { };
	$.each($('form').serializeArray(), function() {
    	result[this.name] = this.value;
	});
  selected_preview_img = result.preview_photo;
   if(isediting==1){
     // if it is editing a post.  
     var confirm_submition = true;
     if(confirm_submition){
      $.ajax({
          url: 'includes/post_function.php',
          type: 'POST',
          data: 
          {
            process:"UpdatingPost",
            post_id:php_get_edit_postID,
            post_type:result.post_type,
            post_desc:result.post_Description,
            post_currency:result.post_currency_type,
            post_price:result.post_price
          },
          success: function(posting_result) {
            // Update The Picture Preview to Selected Picture
              document.getElementById("post-option-container").style.display="block";
              $('#post-msg').text(""+posting_result);
              var previous_preview_img = "<?php (isset($post_pre_img)) ? Print($post_pre_img) : Print("Error: Can't Get A Title") ?>";
              if(isreupload==false && previous_preview_img != result.preview_photo_old){
                $.ajax({
                      url: 'includes/post_function.php',
                      type: 'POST',
                      data: 
                      {
                        process:"UpdatingPreview",
                        post_id:php_get_edit_postID,
                        post_preview:result.preview_photo_old
                      },
                    success: function(posting_result) {	
                      <? if ($_COOKIE['language']=="zh-tw") : ?>
                        $('#post-msg').text(""+posting_result);
                      <? elseif ($_COOKIE['language']=="eng") : ?>
                        $('#post-msg').text(""+posting_result);
                      <? endif; ?>
                    }
                });
              }
              if(isreupload){
                // Whem User Reupload the picture, Remove all pics in server and upload new pics to server
                <? if ($_COOKIE['language']=="zh-tw") : ?>
                  $('#post-msg').text(""+posting_result+" 及成功重新上載圖片");
                <? elseif ($_COOKIE['language']=="eng") : ?>
                  $('#post-msg').text(""+posting_result+" and new photos has been uploaded");
                <? endif; ?>
                  $.ajax({
                        url: 'includes/post_function.php',
                        type: 'POST',
                        data: 
                        {
                          process:"RemovingPics",
                          post_PostBy_PostID:php_get_edit_postID
                        },
                      success: function(posting_result) {	
                        
                      }
                  });
                  uploadimage();
              }
              setTimeout(function() {
                location.href="viewpost.php?ID="+php_get_edit_postID;
              }, 1500);
          }
        });
      $('#cover').fadeIn('slow');
      $('#post-msg-container').fadeIn('slow');
     }
   } else {
     // if it is creating a new post.           
      $.ajax({
        url: 'includes/post_function.php',
        type: 'POST',
        data: 
        {
          process:"Posting",
          post_type:result.post_type,
          post_title:result.post_Title,
          post_desc:result.post_Description,
          post_currency:result.post_currency_type,
          post_price:result.post_price,
        },
        success: function(posting_result) {	
          document.getElementById("post-option-container").style.display="block";
          $('#post-msg').text(""+posting_result);
          uploadimage();
          document.getElementById("postform").reset();
          setTimeout(function() {
              location.href="mypost.php";
          }, 1500);
        }
      });
    $('#cover').fadeIn('slow');
    $('#post-msg-container').fadeIn('slow');
  }
 return false;
});

function uploadimage(){
  var form_data = new FormData();
  // Read selected files
  var totalfiles = document.getElementById('post_pic').files.length;
  for (var index = 0; index < totalfiles; index++) {
    form_data.append("files[]", document.getElementById('post_pic').files[index]);
  }
  form_data.append("preview_img",selected_preview_img);
  //AJAX upload pics
        $.ajax({
        url: 'includes/post_upload_file.php', 
        type: 'post',
        data: form_data,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {

      }
    });
}

function showFilesName () {
  var files_size=[];
  totalsize=0;
  files_count=0;
  var target=document.getElementById('post_pic');
  document.getElementById('files_list').innerHTML='';
  document.getElementById('preview_tips_label').style.display="";
  for(var i = 0; i < target.files.length; i++){
    var radioHtml = '<input type="radio" name="preview_photo" value="' + target.files[i].name + '" required>' + target.files[i].name + " ("+Math.round(target.files[i].size / 1024 * 100) / 100+"KB) <br>";
    var preview_img = '<img width=10% height=10% src="'+ URL.createObjectURL(target.files[i]) +'"/>';
    totalsize+=target.files[i].size;
    files_count+=1;
    files_size.push(target.files[i].size);
    document.getElementById('files_list').innerHTML+=("<br>"+radioHtml);
    document.getElementById('files_list').innerHTML+=(preview_img);
  }
  document.getElementById('files_list').innerHTML+="<br>"+files_count+", "+totalsize;
  if(files_count==0){
    document.getElementById('files_list').innerHTML='';
    document.getElementById('preview_tips_label').style.display="none";
  }
  if ((files_count>5 && isediting==0 )||(files_count>5 && isediting==1 && isreupload)){
      <? if ($_COOKIE['language']=="zh-tw") : ?>
					$('#post-msg').text("你只可以上傳最多 5 張圖片");
			<? elseif ($_COOKIE['language']=="eng") : ?>
          $('#post-msg').text("You can only upload a maximum of 5 files");
			<? endif; ?>
    document.getElementById('files_list').innerHTML='';
    document.getElementById('preview_tips_label').style.display="none";
    $("#post_pic").val('');
    $('#cover').hide().fadeIn('slow');
    $('#post-msg-container').fadeIn('slow');
  }
  if ((isediting==0 && (totalsize>10240000 || !files_size.every(checksize))) || (isediting==1 && isreupload && (totalsize>10240000 || !files_size.every(checksize)))){
        <? if ($_COOKIE['language']=="zh-tw") : ?>
					$('#post-msg').text("你上傳的檔案大小超出上傳限制 (每個檔案不多於2MB)");
				<? elseif ($_COOKIE['language']=="eng") : ?>
          $('#post-msg').text("The size of the files you uploaded exceeds the upload limit (no more than 2MB per file)");
				<? endif; ?>
    document.getElementById('files_list').innerHTML='';
    document.getElementById('preview_tips_label').style.display="none";
    $("#post_pic").val('');
    $('#cover').hide().fadeIn('slow');
    $('#post-msg-container').fadeIn('slow');
  }
};

</script>

</body>
</html>