<?php
# http://localhost/Maize/annotation.php?start=50000&end=260000&genome_width=813

include('settings.php');
include('database.php');

$session_id = $_GET['session_id'];

$query = "
SELECT DISTINCT(org) FROM 
(
SELECT DISTINCT(org1) AS org FROM ".$session_id."_synteny 
UNION
SELECT DISTINCT(org2) AS org FROM ".$session_id."_synteny
) 
AS tb1
";

$result = mysql_query($query);

$gene = array();
$i = 0;
while($row = mysql_fetch_assoc($result)){
	$gene[$i] = array();
	$gene[$i]['id'] = $row['org'];
	$maxv = maxValue($row['org'], $session_id);
	$minv = minValue($row['org'], $session_id);
	$gene[$i]['range'] = $minv.'_'.$maxv;
	$gene[$i]['pos_start'] = '';
	$gene[$i]['pos_end'] = '';
	$i += 1;
}

## Return the JSON object
echo json_encode($gene);

function maxValue($orgName, $session_id) {
	$query = "
	SELECT MAX(m) AS mm FROM 
	(
	SELECT MAX(org1_start) AS m FROM ".$session_id."_synteny WHERE org1 LIKE '$orgName' 
	UNION
	SELECT MAX(org2_start) AS m FROM ".$session_id."_synteny WHERE org2 LIKE '$orgName'
	UNION
	SELECT MAX(org1_end) AS m FROM ".$session_id."_synteny WHERE org1 LIKE '$orgName' 
	UNION
	SELECT MAX(org2_end) AS m FROM ".$session_id."_synteny WHERE org2 LIKE '$orgName'
	)
	AS tb1";
	
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}

function minValue($orgName, $session_id) {
	$query = "
	SELECT MIN(m) AS mm FROM 
	(
	SELECT MIN(org1_start) AS m FROM ".$session_id."_synteny WHERE org1 LIKE '$orgName' 
	UNION
	SELECT MIN(org2_start) AS m FROM ".$session_id."_synteny WHERE org2 LIKE '$orgName'
	UNION
	SELECT MIN(org1_end) AS m FROM ".$session_id."_synteny WHERE org1 LIKE '$orgName' 
	UNION
	SELECT MIN(org2_end) AS m FROM ".$session_id."_synteny WHERE org2 LIKE '$orgName'
	)
	AS tb1";
	
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	return $row[0];
}

?>
