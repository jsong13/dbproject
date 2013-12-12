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
<td> Social 
<td> <a href="list_boards.php">My Boards </a>

<td> My Pins 
<td> Search
<td> My Streams 
<td> Recommends
<td>
<?php
	$user_id = $_SESSION['user_id'];
	if (empty($user_id)) {
		echo "<a href=\"index.php\">Sign in</a>";
	} else {
		echo "<a href=\"signout.php\">Sign out</a>";
	}
?>
</table>
</div>
<?php } ?>


<?php function display_pin($pin_id) { 
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, 
		"select * from pin inner join picture on pin.picture_id = picture.picture_id
			where pin.pin_id=$pin_id ; ");
	$rs = pg_fetch_all($rs);
	$row=$rs[0];
	$url = $row['url'];
	$picture_id=$row['picture_id'];

	$rs = pg_query($con, 
		"select count(user_id) as c from likepicture 
			where picture_id = $picture_id ;");
	$counts = pg_fetch_all($rs)[0]['c'];
	
	$rs = pg_query($con, 
		"select * from pin inner join useraccount on pin.user_id = useraccount.user_id
			where pin.pin_id=$pin_id ; ");
	$username = pg_fetch_all($rs)[0]["username"];

	$rs = pg_query($con, 
		"select * from pin inner join pinboard on pinboard.pinboard_id = pin.pinboard_id
			where pin.pin_id=$pin_id ; ");
	$pinboard_name = pg_fetch_all($rs)[0]["pinboard_name"];
	$pinboard_id = pg_fetch_all($rs)[0]["pinboard_id"];


?>
	<div align="center" width="50%">
	<table border="1">
	<tr>
	<td align="center" colspan="2">
	<a href="view_pin.php?pin_id=<?php echo $pin_id ;?>">
	<img src=" <?php echo $row['url']; ?>" alt="images/default.png"
		width="400px"
	/></a>
	<td >

	by <?php echo $username ; ?>

	<br>

	from <a href="view_board.php?pinboard_id=<?php echo $pinboard_id;?>"><?php echo $pinboard_name;?></a>


	<form action="repin.php" method="post">
		<input type="submit" value="repin"/>
	</form>

	<form action="delete_pin.php" method="post">
		<input type="submit" value="Delete"/>
		<input type="hidden" name="pin_id" value=<?php echo $pin_id; ?>>
	</form>
	<form action="like_pin.php" method="post">
		<input type="submit" value="Like/Dislike"/>
	</form>
	Likes: <?php echo $counts ;?>
	</table>
	</div>
<?php
} ?>
