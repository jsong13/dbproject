<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
?>
<form action="add_board.php" method="post">
board name:
<input type="text" name="pinboard_name">
<button type="submit" >add</button>
</form>

<table>
<tr>
<th> Boardname

<?php
try {
	$db = new DBC();
	$con = $db->con;
	$rs = pg_query($con, 
		"select * from pinboard 
			where user_id=$user_id ; ");

	while ($row = pg_fetch_assoc($rs)) {
		$pinboard_id=$row['pinboard_id'];
		echo "<tr>";
		echo "<td>";
		echo "<a href=view_board.php?pinboard_id=$pinboard_id>";
		echo $row['pinboard_name'];	
		echo "</a>"

?>
	<td>
	<form method="post" action="delete_board.php">
	<input type="hidden" name="pinboard_id" value="<?php echo $pinboard_id ; ?>">
		<input type="submit" value="delete">
	</form>
<?php
	}
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("register.php");
	header("Location: $eurl");
}


?>
</table>



<?php
	display_footer();
?>
