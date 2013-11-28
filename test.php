<?php
	echo '<br>';
	echo 'KO';
	echo http_build_url('1.php');

	unset($a);
	$a=100;
	echo '|'.$a.'|' . '<br>';
	echo 'isset:|';
	echo True==isset($a);
	echo '|<br>';
	echo "empty?|";
	echo empty($a);
	echo "|<br>";
	echo False;
?>
