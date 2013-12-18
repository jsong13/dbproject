<?php
	session_start();
	require_once('lib_view.php');
	require_once('lib_control.php');

	display_header();
	display_logo();
	display_menu();
	$user_id = $_SESSION["user_id"];

	$content = $_GET['content'];
	$field = $_GET['field'];
	$keyword = $_GET['keyword'];

?>

<div align="center">
<form action="search.php" method="get">
	<fieldset> 
		<legend>Search for pins</legend>
		<input type="text" name="keyword" size=30 value="
<?php if ($content=="pin") echo $keyword;?>
"/>
		in
		<select name="field">
			<option name="tag">tag</option>
		</select>
	
		<input type="submit" value="Search" size=30 />
		<input type="hidden" name="content" value="pin" size=30 />
	</fieldset>
</form> 
<form action="search.php" method="get">
	<fieldset> 
		<legend>Search for boards</legend>
		<input type="text" name="keyword" size=30 value="
<?php if ($content=="board") echo $keyword;?>
"/>
		in
		<select name="field">
			<option name="tag">tag</option>
			<option name="name">name</option>
		</select>
		<input type="submit" value="Search" size=30 />
		<input type="hidden" name="content" value="board" size=30 />
	</fieldset>
</form> 
</div>


<?php
try {
	$db = new DBC();
	$con = $db->con;
	
	if ($keyword=="") {
		$keyword='';
	} else {
		$keyword = '%'. $keyword . '%';
	}

	$query_pin_tag = "select pin.pin_id as ret_id from pin, pin_has_tag as pt, tag 
			where pin.pin_id = pt.pin_id and pt.tag_id = tag.tag_id and
			tag.tag_name like '$keyword' ;";

	$query_board_tag = "select pb.pinboard_id as ret_id from pinboard as pb, board_has_tag as bt, tag 
			where pb.pinboard_id = bt.pinboard_id and bt.tag_id = tag.tag_id and
			tag.tag_name like '$keyword' ;";

	$query_board_name = "select pb.pinboard_id as ret_id from pinboard as pb 
		where pb.pinboard_name like '$keyword' ;";

	$query = "";
	if ($content=='pin') {
		$query = $query_pin_tag;
	} elseif ($content=="board") {
		if ($field == 'tag') {
			$query = $query_board_tag;
		} elseif ($field == "name") {
			$query = $query_board_name;
		}
	}

	$ret = array();
	if ($query!="") {
		$rs = pg_query($con, $query);	
		while ($row = pg_fetch_assoc($rs)) {
			array_push($ret, $row["ret_id"]);
		}
	}

	echo '<hr>';
	echo '<div align="center">';
	echo '<h2>' . count($ret). ' results </h2>';
	if ($content=='pin') {
		foreach($ret as $pin_id) {
			display_pin($pin_id, 'search.php');
		}	
	} elseif ($content == 'board') {
		echo '<table border=1>';
		echo '<tr><th>name<th>user<th>comment<th>tags';
		foreach($ret as $pinboard_id) {
			echo '<tr>';
			display_pinboard_tds($pinboard_id, 'search.php');
		}
		echo '</table>';
	}
	echo '</div>';

} catch (Exception $e) {
	$eurl = "error.php?message=".urlencode($e->getMessage());
	$eurl .= "&to=".urlencode("search.php");
	header("Location: $eurl");
}

	display_footer();
?>
