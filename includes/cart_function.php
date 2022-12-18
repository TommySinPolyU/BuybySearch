<?php
if(isset($_POST['addcart_PostID'])){
    echo addcart($_POST['addcart_PostID']);
} else if(isset($_POST['removecart_PostID'])){
    echo removecart($_POST['removecart_PostID']);
} else if(isset($_POST['cart_clearall'])){
    echo clearcart();
}

function addcart($PostID){
    require_once('connect.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if(in_array($PostID, $_SESSION['cart_postID'])){
        switch($_COOKIE['language']){
            case "eng":
                print_r("Failed\nThis thing has been added in cart");
            break;
            case "zh-tw":
                print_r("加入失敗\n此東西已經加入購物車");
            break;
        }
    } else {
        if(!isset($_SESSION['cart_postID']) || !is_array($_SESSION['cart_postID']))
            $_SESSION['cart_postID'] = array();
        array_push($_SESSION['cart_postID'], $PostID);
        switch($_COOKIE['language']){
            case "eng":
                print_r("Added To Cart");
            break;
            case "zh-tw":
                print_r("已加至購物車");
            break;
        }
    }
    $Table_cart = table_cart;
    $cart_count_sql = $conn->prepare("SELECT * FROM $Table_cart WHERE UID = ?");
    $cart_count_sql->execute(array($_SESSION['UID']));	
    $cart_resultcount = $cart_count_sql->rowCount();
    $postID_separated = implode(",", $_SESSION['cart_postID']);
    if($cart_resultcount == 0){
        $cart_insert_sql = $conn->prepare("INSERT INTO $Table_cart(UID, Cart_PostID) VALUES(?, ?)");
        $cart_insert_sql->execute(array($_SESSION['UID'], $postID_separated));	
    } else {
        $cart_update_sql = $conn->prepare("UPDATE $Table_cart SET Cart_PostID=? WHERE UID = ?");
        $cart_update_sql->execute(array($postID_separated, $_SESSION['UID']));	
    }
}

function removecart($PostID){
    require_once('connect.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $key = array_search($PostID,$_SESSION['cart_postID']);
    //unset($_SESSION['cart_postID'][$key]);

    array_splice($_SESSION['cart_postID'],$key,1);
    switch($_COOKIE['language']){
        case "eng":
            print_r("Removed From Cart");
        break;
        case "zh-tw":
            print_r("已從購物車內移除此物品");
        break;
    }	
    $Table_cart = table_cart;
    $cart_count_sql = $conn->prepare("SELECT * FROM $Table_cart WHERE UID = ?");
    $cart_count_sql->execute(array($_SESSION['UID']));	
    $cart_resultcount = $cart_count_sql->rowCount();
    $postID_separated = implode(",", $_SESSION['cart_postID']);
    if($cart_resultcount == 0){
        $cart_insert_sql = $conn->prepare("INSERT INTO $Table_cart(UID, Cart_PostID) VALUES(?, ?)");
        $cart_insert_sql->execute(array($_SESSION['UID'], $postID_separated));	
    } else {
        $cart_update_sql = $conn->prepare("UPDATE $Table_cart SET Cart_PostID=? WHERE UID = ?");
        $cart_update_sql->execute(array($postID_separated, $_SESSION['UID']));	
    }
    if($postID_separated == ""){
        $Table_cart = table_cart;
        $cart_clear_sql = $conn->prepare("DELETE FROM $Table_cart WHERE UID=?");
        $cart_clear_sql->execute(array($_SESSION['UID']));	
    }
}

function clearcart(){
    require_once('connect.php');
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['cart_postID'] = array();
    switch($_COOKIE['language']){
        case "eng":
            print_r("Shopping cart has been emptied");
        break;
        case "zh-tw":
            print_r("購物車已清空");
        break;
    }
    $Table_cart = table_cart;
    $cart_clear_sql = $conn->prepare("DELETE FROM $Table_cart WHERE UID=?");
    $cart_clear_sql->execute(array($_SESSION['UID']));	
}
?>