<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];


?>


<?php
try {
	$db = new DBC();
	$con = $db->con;
	
	// get the top 10 across the site
	$rs1 = pg_query($con, "select pin.picture_id as picture_id, count(*) as nlikes  
		from likepicture join pin on pin.picture_id = likepicture.picture_id 
		group by pin.picture_id order by nlikes desc; ");

	$picture_array = array();
	while ($row = pg_fetch_assoc($rs1)) {
		array_push($picture_array, $row['picture_id']);
	}

	$pin_array = array();
	foreach( $picture_array as $picture_id) {
		$rs = pg_query($con, "select * from pin where picture_id=$picture_id order by time ;");
		$rs = pg_fetch_all($rs);
		if ($rs == false) continue;
		array_push($pin_array, $rs[0]['pin_id']);
	}

	$pin_array2 = array();
	$rs = pg_query($con, "select pin_id from comments where user_id = $user_id order by time desc; "); 
	while($row = pg_fetch_assoc($rs)) {
		if (!is_this_my_pin($row['pin_id'], $user_id))
			array_push($pin_array2, $row['pin_id']);
	}
	
	foreach($pin_array as $pin_id) {
		if (!is_this_my_pin($pin_id, $user_id))
			array_push($pin_array2, $pin_id);
	}
?>
<div align='center'>
<table>
<tr>
<th align="center"> Other people like:
<th align="center"> You might be instereted in:
<tr>
<td>
<?php 
	foreach($pin_array as $pin_id) display_pin($pin_id, "recommend.php");
?>
<td valign="top">
<?php 
	foreach($pin_array2 as $pin_id) display_pin($pin_id, "recommend.php");
?>

</table>
</div>





<?php
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("search.php");
	header("Location: $eurl");
}

	display_footer();
?>
