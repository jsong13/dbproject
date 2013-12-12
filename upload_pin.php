<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$pinboard_id = $_POST['pinboard_id'];

try {
	if ($_FILES["file"]["error"] > 0) {
		throw Exception("upload failed");
	}

	$tmp_name = $_FILES["file"]["tmp_name"];

	$ptype = str_replace('image/', "", $_FILES["file"]["type"]);
	$parts = pathinfo($tmp_name);

	$relname = 'images/' . $parts["filename"] .".". date("ywdhms") .".". $ptype ;
	$filenameOut = __DIR__ . '/'. $relname;
	move_uploaded_file($tmp_name, $filenameOut); 

	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, "insert into picture (source_url, url, user_id) values
			('$relname', '$relname', $user_id ) ; ");
	pg_fetch_all($rs);

	$rs = pg_query($con, "select picture_id from picture where source_url='$relname' ; " );
	$picture_id = pg_fetch_all($rs)[0]["picture_id"];
	
	// insert into pin
	$rs = pg_query($con, "insert into pin (picture_id, user_id, pinboard_id) values 
		($picture_id, $user_id, $pinboard_id) ;");

	header("Location: view_board.php?pinboard_id=$pinboard_id");

	
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("view_board.php?pinboard_id=$pinboard_id");
	header("Location: $eurl");
}
exit();

?>
