<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$backto = 'browse.php';

	try{
		$dbc = new DBC();
		$con = $dbc->con;
		$row = get_user_attrs($user_id);
?>
	<div align="center">
	<form method="post" action="edit_profile.php">
	<table>
	<tr> 
	<td> username:
	<td> <input type="text" name="username" value="<?php echo $row["username"];?>" />
	<tr>
	<td> email:
	<td> <input type="text" name="email" value="<?php echo $row["email"];?>" disabled>
	<tr>
	<td> password:
	<td> <input type="password" name="password" value="<?php echo $row["password"];?>">
	<tr>
	<td> password again:
	<td> <input type="password" name="password2" value="<?php echo $row["password"];?>">
	</table>
	<input type="submit" value="update" />
	<input type="hidden" name="backto" value="view_profile.php" />

	</form>
	</div>
	
<?php

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode($backto);
		header("Location: $eurl");
	}

?>

