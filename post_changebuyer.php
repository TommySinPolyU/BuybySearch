<?php
require_once('includes/connect.php');
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
if(!isset($_SESSION['UID'])){
    //Because It is a function for logged-in user.
    //if No any user logged-in, Return page to home page.
    //To Avoid people type URL manually without logged-in.
    //Also If System cannot find the PostID and Buyer Value in URL, Return To Index Page
    header('Location: index.php');
    die();
}

$Table_post = table_post;
$Table_notification = table_notification;
$notify_MsgID=array();
$notify_FromNickName=array();
$notify_ToNickName=array();
$notify_Msg=array();
$notify_SendDate=array();
$SQL_getPost = $conn->prepare("SELECT ID, Title,Post_Status,Selected_Buyer,ExpireDate FROM $Table_post WHERE PostBy_UID = ?");
$SQL_getPost->execute(array($_SESSION['UID']));
$UserPost_Title=array();
$UserPost_ID=array();
$buyerList_NickName = array();
$buyerList_MsgID = array();
$datetime_expire = array();
$isexpired = array();
while($Post_Result = $SQL_getPost->fetch()) {
    $UserPost_Title[] = $Post_Result['Title'];
    $UserPost_ID[] = $Post_Result['ID'];
    $post_Status[] = $Post_Result['Post_Status'];
    $datetime_expire[] = new DateTime($Post_Result['ExpireDate']);
}
for($i=0;$i<sizeof($datetime_expire);$i++){
    $datetime_now = new DateTime(date("Y-m-d H:i:s"));
    $datediff_expire = $datetime_expire[$i]->diff($datetime_now);
    $isexpired[] = ($datetime_now>$datetime_expire);
}

if(isset($_GET['PostID'])){
    $SQL_getLatest_Buyer = $conn->prepare("SELECT Msg_ID,PostID,From_NickName FROM $Table_notification where (Msg_ID,PostID, Send_Date) in (select MAX(Msg_ID) AS LatestMsgID,PostID, max(Send_Date) as DATE from $Table_notification WHERE PostID = ? group by From_NickName) AND From_NickName!=?");
    $SQL_getLatest_Buyer->execute(array($_GET['PostID'],$_SESSION['NickName']));
    while($Latest = $SQL_getLatest_Buyer->fetch()) {
      $buyerList_NickName[] = $Latest['From_NickName'];
      $buyerList_MsgID[] = $Latest['Msg_ID'];
    }

    $SQL_getPostTitle = $conn->prepare("SELECT Title, Selected_Buyer FROM $Table_post WHERE ID = ?");
    $SQL_getPostTitle->execute(array($_GET['PostID']));
    while($Title_Result = $SQL_getPostTitle->fetch()) {
        $notify_PostTitle = $Title_Result['Title'];
        $Selected_Buyer = $Title_Result['Selected_Buyer'];
    }
}

if(isset($_GET['PostID']) && isset($_GET['Buyer'])){
    $SQL_getLatest_Seller = $conn->prepare("SELECT Msg_ID,PostID,From_NickName FROM $Table_notification where (Msg_ID,PostID, Send_Date) in (select MAX(Msg_ID) AS LatestMsgID,PostID, max(Send_Date) as DATE from $Table_notification WHERE PostID = ? group by From_NickName) AND From_NickName=?");
    $SQL_getLatest_Seller->execute(array($_GET['PostID'],$_SESSION['NickName']));
    while($Latest = $SQL_getLatest_Seller->fetch()) {
      $SellerLaster_MsgID = $Latest['Msg_ID'];
    }
    $SQL_getNotification = $conn->prepare("SELECT * FROM $Table_notification WHERE PostID = ? AND (From_NickName = ? OR To_NickName = ?) ORDER BY Send_Date DESC");
    $SQL_getNotification->execute(array($_GET['PostID'],$_GET['Buyer'],$_GET['Buyer']));
    while($Notification_Result = $SQL_getNotification->fetch()) {
        $notify_MsgID[] = $Notification_Result['Msg_ID'];
        $notify_FromNickName[] = $Notification_Result['From_NickName'];
        $notify_ToNickName[] = $Notification_Result['To_NickName'];
        $notify_Msg[] = $Notification_Result['Message'];
        $notify_SendDate[] = $Notification_Result['Send_Date'];
    }
}

