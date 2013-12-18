<html>
<form action="test.php" method="post">
	<input type="checkbox" name="name1" checked>1</input><br>
	<input type="checkbox" name="name2" >2</input><br>
	<input type="checkbox" name="name3" >3</input><br>
	<input type="checkbox" name="name4" >4</input><br>
	<input type="checkbox" name="name5" >5</input><br>
	<input type="checkbox" name="name6" >6</input><br>
	<input type="">
	<input type="submit" value="add"/>
</form>

<?php
foreach( array_keys($_POST) as $item) {
	echo '<br>';
	echo var_dump($item);
}

echo "<br>test tag_id<br>";
require_once('lib_control.php');
echo var_dump(ensure_tag_id("abC "));

?>
</html>
