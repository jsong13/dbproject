<?php
require_once('lib_control.php');
session_start();
$pin_id = $_POST['pin_id'];
$user_id = $_SESSION["user_id"];
echo $pin_id."<br>";
try {
	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, "select picture.user_id as owner
		from pin join picture on picture.picture_id = pin.picture_id where pin.pin_id = $pin_id ;");
	$owner = pg_fetch_all($rs)[0]['owner'];

	echo $user_id . "<br>";
	echo $owner . '<br>';
	// SHIT
	exit();

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
