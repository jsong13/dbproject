<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$pin_id = $_POST['pin_id'];
$backto=$_POST["backto"];

try {
	$db = new DBC();
	$con = $db->con;

	
	$rs = pg_query($con, "select * from pin where pin_id=$pin_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) throw new Exception("Pin doesn't exist");
	
	if (! is_this_my_pin($pin_id, $user_id)) throw new Exception("Only owner can edit!");


	$new_tags = array();
	foreach ( $_POST['kept_tags'] as $tag_name) {
		array_push($new_tags, $tag_name);
	}

	$pieces = explode("," , $_POST["added_tags"]);
	foreach ($pieces as $p) {
		$p1 = strtolower(trim($p));
		if (!empty($p1)) {
			array_push($new_tags, $p1);
		}
	}


	$rs = pg_query($con, "delete from pin_has_tag where pin_id = $pin_id ;");
	pg_fetch_all($rs);

	foreach($new_tags as $p) {
		echo $p . '<br>';
		$tag_id = ensure_tag_id($p);

		$rs = pg_query($con, "insert into pin_has_tag 
			(pin_id, tag_id) values ($pin_id, $tag_id);");
		pg_fetch_all($rs);
	}

	header("Location: $backto");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode($backto);
	header("Location: $eurl");
}
exit();

?>
