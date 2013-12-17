<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$pin_id = $_POST["pin_id"];
	$backto = $_POST['backto'];

	try{
		$dbc = new DBC();
		$con = $dbc->con;

		// test if the pin exsits
		$rs = pg_query($con, "select * from pin where pin_id = $pin_id;");
		$rs = pg_fetch_all($rs);
		if ($rs == false) throw new Exception("pin $pin_id does not exist");

		echo '<div align="center">';
		echo '<form action="repin.php" method="post">';
		echo '<h3>Please select a board:</h3>';
		echo '<select name="pinboard_id">';
		$rs = pg_query($con, "select * from pinboard  where user_id = $user_id ;");
		while ($row = pg_fetch_assoc($rs)) {
			$pinboard_id = $row['pinboard_id'];
			echo "<option value=$pinboard_id>" . $row['pinboard_name'] . '</option>';
		}
		echo '</select>';
		echo '<br>';
		echo '<button type="submit">Add</button>';
		echo '<input type="hidden" name="pin_id" value="'. $pin_id .'"/>'; 
		echo '<input type="hidden" name="backto" value="'. $backto .'"/>'; 
		echo '</form>';
		echo '</div>';

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("browse.php");
		header("Location: $eurl");
	}

?>



