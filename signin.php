<?php
session_start();
require_once('lib_control.php');

$email = $_POST['email'];
$password = $_POST['password'];


$dbc = new DBC();
$con = $dbc->con;

try {
	if (empty($email)) throw new Exception("email can not be empty");
	if (empty($password)) throw new Exception("password can not be empty");

	$q = "select user_id from useraccount where email = '$email' and password='$password';";
	$rs = pg_query($con, $q);
	$rs = pg_fetch_all($rs);
	if ($rs === false) throw new Exception("can not sign in");

	$_SESSION['email'] = $email;
	$_SESSION['user_id'] = $rs[0]["user_id"];
	header('Location: browse.php');
} catch (Exception $e) {

	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("index.php");
	header("Location: $eurl");
}

exit();
?>
