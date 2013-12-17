<?php
require_once('lib_control.php');
session_start();
$user_id = $_SESSION["user_id"];
$other_user_id = $_POST["other_user_id"];
$backto = $_POST["backto"];

try {
	$db = new DBC();
	$con = $db->con;

	// verfiy there is one record for each pair
	$rs = pg_query($con, "select * from friendship where 
		( user1_id = $user_id and user2_id = $other_user_id );");
	$rs1 = pg_fetch_all($rs);
	
	$rs = pg_query($con, "select * from friendship where 
		( user2_id = $user_id and user1_id = $other_user_id );");
	$rs2 = pg_fetch_all($rs);

	// delete the new one
	if ($rs1 != false and $rs2 != false) {
		if ($rs1[0]['time'] > $rs2[0]['time']) {
			$rs = pg_query($con, "delete from friendship where 
				( user1_id = $user_id and user2_id = $other_user_id );");
			$rs = pg_fetch_all($rs);
		} else {
			$rs = pg_query($con, "delete from friendship where 
				( user2_id = $user_id and user1_id = $other_user_id );");
			$rs = pg_fetch_all($rs);
		}
	}

	// handle
	if (isset($_POST["send"])) {
		$rs = pg_query($con, "insert into friendship (user1_id, user2_id, status) values 
			($user_id, $other_user_id, 'PENDING');");
		pg_fetch_all($rs);
	} elseif (isset($_POST["accept"])) {
		$rs = pg_query($con, "update friendship set status='ACCEPTED' where
			user1_id = $other_user_id and user2_id = $user_id ;");
		pg_fetch_all($rs);
	} elseif (isset($_POST["decline"])) {
		$rs = pg_query($con, "delete from friendship where
			(user1_id = $other_user_id and user2_id = $user_id) or
			(user2_id = $other_user_id and user1_id = $user_id) ;");
		$rs = pg_query($con, "delete from friendship where
			user1_id = $other_user_id and user2_id = $user_id ;");
		pg_fetch_all($rs);
	} elseif (isset($_POST["unfriend"])) {
		$rs = pg_query($con, "delete from friendship where
			(user1_id = $other_user_id and user2_id = $user_id) or
			(user2_id = $other_user_id and user1_id = $user_id) ;");
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
	} elseif (isset($_POST["unfriend"])) {
		echo "friend";
	}
	

	exit();
	header("Location: $backto");
} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("view_board.php?pinboard_id=$pinboard_id");
	header("Location: $eurl");
}
exit();

?>
