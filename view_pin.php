<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$pin_id = $_GET["pin_id"];



	try{
		$dbc = new DBC();
		$con = $dbc->con;

		// test if the pin exsits
		$rs = pg_query($con, "select * from pin where pin_id = $pin_id;");
		$rs = pg_fetch_all($rs);
		if ($rs == false) {
			throw new Exception("pin $pin_id does not exist");
		}

		echo '<div align="center">';
		display_pin($pin_id, "view_pin.php?pin_id=$pin_id");
		echo "<hr>";
		echo "<h2>Comments</h2>";

		$rs = pg_query($con, "select 
			u.username as username ,
		  	c.body as body,
			date_trunc('second', c.time) as time	
			from comments as c, pin as p, useraccount as u where 
			p.pin_id = c.pin_id and u.user_id = c.user_id
		   and c.pin_id = $pin_id order by c.time desc;	");
	
		echo '<table width=900 border="1px">';
		echo "<tr><th width=100>username";
		echo "<th width=200>time";
		echo "<th width=600>body";
		while ($row = pg_fetch_assoc($rs)) {

?>
	<tr>
	<td> <?php echo $row["username"];?>
	<td> <?php echo $row["time"];?>
	<td> <?php echo $row["body"];?>
<?php
		}
		echo "</table>";

?>

<?php 
	$rs = pg_query($con, "select * from pin where pin_id=$pin_id;");
	$rs = pg_fetch_all($rs);
	$pin_user_id = $rs[0]['user_id'];
	
	$rs = pg_query($con, "select * from pinboard join pin on pin.pinboard_id = pinboard.pinboard_id  
		where pin_id=$pin_id;");
	$rs = pg_fetch_all($rs);
	$friend_comment_only = $rs[0]['friend_comment_only'];

	
	if ( ($user_id != $pin_user_id) 
		and $friend_comment_only 
		and (! are_we_friends($user_id, $pin_user_id))) {
		echo "Only friend can make comment!";
	}else {
?>
	<form action="add_comment.php" method="post">
		<textarea cols="90" rows="2" name="body"></textarea>
		<?php
		echo html_input_hidden_int("pin_id", $pin_id);
		echo html_input_hidden_string("backto", "view_pin.php?pin_id=$pin_id");
		?>
		<br>
		<input type="submit" value="add"/>
	</form>
	
<?php } ?>
	
	</div>

<?php
	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("browse.php");
		header("Location: $eurl");
	}


?>



