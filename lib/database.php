<?php
include('settings.php');

$link = mysql_connect($database_host, $database_user, $database_pass);
if($link == FALSE){
	echo 'Cannot connect to database';
}

@mysql_select_db($database_name) or die("Unable to connect to database");

function execute_sql($sql){
	$result = mysql_query($sql);
	return $result;
}

?>