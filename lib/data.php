<?php
include('database.php');

## Get parameters
$org = $_GET['org'];
$data = $_GET['data'];
$session_id = $_GET['session_id'];

$array = array();

if($data == 'annotation') {
	$query = "SELECT track_name FROM ".$session_id."_annotation WHERE org_id like '$org' GROUP BY track_name ";
	$result = mysql_query($query);
	
	if($result != ''){
		while($row = mysql_fetch_assoc($result)){
			array_push($array, $row['track_name']);
		}
	}
	//array_push($syn_array, "10_50_20_70");
} 
else if ($data == 'synteny') {
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
else if ($data == 'size'){
	$query = "select distinct org1,org2 from ".$session_id."_synteny union select distinct org1,org2 from ".$session_id."_synteny ";
	//echo $query,"<br>";
	$result = mysql_query($query);
	while($row = mysql_fetch_assoc($result)){
		#echo $row['org1'], "<br>";
		$q = "select max(output) as max from (select max(greatest(org1_start, org1_end)) as output from ".$session_id."_synteny where org1 like '" . $row['org1'] . "' union select max(greatest(org2_start, org2_end)) as output from ".$session_id."_synteny where org2 like '" . $row['org1'] . "') as t1";
		#echo $q, "<br>";
		$res = 	mysql_query($q);
		if (!$res) {
    	die('Could not query:' . mysql_error());
		}
		//echo $row['org1'],"<br>";
		//echo $row['org2'],"<br>";
		if (! isset($array[$row['org1']])){
			//echo "add<br>";
			$array[$row['org1']] = mysql_result($res,0);
		}
		$q = "select max(output) as max from (select max(greatest(org1_start, org1_end)) as output from ".$session_id."_synteny where org1 like '" . $row['org2'] . "' union select max(greatest(org2_start, org2_end)) as output from ".$session_id."_synteny where org2 like '" . $row['org2'] . "') as t1";
		#echo $q, "<br>";
		$res = 	mysql_query($q);
		if (!$res) {
    	die('Could not query:' . mysql_error());
		}
		if (! isset($array[$row['org2']])){
			//echo "add<br>";
			$array[$row['org2']] = mysql_result($res,0);
		}		
	}
}
else if ($data == 'order'){
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
		$assarr[array_search($row['org1'], $default)][array_search($row['org2'], $default)] = 1;
		$assarr[array_search($row['org2'], $default)][array_search($row['org1'], $default)] = 1;
		#echo array_search($row['org1'], $default) . "][ ". array_search($row['org2'], $default) . '<br>';
		$assarr[array_search($row['org1'], $default)][array_search($row['org1'], $default)] = 0;		
		#echo array_search($row['org1'], $default) . "][ ". array_search($row['org1'], $default) . '<br>';
		$assarr[array_search($row['org2'], $default)][array_search($row['org2'], $default)] = 0;
		#echo array_search($row['org2'], $default) . "][ ". array_search($row['org2'], $default) . '<br>';
	}
	$random = $default;
	shuffle($random);
	$diff = array_diff_assoc($default,$random);
	while( sizeof($diff) == 0){
		shuffle($random);
		$diff = array_diff_assoc($default,$random);
	}
	$array['def'] = join('__ORDER__', $default);
	$array['ran'] = join('__ORDER__', $random);
	
	
	foreach($default as $a){
		//echo "$a<br>";
	}
	//echo "---<br>";
	foreach($random as $a){
		//echo "$a<br>";
	}
	//echo "---<br>";
	$a = FindOrder($assarr);
	$sugg = array();
	//echo sizeof($a),'<br>';
	foreach($a as $b){
		//echo $default[$b],"<br>";
		array_push($sugg, $default[$b]);
	}
	$array['sug'] = join('__ORDER__', $sugg);
}

function FindOrder($graph){
	$r = array();
	$last = -1;
	while(!isEmpty($graph)){
		$start = leastEdges($graph);
		$path = longestPath($graph, $start, 0);
		$path = completeCycle($graph, $path);
		if($path[0] != $last){
			array_push($r, $path[0]);
		}
		for($x = 1; $x < sizeof($path); $x++){
			array_push($r, $path[$x]);
		}
		$last = $path[sizeof($path)-1];
		$graph = removePath($graph, $path);
	}
	return $r;
}

function longestPath($graph, $cur, $len){
	$path = array();
	array_push($path, $cur);
	$longestSubpath = array();
	for($x = 0; $x < sizeof($graph); $x++){
		if($graph[$cur][$x] != 0){
			$subpath = longestPath(removeVertex($graph, $cur), $x, $len + $graph[$cur][$x]);
			if(sizeof($subpath) > sizeof($longestSubpath)){
				$longestSubpath = $subpath;
			}
		}
	}
	foreach($longestSubpath as $x){
		array_push($path, $x);
	}
	return $path;
}
		
function completeCycle($graph, $path){
	$graph = removePath($graph, $path);
	$last = $path[sizeof($path)-1];
	for($x = 0; $x < sizeof($graph); $x++){
		if($graph[$last][$x] != 0){
			array_push($path, $x);
			return $path;
		}
	}
	return $path;
}
		
function removePath($graph, $path){
	for($x = 0; $x < sizeof($path) - 1; $x++){
		$arr = $path[$x]; //***forgot the $ in front of path***
		$b = $path[$x+1]; //***forgot the $ in front of path***
		$graph[$arr][$b] = 0;
		$graph[$b][$arr] = 0;
	}
	return $graph;
}
		
function removeVertex($graph, $vtx){
	if($vtx < 0 || $vtx >= sizeof($graph)){
		return copy($graph);
	}
	for($x = 0; $x < sizeof($graph); $x++){
		$graph[$x][$vtx] = 0;
		$graph[$vtx][$x] = 0;
	}
	return $graph;
}
		
function numEdges($vtx){
	$r = 0;
	foreach($vtx as $x){
		if($x != 0){
			$r++;
		}
	}
	return $r;
}

function leastEdges($graph){
	$r = -1;
	$min = 2147483647;
	for($x = 0; $x < sizeof($graph); $x++){
		$e = numEdges($graph[$x]);
		if($e != 0 && $e < $min){
			$r = $x;
			$min = $e;
		}
	}
	return $r;
}
		
function isEmpty($arr){
	$r = true;
	foreach($arr as $x){
		$r = $r && numEdges($x) == 0;
	}
	return $r;
}

## Return the JSON object
echo json_encode($array);
?>
