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
?>
