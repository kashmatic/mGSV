<?php
include('database.php');

## Get parameters
$data = $_GET['data'];
$session_id = $_GET['session_id'];

$array = array();

if($data == 'synteny') {
	$query = "SELECT distinct org1, org2 FROM ".$session_id."_synteny";
	$result = mysql_query($query);
	
	while($row = mysql_fetch_assoc($result)){
		if(! in_array($row['org2'] . "_" . $row['org1'], $array)){
			array_push($array, $row['org2'] . "__SEP__" . $row['org1']);
		}
	}
}
else if ($data == 'fields') {
	$query = "DESC ".$session_id."_synteny";
	$result = mysql_query($query);
	while($row = mysql_fetch_assoc($result)){
		if($row['Field'] == 'id'){ continue; }
		if($row['Field'] == 'blocks'){ continue; }
		if($row['Field'] == 'SYNcolor'){ continue; }
		if(! preg_match("/^org[12]+[_]?[start|end]?/", $row['Field'])){
			array_push($array, $row['Field']);
		}
	}
} 
## Return the JSON object
echo json_encode($array);

?>