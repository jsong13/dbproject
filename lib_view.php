<?php
	require_once("lib_control.php");
?>


<?php function display_header() { ?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="theme.css">
</head>
<body>
<?php } ?>


<?php function display_logo() { ?>
<h1 align="center">PINBOARD</h1>
<?php } ?>


<?php function  display_footer() { ?>
</body>
</html>
<?php } ?>

<?php function display_menu(){ ?>
<div id="menu" align="center">
<table>
<tr>
<td> <a href="browse.php">Browse</a>
<td> My Profile
<td> <a href="view_social.php">Social</a> 
<td> <a href="list_boards.php">My Boards </a>

<td> <a href="list_pins.php">My Pins</a> 
<td> <a href="search.php">Search</a>
<td> <a href="list_streams.php">My Streams</a>
<td> Recommends
<td>
<?php
	$user_id = $_SESSION['user_id'];
	if (!isset($user_id)) {
		echo "<a href=\"index.php\">Sign in</a>";
	} else {
		$dbc = new DBC();
		$con = $dbc->con;

		$rs = pg_query("select * from useraccount where user_id=$user_id;");
		echo "<a href=\"signout.php\">Sign out</a> " . pg_fetch_all($rs)[0]["username"];
	}
?>
</table>
</div>
<?php } ?>


