<?php
require_once('includes/connect.php');
$ErrorMsg="";
$result_page_count=0;
$current_page_post_ID=array();
$current_page_post_Type=array();
$current_page_post_Title=array();
$current_page_post_Currency=array();
$current_page_post_Price=array();
$current_page_post_NickName=array();
$current_page_post_img_Folder=array();
$current_page_post_Preview=array();
$current_page_post_ExpireDateString=array();
$current_page_post_PostDateString=array();
$current_page_post_PostStatus=array();

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

// A New Search Request has been sent by search bar
if(isset($_POST['search_request_send'])){
  unset($_SESSION['post_ID']);
  if(!isset($_GET['page'])||$_GET['page']<1){
    $_GET['page'] = 1; // If page is not set in url or page less than 1, initialize page by code
    header("Location: search_result.php?page=".$_GET['page']);
  }
  if($_GET['page']>$result_page_count){
    $_GET['page'] = $result_page_count; // If page more than 1, reset page number to maximum page.
    header("Location: search_result.php?page=".$_GET['page']);
  }
}

if(isset($_SESSION['post_ID'])){
  $result_page_count=ceil($_SESSION['count']/10);
  if($result_page_count==0){
    switch($_COOKIE['language']){
      case "eng":
        $ErrorMsg="Nothing Found";
      break;
      case "zh-tw":
        $ErrorMsg="找不到任何相關記錄";
      break;
    }
  } else {
  if(!isset($_GET['page'])||$_GET['page']<1){
    $_GET['page'] = 1; // If page is not set in url or page less than 1, initialize page by code
    header("Location: search_result.php?page=".$_GET['page']);
  }
  if($_GET['page']>$result_page_count){
    $_GET['page'] = $result_page_count; // If page more than 1, reset page number to maximum page.
    header("Location: search_result.php?page=".$_GET['page']);
  }
  if($result_page_count==0){
    header('Location: index.php');
    die();
  }
  
  /*
  //For Debugging
  print_r("Current Page".$_GET['page']."<br>");
  print_r($_SESSION['post_ID']);
  print_r("<br>Result Count: ".$_SESSION['count']);
  print_r("<br>Page Count: ".$result_page_count);
  */

  for($i=0; $i<10;$i++){
    if(!((($_GET['page'] -1 )*10+$i >= $_SESSION['count']))){
      $type_convert = $_SESSION['post_Type'][(($_GET['page'] -1 )*10+$i)];
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
      $status_convert = $_SESSION['post_status'][(($_GET['page'] -1 )*10+$i)];
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
      $current_page_post_Type[] = $type_convert;
      $current_page_post_Title[] = $_SESSION['post_Title'][(($_GET['page'] -1 )*10+$i)];
      $current_page_post_Currency[] = $_SESSION['post_Currency'][(($_GET['page'] -1 )*10+$i)];
      $current_page_post_Price[] = $_SESSION['post_Price'][(($_GET['page'] -1 )*10+$i)];
      $current_page_post_NickName[] = $_SESSION['post_NickName'][(($_GET['page'] -1 )*10+$i)];
      $current_page_post_Preview[] = $_SESSION['post_Preview'][(($_GET['page'] -1 )*10+$i)];
      $current_page_post_img_Folder[] = $_SESSION['post_img_Folder'][(($_GET['page'] -1 )*10+$i)];
      $current_page_post_PostStatus[] = $status_convert;
      $datetime_now = new DateTime(date("Y-m-d H:i:s"));
      $datetime_expire = new DateTime($_SESSION['post_ExpireDate'][(($_GET['page'] -1 )*10+$i)]);
      $datetime_post = new DateTime($_SESSION['post_PostDate'][(($_GET['page'] -1 )*10+$i)]);
      $datediff_expire = $datetime_expire->diff($datetime_now);
      $datediff_post = $datetime_post->diff($datetime_now);
      $isexpired = ($datetime_now>$datetime_expire);
      if ($_COOKIE['language']=="zh-tw"){
        if(($datediff_post->d)>0 && ($datediff_post->h)<24){
          $current_page_post_PostDateString[]= $datediff_post->d." 日前";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)>0 && ($datediff_post->h)<=24) {
          $current_page_post_PostDateString[]= $datediff_post->h." 小時前";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)<=0 && ($datediff_post->i)<=60 && ($datediff_post->i)>0){
          $current_page_post_PostDateString[]= $datediff_post->i." 分鐘前";
        } else {
          $current_page_post_PostDateString[]= "剛剛";
        }
      } else if ($_COOKIE['language']=="eng"){
        if(($datediff_post->d)>0 && ($datediff_post->h)<24){
          $current_page_post_PostDateString[]= $datediff_post->d." Days ago";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)>0 && ($datediff_post->h)<=24) {
          $current_page_post_PostDateString[]= $datediff_post->h." Hours ago";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)<=0 && ($datediff_post->i)<=60 && ($datediff_post->i)>0){
          $current_page_post_PostDateString[]= $datediff_post->i." Mins ago";
        } else {
          $current_page_post_PostDateString[]= "just recently";
        }
      }
      
      if ($_COOKIE['language']=="zh-tw"){
        if(($datediff_expire->d)>0 && ($datediff_expire->h)<24){
          $current_page_post_ExpireDateString[] = "於 ".$datediff_expire->d."日 ".$datediff_expire->h."小時 ".$datediff_expire->i."分鐘 後過期";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)>0 && ($datediff_expire->h)<=24) {
          $current_page_post_ExpireDateString[] = "於 ".$datediff_expire->h."小時 ".$datediff_expire->i."分鐘 後過期";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)<=0 && ($datediff_expire->i)<=60 && ($datediff_expire->i)>0){
          $current_page_post_ExpireDateString[] = "於 ".$datediff_expire->i."分鐘 後過期";
        } else {
          $current_page_post_ExpireDateString[]= "即將過期";
        }
      } else if ($_COOKIE['language']=="eng"){
        if(($datediff_expire->d)>0 && ($datediff_expire->h)<24){
          $current_page_post_ExpireDateString[] = "Expires after ".$datediff_expire->d."D ".$datediff_expire->h."H ".$datediff_expire->i."M";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)>0 && ($datediff_expire->h)<=24) {
          $current_page_post_ExpireDateString[] = "Expires after ".$datediff_expire->h."H ".$datediff_expire->i."M";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)<=0 && ($datediff_expire->i)<=60 && ($datediff_expire->i)>0){
          $current_page_post_ExpireDateString[] = "Expires after ".$datediff_expire->i."M";
        } else {
          $current_page_post_ExpireDateString[]= "Expiring soon";
        }
      }
      if(!$isexpired && $_SESSION['post_status'][(($_GET['page'] -1 )*10+$i)]!="Transaction complete"){
        $current_page_post_ID[] = $_SESSION['post_ID'][(($_GET['page'] -1 )*10+$i)];
      } else {
        $current_page_post_ID[] = NULL;
      }
    }
  }
}
} else {
    $_SESSION['post_ID']=array();
    $_SESSION['post_Type']=array();
    $_SESSION['post_Title']=array();
    $_SESSION['post_Currency']=array();
    $_SESSION['post_Price']=array();
    $_SESSION['post_NickName']=array();
    $_SESSION['post_PostDate']=array();
    $_SESSION['post_Preview']=array();
    $_SESSION['post_img_Folder']=array();
    $_SESSION['post_ExpireDate']=array();
    $_SESSION['post_status']=array();
    $_SESSION['count']=0;
    if($_POST['search_type']=="SearchBy_ID"){
      if($_POST['SearchBy_ID'] ){
        header('Location: viewpost.php?ID='.$_POST['SearchBy_ID']);
        die();
      } else {
        header('Location: index.php');
        die();
      }
    } else if ($_POST['search_type']=="SearchBy_Type" && $_POST['SearchBy_Type']){
        $Table_post = table_post;
        $SQL_getPost = $conn->prepare("SELECT * FROM $Table_post WHERE Type = ?");
        $SQL_getPost->execute(array($_POST['SearchBy_Type']));
    } else if($_POST['search_type']=="SearchBy_UID" && $_POST['SearchBy_UID']){
        $Table_post = table_post;
        $SQL_getPost = $conn->prepare("SELECT * FROM $Table_post WHERE PostBy_UID = ?");
        $SQL_getPost->execute(array($_POST['SearchBy_UID']));
    } else if($_POST['search_type']=="SearchBy_Keywords" && $_POST['SearchBy_Keywords']){
        $SearchBy_Keywords = $_POST['SearchBy_Keywords'];
        $Table_post = table_post;
        $SQL_getPost = $conn->prepare("SELECT * FROM $Table_post WHERE Title LIKE '%$SearchBy_Keywords%' OR Description LIKE '%$SearchBy_Keywords%' OR PostBy LIKE '%$SearchBy_Keywords%'");
        $SQL_getPost->execute();
    } else {
      header('Location: index.php');
      die();
    }
    $_SESSION['count'] = $SQL_getPost->rowCount();
    if($_SESSION['count']>0){
      while($row = $SQL_getPost->fetch()) {
        $_SESSION['post_ID'][] = $row['ID'];
        $_SESSION['post_Type'][] = $row['Type'];
        $_SESSION['post_Title'][] = $row['Title'];
        $_SESSION['post_Currency'][] = $row['Currency'];
        $_SESSION['post_Price'][] = $row['Price'];
        $_SESSION['post_NickName'][] = $row['PostBy'];
        $_SESSION['post_PostDate'][] = $row['PostDate'];
        $_SESSION['post_Preview'][] = $row['preview_img'];
        $_SESSION['post_img_Folder'][] = $row['img_folder'];
        $_SESSION['post_ExpireDate'][] = $row['ExpireDate'];
        $_SESSION['post_status'][] = $row['Post_Status'];
      }
    }
  }
