<?php
require_once('lib_control.php');
session_start();
$stream_id = $_POST['stream_id'];
$user_id = $_SESSION["user_id"];
$backto = $_POST['backto'];
// backto is ingored in delete

try {
	$db = new DBC();
	$con = $db->con;
	
	// check if you are the owner
	if (! is_this_my_stream($stream_id, $user_id)) {
		throw new Exception('not your stream, can not delete');
	}

	$rs = pg_query($con, "delete from stream where stream_id=$stream_id ; ");
	$rs = pg_fetch_all($rs);
	header('Location: list_streams.php');
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("list_streams.php");
	header("Location: $eurl");
}
exit();

?>
