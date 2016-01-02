<?php
function get_con() {
	$con = mysql_connect(DBHOST, DBUSER, DBPASS);
	if (!$con) {
	    die('Could not connect: ' . mysql_error());
	}
	mysql_select_db(DBNAME, $con);
	
	mysql_query("SET NAMES {DBCHARSET}");
	
	return $con;
}

function close_con($con) {
	mysql_close($con);
}
