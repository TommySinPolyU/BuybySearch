<html>
<!-- It is a footer file for all page -->
<!-- Just include this file at the end of the website -->
<!-- Modify This file To apply all the change of footer -->
<!-- It is recommended to write all javascript at the script in this file, if the code can be use on any page -->
<body>
<div class="popupmsg-container" id="sendmsg-container"> 
	<div id="sendmsg_form_div">
    <div style='text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;'><h1>
    <? if ($_COOKIE['language']=="zh-tw") : ?>傳送訊息
      <? elseif ($_COOKIE['language']=="eng") : ?>Send Message: 
        <? endif; ?>
    </h1></div><br>
	</div>
	<br><br>
	<form class="register_form-container" id="sendmsg_form">
    <input type="hidden" id="msg_postid" name="msg_postid"></input>
    <input type="hidden" id="reply_msgid" name="reply_msgid"></input>
    <div class="form_column">
      <div class="form_label">
      <? if ($_COOKIE['language']=="zh-tw") : ?>發文標題: 
      <? elseif ($_COOKIE['language']=="eng") : ?>Post Title: 
      <? endif; ?>
      <a id="sendmsg_postTitle"></a>
      </div>
    </div>
    <div class="form_column">
      <div class="form_label">
      <? if ($_COOKIE['language']=="zh-tw") : ?>發文者: 
      <? elseif ($_COOKIE['language']=="eng") : ?>Author: 
      <? endif; ?>
      <a id="sendmsg_postBy"></a>
      </div>
    </div>
    <br>
    <div class="form_column">
      <? if ($_COOKIE['language']=="zh-tw") : ?>
        <div class="form_label"><label for="send_msg">訊息 (長度必須少於 400 個字):</label><br></div>
        <textarea id="send_msg" name="send_msg" placeholder="訊息 (長度必須少於 400 個字)" required minlength="10" maxlength="400" form="sendmsg_form"></textarea>
      <? elseif ($_COOKIE['language']=="eng") : ?>
        <div class="form_label"><label for="send_msg">Message (Length must be less than 400 characters):</label><br></div>
        <textarea id="send_msg" name="send_msg" placeholder="Message (Length must be less than 400 characters)" required minlength="10" maxlength="400" form="sendmsg_form"></textarea>
      <? endif; ?>
    </div>
    <br>
	  <? if ($_COOKIE['language']=="zh-tw") : ?>
			<button class="green_btn" type="submit" id="submit_sendmsg">提交</button>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<button class="green_btn" type="submit" id="submit_sendmsg">Submit</button>
		<? endif; ?>
	</form>
    <? if ($_COOKIE['language']=="zh-tw") : ?>
			<button class="red_btn" id="sendmsg_close">取消</button>
		<? elseif ($_COOKIE['language']=="eng") : ?>
			<button class="red_btn" id="sendmsg_close">Cancel</button>
		<? endif; ?>
</div>

<!--  Begin of Footer of Website (Bottom Bar)	-->
<div class="footer">
  		<p>©2019 by BuyBySearch</p>
</div>
<button id="topbtn"><img src="images/back_top.png"/></button>
<!--  End of Footer of Website (Bottom Bar)	-->
</body>
<script>
// Handle The Search Bar Element display
// Default: Hide all input element except the search_type (Selection of Search Criteria)
document.getElementById('search_text').style.display="none";
document.getElementById('searchby_type_selection').style.display="none";

$('#sendmsg_close').each(function(){
    $(this).click(function(){ 
      $('#cover').fadeOut('slow');
      $('#sendmsg-container').fadeOut('slow');
      $('#sendmsg-msg-container').fadeOut('slow');
    });
});

// function for control the searchbar elements display
function searchtext_name_change(name){
  if(name=="SearchBy_Type"){
    document.getElementById('search_text').setAttribute("name","");
    document.getElementById('search_text').style.display="none";
    document.getElementById('searchby_type_selection').style.display="";
    document.getElementById('searchby_type_selection').setAttribute("name",name);
    document.getElementById('search_text').required = false;
    document.getElementById('searchby_type_selection').required = true;
  } else if(name!="") {
    document.getElementById('search_text').setAttribute("name",name);
    document.getElementById('searchby_type_selection').setAttribute("name","");
    document.getElementById('search_text').style.display="";
    document.getElementById('searchby_type_selection').style.display="none";
    document.getElementById('search_text').required = true;
    document.getElementById('searchby_type_selection').required = false;
  } else {
    document.getElementById('search_text').style.display="none";
    document.getElementById('searchby_type_selection').style.display="none";
  }
}
// Submit a Search Bar by click the category button on the top navigation bar.
function submit_search_type(type){
  document.getElementById("search_type").value="SearchBy_Type";
  searchtext_name_change("SearchBy_Type");
  document.getElementById("searchby_type_selection").value=type;
  document.getElementById("searchbar").submit();
}

