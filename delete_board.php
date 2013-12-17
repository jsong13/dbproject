<?php
require_once('lib_control.php');
session_start();
$pinboard_id = $_POST['pinboard_id'];
$user_id = $_SESSION["user_id"];
//echo "here";
//echo $pinboard_id ;
try {
	$db = new DBC();
	$con = $db->con;
	// check if you are the owner of the board
	$rs = pg_query($con, "select user_id from pinboard where pinboard_id=$pinboard_id ; ");
	if ($rs == false) {
		throw new Exception("Board $pinboard_id do not exisit.");
	}

	$rs = pg_fetch_all($rs);
	if ($user_id !=	$rs[0]['user_id']) {
		throw new Exception('not your board, can not delete');
	}

	$rs = pg_query($con, "delete from pinboard where pinboard_id=$pinboard_id ; ");
	$rs = pg_fetch_all($rs);
	header('Location: list_boards.php');
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("list_boards.php");
	header("Location: $eurl");
}
exit();

?>
