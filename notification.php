<?php
require_once('includes/connect.php');
$ErrorMsg="";
$PageTitle="";
$result_page_count=0;
$current_page_notify_MsgID=array();
$current_page_notify_PostID=array();
$current_page_notify_Post_Status=array();
$current_page_notify_Post_PostBy=array();
$current_page_notify_PostTitle=array();
$current_page_notify_FromNickName=array();
$current_page_notify_ToNickName=array();
$current_page_notify_Msg=array();
$current_page_notify_SendDate=array();
$current_page_notify_Status=array();
$current_page_notify_System_Reply_PostID=array();
$current_page_notify_ReplyMsgID=array();
$current_page_notify_ReplyMsg_Msg=array();
$current_page_notify_ReplyMsg_SendDate=array();
$current_page_notify_ReplyMsg_FromNickName=array();
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

if(!isset($_SESSION['UID'])){
    //Because It is a function for logged-in user.
    //if No any user logged-in, Return page to home page.
    //To Avoid people type URL manually without logged-in.
    header('Location: index.php');
    die();
}

$Table_post = table_post;
$Table_notification = table_notification;
  $_SESSION['notify_MsgID']=array();
  $_SESSION['notify_PostID']=array();
  $_SESSION['notify_Post_Status']=array();
  $_SESSION['notify_Post_PosyBy']=array();
  $_SESSION['notify_PostTitle']=array();
  $_SESSION['notify_System_Reply_PostID']=array();
  $_SESSION['notify_FromNickName']=array();
  $_SESSION['notify_ToNickName']=array();
  $_SESSION['notify_Msg']=array();
  $_SESSION['notify_SendDate']=array();
  $_SESSION['notify_Status']=array();
  $_SESSION['notify_ReplyMsgID']=array();
  $_SESSION['notify_ReplyMsg_Msg']=array();
  $_SESSION['notify_ReplyMsg_SendDate']=array();
  $_SESSION['notify_ReplyMsg_FromNickName']=array();
  $_SESSION['count']=0;


  if(!isset($_SESSION['NotifyType']))
    $_SESSION['NotifyType'] = "Receive";

  if(isset($_POST['notify_type_submit'])){
    if($_POST['notify_type']=="Receive"){
      $_SESSION['NotifyType'] = "Receive";
    }
	  else if($_POST['notify_type']=="Sent"){
      $_SESSION['NotifyType'] = "Sent";
    }
  }
    if($_SESSION['NotifyType']=="Receive"){
      $SQL_getNotification_PostID = $conn->prepare("SELECT DISTINCT PostID FROM $Table_notification WHERE To_NickName = ? ORDER BY PostID ASC, Send_Date DESC");
      $SQL_getNotification_PostID->execute(array($_SESSION['NickName']));
      switch($_COOKIE['language']){
        case "eng":
          $PageTitle = "Notification Center - Inbox";
        break;
        case "zh-tw":
          $PageTitle = "訊息中心 - 收件箱";
        break;
      }
    } else if($_SESSION['NotifyType']=="Sent"){
      $SQL_getNotification_PostID = $conn->prepare("SELECT DISTINCT PostID FROM $Table_notification WHERE From_NickName = ? ORDER BY PostID ASC, Send_Date DESC");
      $SQL_getNotification_PostID->execute(array($_SESSION['NickName']));
      switch($_COOKIE['language']){
        case "eng":
          $PageTitle = "Notification Center - Outbox";
        break;
        case "zh-tw":
          $PageTitle = "訊息中心 - 寄件箱";
        break;
      }
    }
    $_SESSION['count'] = $SQL_getNotification_PostID->rowCount();
    if($_SESSION['count']>0){
      while($Notification_PostID_Result = $SQL_getNotification_PostID->fetch()) {
          $_SESSION['notify_PostID'][] = $Notification_PostID_Result['PostID'];
          $Total_Reply_Msg=array();
          $Total_Reply_MsgDate=array();
          $Total_Reply_MsgNN=array();
          if($_SESSION['NotifyType']=="Receive"){
            $SQL_getNotification = $conn->prepare("SELECT * FROM $Table_notification WHERE PostID = ? AND To_NickName = ? ORDER BY Send_Date DESC");
            $SQL_getNotification->execute(array($Notification_PostID_Result['PostID'],$_SESSION['NickName']));
          } else if($_SESSION['NotifyType']=="Sent"){
            $SQL_getNotification = $conn->prepare("SELECT * FROM $Table_notification WHERE PostID = ? AND From_NickName = ? ORDER BY Send_Date DESC");
            $SQL_getNotification->execute(array($Notification_PostID_Result['PostID'],$_SESSION['NickName']));
          }

          $SQL_getPostTitle = $conn->prepare("SELECT DISTINCT * FROM $Table_post WHERE ID = ?");
          $SQL_getPostTitle->execute(array($Notification_PostID_Result['PostID']));
          while($Title_Result = $SQL_getPostTitle->fetch()) {
            $_SESSION['notify_PostTitle'][] = $Title_Result['Title'];
            $_SESSION['notify_Post_Status'][] = $Title_Result['Post_Status'];
            $_SESSION['notify_Post_PosyBy'][] = $Title_Result['PostBy'];
          }

          $CurrentMsg_ID = array();
          $CurrentMsg_FromNickName = array();
          $CurrentMsg_ToNickName = array();
          $CurrentMsg_Msg = array();
          $CurrentMsg_ReplyMsgID = array();
          $CurrentMsg_SendDate = array();
          $CurrentMsg_Status = array();
          $CurrentMsg_System_Reply_PostID = array();
          while($Notification_Msg_Result = $SQL_getNotification->fetch()) {
            array_push($CurrentMsg_ID,$Notification_Msg_Result['Msg_ID']);
            array_push($CurrentMsg_FromNickName,$Notification_Msg_Result['From_NickName']);
            array_push($CurrentMsg_ToNickName,$Notification_Msg_Result['To_NickName']);
            array_push($CurrentMsg_Msg,$Notification_Msg_Result['Message']);
            array_push($CurrentMsg_ReplyMsgID,$Notification_Msg_Result['Reply_MsgID']);
            array_push($CurrentMsg_SendDate,$Notification_Msg_Result['Send_Date']);
            array_push($CurrentMsg_Status,$Notification_Msg_Result['Status']);
            array_push($CurrentMsg_System_Reply_PostID,$Notification_Msg_Result['System_Reply_PostID']); 
            if($Notification_Msg_Result['Reply_MsgID']!=NULL){
              $SQL_getPreviousMsg= $conn->prepare("SELECT * FROM $Table_notification WHERE Msg_ID = ?");
              $SQL_getPreviousMsg->execute(array($Notification_Msg_Result['Reply_MsgID']));
              while($Pre_Msg_Result = $SQL_getPreviousMsg->fetch()) {
                $CurrentPreMsg = $Pre_Msg_Result['Reply_MsgID'];
                $MsgIN_Reply_Msg = array($Pre_Msg_Result['Message']);
                $MsgIN_Reply_MsgDate = array($Pre_Msg_Result['Send_Date']);      
                $MsgIN_Reply_MsgNN = array($Pre_Msg_Result['From_NickName']);          
                while($CurrentPreMsg != NULL){
                  $SQL_getPreviousMsg_Continue= $conn->prepare("SELECT * FROM $Table_notification WHERE Msg_ID = ?");
                  $SQL_getPreviousMsg_Continue->execute(array($CurrentPreMsg));
                  while($Pre_Msg_Continue_Result = $SQL_getPreviousMsg_Continue->fetch()) {
                    $CurrentPreMsg = $Pre_Msg_Continue_Result['Reply_MsgID'];
                    array_push($MsgIN_Reply_MsgNN,$Pre_Msg_Continue_Result['From_NickName']);
                    array_push($MsgIN_Reply_Msg,$Pre_Msg_Continue_Result['Message']);
                    array_push($MsgIN_Reply_MsgDate,$Pre_Msg_Continue_Result['Send_Date']);
                  }
                }
              }
              array_push($Total_Reply_Msg,$MsgIN_Reply_Msg);
              array_push($Total_Reply_MsgDate,$MsgIN_Reply_MsgDate);
              array_push($Total_Reply_MsgNN,$MsgIN_Reply_MsgNN);
            }
          }
          $_SESSION['notify_ReplyMsg_FromNickName'][] = $Total_Reply_MsgNN;
          $_SESSION['notify_ReplyMsg_Msg'][] = $Total_Reply_Msg;
          $_SESSION['notify_ReplyMsg_SendDate'][] = $Total_Reply_MsgDate;
          $_SESSION['notify_MsgID'][] = $CurrentMsg_ID;
          $_SESSION['notify_FromNickName'][] = $CurrentMsg_FromNickName;
          $_SESSION['notify_ToNickName'][] = $CurrentMsg_ToNickName;
          $_SESSION['notify_Msg'][] = $CurrentMsg_Msg;
          $_SESSION['notify_ReplyMsgID'][] = $CurrentMsg_ReplyMsgID;
          $_SESSION['notify_SendDate'][] = $CurrentMsg_SendDate;
          $_SESSION['notify_Status'][] = $CurrentMsg_Status;
          $_SESSION['notify_System_Reply_PostID'][] = $CurrentMsg_System_Reply_PostID ;
      }
    } else {
      $_SESSION['notify_ReplyMsg_FromNickName'][] = NULL;
      $_SESSION['notify_ReplyMsg_Msg'][] = NULL;
      $_SESSION['notify_ReplyMsg_SendDate'][] = NULL;
    }