function addtocart(postid){
  var isloggedin = <?php (isset($_SESSION['UID'])) ? print 1 : print 0  ?>;
  if(isloggedin == 1){
    $.ajax({
      url: 'includes/cart_function.php',
      type: 'POST',
      data: 
      {
        addcart_PostID:postid
      },
      success: function(result) {	
        $('#result-msg').text(""+result);
        $('#cover').fadeIn('slow');
        $('#result-msg-container').fadeIn('slow');   
        setTimeout(() => {
          $('#cover').fadeOut('slow');
          $('#result-msg-container').fadeOut('slow');   
          location.reload();
        }, 1250);             
      }
    });
  } else {
      <? if ($_COOKIE['language']=="zh-tw") : ?>
        $('#result-msg').text("請先登入以使用會員功能");
      <? elseif ($_COOKIE['language']=="eng") : ?>
        $('#result-msg').text("Please login first to use the membership function");
      <? endif; ?>
      $('#cover').fadeIn('slow');
      $('#result-msg-container').fadeIn('slow');   
      setTimeout(() => {
          $('#cover').fadeOut('slow');
          $('#result-msg-container').fadeOut('slow');    
        }, 1250);     
  }
}

function removefromcart(postid){
    $.ajax({
      url: 'includes/cart_function.php',
      type: 'POST',
      data: 
      {
        removecart_PostID:postid
      },
      success: function(result) {	
        $('#result-msg').text(""+result);
        $('#cover').fadeIn('slow');
        $('#result-msg-container').fadeIn('slow');    
        setTimeout(() => {
          $('#cover').fadeOut('slow');
          $('#result-msg-container').fadeOut('slow');  
          location.reload(); 
        }, 1250);            
      }
    });
}

function notify_seller(postid){
  document.getElementById('msg_postid').value=postid;
  document.getElementById('reply_msgid').value="";
  $.ajax({
      url: 'includes/post_function.php',
      type: 'POST',
      data: 
      {
        process:"GetPostDetails",
        PostID:postid
      },
      success: function(result) {	
        var post_details = result.split(",");
        document.getElementById('sendmsg_postTitle').innerHTML=post_details[2];
        document.getElementById('sendmsg_postBy').innerHTML=post_details[6];
      }
    });
  $('#cover').fadeIn('slow');
  $('#sendmsg-container').fadeIn('slow');   
  $('#sendmsg-msg-container').fadeIn('slow');
}

function reply_seller(postid, replyid){
  document.getElementById('msg_postid').value=postid;
  document.getElementById('reply_msgid').value=replyid;
  $.ajax({
      url: 'includes/post_function.php',
      type: 'POST',
      data: 
      {
        process:"GetPostDetails",
        PostID:postid
      },
      success: function(result) {	
        var post_details = result.split(",");
        document.getElementById('sendmsg_postTitle').innerHTML=post_details[2];
        document.getElementById('sendmsg_postBy').innerHTML=post_details[6];
      }
    });
  $('#cover').fadeIn('slow');
  $('#sendmsg-container').fadeIn('slow');   
  $('#sendmsg-msg-container').fadeIn('slow');
}

$('#sendmsg_form').submit(function () {
	var result = { };
	$.each($('form').serializeArray(), function() {
    	result[this.name] = this.value;
	});
	$('#sendmsg_close').click();
  if(result.reply_msgid==""){
    $.ajax({
      url: 'includes/msg_function.php',
      type: 'POST',
      data: {
        process:"Send_NewMsg",
        post_id:result.msg_postid,
        trading_msg:result.send_msg
        },
      success: function(result) {
        $('#login-msg').text(""+result);
        setTimeout(() => {
          $('#cover').fadeOut('slow');
          $('#login-msg-container').fadeOut('slow');
          $('#login-container').fadeOut('slow');
          location.reload(); 
        }, 1250);    
      }
    });
  } else {
    $.ajax({
      url: 'includes/msg_function.php',
      type: 'POST',
      data: {
        process:"Reply_Msg",
        reply_msg_id:result.reply_msgid,
        trading_msg:result.send_msg
        },
      success: function(result) {
        $('#login-msg').text(""+result);
        
        setTimeout(() => {
          $('#cover').fadeOut('slow');
          $('#login-msg-container').fadeOut('slow');
          $('#login-container').fadeOut('slow');
          location.reload(); 
        }, 1250);    
      }
    });
  }
  document.getElementById("login-msg-option-container").style.display="none";
  $('#cover').fadeIn('slow');
  $('#login-msg-container').fadeIn('slow');
	$('#login-container').fadeOut('slow');
 return false;
});

$('#topbtn').each(function(){
    $(this).click(function(){ 
        $('html,body').animate({ scrollTop: 0 }, 'slow');
        return false; 
    });
});

</script>

</html>