function gen_result_format($ismobile){ 
  global $ErrorMsg;
  global $result_page_count;
  global $current_page_post_ID;
  global $current_page_post_Type;
  global $current_page_post_Title;
  global $current_page_post_Currency;
  global $current_page_post_Price;
  global $current_page_post_NickName;
  global $current_page_post_Preview;
  global $current_page_post_img_Folder;
  global $current_page_post_ExpireDateString;
  global $current_page_post_PostDateString;
  global $current_page_post_PostStatus;
  if(isset($_GET['page'])){
      $current_page_post_count = 0;
      for($p=0; $p<10; $p++){
        if(isset($current_page_post_ID[$p])){
          $current_page_post_count+=1;
        }
      }
      if($current_page_post_count<=0){
        switch($_COOKIE['language']){
          case "eng":
            $ErrorMsg="Nothing Found";
          break;
          case "zh-tw":
            $ErrorMsg="找不到任何相關記錄";
          break;
        }
        echo "<div style='text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;'><h1>".$ErrorMsg."</h1></div><br>";
        $result_page_count=0;
      }
      if(!$ismobile){
        // Gen a HTML Code for PC/High Resolution version display of search results.
          for($i=0; $i<10; $i++){
            if(!((($_GET['page'] -1 )*10+$i >= $_SESSION['count'])) && $result_page_count!=0){
              if($current_page_post_ID[$i]!=NULL){
                echo "<div id=result-".$i,"><table id='result_table_Col1' width=100%;><tr>";
                echo "<td align='center'width=40%><div class='img-container'><div style='width=100%;height=100%;'><img style='display: block;margin:0 auto;min-width:45%;max-width:100%;height:100%;' src=user_upload_images/".$current_page_post_img_Folder[$i]."/".$current_page_post_Preview[$i]."></div></div></td>";
                  echo "<td width=100%><table id='result_table_SubCol1' width=100% style='font-size:19px;'>";
                  echo "<tr><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$current_page_post_ID[$i].") <a href=viewpost.php?ID=".$current_page_post_ID[$i].">".$current_page_post_Title[$i]."</a>&nbsp;</tr>";
                  echo "<tr><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/category.png>&nbsp;&nbsp;".$current_page_post_Type[$i]."</td><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$current_page_post_PostStatus[$i]."</tr>";
                  echo "<tr><td width=50%><img style='vertical-align: middle;' src=images/user_bgwhite.png>".$current_page_post_NickName[$i]."</td>";
                  if($current_page_post_Price[$i] == 0){
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;Free</td></tr>";
                      break;
                      case "zh-tw":
                        echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;免費</td></tr>";
                      break;
                    }                  
                  } else {
                    echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;".$current_page_post_Currency[$i]." ".$current_page_post_Price[$i]."</td></tr>";
                  }
                  echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_post_PostDateString[$i]."</td><td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/expirydate.png>&nbsp;".$current_page_post_ExpireDateString[$i]."</td></tr>";
                  if($current_page_post_PostStatus[$i]!="等待確定" && $current_page_post_PostStatus[$i]!="Waiting for confirmation"){
                    if(isset($_SESSION['NickName'])){
                      if($current_page_post_NickName[$i]!=$_SESSION['NickName']){
                        switch($_COOKIE['language']){
                          case "eng":
                            echo "<tr><td><button class='green_btn' onclick=addtocart(".$current_page_post_ID[$i].")> Add to Cart </button></td></tr>";
                          break;
                          case "zh-tw":
                            echo "<tr><td><button class='green_btn' onclick=addtocart(".$current_page_post_ID[$i].")> 加至購物車 </button></td></tr>";
                          break;
                        }
                      } else {
                        switch($_COOKIE['language']){
                          case "eng":
                            echo "<tr><td><button class='black_btn' onclick=window.location.href='post.php?PostID=".$current_page_post_ID[$i]."'> Edit Post </button></td></tr>";
                          break;
                          case "zh-tw":
                            echo "<tr><td><button class='black_btn' onclick=window.location.href='post.php?PostID=".$current_page_post_ID[$i]."'> 修改發文 </button></td></tr>";
                          break;
                        }
                      }
                  }
                }
              echo "</table></td>";
            echo "</tr></table><br></div>";
            echo "<hr class='style-two' />";
            }
          }
        }
      } else {
        // Gen a HTML Code for mobile version display of search results.
          for($i=0; $i<10; $i++){
            if(!((($_GET['page'] -1 )*10+$i >= $_SESSION['count'])) && $result_page_count!=0){
              if($current_page_post_ID[$i]!=NULL){
                echo "<div id=result-".$i."><table id='result_table_Col1' width=100%;><tr>";
                  echo "<div class='post_title' style='text-align:center;'><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$current_page_post_ID[$i].") <a href=viewpost.php?ID=".$current_page_post_ID[$i].">".$current_page_post_Title[$i]."</a>&nbsp";
                  echo "<td align='center'width=40%><div class='img-container'><div style='width=100%;height=100%;'><img style='display: block;margin:0 auto;min-width:45%;max-width:100%;height:100%;' src=user_upload_images/".$current_page_post_img_Folder[$i]."/".$current_page_post_Preview[$i]."></div></div></td>";
                  echo "<table id='result_table_SubCol1' width=100% style='font-size:19px;'>";
                    echo "<tr><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/category.png>&nbsp;&nbsp;".$current_page_post_Type[$i]."</td><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$current_page_post_PostStatus[$i]."</tr>";
                    echo "<tr><td width=50%><img style='vertical-align: middle;' src=images/user_bgwhite.png>".$current_page_post_NickName[$i]."</td>";
                    if($current_page_post_Price[$i] == 0){
                      switch($_COOKIE['language']){
                        case "eng":
                          echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;Free</td></tr>";
                        break;
                        case "zh-tw":
                          echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;免費</td></tr>";
                        break;
                      }                  
                    } else {
                      echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;".$current_page_post_Currency[$i]." ".$current_page_post_Price[$i]."</td></tr>";
                    }                    
                    echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_post_PostDateString[$i]."</td><td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/expirydate.png>&nbsp;".$current_page_post_ExpireDateString[$i]."</td></tr>";
                    echo "</table>";
                    if($current_page_post_PostStatus[$i]!="等待確定" && $current_page_post_PostStatus[$i]!="Waiting for confirmation"){
                      if(isset($_SESSION['NickName'])){
                        if($current_page_post_NickName[$i]!=$_SESSION['NickName']){
                          switch($_COOKIE['language']){
                            case "eng":
                              echo "<tr><td width=100%><button class='green_btn' onclick=addtocart(".$current_page_post_ID[$i].")> Add to Cart </button></td></tr>";
                            break;
                            case "zh-tw":
                              echo "<tr><td width=100%><button class='green_btn' onclick=addtocart(".$current_page_post_ID[$i].")> 加至購物車 </button></td></tr>";
                            break;
                          }
                        }
                      }
                    }
              echo "</tr></table><br></div>";
              echo "<hr class='style-two' />";
            }
          }
        }
    }
  }
}
?>
<html>
<head>
	<title> BuybySearch - Search Result </title>
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
  <div id="post_content" style="padding-left:20px;padding-right:25px">
      <div id="post_topbar">
      <? if ($ErrorMsg!="") : ?>
        <!--<div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1><?php// echo $ErrorMsg; ?></h1></div><br>-->
        <? else : ?>
            <? if ($_COOKIE['language']=="zh-tw") : ?>
              <? if ($result_page_count<=0) : ?>
                <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>搜尋結果</h1></div><br>
                <br><br><br>
              <? endif; ?>
              <a onclick="prev_page()" id="prev_page" class="prev_page">&#10094; 上一頁</a>
              <a onclick="next_page()" id="next_page" class="next_page">下一頁 &#10095;</a>
            <? elseif ($_COOKIE['language']=="eng") : ?>
              <? if ($result_page_count<=0) : ?>
                <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>Search Results</h1></div><br>
                <br><br><br>
              <? endif; ?>
              <a onclick="prev_page()" id="prev_page" class="prev_page">&#10094; Previous Page</a>
              <a onclick="next_page()" id="next_page" class="next_page">Next Page &#10095;</a>
            <? endif; ?>

      <? endif; ?>
      </div>
      <div id="result_main_div" >
              <div id="result_list">

              </div>
      </div>
  </div>