function gen_msg_result(){
    global $buyerList_NickName;
    global $notify_MsgID;
    global $notify_FromNickName;
    global $notify_ToNickName;
    global $notify_Msg;
    global $notify_SendDate;
    $current_count=sizeof($notify_MsgID);
    if(isset($_GET['PostID']) && isset($_GET['Buyer'])){
        if(in_array($_GET['Buyer'],$buyerList_NickName)){
            switch($_COOKIE['language']){
                case "eng":
                    echo "<div class='form_label' style='padding-left:15px;padding-top:15px;'>Your message with ".$_GET['Buyer']." on this transaction:</div>";
                break;
                case "zh-tw":
                    echo "<div class='form_label' style='padding-left:15px;padding-top:15px;'>你與 ".$_GET['Buyer']." 於此交易的訊息來往:</div>";
                break;
            }
            for($n=0;$n<sizeof($notify_MsgID);$n++){
                if($notify_ToNickName[$n]!=$_SESSION['NickName']){
                    switch($_COOKIE['language']){
                        case "eng":
                            echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>&nbsp;&nbsp;Out - ".$current_count."&nbsp;&nbsp;</td></tr>";
                        break;
                        case "zh-tw":
                            echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>&nbsp;&nbsp;寄件 - ".$current_count."&nbsp;&nbsp;</td></tr>";
                        break;
                    }
                } else {
                    switch($_COOKIE['language']){
                        case "eng":
                            echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>&nbsp;&nbsp;In - ".$current_count."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
                        break;
                        case "zh-tw":
                            echo "<table><tr align=center style=font-size:22px;><td rowspan='4' width=20%>&nbsp;&nbsp;收件 - ".$current_count."&nbsp;&nbsp;</td></tr>";
                        break;
                    }        }
                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/sender.png>&nbsp;".$notify_FromNickName[$n]."</td></tr>";
                echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/postdate.png>&nbsp;".$notify_SendDate[$n]."</td></tr>";
                switch($_COOKIE['language']){
                    case "eng":
                        echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>Message Content<br><div style='padding-left:32px;'>".$notify_Msg[$n]."</div>";
                    break;
                    case "zh-tw":
                        echo "<tr><td><img style='vertical-align: middle;' width=32; height=32; src=images/msg_content.png>訊息內容<br><div style='padding-left:32px;'>".$notify_Msg[$n]."</div>";
                    break;
                }
                echo "</td></tr></table><hr style='border: 2px solid #006269;'/>";
                $current_count--;
            }
            echo "<button class='green_btn' onclick='confirm_final_trader()'>"; 
            switch($_COOKIE['language']){
                case "eng":
                    echo "Confirm selection of ".$_GET['Buyer']." as the last trader";
                break;
                case "zh-tw":
                    echo "確認選擇 ".$_GET['Buyer']." 為最後交易者";
                break;
            }
            echo "</button>";
        } else {
            if($_GET['Buyer']!=""){
                switch($_COOKIE['language']){
                    case "eng":
                        echo "<br><br><div style='text-align:center;background-color:red;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;'><h1 id='Post_Form_Title'>This user could not be found or this user has not responded to your post.</h1></div>";
                    break;
                    case "zh-tw":  
                        echo "<br><br><div style='text-align:center;background-color:red;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;'><h1 id='Post_Form_Title'>找不到此用戶或此用戶未曾回覆你這篇發文</h1></div>";
                    break;
                }
            }
        }
    }
}

?>
<html>
<body>
<?php include_once('header.php') ?>

<!--  Begin of Content (Body)	-->
<div id="mainbody">
<? if ($_COOKIE['language']=="zh-tw") : ?>
    <div style='text-align:center;;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;'><h1 id='Post_Form_Title'>確認最後交易者</h1></div>
    <div class="form_column" id="buyer_selection_postid_container">
    <!--<div class="form_label" style="padding-top:15px;"><label for="buyer_selection_postid">選擇指定發文: </label></div>-->
        <select id="buyer_selection_postid" name="buyer_selection_postid" onchange="post_changed(this.options[this.selectedIndex].value)">
            
        </select>
    </div>
    <div class="form_column" id="buyer_selection_container">
    <div class="form_label" style="padding-top:15px;"><label for="buyer_selection">選擇指定交易者: </label></div>
        <select id="buyer_selection" name="buyer_selection" onchange="post_buyer_changed(this.options[this.selectedIndex].value)">
            
        </select>
    </div>
