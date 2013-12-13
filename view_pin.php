<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];
	$pin_id = $_GET["pin_id"];

	echo '<div align="center">';
	display_pin($pin_id, "view_pin.php?pin_id=$pin_id");

	echo "<h2>Comments</h2>";
	
	try{
		$dbc = new DBC();
		$con = $dbc->con;
		$rs = pg_query($con, "select 
			u.username as username ,
		  	c.body as body,
			date_trunc('second', c.time) as time	
			from comments as c, pin as p, useraccount as u where 
			p.pin_id = c.pin_id and u.user_id = c.user_id
		   and c.pin_id = $pin_id order by c.time desc;	");
	
		echo '<table border="1px">';
		echo "<tr><th>username";
		echo "<th>time";
		echo "<th>body";
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

	<form action="add_comment.php" method="post">
		<textarea cols="80" rows="10" name="body"></textarea>
		<input type="hidden" name="pin_id" value=<?php echo $pin_id;?>>
		<br>
		<input type="submit" value="add"/>
	</form>
	</div>

<?php

	} catch (Exception $e) {
		$eurl = "error.php?message=".urlencode($e->getMessage());
		$eurl .= "&to=".urlencode("register.php");
		header("Location: $eurl");
	}


?>