</div>
<div id="cover"></div>
<div class="popupmsg-container" style="width: 400px;height: 180px; margin-top: -90px;margin-left: -200px;" id="result-msg-container"> 
	<p id="result-msg" style="white-space: pre-line;margin-top: 60px">Please Enter Again!</p>
</div>
<!--  End of Content (Body)	-->

<!--  Begin of Footer of Website (Bottom Bar)	-->
<?php include_once('footer.php') ?>
<!--  End of Footer of Website (Bottom Bar)	-->

<script>
var page=<?php Print($_GET['page']) ?>; // Get Current Page from PHP
var totalpage = <?php Print(ceil($_SESSION['count']/10)) ?>; // Get Totalpage from PHP
check_page();


// Page Controller of Result Page
function prev_page(){
  page-=1;
  location.href="search_result.php?page="+page;
}

function next_page(){
  page+=1;
  location.href="search_result.php?page="+page;
}

// Check page number for the display of previous / next page button
function check_page(){
  if(totalpage>0){
    if((page-1)<=0){
      document.getElementById('prev_page').style.display="none";
    } else {
      document.getElementById('prev_page').style.display="";
    }
    if((page+1)>totalpage){
      document.getElementById('next_page').style.display="none";
    } else {
      document.getElementById('next_page').style.display="";
    }
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

/*
// JavaScript Version for Changing The Page of Results
// Changed To PHP Version Now
var result_count = <?php// Print($_SESSION['count']) ?>;
var totalpage = <?php// Print(ceil($_SESSION['count']/10)) ?>;
var page=0;
hide_all_result();
move_to_page(page);
document.getElementById('prev_page').style.display="none";

function hide_all_result(){
  for(var i = 0; i < result_count; i++){
    var Current_ID = "result-"+i;
    document.getElementById(Current_ID).style.display="none";
  }
}

function check_page(){
  if((page-1)<0){
    document.getElementById('prev_page').style.display="none";
  } else {
    document.getElementById('prev_page').style.display="";
  }
  if((page+1)>totalpage-1){
    document.getElementById('next_page').style.display="none";
  } else {
    document.getElementById('next_page').style.display="";
  }
}

function prev_page(){
  hide_all_result();
  page-=1;
  if(page<0){
      page=0;
  }
  move_to_page(page);
}

function next_page(){
  hide_all_result();
  page+=1;
  if(page>totalpage-1){
      page=totalpage-1;
  } 
  move_to_page(page);
}

function move_to_page(page){
  check_page();
  for(var i = 0; i < 10; i++){
    var Current_ID = "result-"+(page*10+i);
    if((page*10+i)<result_count){
      document.getElementById(Current_ID).style.display="";
    }
    else {
      return;
    }
  }
}
*/
document.getElementById('searchby_type_selection').style.display="none";
</script>

</body>
</html>