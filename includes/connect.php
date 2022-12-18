<?php
// A Connection Setting Parts at below are Refer To https://www.w3schools.com/php/php_mysql_connect.asp
// All DB Connection and preparation of the SQL are Refer To https://www.w3schools.com/php/php_mysql_prepared_statements.asp

date_default_timezone_set("Asia/Hong_Kong"); // Change the php timezone to HK for insert the HK datetime to DB. 
//define("DB_HOST", "mysql.comp.polyu.edu.hk"); //PolyU SQL Server
define("DB_HOST", "localhost"); //Local
define("DB_USER", "u643641743_bbs_admin"); // DB USERNAME
define("DB_PASS", "localB2B1S2*admin");  // DB PASSWORD
define("DB_NAME", "u643641743_buybysearch"); // DB NAME
define("table_ac", "tb_account"); // A table that contain a account info
define("table_userinfo", "tb_userinfo"); // A table that contain a user info
define("table_post", "tb_post"); // A table that contain a post data
define("table_cart", "tb_usercart"); // A table that contain a user cart data
define("table_trans", "tb_transaction"); // A table that contain a add value transaction
define("table_notification", "tb_notification"); // A table that contain trading notification by users

try{
    $conn = new PDO('mysql:host='.DB_HOST.';'.'dbname='.DB_NAME.';'.'charset=utf8', DB_USER,DB_PASS);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
// set the PDO error mode to exception
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>