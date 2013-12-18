<?php
require_once('lib_control.php');
session_start();
$pinboard_name = $_POST['pinboard_name'];
$user_id = $_SESSION["user_id"];

try {
	$db = new DBC();
	$con = $db->con;
	$pinboard_name =  trim($pinboard_name);
	if ($pinboard_name == "") throw new Exception("pinboard name can not be empty!");


	$rs = pg_query($con, "insert into pinboard (pinboard_name, user_id, friend_comment_only) 
		values ('$pinboard_name', $user_id, 'FALSE'); ");
	$rs = pg_fetch_all($rs);
	header('Location: list_boards.php');
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("list_boards.php");
	header("Location: $eurl");
}

?>
