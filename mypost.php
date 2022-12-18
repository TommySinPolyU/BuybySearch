<?php
$ErrorMsg="";
$post_ID=array();
$post_Type=array();
$post_Title=array();
$post_Currency=array();
$post_Price=array();
$post_NickName=array();
$post_PostDate=array();
$post_ExpireDate=array();
$post_status=array();
$post_status_DB=array();
$post_Selected_Buyer=array();
require_once('includes/connect.php');
if (session_status() == PHP_SESSION_NONE) {
  session_start(); // Start a Session for using $_SESSION
}
if(!isset($_SESSION['UID'])){
  //Because It is a function for logged-in user.
  //if No any user logged-in, Return page to home page.
  //To Avoid people type URL manually without logged-in.
  header('Location: index.php');
  die();
}


function gen_result_format($ismobile){ 
//if user is logged-in
//Request Server to find All Posted Post by currert logged-in user
//Then Show on page.
echo "<table id='result_table_Col1' width=100%><tr>";
global $conn;
$Table_post = table_post;
$SQL_getPost_ByUID = $conn->prepare("SELECT * FROM $Table_post WHERE PostBy_UID = ?");
$SQL_getPost_ByUID->execute(array($_SESSION['UID']));
$count = $SQL_getPost_ByUID->rowCount();
if($count>0){
  while($row = $SQL_getPost_ByUID->fetch()) {
    $post_ID[] = $row['ID'];
    $type_convert = $row['Type'];
    $post_status_DB[] = $row['Post_Status'];
    switch($_COOKIE['language']){
      case "zh-tw":
        if($type_convert=="Product")
          $type_convert = "產品";
        else if($type_convert=="Service")
          $type_convert = "服務";
        else if($type_convert=="Information")
          $type_convert = "資訊";
      break;
    }
    $status_convert = $row['Post_Status'];
      switch($_COOKIE['language']){
        case "zh-tw":
          if($status_convert=="Idle")
            $status_convert = "空閒";
          else if($status_convert=="Waiting for confirmation")
            $status_convert = "等待確定";
          else if($status_convert=="Transaction complete")
            $status_convert = "交易已完成";
        break;
      }
    $post_status[] = $status_convert;
    $post_Type[] = $type_convert;
    $post_Title[] = $row['Title'];
    $post_Currency[] = $row['Currency'];
    $post_Price[] = $row['Price'];
    $post_NickName[] = $row['PostBy'];
    $post_PostDate[] = $row['PostDate'];
    $post_ExpireDate[] = $row['ExpireDate'];
    $post_Selected_Buyer[] = $row['Selected_Buyer'];
}
} else {
  // If Current logged-in user didn't post anything in server.
  switch($_COOKIE['language']){
    case "eng":
      $ErrorMsg="You didn't post anything";
    break;
    case "zh-tw":
      $ErrorMsg="你並沒有任何發文";
    break;
  }
}
echo "<table id='result_table_Container' width=100%>";
// Gen all posts details by for loop
  for($i=0; $i<$count; $i++){
    // Checking The Expiry of the post
    $datetime_now = new DateTime(date("Y-m-d H:i:s"));
    $datetime_expire = new DateTime($post_ExpireDate[$i]);
    $datetime_post = new DateTime($post_PostDate[$i]);
    $datediff_expire = $datetime_expire->diff($datetime_now);
    $datediff_post = $datetime_post->diff($datetime_now);
    $isexpired = ($datetime_now>$datetime_expire);
    if ($_COOKIE['language']=="zh-tw"){
      if(($datediff_post->d)>0 && ($datediff_post->h)<24){
        $PostDateString= $datediff_post->d." 日前";
      } else if(($datediff_post->d)<=0 && ($datediff_post->h)>0 && ($datediff_post->h)<=24) {
        $PostDateString= $datediff_post->h." 小時前";
      } else if(($datediff_post->d)<=0 && ($datediff_post->h)<=0 && ($datediff_post->i)<=60 && ($datediff_post->i)>0){
        $PostDateString= $datediff_post->i." 分鐘前";
      } else {
        $PostDateString= "剛剛";
      }
    } else if ($_COOKIE['language']=="eng"){
      if(($datediff_post->d)>0 && ($datediff_post->h)<24){
        $PostDateString = $datediff_post->d." Days ago";
      } else if(($datediff_post->d)<=0 && ($datediff_post->h)>0 && ($datediff_post->h)<=24) {
        $PostDateString = $datediff_post->h." Hours ago";
      } else if(($datediff_post->d)<=0 && ($datediff_post->h)<=0 && ($datediff_post->i)<=60 && ($datediff_post->i)>0){
        $PostDateString = $datediff_post->i." Mins ago";
      } else {
        $PostDateString = "just recently";
      }
    }
    if(!$isexpired){
      // If the post is not expired
      if ($_COOKIE['language']=="zh-tw"){
        if(($datediff_expire->d)>0 && ($datediff_expire->h)<24){
            $ExpireDateString = "於 ".$datediff_expire->d."日 ".$datediff_expire->h."小時 ".$datediff_expire->i."分鐘 後過期";
          } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)>0 && ($datediff_expire->h)<=24) {
            $ExpireDateString = "於 ".$datediff_expire->h."小時 ".$datediff_expire->i."分鐘 後過期";
          } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)<=0 && ($datediff_expire->i)<=60 && ($datediff_expire->i)>0){
            $ExpireDateString = "於 ".$datediff_expire->i."分鐘 後過期";
          } else {
            $ExpireDateString= "即將過期";
          }
          } else if ($_COOKIE['language']=="eng"){
            // If the post is expired
            if(($datediff_expire->d)>0 && ($datediff_expire->h)<24){
              $ExpireDateString = "Expires after ".$datediff_expire->d."D ".$datediff_expire->h."H ".$datediff_expire->i."M";
            } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)>0 && ($datediff_expire->h)<=24) {
              $ExpireDateString = "Expires after ".$datediff_expire->h."H ".$datediff_expire->i."M";
            } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)<=0 && ($datediff_expire->i)<=60 && ($datediff_expire->i)>0){
              $ExpireDateString = "Expires after ".$datediff_expire->i."M";
            } else {
              $ExpireDateString= "Expiring soon";
            }
          }
    } else {
      if ($_COOKIE['language']=="zh-tw"){
        $ExpireDateString = "已過期";
      } else if ($_COOKIE['language']=="eng"){
        $ExpireDateString = "Expired";
      }
    }
    if ($_COOKIE['language']=="zh-tw"){
      $editpost_String = "修改發文";
    } else if ($_COOKIE['language']=="eng"){
      $editpost_String = "Edit Post";
    }
    if ($_COOKIE['language']=="zh-tw"){
      $extendpost_String = "延長發文時間";
    } else if ($_COOKIE['language']=="eng"){
      $extendpost_String = "Extend Expiry Date";
    }
    if ($_COOKIE['language']=="zh-tw"){
      $deletepost_String = "刪除發文";
    } else if ($_COOKIE['language']=="eng"){
      $deletepost_String = "Delete Post";
    }
    if(!$ismobile){
        // Gen post details
        echo "<table id='result_table_Col1' width=100%><tr>";
        if($post_status_DB[$i]=="Transaction complete"){
          echo "<td width=70%><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$post_ID[$i].") "."<a>".$post_Title[$i]."</a></td>";
          echo "<td width=10% align=right>".$post_Type[$i]."&nbsp;<img style='vertical-align: middle;' width=32; height=32; src=images/category.png></td>";
          echo "<td width=20%><button class='red_btn' onclick='RemovePost(".$post_ID[$i].")'>".$deletepost_String."</button></td>";
        } else {
          if(!$isexpired){
            echo "<td width=70%><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$post_ID[$i].") "."<a href=viewpost.php?ID=".$post_ID[$i].">".$post_Title[$i]."</a></td>";
            echo "<td width=10% align=right>".$post_Type[$i]."&nbsp;<img style='vertical-align: middle;' width=32; height=32; src=images/category.png></td>";
            echo "<td width=20%><button class='green_btn' onclick=location.href='post.php?PostID=".$post_ID[$i]."'>".$editpost_String."</button></td>";
          } else {
            echo "<td width=70%><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$post_ID[$i].") "."<a>".$post_Title[$i]."</a></td>";
            echo "<td width=10% align=right>".$post_Type[$i]."&nbsp;<img style='vertical-align: middle;' width=32; height=32; src=images/category.png></td>";
            echo "<td width=20%><button class='black_btn' onclick=extend_post(".$post_ID[$i].")>".$extendpost_String."</button></td>";
          }
        }
        echo "</tr></table>";
        echo "<table id='result_table_Col2' width=100%><tr>";
          echo "<td width=20%><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$PostDateString."</td>";
          if($post_Price[$i] == 0){
            switch($_COOKIE['language']){
              case "eng":
                echo "<td width=20%>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;Free</td>";
              break;
              case "zh-tw":
                echo "<td width=20%>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;免費</td>";
              break;
            }                  
          } else {
            echo "<td width=20%><img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;".$post_Currency[$i]." ".$post_Price[$i]."</td>";
          }
          echo "<td width=40%><img style='vertical-align: middle;' width=32; height=32; src=images/expirydate.png>&nbsp;".$ExpireDateString."</td>";
          echo "<td width=20%>";
          if($post_status_DB[$i]=="Idle" && !$isexpired){
            echo "<button class='red_btn' onclick='RemovePost(".$post_ID[$i].")'>".$deletepost_String."</button>";
            switch($_COOKIE['language']){
              case "eng":
                echo "<br><button class='black_btn' onclick=window.location.href='post_changebuyer.php?PostID=".$post_ID[$i]."'>"."Confirm Final Trader"."</button>";
              break;
              case "zh-tw":
                echo "<br><button class='black_btn' onclick=window.location.href='post_changebuyer.php?PostID=".$post_ID[$i]."'>"."確認最後交易者"."</button>";
              break;
            }     
            echo "<br><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$post_status[$i]."</td>";
          } else if($post_status_DB[$i]!="Idle" && $isexpired) {
            echo "<button class='red_btn' onclick='RemovePost(".$post_ID[$i].")'>".$deletepost_String."</button>";
            echo "<br><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$post_status[$i]."</td>";
          } else if($post_status_DB[$i]=="Waiting for confirmation" || $post_status_DB[$i]=="Transaction complete"){
            echo "<br><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$post_status[$i]." [".$post_Selected_Buyer[$i]."]</td>";
          } else {
            if($isexpired)
              echo "<button class='red_btn' onclick='RemovePost(".$post_ID[$i].")'>".$deletepost_String."</button>";
            echo "<br><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$post_status[$i]."</td>";
          }
        echo "</tr></table><br>";
        echo "<hr class='style-two' />";
      } else {
        // Gen post details
        echo "<table id='result_table_Col1' width=100%><tr>";
        if($post_status_DB[$i]=="Transaction complete"){
          echo "<td width=70%><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$post_ID[$i].") "."<a>".$post_Title[$i]."</a></td>";
        } else {
          if(!$isexpired){
            echo "<td width=70%><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$post_ID[$i].") "."<a href=viewpost.php?ID=".$post_ID[$i].">".$post_Title[$i]."</a></td>";
            echo "<td width=30% align=right>".$post_Type[$i]."&nbsp;<img style='vertical-align: middle;' width=32; height=32; src=images/category.png></td>";
          } else {
            echo "<td width=70%><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$post_ID[$i].") "."<a>".$post_Title[$i]."</a></td>";
            echo "<td width=30% align=right>".$post_Type[$i]."&nbsp;<img style='vertical-align: middle;' width=32; height=32; src=images/category.png></td>";
          }
        }
        echo "</tr></table>";
        echo "<table id='result_table_Col2' width=100%><tr>";
          echo "<td width=33%><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$PostDateString."</td>";
          if($post_Price[$i] == 0){
            switch($_COOKIE['language']){
              case "eng":
                echo "<td width=33%>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;Free</td>";
              break;
              case "zh-tw":
                echo "<td width=33%>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;免費</td>";
              break;
            }                  
          } else {
            echo "<td width=33%><img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;".$post_Currency[$i]." ".$post_Price[$i]."</td>";
          }
          echo "<td width=34%><img style='vertical-align: middle;' width=32; height=32; src=images/expirydate.png>&nbsp;".$ExpireDateString."</td>";
        echo "</tr>";
        echo "</table>";
        echo "<table id='result_table_Col3' width=100%><tr>";
        if($post_status_DB[$i]=="Idle"){
            if(!$isexpired){
              echo "<td width=50%><button class='green_btn' onclick=location.href='post.php?PostID=".$post_ID[$i]."'>".$editpost_String."</button></td>";     
            } else {
              echo "<td width=50%><button class='black_btn' onclick=extend_post(".$post_ID[$i].")>".$extendpost_String."</button></td>";
            }
        } 
          if($isexpired || $post_status_DB[$i]!="Waiting for confirmation")
            echo "<td width=50%><button class='red_btn' onclick='RemovePost(".$post_ID[$i].")'>".$deletepost_String."</button></td>";
          echo "</tr>";
          if($post_status_DB[$i]=="Waiting for confirmation" || $post_status_DB[$i]=="Transaction complete"){
            echo "<tr><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$post_status[$i]." [".$post_Selected_Buyer[$i]."]</td>";
          } else {
            echo "<tr><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$post_status[$i]."</td>";
          }
          if($post_status_DB[$i]=="Idle" && !$isexpired){
            switch($_COOKIE['language']){
              case "eng":
                echo "<td width=50%><button class='black_btn' onclick=window.location.href='post_changebuyer.php?PostID=".$post_ID[$i]."'>"."Confirm Final Trader"."</button></td></tr>";
              break;
              case "zh-tw":
                echo "<td width=50%><button class='black_btn' onclick=window.location.href='post_changebuyer.php?PostID=".$post_ID[$i]."'>"."確認最後交易者"."</button></td></tr>";
              break;
            }
          }
          echo "</table>";
          echo "<hr class='style-two' />";
    }
  }
  echo '</table>';
}
?>

