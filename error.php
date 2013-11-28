<?php
session_start();
require_once('lib_view.php');

display_header();
display_logo();

$message = $_GET['message'];
$to = $_GET['to'];

echo '<div id="error" align="center">';
echo "<h2>Error:</h2> ";
echo '<h3>'. $message . '</h3>';
echo "<br>";
echo "<a href=$to>try again!</a><br>";
echo '</div>';

display_footer();
?>