$result_page_count=ceil(count($_SESSION['notify_MsgID'])/10);
if($result_page_count==0){
  
  $_GET['page'] = 1;
} else {
  if(!isset($_GET['page'])||$_GET['page']<1){
    $_GET['page'] = 1; // If page is not set in url or page less than 1, initialize page by code
    header("Location: notification.php?page=".$_GET['page']);
  }
  if($_GET['page']>$result_page_count){
    $_GET['page'] = $result_page_count; // If page more than 1, reset page number to maximum page.
    header("Location: notification.php?page=".$_GET['page']);
  }

  /*
    //For Debugging
  print_r("Current Page".$_GET['page']);
  print_r("<br>Result Count: ".$_SESSION['count']);
  print_r("<br>Page Count: ".$result_page_count."<br>");
  print_r($_SESSION['notify_PostID']);
  print_r("<br>");
  print_r($_SESSION['notify_PostTitle']);
  print_r("<br>");
  print_r($_SESSION['notify_MsgID']);
  print_r("<br>");
  print_r($_SESSION['notify_FromNickName']);
  print_r("<br>");
  print_r($_SESSION['notify_ToNickName']);
  print_r("<br>");
  print_r($_SESSION['notify_Msg']);
  print_r("<br>");
  print_r($_SESSION['notify_ReplyMsgID']);
  print_r("<br>");
  print_r($_SESSION['notify_SendDate']);
  print_r("<br>");
  print_r($_SESSION['notify_Status']);
  print_r("<br>");
  print_r($_SESSION['notify_ReplyMsg_Msg'][0][0]);
  print_r("<br>");
  print_r(sizeof($_SESSION['notify_ReplyMsg_Msg']));
  */

  for($i=0; $i<10;$i++){
    if(!((($_GET['page'] -1 )*10+$i >= $_SESSION['count']))){
      $current_page_notify_MsgID[] = $_SESSION['notify_MsgID'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_PostID[] = $_SESSION['notify_PostID'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_PostTitle[] = $_SESSION['notify_PostTitle'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_Post_PostBy[] = $_SESSION['notify_Post_PosyBy'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_Post_Status[] = $_SESSION['notify_Post_Status'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_FromNickName[] = $_SESSION['notify_FromNickName'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_ToNickName[] = $_SESSION['notify_ToNickName'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_Msg[] = $_SESSION['notify_Msg'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_ReplyMsgID[] = $_SESSION['notify_ReplyMsgID'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_SendDate[] = $_SESSION['notify_SendDate'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_Status[] = $_SESSION['notify_Status'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_ReplyMsg_Msg[] = $_SESSION['notify_ReplyMsg_Msg'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_ReplyMsg_SendDate[] = $_SESSION['notify_ReplyMsg_SendDate'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_ReplyMsg_FromNickName[] = $_SESSION['notify_ReplyMsg_FromNickName'][(($_GET['page'] -1 )*10+$i)];
      $current_page_notify_System_Reply_PostID[] = $_SESSION['notify_System_Reply_PostID'][(($_GET['page'] -1 )*10+$i)];
    }
  }
  
}
function gen_result_format($ismobile){ 
  global $result_page_count;
  global $current_page_notify_MsgID;
  global $current_page_notify_PostID;
  global $current_page_notify_PostTitle;
  global $current_page_notify_Post_PostBy;
  global $current_page_notify_Post_Status;
  global $current_page_notify_FromNickName;
  global $current_page_notify_ToNickName;
  global $current_page_notify_Msg;
  global $current_page_notify_ReplyMsgID;
  global $current_page_notify_SendDate;
  global $current_page_notify_Status;
  global $current_page_notify_ReplyMsg_FromNickName;
  global $current_page_notify_ReplyMsg_Msg;
  global $current_page_notify_ReplyMsg_SendDate;
  global $current_page_notify_System_Reply_PostID;
  global $ErrorMsg;
  if(isset($_GET['page'])){
      $current_page_notify_count = 0;
      for($p=0; $p<10; $p++){
        if(isset($current_page_notify_MsgID[$p])){
          $current_page_notify_count+=1;
        }
      }
      if($current_page_notify_count<=0){
        switch($_COOKIE['language']){
            case "eng":
                $ErrorMsg="No notifications yet";
            break;
              case "zh-tw":
                $ErrorMsg="目前並沒有任何通知";
            break;
        }
        echo "<div style='text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;'><h1>".$ErrorMsg."</h1></div><br>";
      }
        // Gen a HTML Code for PC/High Resolution and mobile version display of search results.
          for($i=0; $i<10; $i++){
            if(!((($_GET['page'] -1 )*10+$i >= $_SESSION['count'])) && $result_page_count!=0){
              if($current_page_notify_PostID[$i]!=NULL){
                if($current_page_notify_PostID[$i]==-1){
                  echo "<img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;<a style='color:red;'>".$current_page_notify_PostTitle[$i]."</a>"."<br>";
                } else {
                  echo "<img style='vertical-align: middle;' width=32; height=32; src=images/title.png>&nbsp;(".$current_page_notify_PostID[$i].") <a href=viewpost.php?ID=".$current_page_notify_PostID[$i].">".$current_page_notify_PostTitle[$i]."</a>"."<br>";
                }
                echo "<div class='flip_black' onclick='post_expend_trigger(this)' name=post_msg_".$current_page_notify_PostID[$i].">";
                switch($_COOKIE['language']){
                  case "eng":
                    echo "<div style='text-align:center;'>Click here to Open / Close notification about this post";
                  break;
                  case "zh-tw":
                    echo "<div style='text-align:center;'>點擊此處 打開/關閉 此發文的相關信息";
                  break;
                }
                echo "</div></div>";
                echo "<div class='panel' onclick='quote_replymsg_trigger(this)' id=post_msg_".$current_page_notify_PostID[$i].">"; // Start a Expand / Collapse Box for Post

                // Show Latest Msg
                echo "<div id=result-".$i."><table id='result_table_Col1' width=100%;><tr>";
                      echo "<td width=100%><table id='result_table_SubCol1' width=100% style='font-size:19px;'>";
                        switch($_COOKIE['language']){
                          case "eng":
                            echo "<tr style='color:red;'><td>**Latest Message**</td></tr>";
                          break;
                          case "zh-tw":
                            echo "<tr style='color:red;'><td>**最新消息**</td></tr>";
                          break;
                        }
                      echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/sender.png>&nbsp;".$current_page_notify_FromNickName[$i][0]."</td></tr>";
                      echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_notify_SendDate[$i][0]."</td></tr>";
                      echo "<tr><td><hr style='border: 2px solid black;'/></tr></td>";
                      switch($_COOKIE['language']){
                        case "eng":
                          echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>Message Content<br><div style='padding-left:32px;'>".$current_page_notify_Msg[$i][0]."</div></td></tr>";    
                        break;
                        case "zh-tw":
                          echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>訊息內容<br><div style='padding-left:32px;'>".$current_page_notify_Msg[$i][0]."</div></td></tr>";    
                        break;
                      }
                      if($_SESSION['NotifyType']=="Receive" && $current_page_notify_FromNickName[$i][0]!="BuyBySearch Notification System / 系統訊息"){
                        switch($_COOKIE['language']){
                          case "eng":
                            echo "<tr><td><button class='green_btn' onclick=reply_seller(".$current_page_notify_PostID[$i].",".$current_page_notify_MsgID[$i][0].")> Reply </button></td></tr>";
                          break;
                          case "zh-tw":
                            echo "<tr><td><button class='green_btn' onclick=reply_seller(".$current_page_notify_PostID[$i].",".$current_page_notify_MsgID[$i][0].")> 回覆 </button></td></tr>";
                          break;
                        }
                      } else if($current_page_notify_PostID[$i]==-1){
                        $status = $current_page_notify_Post_Status[array_search($current_page_notify_System_Reply_PostID[$i][0], $current_page_notify_PostID)];
                        if($status=="Waiting for confirmation" && $current_page_notify_ToNickName[$i][0] != $current_page_notify_Post_PostBy[array_search($current_page_notify_System_Reply_PostID[$i][0], $current_page_notify_PostID)]){
                          switch($_COOKIE['language']){
                            case "zh-tw":
                              echo "<tr><td><button class='black_btn' onclick=confirm_settlement(".$current_page_notify_System_Reply_PostID[$i][0].",'".$_SESSION['NickName']."','".$current_page_notify_Post_PostBy[array_search($current_page_notify_System_Reply_PostID[$i][0], $current_page_notify_PostID)]."')> 確認交收 </button></td></tr>";
                            break;
                            case "eng":
                              echo "<tr><td><button class='black_btn' onclick=confirm_settlement(".$current_page_notify_System_Reply_PostID[$i][0].",'".$_SESSION['NickName']."','".$current_page_notify_Post_PostBy[array_search($current_page_notify_System_Reply_PostID[$i][0], $current_page_notify_PostID)]."')> Confirm Settlement </button></td></tr>";
                            break;
                          }
                        }
                      }
                      if($current_page_notify_ReplyMsgID[$i][0]!=NULL){
                        echo "<tr><td><p style='padding-left:20px;' id=quote_reply_".$current_page_notify_MsgID[$i][0].">";
                        echo "<hr style='border: 2px solid #006269;'/>";
                          $current_count=sizeof($current_page_notify_ReplyMsg_Msg[$i][0]);
                          switch($_COOKIE['language']){
                            case "eng":
                              echo "<div class='flip' onclick='quote_replymsg_trigger(this)' name=quote_msg_".$current_page_notify_MsgID[$i][0]."_".$current_page_notify_ReplyMsgID[$i][0]."><img style='vertical-align: middle;' width=32; height=32; src=images/quote_msg.png>&nbsp;Click here to Open / Close Previous Reply"."</div>";
                              echo "<div class='panel' onclick='quote_replymsg_trigger(this)' id=quote_msg_".$current_page_notify_MsgID[$i][0]."_".$current_page_notify_ReplyMsgID[$i][0].">";
                              for($n=0;$n<sizeof($current_page_notify_ReplyMsg_Msg[$i][0]);$n++){
                                echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>".$current_count."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/sender.png>&nbsp".$current_page_notify_ReplyMsg_FromNickName[$i][0][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_notify_ReplyMsg_SendDate[$i][0][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>Message Content<br><div style='padding-left:32px;'>".$current_page_notify_ReplyMsg_Msg[$i][0][$n]."</div>";
                                echo "</td></tr></table><hr style='border: 2px solid #006269;'/>";
                                $current_count--;
                              }
                              echo "</div>";                          
                            break;
                            case "zh-tw":
                              echo "<div class='flip' onclick='quote_replymsg_trigger(this)' name=quote_msg_".$current_page_notify_MsgID[$i][0]."_".$current_page_notify_ReplyMsgID[$i][0]."><img style='vertical-align: middle;' width=32; height=32; src=images/quote_msg.png>&nbsp;點擊此處 打開/關閉 先前回覆的訊息"."</div>";
                              echo "<div class='panel' onclick='quote_replymsg_trigger(this)' id=quote_msg_".$current_page_notify_MsgID[$i][0]."_".$current_page_notify_ReplyMsgID[$i][0].">";
                              for($n=0;$n<sizeof($current_page_notify_ReplyMsg_Msg[$i][0]);$n++){
                                echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>".$current_count."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/sender.png>&nbsp".$current_page_notify_ReplyMsg_FromNickName[$i][0][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_notify_ReplyMsg_SendDate[$i][0][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>訊息內容<br><div style='padding-left:32px;'>".$current_page_notify_ReplyMsg_Msg[$i][0][$n]."</div>";
                                echo "</td></tr></table><hr style='border: 2px solid #006269;'/>";
                                $current_count--;
                              }
                              echo "</div>";                          
                            break;
                          }
                          echo "</p></td></tr>";
                        }
                  echo "</table></td>";
                echo "</tr></table></div><hr style='border: 2px solid #006269;'/>";
                // A Button of Expand / Collapse Function For Old Msg of Current Post
                if(sizeof($current_page_notify_MsgID[$i])>1){
                  echo "<div class='flip_black' onclick='quote_replymsg_trigger(this)' name=old_msg_".$current_page_notify_PostID[$i].">";
                  if($current_page_notify_PostID[$i]!=-1){
                      // Msg From Other User
                      if($_SESSION['NotifyType']=="Receive"){
                        switch($_COOKIE['language']){
                          case "eng":
                            echo "<div style='text-align:center;'>Click here to open / close the message that the other buyer sent you";
                          break;
                          case "zh-tw":
                            echo "<div style='text-align:center;'>點擊此處 打開/關閉 其他買家 曾發送給你的訊息";
                          break;
                        }
                      } else if($_SESSION['NotifyType']=="Sent") {
                        switch($_COOKIE['language']){
                          case "eng":
                            echo "<div style='text-align:center;'>Click here to open / close the message you sent to other buyers";
                          break;
                          case "zh-tw":
                            echo "<div style='text-align:center;'>點擊此處 打開/關閉 你發給 其他買家 的訊息";
                          break;
                        }
                      }
                      echo "</div></div>";
                    } else {
                      // Msg From System
                      switch($_COOKIE['language']){
                        case "eng":
                          echo "<div style='text-align:center;'>Click here to open / close the message that BuyBySearch Notification System sent you";
                        break;
                        case "zh-tw":
                          echo "<div style='text-align:center;'>點擊此處 打開/關閉 BuyBySearch 訊息管理系統 曾發送給你的訊息";
                        break;
                      }
                      echo "</div></div>";
                    }
                }
                // Show Old Msg with Expand / Collapse Style, Default Collapse all previous msg.
                echo "<div class='panel' onclick='quote_replymsg_trigger(this)' id=old_msg_".$current_page_notify_PostID[$i].">";
                for($msg_index=1; $msg_index<sizeof($current_page_notify_MsgID[$i]); $msg_index++){
                echo "<div>";
                // A Button of Expand / Collapse Function For Old Msg
                echo "<div class='flip_black' onclick='quote_replymsg_trigger(this)' name=old_msg_".$current_page_notify_PostID[$i]."_".$current_page_notify_MsgID[$i][$msg_index].">";
                if($current_page_notify_PostID[$i]!=-1){
                  // Msg From Other User
                  if($_SESSION['NotifyType']=="Receive"){
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<div style='text-align:center;'>Click here to Open / Close notification sent at " . $current_page_notify_SendDate[$i][$msg_index]." From ".$current_page_notify_FromNickName[$i][$msg_index];
                      break;
                      case "zh-tw":
                        echo "<div style='text-align:center;'>點擊此處 打開/關閉 ".$current_page_notify_FromNickName[$i][$msg_index]." 於 ".$current_page_notify_SendDate[$i][$msg_index]." 發送給你的訊息";
                      break;
                    }
                  } else if($_SESSION['NotifyType']=="Sent") {
                    switch($_COOKIE['language']){
                      case "eng":
                        echo "<div style='text-align:center;'>Click here to Open / Close notification sent at " . $current_page_notify_SendDate[$i][$msg_index]." To ".$current_page_notify_FromNickName[$i][$msg_index];
                      break;
                      case "zh-tw":
                        echo "<div style='text-align:center;'>點擊此處 打開/關閉 "." 你於 ".$current_page_notify_SendDate[$i][$msg_index]." 發送給 ".$current_page_notify_FromNickName[$i][$msg_index]." 的訊息";
                      break;
                    }
                  }
                } else {
                  // Msg From System
                  switch($_COOKIE['language']){
                    case "eng":
                      echo "<div style='text-align:center;'>Click here to Open / Close notification that the system sent at " . $current_page_notify_SendDate[$i][$msg_index]." about ".$current_page_notify_FromNickName[$i][$msg_index];
                    break;
                    case "zh-tw":
                      echo "<div style='text-align:center;'>點擊此處 打開/關閉 系統於 ".$current_page_notify_SendDate[$i][$msg_index]." 發出 有關 ".$current_page_notify_PostTitle[array_search($current_page_notify_System_Reply_PostID[$i][$msg_index], $current_page_notify_PostID)]." 的 系統訊息";
                    break;
                  }
                }
                echo "</div></div>";
                // Display Old Msg
                echo "<div class='panel' onclick='quote_replymsg_trigger(this)' id=old_msg_".$current_page_notify_PostID[$i]."_".$current_page_notify_MsgID[$i][$msg_index].">";
                    echo "<div id=result-".$i."><table id='result_table_Col1' width=100%;><tr>";
                      echo "<td width=100%><table id='result_table_SubCol1' width=100% style='font-size:19px;'>";
                      echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/sender.png>&nbsp;".$current_page_notify_FromNickName[$i][$msg_index]."</td></tr>";
                      echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_notify_SendDate[$i][$msg_index]."</td></tr>";
                      echo "<tr><td><hr style='border: 2px solid black;'/></tr></td>";
                      echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>訊息內容<br><div style='padding-left:32px;'>".$current_page_notify_Msg[$i][$msg_index]."</div></td></tr>";    
                      if($_SESSION['NotifyType']=="Receive" && $current_page_notify_FromNickName[$i][0]!="BuyBySearch Notification System / 系統訊息"){
                        switch($_COOKIE['language']){
                          case "eng":
                            echo "<tr><td><button class='green_btn' onclick=reply_seller(".$current_page_notify_PostID[$i].",".$current_page_notify_MsgID[$i][$msg_index].")> Reply </button></td></tr>";
                          break;
                          case "zh-tw":
                            echo "<tr><td><button class='green_btn' onclick=reply_seller(".$current_page_notify_PostID[$i].",".$current_page_notify_MsgID[$i][$msg_index].")> 回覆 </button></td></tr>";
                          break;
                        }
                      } else if($current_page_notify_PostID[$i]==-1){
                        $status = $current_page_notify_Post_Status[array_search($current_page_notify_System_Reply_PostID[$i][$msg_index], $current_page_notify_PostID)];
                        if($status=="Waiting for confirmation"  && $current_page_notify_ToNickName[$i][$msg_index] != $current_page_notify_Post_PostBy[array_search($current_page_notify_System_Reply_PostID[$i][$msg_index], $current_page_notify_PostID)]){
                          switch($_COOKIE['language']){
                            case "zh-tw":
                              echo "<tr><td><button class='black_btn' onclick=confirm_settlement(".$current_page_notify_System_Reply_PostID[$i][$msg_index].",'".$_SESSION['NickName']."','".$current_page_notify_Post_PostBy[array_search($current_page_notify_System_Reply_PostID[$i][$msg_index], $current_page_notify_PostID)]."')> 確認交收 </button></td></tr>";
                            break;
                            case "eng":
                              echo "<tr><td><button class='black_btn' onclick=confirm_settlement(".$current_page_notify_System_Reply_PostID[$i][$msg_index].",'".$_SESSION['NickName']."','".$current_page_notify_Post_PostBy[array_search($current_page_notify_System_Reply_PostID[$i][$msg_index], $current_page_notify_PostID)]."')> Confirm Settlement </button></td></tr>";
                            break;
                          }
                        }
                      }
                      if($current_page_notify_ReplyMsgID[$i][$msg_index]!=NULL){
                        echo "<tr><td><p style='padding-left:20px;' id=quote_reply_".$current_page_notify_MsgID[$i][$msg_index].">";
                        echo "<hr style='border: 2px solid #006269;'/>";
                          $current_count=sizeof($current_page_notify_ReplyMsg_Msg[$i][$msg_index]);
                          switch($_COOKIE['language']){
                            case "eng":
                              // A Button of Expand / Collapse Function For Previous Reply Msg on Current Old Msg
                              echo "<div class='flip' onclick='quote_replymsg_trigger(this)' name=quote_msg_".$current_page_notify_MsgID[$i][$msg_index]."_".$current_page_notify_ReplyMsgID[$i][$msg_index]."><img style='vertical-align: middle;' width=32; height=32; src=images/quote_msg.png>&nbsp;Click here to Open / Close Previous Reply"."</div>";
                              // Display ALL Previous Reply Msg on Current Old Msg
                              echo "<div class='panel' onclick='quote_replymsg_trigger(this)' id=quote_msg_".$current_page_notify_MsgID[$i][$msg_index]."_".$current_page_notify_ReplyMsgID[$i][$msg_index].">";
                              for($n=0;$n<sizeof($current_page_notify_ReplyMsg_Msg[$i][$msg_index]);$n++){
                                echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>".$current_count."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/sender.png>&nbsp".$current_page_notify_ReplyMsg_FromNickName[$i][$msg_index][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_notify_ReplyMsg_SendDate[$i][$msg_index][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>Message Content<br><div style='padding-left:32px;'>".$current_page_notify_ReplyMsg_Msg[$i][$msg_index][$n]."</div>";
                                echo "</td></tr></table><hr style='border: 2px solid #006269;'/>";
                                $current_count--;
                              }
                              echo "</div>";                          
                            break;
                            case "zh-tw":
                              // A Button of Expand / Collapse Function For Previous Reply Msg on Current Old Msg
                              echo "<div class='flip' onclick='quote_replymsg_trigger(this)' name=quote_msg_".$current_page_notify_MsgID[$i][$msg_index]."_".$current_page_notify_ReplyMsgID[$i][$msg_index]."><img style='vertical-align: middle;' width=32; height=32; src=images/quote_msg.png>&nbsp;點擊此處 打開/關閉 先前回覆的訊息"."</div>";
                              // Display ALL Previous Reply Msg on Current Old Msg
                              echo "<div class='panel' onclick='quote_replymsg_trigger(this)' id=quote_msg_".$current_page_notify_MsgID[$i][$msg_index]."_".$current_page_notify_ReplyMsgID[$i][$msg_index].">";
                              for($n=0;$n<sizeof($current_page_notify_ReplyMsg_Msg[$i][$msg_index]);$n++){
                                echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>".$current_count."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/sender.png>&nbsp".$current_page_notify_ReplyMsg_FromNickName[$i][$msg_index][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$current_page_notify_ReplyMsg_SendDate[$i][$msg_index][$n]."</td></tr>";
                                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>訊息內容<br><div style='padding-left:32px;'>".$current_page_notify_ReplyMsg_Msg[$i][$msg_index][$n]."</div>";
                                echo "</td></tr></table><hr style='border: 2px solid #006269;'/>";
                                $current_count--;
                              }
                              echo "</div>";                          
                            break;
                          }
                          echo "</p></td></tr>";
                        }
                  echo "</table></td>";
                echo "</tr></table><br></div>"; // End of Display Previous Reply of Current Msg
                echo "</div>"; // End of Old Msg with Expand / Collapse Style
                echo "<tr><td><hr style='border: 3px dashed #5e5e5e;'/></tr></td></div>"; // End of One Post
              }
              echo "</div></div><br>";
            }
          }
        }
      }
}
?>
<html>
<head>
	<title> BuybySearch - Notification </title>
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
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1><?php echo $PageTitle; ?></h1></div><br>
            <a onclick="prev_page()" id="prev_page" class="prev_page">&#10094; 上一頁</a>
            <a onclick="next_page()" id="next_page" class="next_page">下一頁 &#10095;</a>
          <? elseif ($_COOKIE['language']=="eng") : ?>
            <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1><?php echo $PageTitle; ?></h1></div><br>
            <a onclick="prev_page()" id="prev_page" class="prev_page">&#10094; Previous Page</a>
            <a onclick="next_page()" id="next_page" class="next_page">Next Page &#10095;</a>
          <? endif; ?>
          <div id="notify_menubar"> 
          <form style="margin:0px" action="" method="post">
            <input type="hidden" name="notify_type_submit" value="1" />
            <table width=100%>
              <tr>
                <td width=50%><button class="green_btn" type="submit" style="width:100%;" name="notify_type" value="Receive">
                <? if ($_COOKIE['language']=="zh-tw") : ?>收件箱<? elseif ($_COOKIE['language']=="eng") : ?>Inbox<? endif; ?>
                </button></td>
                <td width=50%><button class="green_btn" type="submit" style="width:100%;" name="notify_type" value="Sent">
                <? if ($_COOKIE['language']=="zh-tw") : ?>寄件箱<? elseif ($_COOKIE['language']=="eng") : ?>Outbox<? endif; ?>
                </button></td>
              </tr>
            </table>
            </form>
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
check_page();

// Page Controller of Result Page
function prev_page(){
  page-=1;
  location.href="notification.php?page="+page;
}

function next_page(){
  page+=1;
  location.href="notification.php?page="+page;
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
function quote_replymsg_trigger(element){
  var elementName = $(element).attr('name');
  $("#"+elementName).slideToggle("slow");
}

function post_expend_trigger(element){
  var elementName = $(element).attr('name');
  $("#"+elementName).slideToggle("slow");
  if(elementName.indexOf("post_msg_") !== -1){
    var word_split = elementName.split("_");
    var postid = word_split[word_split.length - 1];
    $.ajax({
      url: 'includes/msg_function.php',
      type: 'POST',
      data: {
        process:"Read_Msg",
        post_id:postid
        },
      success: function(result) {
      }
    });
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


function confirm_settlement(confirm_postid, buyer, seller){
  var confirm_msg;
  <? if ($_COOKIE['language']=="zh-tw") : ?>
      confirm_msg = "你確定已完成交收並且過程無誤嗎？\n一經確定，此發文狀態將會變更為「已完成交易」，\n並關閉此發文，此操作無法復原。";
  <? elseif ($_COOKIE['language']=="eng") : ?>
      confirm_msg = "Are you sure the settlement has been completed and the process is correct?\nOnce confirmed, the status of this post will change to \n'Transaction completed',\nand this post will be closed and cannot be recovered.";
  <? endif; ?>
  if(confirm(confirm_msg)){
      $.ajax({
          url: 'includes/post_function.php',
          type: 'POST',
          data: 
          {
              process:"BuyerConfirm_Settlement",
              post_id:confirm_postid
          },
          success: function(posting_result) {	
              $.ajax({
                  url: 'includes/msg_function.php',
                  type: 'POST',
                  data: {
                        process:"SystemMsg_BuyerConfirm_Settlement",
                        postid:confirm_postid,
                        buyer_nn:buyer,
                        seller_nn:seller
                      },
                  success: function(result) {
                          $('#result-msg').text(""+posting_result);
                          $('#cover').fadeIn('slow');
                          $('#result-msg-container').fadeIn('slow'); 
                           
                          setTimeout(function() {
                              location.href="viewpost.php?ID="+confirm_postid;
                          }, 1500);
                          
                      }
              });
          }
      });
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
document.getElementById('search_again_container').style.display="none";
</script>

</body>
</html>