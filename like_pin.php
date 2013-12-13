<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$picture_id = $_POST['picture_id'];
$pin_id = $_POST['pin_id'];

try {
	$db = new DBC();
	$con = $db->con;


	$rs = pg_query($con, "select 1 from likepicture, pin 
		where pin.picture_id = likepicture.picture_id 
		and pin.pin_id = $pin_id and likepicture.user_id=$user_id; ");
	$rs = pg_fetch_all($rs);
	if ($rs == false) {
		$rs = pg_query($con, "insert into likepicture (user_id, picture_id)
			values ($user_id, $picture_id);");
		pg_fetch_all($rs);
	} else {
		$rs = pg_query($con, "delete from likepicture where user_id = $user_id and picture_id = $picture_id ;");
		pg_fetch_all($rs);
	}


	$backto = $_POST["backto"];
	header("Location: $backto");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("view_board.php?pinboard_id=$pinboard_id");
	header("Location: $eurl");
}
exit();

?>
