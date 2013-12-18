<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];

	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, 
		"(
			select user2_id as other_user_id from friendship where user1_id = $user_id 
		) union (
			select user1_id as other_user_id from friendship where user2_id = $user_id 
		) ;" );

	//	$rs = pg_query($con, "select user_id as other_user_id from useraccount;");

	echo "<h1> List of friends and invitations</h1>";
	echo "<table border=1> ";
	while ($row = pg_fetch_assoc($rs)){
		display_user_tr($row['other_user_id'], "view_social.php");
	}
	echo "</table> ";

	$rs = pg_query($con, 
		" (select user_id as other_user_id from useraccount where user_id <> $user_id ) 
			except
		((
			select user2_id as other_user_id from friendship where user1_id = $user_id 
		) union (
			select user1_id as other_user_id from friendship where user2_id = $user_id 
		)) ;" );

	//$rs = pg_query($con, "select user_id as other_user_id from useraccount;");
	echo "<h1> Other users</h1>";
	echo "<table border=1> ";
	while ($row = pg_fetch_assoc($rs)){
		display_user_tr($row['other_user_id'], "view_social.php");
	}
	echo "</table> ";



	display_footer();
?>