<html>
<head>
	<title> BuybySearch - My Post </title>
	<style>
  #post-new-thing{
    width: 100%;
		background-color: #4CAF50;
		color: white;
		border: none;
		border-radius: 4px;
    cursor: pointer;
    text-align: center;
    bottom:130px;
    height:60px;
    font-size:24px;	
  }

	</style>
</head>
<body>
<!--  Begin of Header of Website (Top Menu Bar)	-->
<?php include_once('header.php') ?>
<!--  End of Header of Website (Top Menu Bar)	-->

<!--  Begin of Content (Body)	-->
<div id="mainbody">
  <div id="post_content" style="padding-left:20px;padding-right:25px">
      <div id="post_topbar">
      <!--  Top Bar of mypost.php, including the title and the link for posting a new thing	-->
      <? if ($ErrorMsg!="") : ?>
        <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1><?php echo $ErrorMsg; ?></h1></div><br>
        <? else : ?>
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>我的發文<br></div>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>My Post<br></div>
          <? endif; ?>
      <? endif; ?>
      <? if ($_COOKIE['language']=="zh-tw") : ?>
        <div style="text-align:center;;background-color:#000;color:#fff;padding-top:3px;padding-bottom:3px;"><h3 id="Post_Form_Tips">發文過期後將不能瀏覽</h3></div>
      <? elseif ($_COOKIE['language']=="eng") : ?>
        <div style="text-align:center;background-color:#000;color:#fff;padding-top:3px;padding-bottom:3px;"><h3 id="Post_Form_Tips">Posts Can't browse after expiration</h3></div>
      <? endif; ?>
      </div>
      <? if ($_COOKIE['language']=="zh-tw") : ?>
          <button id="post-new-thing" onclick="location.href='post.php'">發佈新東西</button>
        <? elseif ($_COOKIE['language']=="eng") : ?> 
          <button id="post-new-thing" onclick="location.href='post.php'">Post New Thing</button>
        <? endif; ?>
    <div id="result_main_div">
      <br>
      <div id="result_list">

      </div>
 
    </div>
