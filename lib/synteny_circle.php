<?php
# http://localhost/mgsv/lib/synteny.php?set1=50000_90000&set2=40000_60000&width=752&height=100&id=Organism_B__SEP__Organism_C

include('database.php');

## Get parameters
$set1 = $_GET['set1'];
$set2 = $_GET['set2'];
$set1_count = $_GET['set1_count'];
$set2_count = $_GET['set2_count'];
$session_id = $_GET['session_id'];
#echo $set1,"<br>";
#echo $set2,"<br>";
#echo $set1_count,"<br>";
#echo $set2_count,"<br>";

$syn_array = array();

$query = "
SELECT * FROM ".$session_id."_synteny WHERE 
(org1 like '$set1' AND org2 like '$set2') OR 
(org1 like '$set2' AND org2 like '$set1')
";

#echo "$query<br>";

$result = mysql_query($query);

while($row = mysql_fetch_assoc($result)){
	if ($row['org1'] == $set1){
		$sl = $row['org1_start'] + $set1_count;
		$sr = $row['org1_end'] + $set1_count;
		$el = $row['org2_end'] + $set2_count;
		$er = $row['org2_start'] + $set2_count;
	}
	else {
		$sl = $row['org2_start'] + $set1_count;
		$sr = $row['org2_end'] + $set1_count;
		$el = $row['org1_end'] + $set2_count;
		$er = $row['org1_start'] + $set2_count;	
	}
	$set = (string) $sl . "_" . (string) $sr . "_" . (string) $er . "_" . (string) $el;
	array_push($syn_array, $set);
}

//array_push($syn_array, "10000_30000_250000_270000");

## Return the JSON object
echo json_encode($syn_array);

?>
