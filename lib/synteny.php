<?php
# http://localhost/mgsv/lib/synteny.php?set1=50000_90000&set2=40000_60000&width=752&height=100&id=Organism_B__SEP__Organism_C

include('database.php');

## Get parameters
$set1 = $_GET['set1'];
$set2 = $_GET['set2'];
$arr_set1 = explode("_", $set1);
$arr_set2 = explode("_", $set2);
$width = $_GET['width'];
$height = $_GET['height'];
$id = mysql_escape_string($_GET['id']);
$arr_id = explode( "__SEP__", $id);
$filter = $_GET['filter'];
$session_id = $_GET['session_id'];
#echo "$filter<br>";

$pixel1 = ($arr_set1[1] - $arr_set1[0]) / $width;
$pixel2 = ($arr_set2[1] - $arr_set2[0]) / $width;
#echo $pixel,"<br>";

$syn_array = array();
$syn_array['pos'] = array();
$syn_array['html'] = array();

$query = "
SELECT * FROM ".$session_id."_synteny WHERE 
(
	(
		( org1_start >= $arr_set1[0] AND org1_end <= $arr_set1[1] )
		OR
		( org1_start >= $arr_set1[0] AND org1_start <= $arr_set1[1] )
		OR
		( org1_end >= $arr_set1[0] AND org1_end <= $arr_set1[1] )
		OR
		( org1_start >= $arr_set1[0] AND org1_end <= $arr_set1[1] )
	)
	OR
	(
		( org2_start >= $arr_set2[0] AND org2_end <= $arr_set2[1] )
		OR
		( org2_start >= $arr_set2[0] AND org2_start <= $arr_set2[1] )
		OR
		( org2_end >= $arr_set2[0] AND org2_end <= $arr_set2[1] )
		OR
		( org2_start >= $arr_set2[0] AND org2_end <= $arr_set2[1] )
	)
)
AND
(
	(org1 like '$arr_id[0]' AND org2 like '$arr_id[1]') OR 
	(org1 like '$arr_id[1]' AND org2 like '$arr_id[0]')
)
";


#echo $query, "<br>";
# SELECT * FROM 304431420120600000016092_synteny WHERE ( org1_start >= 2496 AND org1_end <= 10000 AND org2_start >= 1 AND org2_end <= 98370 ) AND ( (org1 like 'Organism_A' AND org2 like 'Organism_B') OR (org1 like 'Organism_B' AND org2 like 'Organism_A') ) 

if($filter != ''){
	$set = explode("@@@", $filter);
	#echo $set[0],"<br>";
	for($i = 0; $i < sizeof($set); $i++){
		#echo $set[$i],"<br>";
		$query .= " AND ";
		$each = explode("@@", $set[$i]);
		$query .= $each[0] . ' ' . $each[1]. ' ' . $each[2]; 
	}
}
#echo "$query<br>";

$result = mysql_query($query);

while($row = mysql_fetch_assoc($result)){
	$html = '';
	if ($row['org1'] == $arr_id[0]){
		$tl = round(($row['org1_start'] - $arr_set1[0])/$pixel1, 2);
		$tr = round(($row['org1_end'] - $arr_set1[0])/$pixel1, 2);
		$br = round(($row['org2_start'] - $arr_set2[0])/$pixel2, 2);
		$bl = round(($row['org2_end'] - $arr_set2[0])/$pixel2, 2);
	}
	else {
		$tl = round(($row['org2_start'] - $arr_set1[0])/$pixel1, 2);
		$tr = round(($row['org2_end'] - $arr_set1[0])/$pixel1, 2);
		$br = round(($row['org1_start'] - $arr_set2[0])/$pixel2, 2);
		$bl = round(($row['org1_end'] - $arr_set2[0])/$pixel2, 2);
	}
	#echo $row['org1'],' ', $row['org1_start'], ' ', $row['org1_end'], ' ', $row['org2'], ' ', $row['org2_start'], ' ', $row['org2_end'],'<br>';
	#echo $arr_set1[0], '_', $arr_set1[1], '_', $arr_set2[0], '_', $arr_set2[1], '_', $pixel1, '_', $pixel2, '<br>';
	$html .= $row['org1'] .": <br>from ". $row['org1_start']. ' to ' .$row['org1_end'].'<br>';
	$html .= $row['org2'] .": <br>from ". $row['org2_start']. ' to ' .$row['org2_end'];
	$color = getRandomColorHex();
	$set = (string) $tl . "_" . (string) $tr . "_" . (string) $br . "_" . (string) $bl . "_" . $row['SYNcolor'];
	array_push($syn_array['pos'], $set);
	array_push($syn_array['html'], "Coordinates: <br>$html");
}

//array_push($syn_array, "10_50_20_70");

## Return the JSON object
echo json_encode($syn_array);

function getRandomColorHex($max_r = 255, $max_g = 255, $max_b = 255) {
	return sprintf( '#%02X%02X%02X', rand(0,$max_r), rand(0,$max_g), rand(0,$max_b) );
}

?>
