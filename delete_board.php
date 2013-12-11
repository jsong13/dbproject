<?php
require_once('lib_control.php');
session_start();
$pinboard_id = $_POST['pinboard_id'];
//echo "here";
//echo $pinboard_id ;
try {
	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, "delete from pinboard where pinboard_id=$pinboard_id ; ");
	$rs = pg_fetch_all($rs);
	header('Location: list_boards.php');
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("register.php");
	header("Location: $eurl");
}
exit();

?>
