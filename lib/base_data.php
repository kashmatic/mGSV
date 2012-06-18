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
else if($data == 'sorder'){
	$query = "select distinct org1,org2 from ".$session_id."_synteny union select distinct org1,org2 from ".$session_id."_synteny ";
	//echo $query,"<br>";
	$result = mysql_query($query);
	$default = array();
	$assarr = array();
	while($row = mysql_fetch_assoc($result)){
		if( ! in_array($row['org1'], $default)){
			array_push($default, $row['org1']);
			$assarr[sizeof($assarr)] = array();
		}
		if( ! in_array($row['org2'], $default)){
			array_push($default, $row['org2']);
			$assarr[sizeof($assarr)] = array();
		}
		$len_query = "select sum(org1_end) - sum(org1_start) + sum(org2_end) - sum(org1_start) as sum from ".$session_id."_synteny where (org1 like '".$row['org1']."' and org2 like '".$row['org2']."') OR (org1 like '".$row['org2']."' and org2 like '".$row['org1']."')";
		$q_result = mysql_query($len_query);
		$q_row = mysql_fetch_assoc($q_result);
		$assarr[array_search($row['org1'], $default)][array_search($row['org2'], $default)] = $q_row['sum'];
		$assarr[array_search($row['org2'], $default)][array_search($row['org1'], $default)] = $q_row['sum'];
		#echo array_search($row['org1'], $default) . "][ ". array_search($row['org2'], $default) . '<br>';
		$assarr[array_search($row['org1'], $default)][array_search($row['org1'], $default)] = 0;		
		#echo array_search($row['org1'], $default) . "][ ". array_search($row['org1'], $default) . '<br>';
		$assarr[array_search($row['org2'], $default)][array_search($row['org2'], $default)] = 0;
		#echo array_search($row['org2'], $default) . "][ ". array_search($row['org2'], $default) . '<br>';
	}
	
	//display_matrix($assarr);
	
	$a = Insertion($assarr);
	$sugg = array();
	//echo sizeof($a),'<br>';
	foreach($a as $b){
		//echo $default[$b],"<br>";
		array_push($sugg, $default[$b]);
	}
	$array = join('__ORDER__', $sugg);
}

function display_matrix($mat){
	foreach ($mat as $key => $value) {
		//echo "$key => ";
		foreach($value as $k => $v){
			//echo "($k => $v) ";
			echo "($k) ";
		}
		echo "<br>";
	}
}

function Insertion($graph){
	$v = array();
	$v = sortVertices($graph);
	$r = array();
	for($x = 0; $x < sizeof($v); $x++){
		$min = 2147483647;
		$i = -1;
		for($y = 0; $y < sizeof($r)+1; $y++){
			$ol = findOverlaps($graph, $v[$x], $r, $y);
			if($ol < $min){
				$min = $ol;
				$i = $y;
			}
		}
		$r = array_insert($r, $i, $v[$x]);
	}
	return $r;
}

function array_insert($array, $i, $var){
	$temp1 = -1;
	$temp2 = $var;
	for($x = $i; $x < sizeof($array); $x++){
		$temp1 = $array[$x];
		$array[$x] = $temp2;
		$temp2 = $temp1;
	}
	array_push($array, $temp2);
	return $array;
}

function sortVertices($graph){
	$r = array();
	for($x = 0; $x < sizeof($graph); $x++){
		$r[$x] = numEdges($graph[$x]);
	}
	asort($r);
	return array_keys(array_reverse($r));
}

function findOverlaps($graph, $v, $path, $i){
	$r = 0;
	$lead = array();
	$tail = array();
	$lead = array_slice($path, 0, $i);
	$tail = array_slice($path, $i);
	for($x = 0; $x < sizeof($lead); $x++){
		for($y = 0; $y < sizeof($tail); $y++){
				$r += $graph[$lead[$x]][$tail[$y]] * (sizeof($lead)-$x + $y);
		}
	}
	for($x = 0; $x < sizeof($lead)-1; $x++){
		$r += $graph[$v][$lead[$x]] * ($i-$x-1);
	}
	for($x = 1; $x < sizeof($tail); $x++){
			$r += $graph[$v][$tail[$x]] * $x;
	}
	return $r;
}

function numEdges($v){
	$r = 0;
	foreach($v as $x){
		if($x != 0){
			$r++;
		}
	}
	return $r;
}
 
## Return the JSON object
#echo $array,"<br";
echo json_encode($array);

?>
