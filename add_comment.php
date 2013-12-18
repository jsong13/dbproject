<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$pin_id = $_POST['pin_id'];
$body = $_POST['body'];
$backto = $_POST['backto'];
try {
	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, "select * from pin where pin_id=$pin_id;");
	$rs = pg_fetch_all($rs);
	$pin_user_id = $rs[0]['user_id'];
	
	$rs = pg_query($con, "select * from pinboard join pin on pin.pinboard_id = pinboard.pinboard_id  
		where pin_id=$pin_id;");
	$rs = pg_fetch_all($rs);
	$friend_comment_only = $rs[0]['friend_comment_only'];

	// todo: check if user_id can make a comment 
	if ( ($user_id != $pin_user_id) 
		and $friend_comment_only 
		and (! are_we_friends($user_id, $pin_user_id))) {
		throw new Exception("Only friend can make comment!");
	}

	$rs = pg_query($con, 
		"insert into comments (pin_id, user_id, body)
	   		values ($pin_id, $user_id, '$body')	;");
	$rs = pg_fetch_all($rs);	
	header("Location: view_pin.php?pin_id=$pin_id");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode($backto);
	header("Location: $eurl");
}
exit();

?>