</div>
<div id="cover"></div>
<div class="popupmsg-container" style="width: 400px;height: 180px; margin-top: -90px;margin-left: -200px;" id="post-msg-container"> 
	<p id="post-msg" style="white-space: pre-line">Please Enter Again!</p>
	<div class="popupmsg-option-single" id="post-option-container">
		<a id="post-msg-confirm" href="javascript:void(0);">OK</a>
	</div>
</div>
<!--  End of Content (Body)	-->

<!--  Begin of Footer of Website (Bottom Bar)	-->
<?php include_once('footer.php') ?>
<!--  End of Footer of Website (Bottom Bar)	-->

<script>
document.getElementById('search_again_container').style.display="none";
$('#post-msg-confirm').each(function(){
    $(this).click(function(){ 
        $('#cover').fadeOut('slow');
		$('#post-msg-container').fadeOut('slow');
    });
});

/* 
RemovePost(id)
Request Server to remove the selected post
User can click to Delete Post in this page to trigger this function
*/

function extend_post(id){
  var confirm_msg;
  <? if ($_COOKIE['language']=="zh-tw") : ?>
    confirm_msg="你確定要延長 7天 此發文的持續時間嗎？\n每次延長需要 10點 用戶點數。";
  <? elseif ($_COOKIE['language']=="eng") : ?>
    confirm_msg="Are you sure you want to extend the duration of this post by 7 days? \nEach extension requires 10 user points.";
  <? endif; ?>
  if(confirm(confirm_msg)){
    $.ajax({
      url: 'includes/ac.php',
      type: 'POST',
      data: 
      {
        process:"Reduce_Coins",
        post_id:id,
        Coin_Cost:10
      },
      success: function(result) {	
          if(result=="true"){
              $.ajax({
              url: 'includes/post_function.php',
              type: 'POST',
              data: 
              {
                process:"ExtendPost",
                post_id:id
              },
              success: function(posting_result) {	
                $('#post-msg').text(""+posting_result);
                setTimeout(() => {
                  location.reload();
                }, 1000);
              }
            });
        } else {
          $('#post-msg').text(""+result);
        }
      }
    });
    $('#cover').fadeIn('slow');
    $('#post-msg-container').fadeIn('slow');
  }
}

