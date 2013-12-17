<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
?>
<hr>
<div align="center">
<form action="add_board.php" method="post">
add a board:
<input type="text" name="pinboard_name">
<button type="submit" >add</button>
</form>
</div>
<hr>

<?php
try {
	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, "select * from pinboard where user_id=$user_id ; ");

	echo '<div align="center">';
	echo "<table border=\"1\">";
	echo '<tr>';
	echo '<th> board name';
	echo '<th> user';
	echo '<th> comment';
	echo '<th> edit';
	echo '<th> delete';

	while ($row = pg_fetch_assoc($rs)) {
		$pinboard_id=$row['pinboard_id'];

		echo '<tr>';
		display_pinboard_tds($pinboard_id, 'list_boards.php');

		echo "<td>";
		echo '<form method="post" action="edit_board.php">';
		echo '<input type="hidden" name="pinboard_id" value="'. $pinboard_id . '">';
		echo '<input type="submit" value="edit">';
		echo '</form>';


		echo "<td>";
		echo '<form method="post" action="delete_board.php">';
		echo '<input type="hidden" name="pinboard_id" value="'. $pinboard_id . '">';
		echo '<input type="submit" value="delete">';
		echo '</form>';

	}
	echo "</table>";
	echo '</div>';
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("register.php");
	header("Location: $eurl");
}


	display_footer();
?>
