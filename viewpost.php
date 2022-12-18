<?php
require_once('includes/connect.php');
$ErrorMsg="";
// If ID is null or not set already in URL, then return to index page
if(!$_GET['ID'] || $_GET['ID']<0){
  header('Location: index.php');
  die();
}
// Getting a Post Data from DB with specific Post ID
$Table_ac = table_ac;
$Table_post = table_post;
$SQL_getPost = $conn->prepare("SELECT * FROM $Table_post WHERE ID = ?");
    $SQL_getPost->execute(array($_GET['ID']));
    $count = $SQL_getPost->rowCount();
    if($count<1){
      // If cannot find this ID at DB, then return a error msg on the page.
      switch($_COOKIE['language']){
        case "eng":
          $ErrorMsg="Cannot Find Anything With This ID. Please Input a Corrent Post ID Again.";
        break;
        case "zh-tw":
          $ErrorMsg="找不到此ID的資料，請重新輸入正確的Post ID!";
        break;     
      }
    }else{
      $datetime_now = new DateTime(date("Y-m-d H:i:s"));
      while($row = $SQL_getPost->fetch()) {
        $datetime_expire = new DateTime($row['ExpireDate']);
        $isexpired = ($datetime_now>$datetime_expire);
        if($isexpired){
          switch($_COOKIE['language']){
            case "eng":
              $ErrorMsg="This post has expired!<br>Please Search Again";
            break;
            case "zh-tw":
              $ErrorMsg="此發文已過期!<br>請再次搜索";
            break;     
          }
        break;
        } 
        $post_Type  = $row['Type'];
        switch($_COOKIE['language']){
          case "zh-tw":
            if($post_Type=="Product")
              $post_Type = "產品";
            else if($post_Type=="Service")
              $post_Type = "服務";
            else if($post_Type=="Information")
              $post_Type = "資訊";
          break;
        }
        $post_Title = $row['Title'];
        $post_Desc = $row['Description'];
        $post_Currency = $row['Currency'];
        $post_Price = $row['Price'];
        $post_img_folder = $row['img_folder'];
        $post_img = $row['img_files'];
        $post_NickName = $row['PostBy'];
        $post_UID = $row['PostBy_UID'];
        $post_PostDate = $row['PostDate'];
        $post_LastModifyDate = $row['LastModifyDate'];
        $post_PostStatus = $row['Post_Status'];
      }
      if(!$isexpired){
        $SQL_getEmail= $conn->prepare("SELECT Email FROM $Table_ac WHERE UID = ?");
        $SQL_getEmail->execute(array($post_UID));
        while($row = $SQL_getEmail->fetch()) {
          $post_Email = $row['Email'];
        }
        $post_img_arr = explode(", ",$post_img);
      }
    }

    //echo $post_Type.' '.$post_Title.' '.$post_Desc.' '.$post_Price.' '.$post_img.' '.$post_NickName.' '.$post_UID.' '.$post_PostDate;
?>
<html>
<head>
	<title> BuybySearch </title>
	<style>
	 #reset_search {
		width: 100%;
		background-color: #4CAF50;
		color: white;
		border: none;
		border-radius: 4px;
    cursor: pointer;
    text-align: center;
    height:60px;
	}
	</style>
</head>
<body>
<!--  Begin of Header of Website (Top Menu Bar)	-->
<?php include_once('header.php') ?>
<!--  End of Header of Website (Top Menu Bar)	-->

<!--  Begin of Content (Body)	-->
<div id="mainbody">
<? if (isset($post_PostStatus)) : ?>
  <? if ($post_PostStatus=="Transaction complete") : ?>
      <div style="word-wrap:break-word;text-align:center;color:#000;padding-top:5px;padding-bottom:5px;line-height:1.1;"><h1>
        <? if ($_COOKIE['language']=="zh-tw") : ?>此發文已交易完成，你仍可以查看此發文，<br>直至系統或發文者刪除此發文。
        <? elseif ($_COOKIE['language']=="eng") : ?>This post has been completed. You can still view this post until the system or the sender deletes this post.
        <? endif; ?>
      </h1><br></div>
  <? endif; ?>
