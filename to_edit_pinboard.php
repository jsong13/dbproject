<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$pinboard_id = $_POST["pinboard_id"];
	$backto = $_POST['backto'];

	try{
		$dbc = new DBC();
		$con = $dbc->con;

		// test if the pinboard exsits
		$rs = pg_query($con, "select * from pinboard where pinboard_id = $pinboard_id;");
		$rs = pg_fetch_all($rs);
		if ($rs == false) throw new Exception("pinboard $pinboard_id does not exist");

		echo '<div align="center">';
		echo '<h1>Edit pinboard</h1>';

		echo '<form action="edit_pinboard.php" method="post">';

		echo "<table>";
		

		// change board name
		echo "<tr>";
		echo "<td>";
		echo "name: ";
		echo "<td>";
		echo '<input type="text" name="new_pinboard_name" value="';
		echo get_pinboard_attrs($pinboard_id)["pinboard_name"];
		echo '" />';

		// change friend_comment_only
		echo "<tr>";
		echo "<td>";
		echo "friend_comment_only: ";
		echo "<td>";
		echo '<input type="checkbox" name="friend_comment_only" value="true" ';
		if (get_pinboard_attrs($pinboard_id)["friend_comment_only"]) echo 'checked';
		echo '>';


		// delete tags
		echo "<tr>";
		echo "<td>";
		echo "tags: ";
		echo "<td>";
		display_edit_tags_input(get_pinboard_tags($pinboard_id));

		// add new tags
		echo "<tr>";
		echo "<td>";
		echo "add new tags (separted by comma ):";
		echo "<td>";
		echo "<input type=\"text\" name=\"added_tags\"/>";

		echo "</table>";

		echo "<input type=\"submit\" value=\"update\">";
		echo html_input_hidden_int("pinboard_id", $pinboard_id);
		echo html_input_hidden_string("backto", $backto);

		echo '</form>';
		echo '</div>';

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode($backto);
		header("Location: $eurl");
	}

?>

