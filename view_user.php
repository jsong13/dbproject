<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$other_user_id = $_GET["user_id"];

	echo "<table border=1> ";
	display_user_tr($other_user_id, "view_user.php?user_id=$other_user_id");
	echo "</table> ";
	display_footer();
?>

