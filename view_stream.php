<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$stream_id = $_GET["stream_id"];

	try{
		$dbc = new DBC();
		$con = $dbc->con;

		// test if exists
		$rs = pg_query($con, "select * from stream where stream_id = $stream_id;");
		$rs = pg_fetch_all($rs);
		if ($rs == false) {
			throw new Exception("stream $stream_id does not exist");
		}

		$name = get_stream_attrs($stream_id)['name'];
		echo "<h1> $name </h1>";
		$stream_user_id = get_stream_attrs($stream_id)['user_id'];
		$stream_username = get_user_attrs($stream_user_id)['username'];
		echo "<h2> $stream_username </h2>";
	
		// editing tags
		?>
		<div align="center">
		<form action="edit_stream.php" method="post">
			<fieldset>
			<legend> Choose tags to follow</legend>
<?php
		foreach(get_all_active_tags() as $tag_name) {
			echo "<input type=\"checkbox\" name=\"kept_tags[]\" value=\"$tag_name\"";  
			if (in_array($tag_name, get_following_tags() )
			echo "/>";	
		}
?>
			<br>
			<input type="submit" value="update" />
			<?php 
			html_input_hidden_int("stream_id", $stream_id);
			html_input_hidden_int("content", "tag");
			html_input_hidden_string("backto", "view_stream.php&stream_id=$stream_id");
			?>
			</fieldset>
		</form>






		<form action="edit_stream.php" method="post">
			<fieldset>
			<legend> Choose pinboards to follow</legend>
			<br>
			<input type="submit" value="update" />
			<?php 
			html_input_hidden_int("stream_id", $stream_id);
			html_input_hidden_int("content", "pinboard");
			html_input_hidden_string("backto", "view_stream.php&stream_id=$stream_id");
			?>
			</fieldset>
		</form>
		</div>
<?php
		foreach(get_pins_in_stream($stream_id) as $pin_id) {
			display_pin($pin_id, "view_stream.php?stream_id=$stream_id");
		}



	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("list_streams.php");
		header("Location: $eurl");
	}


?>



