<?php
	session_start();
	require_once('lib_view.php');
	display_header();
	display_logo();

	if (isset($_SESSION['user'])) {
		header('Location: browse.php');
		exit();
	}
?>

<div align="center">
<form action="signin.php" method=post>
<table id="signin">
	<tr>
	<td>email
	<td><input type="text" name="email">
	<tr>
	<td>password
	<td><input type="password" name="password">
	<tr>
	<td colspan=2> <input type="submit" value="sign in">
</table>
</form>
<a href="register.php">Get a new account</a>
<br>
</div>


<?php	
	display_footer();
?>


