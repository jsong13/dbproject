<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$pinboard_id = $_POST['pinboard_id'];
$source_url=$_POST["source_url"];
$backto=$_POST['backto'];

try {
	$db = new DBC();
	$con = $db->con;
	// insert into picture if not exists
	// return a pictureid
	$rs = pg_query($con, 
		"select * from picture where source_url='$source_url' ;");
	$rs = pg_fetch_all($rs);	


	if ($rs === false) {
		echo "Not exist in db";
		$filenameIn  = $source_url;
		$parts = pathinfo($source_url);

		$relname = 'images/' . $parts["filename"] .".". date("ywdhms") .".". $parts["extension"] ;
		$filenameOut = __DIR__ ."/". $relname;

		$contentOrFalseOnFailure  = file_get_contents($filenameIn);
		if ($contentOrFalseOnFailure === false) {
			throw new Exception('Not a valid url');
		}

		$byteCountOrFalseOnFailure = file_put_contents($filenameOut, $contentOrFalseOnFailure);
		if ($byteCountOrFalseOnFailure === false) {
			throw new Exception('can not save');
		}
		// insert into picture and return picture_id

		$rs = pg_query($con, "insert into picture (source_url, url, user_id) values
			('$source_url', '$relname', $user_id ) ; ");
		pg_fetch_all($rs);
	} 

	$rs = pg_query($con, "select picture_id from picture where source_url='$source_url' ; " );
	$picture_id = pg_fetch_all($rs)[0]["picture_id"];
	
	// insert into pin
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
