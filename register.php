<?php
require_once('lib_view.php');
session_start();
display_header();
display_logo();
unset($_SESSION['user']);
?>

<form method=post action="signup.php">
<div align="center">
<h2>Sign up</h2>
<table>
	<tr>
		<td> email
		<td> <input type="text" name="email"> 
		<td> * 
		<td> max 15 characters, used when sign in
	<tr> 
		<td> password
		<td> <input type="password" name="password">
		<td> * 
		<td> max 20 characters
	<tr> 
		<td> password again
		<td> <input type="password" name="password2">
		<td> * 
		<td> max 20 characters

	<tr> 
		<td> username
		<td> <input type="username" name="username">
		<td> *
		<td> max 20 characters

	<tr> 
		<td> ...
		<td> ..
		<td>
</table>
	<input type="submit">
</div>
</form>



<?php
display_footer();
?>
