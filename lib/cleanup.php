#!/usr/bin/php
<?= 
require_once('database.php');
$time_now = strtotime(date("Y-m-d"));
##################################
/*After 60 days, it will delete the tables.*/
$day = 60;
##################################

$time_diff = $day * 24 * 60 * 60;
$sql = "select TABLE_NAME, CREATE_TIME from information_schema.TABLES";
$result = execute_sql($sql);

$history_record_sql = "select * from userinfo";
$history_record_result = execute_sql($history_record_sql);

while($row = mysql_fetch_assoc($history_record_result)) {
	$time_created = strtotime($row['create_on']);
	$diff = $time_now - $time_created;
	if ($diff > $time_diff) {
		$nsql = "delete from userinfo where id = " . $row['id'];
		execute_sql($nsql) or die ("Unable to delete the entry: " . mysql_error());
	}
}

while ($row = mysql_fetch_assoc($result)) {
	$time = explode(" ",$row['CREATE_TIME']);
	$time_create = strtotime($time[0]);
	$diff = $time_now - $time_create;
	if ($diff > $time_diff) {
		$patterns = '/_synteny/';
		$patterna = '/_annotation$/';
		if(preg_match($patterns, $row['TABLE_NAME']) || preg_match($patterna, $row['TABLE_NAME'])){
			$drop = "drop table " . $row['TABLE_NAME'];
			execute_sql($drop) or die("Unable to drop the table: " . mysql_error());
		}
	}
} 

?>
