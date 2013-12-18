<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$pinboard_id = $_GET["pinboard_id"];
?>

<?php


	try{
		$db = new DBC();
		$con = $db->con;
			

		$rs = pg_query($con, 
			"select * from pinboard where pinboard_id=$pinboard_id ;");
		$rs = pg_fetch_all($rs);
		if ($rs == false) throw new Exception("pinboard $pinboard_id does not exist");

		$pinboard_name = $rs[0]['pinboard_name'];
		$pinboard_user_id = $rs[0]['user_id'];

		$rs = pg_query($con, "select * from useraccount where user_id=$pinboard_user_id ;");
		$rs = pg_fetch_all($rs);
		$pinboard_username = $rs[0]['username'];

		// show add to board only if I am the owner
		if ($user_id == $pinboard_user_id) {
?>
	<hr>
	<div align="center">
	<form method="post" action="add_pin.php">
		add pin from web <input type="text" name="source_url">
		<input type="submit" value="add">
		<?php html_input_hidden_int("pinboard_id", $pinboard_id) ;?>
		<?php html_input_hidden_string("backto", "view_board.php?pinboard_id=$pinboard_id") ;?>
	</form>

	<form method="post" action="upload_pin.php"
		enctype="multipart/form-data">
		<label for="file">Picture File:</label>
		<input type="file" name="file">
		<input type="submit" value="add">
		<input type="hidden" name="pinboard_id" value="<?php echo $pinboard_id ;?>">
	</form>
	</div>
<?php
		
		}


		echo '<hr>';
		echo '<div align="center">';
		echo '<h1>';
		echo $pinboard_name;
		echo "</h1>";
		echo '<h2> ';
		echo 'by '. $pinboard_username;
		echo "</h2>";
		echo "tags: " . implode(", ", get_pinboard_tags($pinboard_id)) ;
		echo "<br><br>";
		echo "<form action=\"to_edit_pinboard.php\" method=\"post\">"; 
		echo "<input type=\"submit\" name=\"edit\" value=\"edit pinboard\"/>";	
		html_input_hidden_int("pinboard_id", $pinboard_id);
		html_input_hidden_string("backto", "view_board.php?pinboard_id=$pinboard_id");
		echo "</form>";



		$rs = pg_query($con, 
			"select * from pin inner join picture on pin.picture_id = picture.picture_id
			where pin.pinboard_id=$pinboard_id 
			order by pin.time desc; ");


		while ($row = pg_fetch_assoc($rs)) {
			$pin_id=$row['pin_id'];
			//echo '<img src="' . $row['url'] . '"/>';
			display_pin($pin_id, "view_board.php?pinboard_id=$pinboard_id");
		}

		echo '</div>';

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("browse.php");
		header("Location: $eurl");
	}
?>