<?php function display_pin($pin_id, $backto='browse.php') { 
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, 
		"select * from pin inner join picture on pin.picture_id = picture.picture_id
			where pin.pin_id=$pin_id ; ");
	$rs = pg_fetch_all($rs);
	$row=$rs[0];
	$url = $row['url'];
	$picture_id=$row['picture_id'];
	$user_id = $_SESSION["user_id"];

	$rs = pg_query($con, 
		"select count(user_id) as c from likepicture 
			where picture_id = $picture_id ;");
	$counts = pg_fetch_all($rs)[0]['c'];
	
	$rs = pg_query($con, 
		"select count(comment_id) as c from comments
			where comments.pin_id=$pin_id ; ");
	$totalcomments = pg_fetch_all($rs)[0]['c'];


	$rs = pg_query($con, 
		"select * from pin inner join useraccount on pin.user_id = useraccount.user_id
			where pin.pin_id=$pin_id ; ");
	$rs = pg_fetch_all($rs);
	$username = $rs[0]["username"];
	$pin_user_id = $rs[0]["user_id"];

	$rs = pg_query($con, 
		"select * from pin inner join pinboard on pinboard.pinboard_id = pin.pinboard_id
			where pin.pin_id=$pin_id ; ");
	$pinboard_name = pg_fetch_all($rs)[0]["pinboard_name"];
	$pinboard_id = pg_fetch_all($rs)[0]["pinboard_id"];

	$rs = pg_query($con, "select * from likepicture, pin 
		where pin.picture_id = likepicture.picture_id 
		and pin.pin_id = $pin_id and likepicture.user_id=$user_id; ");
	$rs = pg_fetch_all($rs);

	if ($rs == false) {
		$like = false;
	} else {
		$like = true;
	}

?>
	<div align="center" width="50%">
	<table border="1">
	<tr>
	<td align="center" colspan="2">
	<a href="view_pin.php?pin_id=<?php echo $pin_id ;?>">
	<img src=" <?php echo $row['url']; ?>" alt="images/default.png"
		width="200px"
	/></a>
	<td >

	by user
<?php 
	echo "<a href=\"view_user.php?user_id=$pin_user_id\">";
	echo $username ; 
	echo '</a>';
?>
	<br>
	<?php echo get_pin_attrs($pin_id)['time'];?>
	<br>

	in board <a href="view_board.php?pinboard_id=<?php echo $pinboard_id;?>">
		<?php echo $pinboard_name;?></a>

	<form action="to_repin.php" method="post">
		<input type="submit" value="repin"/>
		<input type="hidden" name="pin_id" value="<?php echo $pin_id;?>" />
		<input type="hidden" name="backto" value="<?php echo $backto;?>"/>
	</form>

	<?php if (is_this_my_pin($pin_id, $user_id)) { ?>
	<form action="to_edit_pin.php" method="post">
		<input type="submit" value="edit pin"/>
		<input type="hidden" name="pin_id" value=<?php echo $pin_id; ?>>
		<input type="hidden" name="backto" value="<?php echo $backto;?>"/>
	</form>

	<form action="delete_pin.php" method="post">
		<input type="submit" value="delete pin"/>
		<input type="hidden" name="pin_id" value=<?php echo $pin_id; ?>>
		<input type="hidden" name="backto" value="<?php echo $backto;?>"/>
	</form>
	<?php } ?>

	<form action="like_pin.php" method="post">
		<input type="hidden" name="backto" value="<?php echo $backto;?>"/>
		<input type="hidden" name="picture_id" value="<?php echo $picture_id ;?>">
		<input type="hidden" name="pin_id" value="<?php echo $pin_id ;?>">
		<?php if (!$like) {?>
		<input type="submit" value="like"/>
		<?php } else {?>
		<input type="submit" value="unlike"/>
		<?php } ?>
		
	</form>

	<?php echo $counts ;?> likes
	<br>
	<?php echo $totalcomments; ;?> comments
	<hr>

	<?php
		echo implode(", ", get_pin_tags($pin_id));
	?>

	</table>
	</div>
<?php } ?>



<?php 
function display_user_tr($other_user_id, $backto) {
	$user_id = $_SESSION["user_id"];
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select * from useraccount 
		where user_id=$other_user_id ;");
	$rs = pg_fetch_all($rs);

	echo "<tr>";

	if ($rs == false) {
		echo "user doesn't exist";
		return;
	}
	

	echo "<td>";
	echo $rs[0]["username"];

	echo "<td>";
	echo $rs[0]["email"];

	// friendship status 
	echo "<td>";

	$rs = pg_query($con, "select * from friendship where 
		( user1_id = $user_id and user2_id = $other_user_id );");
	$rs1 = pg_fetch_all($rs);
	
	$rs = pg_query($con, "select * from friendship where 
		( user2_id = $user_id and user1_id = $other_user_id );");
	$rs2 = pg_fetch_all($rs);
	
	if (!isset($user_id)) {
		echo " ";
	} elseif ($user_id == $other_user_id) {
		echo "myself";
	} elseif ($rs1 == false and $rs2 == false) {
		// nothing, show button that can send invitation
		echo "<form action=\"friendship.php\" method=\"post\">";	
		echo "<button type=\"submit\" name=\"send\">send invitation</button>";
		echo "<input type=\"hidden\" name=\"other_user_id\" value=\"$other_user_id\"/>";
		echo "<input type=\"hidden\" name=\"backto\" value=\"$backto\"/>";
		echo "</form>";
	} elseif ($rs1 != false ) {		
		if ($rs1[0]['status'] == "ACCEPTED") {
			echo "<form action=\"friendship.php\" method=\"post\">";	
			echo "friend";
			echo "<button type=\"submit\" name=\"unfriend\">unfriend</button>";
			echo "<input type=\"hidden\" name=\"other_user_id\" value=\"$other_user_id\"/>";
			echo "<input type=\"hidden\" name=\"backto\" value=\"$backto\"/>";
			echo "</form>";

		} else /* PENDING */ {
			// invitation already sent
			echo "<form action=\"friendship.php\" method=\"post\">";	
			echo "Sent";
			echo "<button type=\"submit\" name=\"update\">update</button>";
			echo "<input type=\"hidden\" name=\"other_user_id\" value=\"$other_user_id\"/>";
			echo "<input type=\"hidden\" name=\"backto\" value=\"$backto\"/>";
			echo "</form>";
		}

	} else /* $rs2 != false */{
		if ($rs2[0]['status'] == "ACCEPTED") {
			echo "<form action=\"friendship.php\" method=\"post\">";	
			echo "friend";
			echo "<button type=\"submit\" name=\"unfriend\">unfriend</button>";
			echo "<input type=\"hidden\" name=\"other_user_id\" value=\"$other_user_id\"/>";
			echo "<input type=\"hidden\" name=\"backto\" value=\"$backto\"/>";
			echo "</form>";

		} else {
			// pending for my reply
			echo "<form action=\"friendship.php\" method=\"post\">";	
			echo "<button type=\"submit\" name=\"accept\">accept</button>";
			echo "<button type=\"submit\" name=\"decline\">decline</button>";
			echo "<input type=\"hidden\" name=\"other_user_id\" value=\"$other_user_id\"/>";
			echo "<input type=\"hidden\" name=\"backto\" value=\"$backto\"/>";
			echo "</form>";
		}
	}
}
?>

<?php
function display_pinboard_tds($pinboard_id, $backto) {
	$user_id = $_SESSION["user_id"];
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select * from pinboard where pinboard_id=$pinboard_id ;");
	$rs = pg_fetch_all($rs);

	if ($rs == false) {
		echo "pinboard $pinboard_id doesn't exist";
		return;
	}
	
	$pinboard_user_id = $rs[0]["user_id"];

	echo "<td>";
	echo "<a href=view_board.php?pinboard_id=$pinboard_id>";
	echo $rs[0]['pinboard_name'];	
	echo "</a>";

	$rs = pg_query($con, "select * from useraccount where user_id=$pinboard_user_id ;");
	$rs = pg_fetch_all($rs);
	$pinboard_user_name = $rs[0]["username"];

	echo "<td>";
	echo "<a href=view_user.php?user_id=$pinboard_user_id>";
	echo $pinboard_user_name;
	echo "</a>";
	echo '<td>';
	if ($rs[0]['friend_comment_only']) {
		echo "friends only";
	} else {
		echo "everyone";
	}
	echo '<td>';
	echo implode(", ", get_pinboard_tags($pinboard_id));
}

function display_edit_tags_input($kept_tags) {
	foreach ($kept_tags as $tag){
		echo "<input type=\"checkbox\" name=\"kept_tags[]\"";
		echo " value=\"$tag\" ";
		echo " checked ";
		echo ">";
		echo $tag;
		echo '</input>';
		echo '   ';
	}	
}


function html_input_hidden_string($name, $value) {
	echo " <input type=\"hidden\"";
	echo " name=\"$name\" " ;
	echo " value=\"$value\" />";
}

function html_input_hidden_int($name, $value) {
	echo " <input type=\"hidden\"";
	echo " name=\"$name\"" ;
	echo " value=$value />";
}

function display_select_pinboards($my_id){
	echo '<select name="pinboard_id">';
	foreach( get_my_pinboards($my_id) as $pinboard_id) {
		$pinboard_name = get_pinboard_attrs($pinboard_id)['pinboard_name'];
		echo "<option value=$pinboard_id>" . $pinboard_name . '</option>';
	}
	echo "</select>";
}



?>
