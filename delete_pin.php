<?php
require_once('lib_control.php');
session_start();
$pin_id = $_POST['pin_id'];
$user_id = $_SESSION["user_id"];

try {
	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, "select 
		picture.user_id as owner_id,
		picture.picture_id as picture_id,
		pin.user_id as pinuser_id
		from pin join picture on picture.picture_id = pin.picture_id where pin.pin_id = $pin_id ;");
	$row = pg_fetch_all($rs)[0];
	$owner_id = $row['owner_id'];
	$pinuser_id = $row['pinuser_id'];
	$picture_id = $row['picture_id'];

	$pinboard_id = get_pin_attrs($pin_id)['pinboard_id'];

	echo $user_id . "<br>";
	echo $ownerid . '<br>';

	if ($user_id != $pinuser_id) {
		// not your pin, I cannot delete it
	} elseif ($user_id != $owner_id) {
		$rs = pg_query($con, "delete from pin where pin_id=$pin_id; ");
		pg_fetch_all($rs);
	} else {
		$rs = pg_query($con, "delete from pin where pin_id=$pin_id; ");
		$rs = pg_query($con, "delete from picture where picture_id=$picture_id; ");
	}

	header("Location: view_board.php?pinboard_id=$pinboard_id");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("browse.php");
	header("Location: $eurl");
}

exit();

?>
