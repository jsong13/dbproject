<?php
require_once('lib_control.php');
session_start();
$pinboard_name = $_POST['pinboard_name'];
$user_id = $_SESSION["user_id"];

try {
	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, "insert into pinboard (pinboard_name, user_id) values ('$pinboard_name', $user_id); ");
	$rs = pg_fetch_all($rs);
	header('Location: list_boards.php');
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("register.php");
	header("Location: $eurl");
}
exit();

?>