<? endif; ?>
<? if (isset($post_PostStatus)) : ?>
  <? if ($post_PostStatus=="Waiting for confirmation") : ?>
    <div style="word-wrap:break-word;text-align:center;color:#000;padding-top:5px;padding-bottom:5px;line-height:1.1;"><h1>
        <? if ($_COOKIE['language']=="zh-tw") : ?>此發文已設為「等待確定」，你仍可以查看此發文，<br>但無法加至購物車及透過訊息系統通知發文者。
        <? elseif ($_COOKIE['language']=="eng") : ?>This post has been set to "waiting for confirmation". You can still view this post, <br> but you cannot add to cart and notify the sender via the message system.
        <? endif; ?>
      </h1><br></div>
  <? endif; ?>
<? endif; ?>
  <div id="post_content" style="padding-left:20px;padding-right:25px">
      <div id="post_topbar">
        <? if ($ErrorMsg!="") : ?>
          <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.4;"><h1><?php echo $ErrorMsg; ?></h1></div><br>
        <? endif; ?>
      <? if (isset($post_PostStatus)) : ?>
        <? if ($ErrorMsg=="") : ?>
          <div style="word-wrap:break-word;text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.4;"><h2><?php echo $post_Type."<br>".$post_Title; ?><br>
            <div class="post_price_container" style="float:right;margin-right:5px;font-size: 20px;">
                <?php echo $post_Currency.' '.$post_Price?>
            </div></h2></div>

      <!-- Post By Info -->
      <div style="display:inline-block;text-align:left;padding-top:10px">
        <? if ($_COOKIE['language']=="zh-tw") : ?>
          <img style="vertical-align: middle;" src="images/user_bgwhite.png" width="28" height="28" >
            <span style="vertical-align: middle;font-size:14px;">發佈者</span><br>
              <a style="padding-left:5px;font-size:14px;"><?php echo $post_NickName.'<a href=mailto:'.$post_Email.' style="padding-left:5px;font-size:14px;">'."傳送電郵"."</a>"; ?></a>
        <? elseif ($_COOKIE['language']=="eng") : ?>
          <img style="vertical-align: middle;" src="images/user_bgwhite.png" width="28" height="28">
              <span style="vertical-align: middle;font-size:14px;">Post By</span><br>
              <a style="padding-left:5px;font-size:14px;"><?php echo $post_NickName.'<a href=mailto:'.$post_Email.' style="padding-left:5px;font-size:14px;">'."Send Email"."</a>"; ?></a>
        <? endif; ?>
        </div>
        <!-- Post Date -->
        <div style="display:inline-block;text-align:right;float:right;padding-right:3px;padding-top:10px;">
        <? if ($_COOKIE['language']=="zh-tw") : ?>
          <img style="vertical-align: middle;padding-right:5px;padding-bottom:4px" src="images/user_bgwhite.png" width="22" height="22" >
            <span style="vertical-align: middle;font-size:14px;">發佈日期</span><br>
            <a style="font-size:14px;"><?php echo $post_PostDate; ?></a><br>
            <i style="font-size:11px;"><?php ($post_LastModifyDate!=NULL) ? Print("最後修改日期: ".$post_LastModifyDate) : Print("") ?></i>
        <? elseif ($_COOKIE['language']=="eng") : ?>
            <img style="vertical-align: middle;padding-right:5px;padding-bottom:4px" src="images/calendar.png" width="22" height="22">
              <span style="vertical-align: middle;font-size:14px;">Post Date</span><br>
              <a style="font-size:14px;"><?php echo $post_PostDate; ?></a><br>
              <i style="font-size:11px;"><?php ($post_LastModifyDate!=NULL) ? Print("Modified At : ".$post_LastModifyDate) : Print("") ?></i>
        <? endif; ?>

        </div>
    <? endif; ?>
    </div>
    <br>
      <div id=post_body style="">
        <? if ($ErrorMsg=="") : ?>
          <div class="post_img_container" style="display: block;margin:0 auto;width: 80%;">
              <!-- Refer to w3schools (https://www.w3schools.com/howto/howto_js_slideshow.asp)>  -->
              <!-- Refer to w3schools (https://www.w3schools.com/howto/howto_css_blurred_background.asp) -->
              <div class="slideshow-container">
                <?php 
                    for($i = 0; $i < sizeof($post_img_arr); $i++){
                      $curdir = dirname($_SERVER['REQUEST_URI'])."/";
                      $path = 'user_upload_images/'.$post_img_folder.'/'.$post_img_arr[$i]; 
                      if ($_COOKIE['language']=="zh-tw"){
                        $zoom_tip_string = "點擊圖片以查看原圖";     
                      } else if ($_COOKIE['language']=="eng"){
                        $zoom_tip_string = "Click on the image to see the original size";  
                      }             
                      echo "<div class='mySlides fade' style='text-align:center;margin:auto; vertical-align:middle;'><div class='numbertext'>"."</div><div style='width=100%;height=100%;'><img onclick='zoom_img(this.src)' src=$path style='display: block;margin:0 auto;min-width:45%;max-width:100%;height:100%;'></img></div><br><p>$zoom_tip_string</p></div>";
                    }
                ?>
                
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
              </div>
              <br>
              <div style="text-align:center;">
                  <?php 
                      for($i = 0; $i < sizeof($post_img_arr); $i++){
                        echo "<span class='dot' onclick='currentSlide(".($i+1).")"."'></span>";
                      }
                  ?>
              </div>
          </div>
          <br>
      </div>
      <div class="post_desc_container" style="padding-top:40px;padding-bottom:50px;display: block;margin-left: auto;margin-right: auto;">
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <span style="vertical-align: middle;font-size:26px;"><b>簡介</b></span><br><br>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <span style="vertical-align: middle;font-size:26px;"><b>Description</b></span><br><br>
          <? endif; ?>
            <a style="font-size:18px;word-wrap: break-word;"><?php echo $post_Desc?></a>
          </div>
        <? endif; ?>
        <div id="zoomed_img_container" style="display:none;text-align:center;">
        </div>
        <? if($post_PostStatus!="Waiting for confirmation" && $post_PostStatus!="Transaction complete") : ?>
          <? if ($isexpired == false) : ?>
            <? if(isset($_SESSION['NickName'])) : ?>
                <? if ($post_NickName!=$_SESSION['NickName']) : ?>
                  <button class='green_btn' onclick=addtocart(<?php echo $_GET['ID'] ?>)>
                    <? if ($_COOKIE['language']=="zh-tw") : ?>加至購物車
                    <? elseif ($_COOKIE['language']=="eng") : ?>Add to Cart 
                    <? endif; ?>
                  </button>
                  <button class='black_btn' onclick=notify_seller(<?php echo $_GET['ID'] ?>)> 
                    <? if ($_COOKIE['language']=="zh-tw") : ?>通知賣家以開始進行交易
                    <? elseif ($_COOKIE['language']=="eng") : ?>Notify seller to start trading
                    <? endif; ?>
                  </button>
                <? endif; ?>
            <? endif; ?>   
            <? if(isset($_SESSION['NickName'])) : ?>
                <? if ($post_NickName==$_SESSION['NickName'] && ($post_PostStatus!="Transaction complete")) : ?>
                  <button class='black_btn' onclick=window.location.href="post.php?PostID=<?php echo $_GET['ID'] ?>"> 
                      <? if ($_COOKIE['language']=="zh-tw") : ?>修改發文
                      <? elseif ($_COOKIE['language']=="eng") : ?>Edit Post
                      <? endif; ?>
                    </button>  
                <? endif; ?>
            <? endif; ?>   
          <? endif; ?>
        <? endif; ?>
      <? endif; ?>
  </div>
</div>
  <div id="zoomed_img_cover"></div>
  <div id="cover"></div>
  <div class="popupmsg-container" style="width: 400px;height: 180px; margin-top: -90px;margin-left: -200px;" id="result-msg-container"> 
  	<p id="result-msg" style="white-space: pre-line;margin-top: 60px">Please Enter Again!</p>
  </div>
<!--  End of Content (Body)	-->

<!--  Begin of Footer of Website (Bottom Bar)	-->
<?php include_once('footer.php') ?>
<!--  End of Footer of Website (Bottom Bar)	-->

<script>
var zoomed_img_isopen = false;
var zoomed_img_src = "";
var ErrorMsg = "<?php echo $ErrorMsg ?>";
document.getElementById('searchby_type_selection').style.display="none";
if(ErrorMsg==""){
  var slideIndex = 1;
  setTimeout(showSlides, 0,1);
  const interval = setInterval(function() {
    plusSlides(1);
  }, 5000);
}
function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  if(!document.getElementsByClassName("mySlides")){
    return;
  }
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
  dots[slideIndex-1].className += " active";
}

$('#zoomed_img_cover').each(function(){
    $(this).click(function(){ 
      zoomed_img_isopen = false;
      $('#zoomed_img_cover').fadeOut('slow');
		  $('#zoomed_img_container').fadeOut('slow');
    });
});

function close_zoomimg(){
  zoomed_img_isopen = false;
  $('#zoomed_img_cover').fadeOut('slow');
	$('#zoomed_img_container').fadeOut('slow');
}

  $(window).resize(function() {
    if(zoomed_img_isopen){
      zoom_img(zoomed_img_src);
    }
  });


function zoom_img(src){
  zoomed_img_isopen = true;
  zoomed_img_src = src;
  var img = new Image();
  img.src = src;
  if($(window).width() > (img.width+5) && $(window).height() > (img.height+5)){
    // If Device Screen Width and Height More than Image Width and Height
    document.getElementById('zoomed_img_container').style.marginTop=(-(img.height/2));
    document.getElementById('zoomed_img_container').style.marginLeft=(-(img.width/2));
    var htmlcode = '<img width='+(img.width)+'px; height='+(img.height)+'px; id="zoomed_img" src="'+ src +'"></img><br><img width=42 height=42 style="position: absolute;top: 5%;left: 5%;transform: translate(-50%, -50%);" src="images/close.png" onclick="close_zoomimg()">';
    //alert("Size More than");
  } else if($(window).width() > (img.width+5) && $(window).height() < (img.height+5)) {
    // If Device Screen Width More than Image Width but Screen Height Less than Image Height
    document.getElementById('zoomed_img_container').style.marginTop=(-($(window).height()/2));
    document.getElementById('zoomed_img_container').style.marginLeft=(-(img.width/2));
    var htmlcode = '<img width='+(img.width)+'px; height='+($(window).height())+'px; id="zoomed_img" src="'+ src +'"></img><br><img width=42 height=42 style="position: absolute;top: 5%;left: 5%;transform: translate(-50%, -50%);" src="images/close.png" onclick="close_zoomimg()">';
    //alert("Width More than");
  } else if($(window).width() < (img.width+5) && $(window).height() < (img.height+5)) {
    // If Device Screen Width and Height Less than Image Width and Height
    document.getElementById('zoomed_img_container').style.marginTop=(-($(window).height()/2));
    document.getElementById('zoomed_img_container').style.marginLeft=(-($(window).width()/2));
    var htmlcode = '<img width='+($(window).width())+'px; height='+($(window).height())+'px; id="zoomed_img" src="'+ src +'"></img><br><img width=42 height=42 style="position: absolute;top: 5%;left: 5%;transform: translate(-50%, -50%);" src="images/close.png" onclick="close_zoomimg()">';
    //alert("Size Less than");
  } else if($(window).width() < (img.width+5) && $(window).height() > (img.height+5)) {
    // If Device Screen Width Less than Image Width but Screen Height More than Image Height
    document.getElementById('zoomed_img_container').style.marginTop=(-(img.height/2));
    document.getElementById('zoomed_img_container').style.marginLeft=(-($(window).width()/2));
    var htmlcode = '<img width='+($(window).width())+'px; height='+(img.height)+'px; id="zoomed_img" src="'+ src +'"></img><br><img width=42 height=42 style="position: absolute;top: 5%;left: 5%;transform: translate(-50%, -50%);" src="images/close.png" onclick="close_zoomimg()">';
    //alert("Height More than");
  } 
  document.getElementById('zoomed_img_container').innerHTML = htmlcode;
  $('#zoomed_img_cover').fadeIn('slow');
	$('#zoomed_img_container').fadeIn('slow');
}
</script>

</body>
</html>