function RemovePost(id){
  var confirm_msg;
  <? if ($_COOKIE['language']=="zh-tw") : ?>
    confirm_msg="你確定要刪除此發文嗎？\n一經刪除將不能復原。";
  <? elseif ($_COOKIE['language']=="eng") : ?>
    confirm_msg="Are you sure you want to delete this post?\nOnce deleted, it cannot be undone.";
  <? endif; ?>
  if(confirm(confirm_msg)){
    $.ajax({
      url: 'includes/post_function.php',
      type: 'POST',
      data: 
      {
        process:"RemovingPost",
        post_id:id
      },
      success: function(posting_result) {	
        $('#post-msg').text(""+posting_result);
        setTimeout(() => {
          location.reload();
        }, 1000);
      }
    });
    $('#cover').fadeIn('slow');
    $('#post-msg-container').fadeIn('slow');
  }
}

var isMobile = window.matchMedia("only screen and (max-width: 1000px)").matches; // a check device var of first time loading on this page
check_ismobile(); // First Checking of browser resolution / screen size

// Auto Check Again if browser resolution / screen size changed.
var width = $(window).width();
$(window).on('resize', function() {
  if ($(this).width() != width) {
    width = $(this).width();
    check_ismobile();
  }
});

function check_ismobile(){
    document.getElementById('result_list').innerHTML="";
    if(!isMobile){
      htmlcode = "<?php echo gen_result_format(false) ?>";
      document.getElementById('result_list').innerHTML=htmlcode;
    } else {
      htmlcode = "<?php echo gen_result_format(true) ?>";
      document.getElementById('result_list').innerHTML=htmlcode;
    }
}
</script>

</body>
</html>