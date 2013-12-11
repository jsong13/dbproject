<?php
require_once('lib_control.php');
session_start();
$email = $_POST['email'];
$password = $_POST['password'];
$password2 = $_POST['password2'];
$username = $_POST['username'];

try {
	$invalid = False;
	$invalid |= (filter_var($email, FILTER_VALIDATE_EMAIL) === false);
	
	if ($invalid) throw new Exception('not a valid email');
	
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

	$rs = pg_query($con, "select 1 as result from useraccount where email = '$email'");
	$rs = pg_fetch_all($rs);
	// res is false if nothing found or an array if found records
	$invalid |= ($rs!==false);

	if ($invalid) 
		throw new Exception("$email has been used by others.<br>");

	$q = "insert into useraccount(email, password, username) ";
	$q .= "values ('$email', '$password', '$username' );";
	$rs = pg_query($con, $q);
	$rs = pg_fetch_all($rs);

	$q = "select user_id from useraccount where email = '$email' and password='$password';";
	$rs = pg_query($con, $q);
	$rs = pg_fetch_all($rs);
	if ($rs === false) throw new Exception("can not sign in");

	$_SESSION['email'] = $email;
	$_SESSION['user_id'] = $rs[0]["user_id"];


	//echo 'Debug: you has been registered succussefully!<br>';
	//echo 'Debug: <a href="browse.php">Go</a><br>';
	header('Location: list_boards.php');
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("register.php");
	header("Location: $eurl");
}
exit();

?>
