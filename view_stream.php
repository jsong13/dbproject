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
		$stream_user_id = get_stream_attrs($stream_id)['user_id'];
		$stream_username = get_user_attrs($stream_user_id)['username'];
	
		?>

		<h1 align="center"> <?php echo $name ;?> </h1>
		<h3 align="center"> <?php echo $stream_username ;?> </h3>
		<table width="30%" align="center">
		
		<tr> <td>


		<form action="edit_stream.php" method="post">
			<fieldset>
			<legend> Stream Name</legend>
			<input type="text" name="stream_name" value="<?php echo $name; ?>" />	
			<input type="submit" value="update" />
			<?php 
			html_input_hidden_int("stream_id", $stream_id);
			html_input_hidden_string("content", "stream_name");
			html_input_hidden_string("backto", "view_stream.php?stream_id=$stream_id");
			?>
			</fieldset>
		</form>



		<!-- editing tags-->
		<tr>
		<td >
		<form action="edit_stream.php" method="post">
			<fieldset>
			<legend> Choose tags to follow</legend>
			<?php
			foreach(get_all_active_tags() as $tag_name) {
				echo "<input type=\"checkbox\" name=\"kept_tags[]\" value=\"$tag_name\"";  
				if (in_array($tag_name, get_following_tags($stream_id))) echo " checked ";
				echo ">$tag_name";	
				echo "</input>  ";
				echo '&nbsp;';
				echo '&nbsp;';
				echo '&nbsp;';
				echo '&nbsp;';
			}
			foreach(get_following_tags($stream_id) as $tag_name) {
				if (! in_array($tag_name, get_all_active_tags())) {
					echo "<input type=\"checkbox\" name=\"kept_tags[]\" value=\"$tag_name\"";  
					echo " checked ";
					echo ">$tag_name";	
					echo "</input>  ";
					echo '&nbsp;';
					echo '&nbsp;';
					echo '&nbsp;';
					echo '&nbsp;';
				}
			}
			?>
			<br>
			add more (separated by comma): <input type="text" name="added_tags"/>
			<br>
			<input type="submit" value="update" />
			<?php 
			html_input_hidden_int("stream_id", $stream_id);
			html_input_hidden_string("content", "tag");
			html_input_hidden_string("backto", "view_stream.php?stream_id=$stream_id");
			?>
			</fieldset>
		</form>




		<!--CHOOSE Boards-->


		<tr>
		<td>
		<form action="edit_stream.php" method="post">
			<fieldset>
			<legend> Choose pinboards to follow</legend>

			<!--current Boards-->
			<?php
			foreach (get_all_pinboards() as $pinboard_id ){
				echo "<input type=\"checkbox\" name=\"kept_pinboards[]\" value=$pinboard_id"; 
				if (in_array($pinboard_id, get_following_pinboards($stream_id))) echo " checked ";
				echo ">";
				echo get_pinboard_headline($pinboard_id);
				echo "</input>  ";
				echo '&nbsp;';
				echo '&nbsp;';
				echo '&nbsp;';
				echo '&nbsp;';
			}

			?>

			<br>
			<input type="submit" value="update" />
			<?php 
			html_input_hidden_int("stream_id", $stream_id);
			html_input_hidden_int("content", "pinboard");
			html_input_hidden_string("backto", "view_stream.php?stream_id=$stream_id");
			?>
			</fieldset>
		</form>
		</table>


		<!--pins -->
		<h2 align="center">Pins in this stream</h2>
		<hr>
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



