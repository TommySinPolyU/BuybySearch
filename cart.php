<?php
require_once('includes/connect.php');
$ErrorMsg="";
$result_page_count=0;
$current_page_cart_postID=array();
$current_page_cart_postType=array();
$current_page_cart_postTitle=array();
$current_page_cart_postCurrency=array();
$current_page_cart_postPrice=array();
$current_page_cart_postNickName=array();
$current_page_cart_postimg_Folder=array();
$current_page_cart_postPreview=array();
$current_page_cart_postExpireDateString=array();
$current_page_cart_postPostDateString=array();
$current_page_cart_postStatus=array();
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
$_SESSION['count']=0;
if(!isset($_SESSION['UID'])){
    //Because It is a function for logged-in user.
    //if No any user logged-in, Return page to home page.
    //To Avoid people type URL manually without logged-in.
    header('Location: index.php');
    die();
}
$Table_cart = table_cart;
$cart_count_sql = $conn->prepare("SELECT * FROM $Table_cart WHERE UID = ?");
$cart_count_sql->execute(array($_SESSION['UID']));	
$cart_resultcount = $cart_count_sql->rowCount();
if($cart_resultcount == 1){
    while($row = $cart_count_sql->fetch()) {
        $Result_Cart_PostID = $row['Cart_PostID'];
    }
    $_SESSION['cart_postID'] = $Result_Cart_PostID;
} else {
    $_SESSION['cart_postID'] = array();
}

