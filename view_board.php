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

	<div align="center">
	<form method="post" action="add_pin.php">
		add pin from web <input type="text" name="source_url">
		<input type="submit" value="add">
		<input type="hidden" name="pinboard_id" value="<?php echo $pinboard_id; ?>">
	</form>

	<form method="post" action="upload_pin.php"
		enctype="multipart/form-data">
		<label for="file">Picture File:</label>
		<input type="file" name="file">
		<input type="submit" value="submit">
		<input type="hidden" name="pinboard_id" value="<?php echo $pinboard_id ;?>">
	</form>

	</div>

<?php


	try{
		$db = new DBC();
		$con = $db->con;
			
		$rs = pg_query($con, 
			"select * from pinboard where pinboard_id=$pinboard_id ;");
		$rs = pg_fetch_all($rs);
		if ($rs == false) throw new Exception("pinboard $pinboard_id does not exist");

		$pinboard_name = $rs[0]['pinboard_name'];
		$pinboarduser_id = $rs[0]['user_id'];

		$rs = pg_query($con, "select * from useraccount where user_id=$pinboarduser_id ;");
		$rs = pg_fetch_all($rs);
		$pinboard_username = $rs[0]['username'];



		echo '<hr>';
		echo '<h1 align="center">';
		echo $pinboard_name;
		echo "</h1>";
		echo '<h2 align="center"> ';
		echo 'by '. $pinboard_username;
		echo "</h2>";



		$rs = pg_query($con, 
			"select * from pin inner join picture on pin.picture_id = picture.picture_id
			where pin.pinboard_id=$pinboard_id 
			order by pin.time desc; ");


		while ($row = pg_fetch_assoc($rs)) {
			$pin_id=$row['pin_id'];
			//echo '<img src="' . $row['url'] . '"/>';
			display_pin($pin_id, "view_board.php?pinboard_id=$pinboard_id");
		}

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("browse.php");
		header("Location: $eurl");
	}
?>
