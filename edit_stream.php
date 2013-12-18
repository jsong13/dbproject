<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$stream_id = $_POST['stream_id'];
$backto=$_POST["backto"];
$content=$_POST["content"];

try {
	$db = new DBC();
	$con = $db->con;

	if (! is_this_my_stream($stream_id, $user_id)) 
		throw new Exception("Cannot edit other's stream!");

	if ($content == "tag") {
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


		$rs = pg_query($con, "delete from followtag where stream_id = $stream_id ;");
		pg_fetch_all($rs);

		foreach($new_tags as $p) {
			echo $p . '<br>';
			$tag_id = ensure_tag_id($p);

			$rs = pg_query($con, "insert into followtag 
			(stream_id, tag_id) values ($stream_id, $tag_id);");
			pg_fetch_all($rs);
		}
	} elseif ($content == "pinboard") {
		
		$rs = pg_query($con, "delete from followpinboard where stream_id = $stream_id ;");
		pg_fetch_all($rs);

		foreach ( $_POST['kept_pinboards'] as $pinboard_id) {
			$rs = pg_query($con, "insert into followpinboard 
				(stream_id, pinboard_id) values ( $stream_id, $pinboard_id) ; ");
			pg_fetch_all($rs);
		}
	} elseif ($content=="stream_name") {
		$stream_name = $_POST['stream_name'];
		$stream_name = trim($stream_name);
		if ($stream_name == "") throw new Exception("Stream name can not be empty!");	
		$rs = pg_query($con, "update stream set name='$stream_name' where stream_id = $stream_id;");
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