<? elseif ($_COOKIE['language']=="eng") : ?>
    <div style='text-align:center;;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;'><h1 id='Post_Form_Title'>Confirmation of last trader</h1></div>
    <div class="form_label" style="padding-left:15px;padding-top:15px;"><label for="buyer_selection_postid">Select A Post:</label></div>
    <div class="form_column" id="buyer_selection_postid_container">
        <select id="buyer_selection_postid" name="buyer_selection_postid" onchange="post_changed(this.options[this.selectedIndex].value)">
            
        </select>
    </div>
    <div class="form_label" style="padding-left:15px;padding-top:15px;"><label for="buyer_selection">Select A User for final Trader: </label></div>
    <div class="form_column" id="buyer_selection_container">
        <select id="buyer_selection" name="buyer_selection" onchange="post_buyer_changed(this.options[this.selectedIndex].value)">
            
        </select>
    </div>
<? endif; ?>
    <div id="result_list">

    </div>
<button class='red_btn' onclick="window.location.href='mypost.php'">
<? if ($_COOKIE['language']=="zh-tw") : ?>返回「我的發文」
<? elseif ($_COOKIE['language']=="eng") : ?>Return My Post
<? endif; ?>
</button>
</div>
<div id="cover"></div>
<div class="popupmsg-container" style="width: 400px;height: 180px; margin-top: -90px;margin-left: -200px;" id="post-msg-container"> 
	<p id="post-msg" style="white-space: pre-line">Please Enter Again!</p>
	<div class="popupmsg-option-single" id="post-option-container">
		<a id="post-msg-confirm" href="javascript:void(0);">OK</a>
	</div>
</div>
<!--  End of Content (Body)	-->

<?php include_once('footer.php') ?>
<!-- Script -->
<script>
document.getElementById('search_again_container').style.display="none";
update_post_list();

function post_changed(postid){
    var SelectedID = "<?php if(isset($_GET['PostID'])){ Print($_GET['PostID']);} ?>";
    if(postid!=SelectedID){
        window.location.href = "post_changebuyer.php?PostID="+postid;
    }
}

function post_buyer_changed(buyerNickName){
    var SelectedID = "<?php if(isset($_GET['PostID'])){ Print($_GET['PostID']);} ?>";
    var SelectedBuyer = "<?php if(isset($_GET['Buyer'])){ Print($_GET['Buyer']);} ?>";
    if(buyerNickName!=SelectedBuyer){
        window.location.href = "post_changebuyer.php?PostID="+SelectedID+"&Buyer="+buyerNickName;
    }
}

