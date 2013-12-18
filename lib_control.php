<?php
session_start();
class DBC {
	public $con;
	function __construct(){
		$dbuser = 'jlsong';
		$dbpassword = $dbuser;
		$this->con = pg_connect("host=localhost port=5432 dbname=dbproject user=$dbuser password=$dbpassword");
		if ($this->con === false) {
			throw new Exception("cannot connect to database");
		}
		$rs = pg_query($this->con, "set search_path to 'dbo';");
	}
	
	function __destruct(){
		if ( !isset($this->con) ) {
			pg_close($this->con);
		}
	}
}

function is_this_my_stream($stream_id, $my_id){
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select * from stream where 
		user_id = $my_id and stream_id =$stream_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return false;
	else return true;
}

function is_this_my_picture($picture_id, $my_id){
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select * from picture where 
		user_id = $my_id and picture_id =$picture_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return false;
	else return true;
}

function is_this_my_pin($pin_id, $my_id){
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select * from pin where pin_id = $pin_id and user_id = $my_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return false;
	else return true;
}

function is_this_my_pinboard($pinboard_id, $my_id){
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select * from pinboard where 
		pinboard_id =$pinboard_id and 
		user_id = $my_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return false;
	else return true;

}

function are_we_friends($user1_id, $user2_id){
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, "select * from friendship where
		((user1_id=$user1_id and user2_id=$user2_id) or 
		 (user2_id=$user2_id and user1_id=$user2_id)) and 
		status = 'ACCEPTED' ;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return false;
	else return true;
}

function get_pinboard_attrs($pinboard_id){
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, "select * from pinboard where pinboard_id=$pinboard_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return array();
	return $rs[0];
}

function get_pin_attrs($pin_id){
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, "select * from pin where pin_id = $pin_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return array();
	return $rs[0];
}


function get_pinboard_tags($pinboard_id){
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select tag.tag_name as tag_name
		from board_has_tag join tag 
		on board_has_tag.tag_id = tag.tag_id
		where board_has_tag.pinboard_id = $pinboard_id;" );
	$ret = array();
	while ($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['tag_name']);
	}
	return $ret;
}

function get_pin_tags($pin_id){
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select tag.tag_name as tag_name
		from pin_has_tag join tag 
		on pin_has_tag.tag_id = tag.tag_id
		where pin_has_tag.pin_id = $pin_id;" );
	$ret = array();
	while ($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['tag_name']);
	}
	return $ret;
}

function ensure_tag_id($tag_name){
	$dbc = new DBC();
	$con = $dbc->con;

	$tag_name = trim(strtolower($tag_name));
	if ($tag_name == "") 
		return null;

	$rs = pg_query($con, "select tag_id from tag where tag_name='$tag_name' ;");
	$rs = pg_fetch_all($rs);
	if ($rs==false) {
		echo " not found ";
		$rs = pg_query($con, "insert into tag (tag_name) values ('$tag_name') ;");
		$rs = pg_fetch_all($rs);
	}
	echo " touched here ";

	$rs = pg_query($con, "select tag_id from tag where tag_name='$tag_name' ;");
	$rs = pg_fetch_all($rs);
	return $rs[0]["tag_id"];
}

function get_my_pinboards($my_id){
	$ret = array();
	
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select pinboard_id from pinboard where user_id=$my_id;");
	while ($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['pinboard_id']);
	}
	return $ret;
}

function get_my_streams($my_id){
	$ret = array();
	
	$dbc = new DBC();
	$con = $dbc->con;

	$rs = pg_query($con, "select stream_id from stream where user_id=$my_id;");
	while ($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['stream_id']);
	}
	return $ret;
}

function get_stream_attrs($stream_id) {
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, "select * from stream where stream_id=$stream_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return array();
	return $rs[0];
}

function get_user_attrs($my_id) {
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, "select * from useraccount where user_id=$my_id;");
	$rs = pg_fetch_all($rs);
	if ($rs == false) return array();
	return $rs[0];
}



function get_pins_in_stream($stream_id){
	$dbc = new DBC();
	$con = $dbc->con;
	$rs = pg_query($con, "
	select pin_id from (
		(
			select pt.pin_id as pin_id, pt.time as time
			from followtag as ft, pin_has_tag as pt 
			where ft.tag_id  = pt.tag_id and ft.stream_id = $stream_id
		) 
		union (
			select pin.pin_id as pin_id, pin.time as time
			from followpinboard fb, pin 
			where pin.pinboard_id = fb.pinboard_id and fb.stream_id = $stream_id
		)
	) as pin_stream order by time desc; ");
	$ret = array();

	while ($row=pg_fetch_assoc($rs)){
		array_push($ret, $row['pin_id']);
	}
	return $ret;
}

function get_following_tags($stream_id) {
	$dbc = new DBC();
	$con = $dbc->con;	
	$rs = pg_query($con, "select tag.tag_name as tag_name from tag, followtag where
		tag.tag_id = followtag.tag_id and followtag.stream_id = $stream_id; ");
	$ret = array();
	while($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['tag_name']);
	}
	return $ret;
}

function get_following_pinboards($stream_id) {
	$dbc = new DBC();
	$con = $dbc->con;	
	$rs = pg_query($con, "select   pinboard_id 
		from followpinboard 
		where stream_id = $stream_id; ");
	$ret = array();
	while($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['pinboard_id']);
	}
	return $ret;
}

function get_pinboard_headline($pinboard_id) {
	$dbc = new DBC();
	$con = $dbc->con;	
	$rs = pg_query($con, "select * from  pinboard where pinboard_id = $pinboard_id;");
	$rs = pg_fetch_all($rs);
	$pinboard_name = $rs[0]['pinboard_name'];
	$pinboard_user_id = $rs[0]['user_id'];
	$rs = pg_query($con, "select * from  useraccount where user_id = $pinboard_user_id;");
	$rs = pg_fetch_all($rs);
	$username = $rs[0]['username'];

	$ret = "<b>$pinboard_name</b> by <i>$username</i>";
	return $ret;
}



function get_all_pinboards() {
	$dbc = new DBC();
	$con = $dbc->con;	
	$rs = pg_query($con, "select pinboard_id from pinboard; ");
	$ret = array();
	while($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['pinboard_id']);
	}
	return $ret;
}


function get_all_active_tags(){
	$dbc = new DBC();
	$con = $dbc->con;	
	$rs = pg_query($con, "select distinct tag.tag_name as tag_name from pin_has_tag as pt , tag where tag.tag_id = pt.tag_id ; ");
	$ret = array();
	while($row = pg_fetch_assoc($rs)) {
		array_push($ret, $row['tag_name']);
	}
	return $ret;
}

?>
