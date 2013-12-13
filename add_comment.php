<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$pin_id = $_POST['pin_id'];
$body = $_POST['body'];

try {
	$db = new DBC();
	$con = $db->con;

	// todo: check if user_id can make a comment 
	
	$rs = pg_query($con, 
		"insert into comments (pin_id, user_id, body)
	   		values ($pin_id, $user_id, '$body')	;");
	$rs = pg_fetch_all($rs);	
	header("Location: view_pin.php?pin_id=$pin_id");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("view_board.php?pinboard_id=$pinboard_id");
	header("Location: $eurl");
}
exit();

?>
