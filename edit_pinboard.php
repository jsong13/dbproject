<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$pinboard_id = $_POST['pinboard_id'];
$backto=$_POST["backto"];

try {
	$db = new DBC();
	$con = $db->con;

	// exist
	$rs = pg_query($con, "select * from pinboard where pinboard_id=$pinboard_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false ) throw new Exception("pinboard doesn't exist!");

	// owner check
	if (! is_this_my_pinboard($pinboard_id, $user_id)) {
		throw new Exception("Only owner can edit the board");
	}
		

	// empty  name
	$new_pinboard_name = trim($_POST["new_pinboard_name"]);
	if ($new_pinboard_name == "") {
		throw new Exception("board name can not be empty");
	}

	$rs = pg_query($con, "update pinboard set pinboard_name='$new_pinboard_name' 
		where pinboard_id = $pinboard_id ;");
	$rs = pg_fetch_all($rs);


	// get the friend_comment_only
	if ( $_POST["friend_comment_only"] != "" ) {
		$new_friend_comment_only = "t";	
	} else {
		$new_friend_comment_only = "f";	
	}

	$rs = pg_query($con, "update pinboard set friend_comment_only='$new_friend_comment_only' 
	   				where pinboard_id = $pinboard_id; 	");
	$rs = pg_fetch_all($rs);


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


	$rs = pg_query($con, "delete from board_has_tag where pinboard_id = $pinboard_id ;");
	pg_fetch_all($rs);

	foreach($new_tags as $p) {
		echo $p . '<br>';
		$tag_id = ensure_tag_id($p);

		$rs = pg_query($con, "insert into board_has_tag 
			(pinboard_id, tag_id) values ($pinboard_id, $tag_id);");
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
