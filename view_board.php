<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$pinboard_id = $_GET["pinboard_id"];
	



	try{
		$db = new DBC();
		$con = $db->con;
			
		$rs = pg_query($con, 
			"select * from pinboard 
				where pinboard_id=$pinboard_id ;");
		echo "board name: ";
		echo pg_fetch_all($rs)[0]['pinboard_name'];
		echo "<br>";



		$rs = pg_query($con, 
		"select * from pin 
			where pinboard_id=$pinboard_id and 
			user_id = $user_id ; ");

		while ($row = pg_fetch_assoc($rs)) {
			$pin_id=$row['pin_id'];
			echo "<br>";
			echo $pin_id;
		}

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("register.php");
		header("Location: $eurl");
	}
?>



