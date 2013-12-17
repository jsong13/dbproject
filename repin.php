<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$backto = $_POST["backto"];
$pin_id = $_POST["pin_id"];
$pinboard_id = $_POST["pinboard_id"];

try {
	$db = new DBC();
	$con = $db->con;

	// pin exist
	$rs = pg_query($con, "select * from pin where pin_id=$pin_id ;");
	$rs = pg_fetch_all($rs);	
	if ($rs == false) throw new Exception("pin $pin_id does not exist in db");
	
	// picture_id
	$rs = pg_query($con, "select picture.picture_id as picture_id 
		from pin join picture 
		on pin.picture_id = picture.picture_id where pin.pin_id = $pin_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) throw new Exception("Picture does not exist");
	$picture_id = $rs[0]["picture_id"];

	// is this your board
	$rs = pg_query($con, "select * from pinboard 
		where pinboard_id=$pinboard_id and user_id =$user_id ;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) throw new Exception("Not your board");

	// if this picture already exists in this board
	$rs = pg_query($con, "select * from pinboard join pin 
		on pin.pinboard_id = pinboard.pinboard_id 
		where pin.picture_id = $picture_id and 
		pinboard.pinboard_id =$pinboard_id ;");
	$rs = pg_fetch_all($rs);
	if ($rs != false) throw new Exception("Picutre $picutre_id already exists in this board $board_id.");

	// add
	$rs = pg_query($con, "insert into pin (picture_id, user_id, pinboard_id) values 
		($picture_id, $user_id, $pinboard_id) ;");

	header("Location: $backto");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode($backto);
	header("Location: $eurl");
}
exit();

?>

