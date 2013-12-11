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

