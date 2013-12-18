<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];




try {
	$db = new DBC();
	$con = $db->con;
	
	echo '<div align="center">';

	echo '<form action="add_pin.php" method="post">';
	echo "image url: ";
	echo '<input type="text" name="source_url" />';
	display_select_pinboards($user_id);
	html_input_hidden_string("backto", "list_pins.php");
	echo '<input type="submit" name="add" value="add">';
	echo '</form>';

	echo '<form action="upload_pin.php" method="post" enctype="multipart/form-data">';
	echo "upload: ";
	echo '<input type="file" name="file">';
	display_select_pinboards($user_id);
	html_input_hidden_string("backto", "list_pins.php");
	echo '<input type="submit" name="add" value="add">';
	echo '</form>';

	echo '</div>';


	$rs = pg_query($con, "select * from pin where user_id=$user_id order by time desc; ");


	while ($row = pg_fetch_assoc($rs)) {
		$pin_id=$row['pin_id'];
		display_pin($pin_id, 'list_pins.php');
	}
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("browse.php");
	header("Location: $eurl");
}


	display_footer();
?>
