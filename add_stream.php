<?php
require_once('lib_control.php');
session_start();
$stream_name = $_POST['stream_name'];
$user_id = $_SESSION["user_id"];
$backto = $_POST["backto"];

try {
	$db = new DBC();
	$con = $db->con;

	$stream_name = trim($stream_name);
	if ($stream_name == "" ) throw new Exception("stream_name cannot be null");
	$rs = pg_query($con, "insert into stream (name, user_id) 
		values ('$stream_name', $user_id); ");
	$rs = pg_fetch_all($rs);
	header("Location: $backto");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode($backto);
	header("Location: $eurl");
}

?>
