<?php

// use class destructor to make sure that database connection is always closed
class DBC {
	public $con = NULL;
	function __construct() {
		$dbhost="localhost";
	    $dbuser="jlsong";
	    $dbpass="jlsong";
	    $dbport="5432";
	    $dbname="dbproject";

		$this->con = pg_connect("host=$dbhost port=$dbport dbname=$dbname user=$dbuser password=$dbpass");

		if ($this->con === false) {
			unset($this->con);
			throw new Exception("Cannot connect to database.<br>");
		}

		// user dbo schema
		pg_query($this->con, "set search_path to 'dbo';");

		//echo "Debug: connected to database.<br>";
	}

	function __destruct(){
		if (isset($this->con)) {
			pg_close($this->con);
		}
		//echo "Debug: disconnected from database<br>";
	}
}

?>
