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
		echo '<h1>Edit pin</h1>';

		echo '<form action="edit_pin.php" method="post">';

		echo "<table>";
		echo "<tr>";
		echo "<td>";
		echo "tags: ";
		echo "<td>";
		display_edit_tags_input(get_pin_tags($pin_id));

		echo "<tr>";
		echo "<td>";
		echo "add new tags (separted by comma ):";
		echo "<td>";
		echo "<input type=\"text\" name=\"added_tags\"/>";

		echo "</table>";

		echo "<input type=\"submit\" value=\"update\">";
		echo html_input_hidden_int("pin_id", $pin_id);
		echo html_input_hidden_string("backto", $backto);

		echo '</form>';
		echo '</div>';

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("browse.php");
		header("Location: $eurl");
	}

?>