function update_post_list(){
    var SelectedID = "<?php if(isset($_GET['PostID'])){ Print($_GET['PostID']);} ?>";
    var postList_Title = [<?php if(isset($UserPost_Title)){ echo '"'.implode('","', $UserPost_Title).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post Title For Post Selection
    var postList_ID = [<?php if(isset($UserPost_ID)){ echo '"'.implode('","', $UserPost_ID).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post ID For Post Selection
    var postList_Status = [<?php if(isset($post_Status)){ echo '"'.implode('","', $post_Status).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post Status For Post Selection
    var isexpired_array = [<?php if(isset($isexpired)){ echo '"'.implode('","', $isexpired).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post Status For Post Selection
    var Selected_Buyer = [<?php if(isset($Selected_Buyer)){ echo '"'.implode('","', $Selected_Buyer).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post Status For Post Selection
    $("#buyer_selection_postid").empty();
    document.getElementById('buyer_selection_container').style.display="none";
    // Add Default Value For A Tips of Post Selection
    var PostList_Element = document.getElementById('buyer_selection_postid');
    var i;
    var opt = document.createElement('option');
    opt.value = "";
    <? if ($_COOKIE['language']=="zh-tw") : ?>
        opt.innerHTML = "請選擇你希望確認最終買家的發文";
    <? elseif ($_COOKIE['language']=="eng") : ?>
        opt.innerHTML = "Please select the post you wish to confirm the final buyer";
    <? endif; ?>
    PostList_Element.add(opt);
    if(postList_Title.length>=1){
        for(i=0;i<postList_Title.length;i++){
            if(postList_Status[i] == "Idle" && isexpired_array[i]=='1'){
                var opt = document.createElement('option');
                opt.value = postList_ID[i];
                opt.innerHTML = postList_Title[i];
                PostList_Element.add(opt); 
            }  
        }
    }
    var options_length = PostList_Element.options.length;
    var options=[];
    for(i=0;i<options_length;i++){
        options[i]=PostList_Element.options[i].value;
    }
    if(options.includes(SelectedID)){
        $('#buyer_selection_postid').val(SelectedID).change();
    }
    update_buyer_list(document.getElementById('buyer_selection_postid').selectedIndex);
    document.getElementById('buyer_selection_postid').disabled = true;
}

function update_buyer_list(postid_index){
  var postList_Status = [<?php if(isset($post_Status)){ echo '"'.implode('","', $post_Status).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post Status For Post Selection
  var post_status_value = postList_Status[postid_index-1];// Get Selected Post Status
  if((postid_index-1)<0){
    document.getElementById('buyer_selection_container').style.display="none";
  } else {
        if(post_status_value!="Transaction complete"){
            $("#buyer_selection").empty();
            if(post_status_value=="Idle"){
                var SelectedBuyer = "<?php if(isset($_GET['Buyer'])){ Print($_GET['Buyer']);} ?>";
                document.getElementById('buyer_selection_container').style.display="";
                var buyerList_NickName = [<?php if(isset($buyerList_NickName)){ echo '"'.implode('","', $buyerList_NickName).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Buyer Name For Buyer Selection
                if(buyerList_NickName.length>=1){
                    var buyerList_Element = document.getElementById('buyer_selection');
                    var i;
                    var opt = document.createElement('option');
                    opt.value = "";
                    <? if ($_COOKIE['language']=="zh-tw") : ?>
                        opt.innerHTML = "請選擇與誰進行交易";
                    <? elseif ($_COOKIE['language']=="eng") : ?>
                        opt.innerHTML = "Please choose with whom to trade";
                    <? endif; ?>
                    buyerList_Element.add(opt);   
                    for(i=0;i<buyerList_NickName.length;i++){
                    var opt = document.createElement('option');
                    opt.value = buyerList_NickName[i];
                    opt.innerHTML = buyerList_NickName[i];
                    buyerList_Element.add(opt);   
                    }
                    buyerList_Element.value=SelectedBuyer;
                    gen_msg_result();
                } else {
                    var buyerList_Element = document.getElementById('buyer_selection');
                    var i;
                    var opt = document.createElement('option');
                    opt.value = "";
                    <? if ($_COOKIE['language']=="zh-tw") : ?>
                        opt.innerHTML = "暫未有其他用戶透過本站的訊息功能向你發出訊息";
                    <? elseif ($_COOKIE['language']=="eng") : ?>
                        opt.innerHTML = "No other users have sent you a message through the notification system";
                    <? endif; ?>
                    buyerList_Element.add(opt);
                }
            } else {
                document.getElementById('buyer_selection_container').style.display="none";
            }
        }
    }
}

function confirm_final_trader(){
    var confirm_msg;
    var SellerName = "<?php if(isset($_SESSION['NickName'])){ Print($_SESSION['NickName']);} ?>";
    var postList_Title = [<?php if(isset($UserPost_Title)){ echo '"'.implode('","', $UserPost_Title).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post Title For Post Selection
    var postList_ID = [<?php if(isset($UserPost_ID)){ echo '"'.implode('","', $UserPost_ID).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Post ID For Post Selection
    var SelectedID = "<?php if(isset($_GET['PostID'])){ Print($_GET['PostID']);} ?>";
    var SelectedBuyer = "<?php if(isset($_GET['Buyer'])){ Print($_GET['Buyer']);} ?>";
    var buyerList_NickName = [<?php if(isset($buyerList_NickName)){ echo '"'.implode('","', $buyerList_NickName).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Buyer NickName For Sending System Msg
    var buyerList_MsgID = [<?php if(isset($buyerList_MsgID)){ echo '"'.implode('","', $buyerList_MsgID).'"';} ?>]; // Convert PHP Array To JS Array, Store ALL Buyer Latest MsgID For Sending System Msg
    var SellerLaster_MsgID = <?php if(isset($SellerLaster_MsgID)){ Print($SellerLaster_MsgID);} else {Print -2;} ?>;
    var System_Reply_MsgID = buyerList_MsgID[buyerList_NickName.indexOf(SelectedBuyer)];
    var System_Reply_Post_Title = postList_Title[postList_ID.indexOf(SelectedID)];
    <? if ($_COOKIE['language']=="zh-tw") : ?>
        confirm_msg = "你確定要將最後 買家 設定為 「"+SelectedBuyer+"」嗎？\n一經確定，此發文狀態將會變更為「等待確定」，\n並封鎖此發文，此操作無法復原。";
    <? elseif ($_COOKIE['language']=="eng") : ?>
        confirm_msg = "Are you sure you want to set the final buyer to '" + SelectedBuyer + "' ?\nOnce confirmed, the status of this post will change to \n’Waiting for Confirmation’,\nand this post will be blocked and cannot be recovered.";
    <? endif; ?>
    if(confirm(confirm_msg)){
        $.ajax({
            url: 'includes/post_function.php',
            type: 'POST',
            data: 
            {
                process:"ConfirmBuyer",
                post_id:SelectedID,
                post_selectedbuyer:SelectedBuyer
            },
            success: function(posting_result) {	
                $('#post-msg').text(""+posting_result);
                $('#cover').fadeIn('slow');
                $('#post-msg-container').fadeIn('slow');
                var SystemMsg_Tobuyer;
                var SystemMsg_Toseller;
                SystemMsg_Tobuyer = "親愛的 "+SelectedBuyer+"<br />你早前曾向 "+SellerName+" 發出有關 " + System_Reply_Post_Title + " 的查詢<br />我們現正通知你，發文者已選擇你作為是次交易的最後交易者，<br />請您於確認交收後點擊以下按鈕以作最後確認。<br />請注意，一經確定，將無法取消。<br />我們不會負責任何交收時所出現的任何問題<br /><br />BuyBySearch 訊息管理系統<br />----------------------------------------------<br />"+"Dear "+SelectedBuyer+"<br />You have previously sent a query for "+SellerName+" - "+System_Reply_Post_Title+"<br />Now we inform you that the author chose you as the last trader of this transaction.<br />Please click the button below for final confirmation after confirming the settlement.<br />Please note that once confirmed, it cannot be cancelled.<br />We will not be responsible for any problems that occur during settlement<br /><br />BuyBySearch Notification System";
                SystemMsg_Toseller = "親愛的 "+SellerName+"<br />你選擇了 "+SelectedBuyer+" 作為 「"+System_Reply_Post_Title+" 」的最終交易者 "+"<br />我們現正通知你，請等待對方進行交收確認以完成整個交易程序，<br />請注意，我們不會負責任何交收時所出現的任何問題<br /><br />BuyBySearch 訊息管理系統<br />----------------------------------------------<br />"+"Dear "+SellerName+"<br />You have selected "+ SelectedBuyer +" as the final trader for '"+System_Reply_Post_Title+"'<br />We are now informing you, please wait for the trader to confirm the settlement to complete the entire transaction process,<br />Please note that we will not be responsible for any problems that occur during settlement<br /><br />BuyBySearch Notification System";
                $.ajax({
                    url: 'includes/msg_function.php',
                    type: 'POST',
                    data: {
                        // Sending System Msg To Buyer to inform buyer about the selection of final trader
                        process:"SystemMsg_PostConfirm_Buyer",
                        reply_msg_id:System_Reply_MsgID,
                        trading_msg:SystemMsg_Tobuyer
                        },
                        success: function(result_buyer) {
                            $.ajax({
                                url: 'includes/msg_function.php',
                                type: 'POST',
                                data: {
                                    // Sending System Msg To Seller to inform Seller about the selection of final trader
                                    process:"SystemMsg_PostConfirm_Seller",
                                    reply_msg_id:SellerLaster_MsgID,
                                    trading_msg:SystemMsg_Toseller
                                    },
                                    success: function(result_seller) {
                                        $('#post-msg').text(""+posting_result);
                                        $('#cover').fadeIn('slow');
                                        $('#post-msg-container').fadeIn('slow');   
                                        
                                        setTimeout(function() {
                                            location.href="viewpost.php?ID="+SelectedID;
                                        }, 1500);
                                        
                                    }
                            });
                        }
                });
            }
        });
    }
}

function gen_msg_result(){
    document.getElementById('result_list').innerHTML="";
    htmlcode = "<?php echo gen_msg_result() ?>";
    document.getElementById('result_list').innerHTML=htmlcode;
}

$('#post-msg-confirm').each(function(){
    $(this).click(function(){ 
        $('#cover').fadeOut('slow');
		$('#post-msg-container').fadeOut('slow');
    });
});

</script>
<!-- Script -->



</body>
</html>