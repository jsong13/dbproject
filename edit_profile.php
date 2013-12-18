<?php
require_once('lib_control.php');
session_start();
$password = $_POST['password'];
$password2 = $_POST['password2'];
$username = $_POST['username'];
$backto =$_POST["backto"];
$user_id =$_SESSION["user_id"];

try {
	$invalid = False;
	
	$invalid |= empty($username);
	$invalid |= strlen($username) > 20;
	if ($invalid) throw new Exception('not a valid username');

	$invalid |= empty($password);
	$invalid |= strlen($password) > 20;
	if ($invalid) throw new Exception('not a valid password');
	
	$invalid |= $password !== $password2;
	if ($invalid) throw new Exception('passwords do not match');

	$db = new DBC();
	$con = $db->con;

	$q = "update useraccount set password=$password, username='$username' where user_id=$user_id ; ";
	$rs = pg_query($con, $q);
	$rs = pg_fetch_all($rs);


	header("Location: $backto");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode($backto);
	header("Location: $eurl");
}
exit();

?>
