<?php
require_once('includes/connect.php');
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
if(!isset($_SESSION['UID'])){
    header('Location: index.php');
    die();
}
$ErrorMsg="";
$Table_trans = table_trans;
$_SESSION['Trans_ID']=array();
$_SESSION['Trans_SubDate']=array();
$_SESSION['Trans_Status']=array();
$lastmonth = date("Y-m-d H:i:s", strtotime("-1 months"));
$datetime_now = date("Y-m-d H:i:s");
$SQL_getTransRecords = $conn->prepare("SELECT * FROM $Table_trans WHERE UID = ? AND SubmitionDate BETWEEN '$lastmonth' AND '$datetime_now'");
$SQL_getTransRecords->execute(array($_SESSION['UID']));
$SQL_getTransRecords_count = $SQL_getTransRecords->rowCount();
if($SQL_getTransRecords_count>0){
  while($row = $SQL_getTransRecords->fetch()) {
    $_SESSION['Trans_ID'][] = $row['Transaction_ID'];
    $_SESSION['Trans_SubDate'][] = $row['SubmitionDate'];
    if ($_COOKIE['language']=="zh-tw"){
        switch($row['Status']){
            case "Processing":
                $_SESSION['Trans_Status'][] = "處理中";
            break;
            case "Completed":
                $_SESSION['Trans_Status'][] = "已完成";
            break;
            case "Failed: Cannot find this transaction or upload the wrong transaction proof":
                $_SESSION['Trans_Status'][] = "失敗: 找不到此交易或上傳錯誤的交易證明";
            break;
        }
    } else {
        $_SESSION['Trans_Status'][] = $row['Status'];
    }
  }
} else {
    if ($_COOKIE['language']=="zh-tw"){
        $ErrorMsg="最近30日內並無進行增值";
    } else if ($_COOKIE['language']=="eng"){
        $ErrorMsg="No value added in the last 30 days";
    }
}
?>
<html>
<body>
<?php include_once('header.php') ?>

<!--  Begin of Content (Body)	-->
<div id="mainbody">
    <? if ($_COOKIE['language']=="zh-tw") : ?>
        <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;"><h1>增值記錄<br>只會顯示最近 30天的記錄</h1></div><br>
    <? elseif ($_COOKIE['language']=="eng") : ?>
        <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;line-height:1.1;"><h1>Value-added record<br>Only records from the last 30 days</h1></div><br>
    <? endif; ?>
    <? if ($ErrorMsg!="") : ?>
        <div style="text-align:center;background-color:#000;color:#fff;padding-top:10px;padding-bottom:10px;"><h1><?php echo $ErrorMsg ?></h1></div><br>
    <? endif; ?>
    <table align="center">
    <tr>
        <td width=20% style="text-align:center;"><? if ($_COOKIE['language']=="zh-tw") : ?>交易ID<? elseif ($_COOKIE['language']=="eng") : ?>Transaction ID<? endif; ?></td>
        <td width=20% style="text-align:center;"><? if ($_COOKIE['language']=="zh-tw") : ?>上傳日期<? elseif ($_COOKIE['language']=="eng") : ?>Upload Date<? endif; ?></td>
        <td width=60% style="text-align:center;"><? if ($_COOKIE['language']=="zh-tw") : ?>狀態<? elseif ($_COOKIE['language']=="eng") : ?>Status<? endif; ?></td>
    </tr>
    <?php 
        for($i=0; $i<$SQL_getTransRecords_count; $i++){
            echo '<tr>';
            echo '<td width=20% style="text-align:center;">'.$_SESSION['Trans_ID'][$i].'</td>';
            echo '<td width=20% style="text-align:center;">'.$_SESSION['Trans_SubDate'][$i].'</td>';
            echo '<td width=60% style="text-align:center;">'.$_SESSION['Trans_Status'][$i].'</td>';
            echo '</tr>';
        }  
    ?>
    </table>
</div>
<div id="cover"></div>
<!--  End of Content (Body)	-->

<?php include_once('footer.php') ?>
<!-- Script -->
<script>
document.getElementById('search_again_container').style.display="none";
</script>
<!-- Script -->



</body>
</html>