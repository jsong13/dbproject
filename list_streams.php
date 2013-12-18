<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];

	echo '<div align="center">';
	echo '<h1> My Streams </h1>';
	echo '<table border=1>';
	echo '<tr><th>Stream name<th>time<th>tags<th>pinboards<th>';
	foreach(get_my_streams($user_id) as $sid) {
		$row = get_stream_attrs($sid);
		echo "<tr>";
		echo "<td> <a href=\"view_stream.php?stream_id=$sid \">" . $row['name'] . "</a>";

		echo "<td>" . $row['time'];
		
		echo "<td>" ;
		echo implode(", ", get_following_tags($sid));

		echo "<td>" ;
		echo implode(", ", get_following_pinboards($sid));

		echo "<td>";
		echo '<form method="post" action="delete_stream.php">';
		echo '<input type="submit" value="delete">';
		html_input_hidden_int("stream_id", $sid);
		html_input_hidden_string("backto", "list_streams.php");
		echo '</form>';	
	} 
	echo "</table>";
	echo "</div>";

	dispaly_footer();
?>