if(!isset($_SESSION['cart_postID']) || $_SESSION['cart_postID'] == array()){
    $ErrorMsg="Error";
    $_GET['page'] = 1;
} else {
    $_SESSION['cart_postType']=array();
    $_SESSION['cart_postTitle']=array();
    $_SESSION['cart_postCurrency']=array();
    $_SESSION['cart_postPrice']=array();
    $_SESSION['cart_postNickName']=array();
    $_SESSION['cart_postPostDate']=array();
    $_SESSION['cart_postPreview']=array();
    $_SESSION['cart_postimg_Folder']=array();
    $_SESSION['cart_postExpireDate']=array();
    $_SESSION['cart_postStatus']=array();
    $_SESSION['count']=0;
    $_SESSION['cart_postID'] = explode(",", $_SESSION['cart_postID']);
    if($_SESSION['cart_postID'] != ""){
        $postID_separated = implode(",", $_SESSION['cart_postID']);
        $Table_post = table_post;
        if($postID_separated != ""){
            $SQL_getPost = $conn->prepare("SELECT * FROM $Table_post WHERE ID IN ($postID_separated) ORDER BY FIELD(id, $postID_separated)");
            $SQL_getPost->execute();
            $_SESSION['count'] = $SQL_getPost->rowCount();
            if($_SESSION['count']>0){
            while($row = $SQL_getPost->fetch()) {
                $_SESSION['cart_postType'][] = $row['Type'];
                $_SESSION['cart_postTitle'][] = $row['Title'];
                $_SESSION['cart_postCurrency'][] = $row['Currency'];
                $_SESSION['cart_postPrice'][] = $row['Price'];
                $_SESSION['cart_postNickName'][] = $row['PostBy'];
                $_SESSION['cart_postPostDate'][] = $row['PostDate'];
                $_SESSION['cart_postPreview'][] = $row['preview_img'];
                $_SESSION['cart_postimg_Folder'][] = $row['img_folder'];
                $_SESSION['cart_postExpireDate'][] = $row['ExpireDate'];
                $_SESSION['cart_postStatus'][] = $row['Post_Status'];
            }
            }
        }
    }
  $result_page_count=ceil(count($_SESSION['cart_postID'])/10);
  if($result_page_count==0){
    $_GET['page'] = 1;
  } else {
  if(!isset($_GET['page'])||$_GET['page']<1){
    $_GET['page'] = 1; // If page is not set in url or page less than 1, initialize page by code
    header("Location: cart.php?page=".$_GET['page']);
  }
  if($_GET['page']>$result_page_count){
    $_GET['page'] = $result_page_count; // If page more than 1, reset page number to maximum page.
    header("Location: cart.php?page=".$_GET['page']);
  }
  
  /*
  //For Debugging
  print_r("Current Page".$_GET['page']."<br>");
  print_r($_SESSION['cart_postID']);
  print_r("<br>Result Count: ".$_SESSION['count']);
  print_r("<br>Page Count: ".$result_page_count);
  */

  for($i=0; $i<10;$i++){
    if(!((($_GET['page'] -1 )*10+$i >= $_SESSION['count']))){
      $type_convert = $_SESSION['cart_postType'][(($_GET['page'] -1 )*10+$i)];
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
      $current_page_cart_postType[] = $type_convert;
      $current_page_cart_postTitle[] = $_SESSION['cart_postTitle'][(($_GET['page'] -1 )*10+$i)];
      $current_page_cart_postCurrency[] = $_SESSION['cart_postCurrency'][(($_GET['page'] -1 )*10+$i)];
      $current_page_cart_postPrice[] = $_SESSION['cart_postPrice'][(($_GET['page'] -1 )*10+$i)];
      $current_page_cart_postNickName[] = $_SESSION['cart_postNickName'][(($_GET['page'] -1 )*10+$i)];
      $current_page_cart_postPreview[] = $_SESSION['cart_postPreview'][(($_GET['page'] -1 )*10+$i)];
      $current_page_cart_postimg_Folder[] = $_SESSION['cart_postimg_Folder'][(($_GET['page'] -1 )*10+$i)];
      $current_page_cart_postStatus[] = $_SESSION['cart_postStatus'][(($_GET['page'] -1 )*10+$i)];
      $datetime_now = new DateTime(date("Y-m-d H:i:s"));
      $datetime_expire = new DateTime($_SESSION['cart_postExpireDate'][(($_GET['page'] -1 )*10+$i)]);
      $datetime_post = new DateTime($_SESSION['cart_postPostDate'][(($_GET['page'] -1 )*10+$i)]);
      $datediff_expire = $datetime_expire->diff($datetime_now);
      $datediff_post = $datetime_post->diff($datetime_now);
      $isexpired = ($datetime_now>$datetime_expire);
      if ($_COOKIE['language']=="zh-tw"){
        if(($datediff_post->d)>0 && ($datediff_post->h)<24){
          $current_page_cart_postPostDateString[]= $datediff_post->d." 日前";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)>0 && ($datediff_post->h)<=24) {
          $current_page_cart_postPostDateString[]= $datediff_post->h." 小時前";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)<=0 && ($datediff_post->i)<=60 && ($datediff_post->i)>0){
          $current_page_cart_postPostDateString[]= $datediff_post->i." 分鐘前";
        } else {
          $current_page_cart_postPostDateString[]= "剛剛";
        }
      } else if ($_COOKIE['language']=="eng"){
        if(($datediff_post->d)>0 && ($datediff_post->h)<24){
          $current_page_cart_postPostDateString[]= $datediff_post->d." Days ago";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)>0 && ($datediff_post->h)<=24) {
          $current_page_cart_postPostDateString[]= $datediff_post->h." Hours ago";
        } else if(($datediff_post->d)<=0 && ($datediff_post->h)<=0 && ($datediff_post->i)<=60 && ($datediff_post->i)>0){
          $current_page_cart_postPostDateString[]= $datediff_post->i." Mins ago";
        } else {
          $current_page_cart_postPostDateString[]= "just recently";
        }
      }
      
      if ($_COOKIE['language']=="zh-tw"){
        if(($datediff_expire->d)>0 && ($datediff_expire->h)<24){
          $current_page_cart_postExpireDateString[] = "於 ".$datediff_expire->d."日 ".$datediff_expire->h."小時 ".$datediff_expire->i."分鐘 後過期";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)>0 && ($datediff_expire->h)<=24) {
          $current_page_cart_postExpireDateString[] = "於 ".$datediff_expire->h."小時 ".$datediff_expire->i."分鐘 後過期";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)<=0 && ($datediff_expire->i)<=60 && ($datediff_expire->i)>0){
          $current_page_cart_postExpireDateString[] = "於 ".$datediff_expire->i."分鐘 後過期";
        } else {
          $current_page_cart_postExpireDateString[]= "即將過期";
        }
      } else if ($_COOKIE['language']=="eng"){
        if(($datediff_expire->d)>0 && ($datediff_expire->h)<24){
          $current_page_cart_postExpireDateString[] = "Expires after ".$datediff_expire->d."D ".$datediff_expire->h."H ".$datediff_expire->i."M";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)>0 && ($datediff_expire->h)<=24) {
          $current_page_cart_postExpireDateString[] = "Expires after ".$datediff_expire->h."H ".$datediff_expire->i."M";
        } else if(($datediff_expire->d)<=0 && ($datediff_expire->h)<=0 && ($datediff_expire->i)<=60 && ($datediff_expire->i)>0){
          $current_page_cart_postExpireDateString[] = "Expires after ".$datediff_expire->i."M";
        } else {
          $current_page_cart_postExpireDateString[]= "Expiring soon";
        }
      }
      if(!$isexpired && isset($_SESSION['cart_postID'][(($_GET['page'] -1 )*10+$i)])){
        $current_page_cart_postID[] = $_SESSION['cart_postID'][(($_GET['page'] -1 )*10+$i)];
      } else {
        $current_page_cart_postID[] = NULL;
      }
    }
  }
}
}
function gen_result_format($ismobile){ 
  global $result_page_count;
  global $current_page_cart_postID;
  global $current_page_cart_postType;
  global $current_page_cart_postTitle;
  global $current_page_cart_postCurrency;
  global $current_page_cart_postPrice;
  global $current_page_cart_postNickName;
  global $current_page_cart_postPreview;
  global $current_page_cart_postimg_Folder;
  global $current_page_cart_postExpireDateString;
  global $current_page_cart_postPostDateString;
  global $current_page_cart_postStatus;
  if(isset($_GET['page'])){
      $current_page_cart_postcount = 0;
      for($p=0; $p<10; $p++){
        if(isset($current_page_cart_postID[$p])){
          $current_page_cart_postcount+=1;
          switch($_COOKIE['language']){
            case "zh-tw":
              if($current_page_cart_postStatus[$p]=="Idle")
                $current_page_cart_postStatus[$p] = "空閒";
              else if($current_page_cart_postStatus[$p]=="Waiting for confirmation")
                $current_page_cart_postStatus[$p] = "等待確定";
              else if($current_page_cart_postStatus[$p]=="Transaction complete")
                $current_page_cart_postStatus[$p] = "交易已完成";
            break;
          }
        }
      }
      if($current_page_cart_postcount<=0){
        switch($_COOKIE['language']){
            case "eng":
                $ErrorMsg="Cart<br>Nothing in your cart";
            break;
              case "zh-tw":
                $ErrorMsg="購物車<br>目前並沒有任何東西";
            break;
        }
        echo "<div style='text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;'><h1>".$ErrorMsg."</h1></div><br>";
      }
      if(!$ismobile){
        // Gen a HTML Code for PC/High Resolution version display of search results.
          for($i=0; $i<10; $i++){
            if(!((($_GET['page'] -1 )*10+$i >= $_SESSION['count'])) && $result_page_count!=0){
              if($current_page_cart_postID[$i]!=NULL){
                echo "<div id=result-".$i,"><table id='result_table_Col1' width=100%;><tr>";
                echo "<td align='center'width=40%><div class='img-container'><div style='width=100%;height=100%;'><img style='display: block;margin:0 auto;min-width:45%;max-width:100%;height:100%;' src=user_upload_images/".$current_page_cart_postimg_Folder[$i]."/".$current_page_cart_postPreview[$i]."></div></div></td>";
                  echo "<td width=100%><table id='result_table_SubCol1' width=100% style='font-size:19px;'>";
                  if($current_page_cart_postStatus[$i]!="Transaction complete" && $current_page_cart_postStatus[$i]!="交易已完成")
                    echo "<tr><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$current_page_cart_postID[$i].") <a href=viewpost.php?ID=".$current_page_cart_postID[$i].">".$current_page_cart_postTitle[$i]."</a>&nbsp;</tr>";
                  else
                    echo "<tr><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$current_page_cart_postID[$i].") <a>".$current_page_cart_postTitle[$i]."</a>&nbsp;</tr>";
                  echo "<tr><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/category.png>&nbsp;&nbsp;".$current_page_cart_postType[$i]."</td><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$current_page_cart_postStatus[$i]."</tr>";
                  echo "<tr><td width=50%><img style='vertical-align: middle;' src=images/user_bgwhite.png>".$current_page_cart_postNickName[$i]."</td>";
                  if($current_page_cart_postPrice[$i] == 0){
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;Free</td></tr>";
                      break;
                      case "zh-tw":
                        echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;免費</td></tr>";
                      break;
                    }                  
                  } else {
                    echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;".$current_page_cart_postCurrency[$i]." ".$current_page_cart_postPrice[$i]."</td></tr>";
                  }
                  echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_cart_postPostDateString[$i]."</td><td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/expirydate.png>&nbsp;".$current_page_cart_postExpireDateString[$i]."</td></tr>";
                  switch($_COOKIE['language']){
                    case "eng":
                      echo "<tr><td><button class='red_btn' onclick=removefromcart(".$current_page_cart_postID[$i].")> Remove from Cart </button></td></tr>";
                    break;
                    case "zh-tw":
                      echo "<tr><td><button class='red_btn' onclick=removefromcart(".$current_page_cart_postID[$i].")> 從購物車內移除 </button></td></tr>";
                    break;
                  }
                  if($current_page_cart_postStatus[$i]=="Idle" || $current_page_cart_postStatus[$i]=="空閒"){
                    if($current_page_cart_postNickName[$i]!=$_SESSION['NickName']){
                      switch($_COOKIE['language']){
                        case "eng":
                          echo "<tr><td><button class='black_btn' onclick=notify_seller(".$current_page_cart_postID[$i].")> Notify seller to start trading </button></td></tr>";
                        break;
                        case "zh-tw":
                          echo "<tr><td><button class='black_btn' onclick=notify_seller(".$current_page_cart_postID[$i].")> 通知賣家以開始進行交易 </button></td></tr>";
                        break;
                      }
                    }
                  }
                  else if($current_page_cart_postStatus[$i]=="Transaction complete" || $current_page_cart_postStatus[$i]=="交易已完成"){
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<tr><td>This post has been set as <b style='color:red;'>Transaction Completed</b>, You will no longer be able to view the information and notify the seller</td></tr>";
                      break;
                      case "zh-tw":
                        echo "<tr><td>此發文已被設為<b style='color:red;'>交易已完成</b><br>你將不能再查看有關資訊及通知賣家</td></tr>";
                      break;
                    }
                  } else if($current_page_cart_postStatus[$i]=="Waiting for confirmation" || $current_page_cart_postStatus[$i]=="等待確定"){
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<tr><td>This post has been set as <b style='color:red;'>Waiting for confirmation</b>, You will no longer be able to view the information and notify the seller</td></tr>";
                      break;
                      case "zh-tw":
                        echo "<tr><td>此發文已被設為<b style='color:red;'>等待確定</b><br>你暫時不能再通知賣家</td></tr>";
                      break;
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
              if($current_page_cart_postID[$i]!=NULL){
                echo "<div id=result-".$i."><table id='result_table_Col1' width=100%;><tr>";
                  if($current_page_cart_postStatus[$i]!="Transaction complete" && $current_page_cart_postStatus[$i]!="交易已完成")
                    echo "<div class='cart_posttitle' style='text-align:center;'><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$current_page_cart_postID[$i].") <a href=viewpost.php?ID=".$current_page_cart_postID[$i].">".$current_page_cart_postTitle[$i]."</a>&nbsp";
                  else
                    echo "<div class='cart_posttitle' style='text-align:center;'><img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$current_page_cart_postID[$i].") <a>".$current_page_cart_postTitle[$i]."</a>&nbsp";
                  echo "<td align='center'width=40%><div class='img-container'><div style='width=100%;height=100%;'><img style='display: block;margin:0 auto;min-width:45%;max-width:100%;height:100%;' src=user_upload_images/".$current_page_cart_postimg_Folder[$i]."/".$current_page_cart_postPreview[$i]."></div></div></td>";
                  echo "<table id='result_table_SubCol1' width=100% style='font-size:19px;'>";
                    echo "<tr><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/category.png>&nbsp;&nbsp;".$current_page_cart_postType[$i]."</td><td width=50%><img style='vertical-align: middle;' width=32; height=32; src=images/post_status.png>&nbsp;&nbsp;".$current_page_cart_postStatus[$i]."</tr>";
                    echo "<tr><td width=50%><img style='vertical-align: middle;' src=images/user_bgwhite.png>".$current_page_cart_postNickName[$i]."</td>";
                    if($current_page_cart_postPrice[$i] == 0){
                      switch($_COOKIE['language']){
                        case "eng":
                          echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;Free</td></tr>";
                        break;
                        case "zh-tw":
                          echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;免費</td></tr>";
                        break;
                      }                  
                    } else {
                      echo "<td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/money.png>&nbsp;".$current_page_cart_postCurrency[$i]." ".$current_page_cart_postPrice[$i]."</td></tr>";
                    }                    
                    echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_cart_postPostDateString[$i]."</td><td>"."<img style='vertical-align: middle;' width=36; height=36; src=images/expirydate.png>&nbsp;".$current_page_cart_postExpireDateString[$i]."</td></tr>";
                  echo "</table>";
                  switch($_COOKIE['language']){
                    case "eng":
                      echo "<tr><td><button class='red_btn' onclick=removefromcart(".$current_page_cart_postID[$i].")> Remove from Cart </button></td></tr>";
                    break;
                    case "zh-tw":
                      echo "<tr><td><button class='red_btn' onclick=removefromcart(".$current_page_cart_postID[$i].")> 從購物車內移除 </button></td></tr>";
                    break;
                  }
                  if($current_page_cart_postStatus[$i]=="Idle" || $current_page_cart_postStatus[$i]=="空閒"){
                    if($current_page_cart_postNickName[$i]!=$_SESSION['NickName']){
                      switch($_COOKIE['language']){
                        case "eng":
                          echo "<tr><td><button class='black_btn' onclick=notify_seller(".$current_page_cart_postID[$i].")> Notify seller to start trading </button></td></tr>";
                        break;
                        case "zh-tw":
                          echo "<tr><td><button class='black_btn' onclick=notify_seller(".$current_page_cart_postID[$i].")> 通知賣家以開始進行交易 </button></td></tr>";
                        break;
                      }
                    }
                  }
                  else if($current_page_cart_postStatus[$i]=="Transaction complete" || $current_page_cart_postStatus[$i]=="交易已完成"){
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<tr><td>This post has been set as <b style='color:red;'>Transaction Completed</b>, You will no longer be able to view the information and notify the seller</td></tr>";
                      break;
                      case "zh-tw":
                        echo "<tr><td>此發文已被設為<b style='color:red;'>交易已完成</b><br>你將不能再查看有關資訊及通知賣家</td></tr>";
                      break;
                    }
                  } else if($current_page_cart_postStatus[$i]=="Waiting for confirmation" || $current_page_cart_postStatus[$i]=="等待確定"){
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<tr><td>This post has been set as <b style='color:red;'>Waiting for confirmation</b>, You will no longer be able to view the information and notify the seller</td></tr>";
                      break;
                      case "zh-tw":
                        echo "<tr><td>此發文已被設為<b style='color:red;'>等待確定</b><br>你暫時不能再通知賣家</td></tr>";
                      break;
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
	<title> BuybySearch - Cart </title>
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
  <div id="cart_postcontent" style="padding-left:20px;padding-right:25px">
      <div id="cart_posttopbar">
      <? if ($ErrorMsg!="") : ?>
        <!--<div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1><?php// echo $ErrorMsg; ?></h1></div><br>-->
        <? else : ?>
          <? if ($_COOKIE['language']=="zh-tw") : ?>
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>購物車</h1></div><br>
            <a onclick="prev_page()" id="prev_page" class="prev_page">&#10094; 上一頁</a>
            <a onclick="next_page()" id="next_page" class="next_page">下一頁 &#10095;</a>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1>Cart</h1></div><br>
            <a onclick="prev_page()" id="prev_page" class="prev_page">&#10094; Previous Page</a>
            <a onclick="next_page()" id="next_page" class="next_page">Next Page &#10095;</a>
          <? endif; ?>
          <div id="cart_menubar" style="margin-left:35%;margin-right:35%;"> 
            <table width=100%><tr><td>
            <? if ($_COOKIE['language']=="zh-tw") : ?>
                <button class='red_btn' onclick="clearall()">清空購物車</button>
            <? elseif ($_COOKIE['language']=="eng") : ?>
                <button class='red_btn' onclick="clearall()">Clear cart</button>
            <? endif; ?>
            </td></tr></table>
          </div>
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
	<p id="result-msg" style="white-space: pre-line;margin-top: 60px;">Please Enter Again!</p>
</div>
<!--  End of Content (Body)	-->

<!--  Begin of Footer of Website (Bottom Bar)	-->
<?php include_once('footer.php') ?>
<!--  End of Footer of Website (Bottom Bar)	-->

<script>
var page=<?php Print($_GET['page']) ?>; // Get Current Page from PHP
var totalpage = <?php Print(ceil($_SESSION['count']/10)) ?>; // Get Totalpage from PHP
if(totalpage>0)
  check_page();

// Page Controller of Result Page
function prev_page(){
  page-=1;
  location.href="cart.php?page="+page;
}

function next_page(){
  page+=1;
  location.href="cart.php?page="+page;
}

// Check page number for the display of previous / next page button
function check_page(){
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

var isMobile = window.matchMedia("only screen and (max-width: 1000px)").matches; // a check device var of first time loading on this page
check_ismobile(); // First Checking of browser resolution / screen size

// Auto Check Again if browser resolution / screen size changed.
$(window).resize(function() {
  check_ismobile();
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

function clearall(){
    $.ajax({
    url: 'includes/cart_function.php',
    type: 'POST',
    data: 
    {
      cart_clearall:true
    },
    success: function(result) {	
      $('#result-msg').text(""+result);
      $('#cover').fadeIn('slow');
	  $('#result-msg-container').fadeIn('slow');   
      setTimeout(() => {
        $('#cover').fadeOut('slow');
	    $('#result-msg-container').fadeOut('slow');   
        location.reload();
      }, 1000);         
    }
  });
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
document.getElementById('search_again_container').style.display="none";
</script>

</body>
</html>