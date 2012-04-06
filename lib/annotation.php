<?php
# http://localhost/Maize/annotation.php?start=50000&end=260000&genome_width=813

## Get parameters
$start = $_GET['start'];
$end = $_GET['end'];
$org = $_GET['org'];
$height = $_GET['height'];
$ann = $_GET['ann'];
$pixel = ($end - $start) / $_GET['width'];
$session_id = $_GET['session_id'];

## Set other parameters
$yaxis = 40; ## y-axis of the gene
$gap = 30;	## gap between rows of genes
$base = $height - 40;

## Include the other functions
include('database.php');

$query = "
SELECT * FROM ".$session_id."_annotation WHERE 
start >= $start AND 
end <= $end AND 
org_id like '$org' AND
track_name like '$ann' 
";
#echo "$query<br>";

$result = mysql_query($query);

## Initialize the array
$gene = array(); 
## Array to hold the lines of genes in each level
$pos_arr[0] = array();
$i = 0;

if($result != ''){
	while($row = mysql_fetch_assoc($result)){
	  ## Calculate the x value to start
	  $x1 = round(($row['start'] - (float) $start)/$pixel, 1);
	  ## Calculate the x value to end
	  $x2 = round(($row['end'] - (float) $start)/$pixel, 1);
	  ## Get the direction
	  $d = $row['strand'];
		
	  /** For a crowded image 
	   * if(($x2 - $x1) < 1)
	   * - save the pixel into a hash
	   * - in the end send it as a box coordinates
	   * - this reduces the load a lot
	   * */
		
		## If the x1 and x2 points are not negative
		if(($ann != 'expression')){
			## Set the level array to get the level
			##echo "($x1, $x2, $pos_arr)";
				list($pos_arr, $level) = set_array($x1, $x2, $pos_arr);
			
			## Set the number of rows to be displayed.
			## remove this condition if not required
			if ($level < 2) {
				## assign the level of each gene
				$y = $yaxis + ($gap * $level);		
				## load this into an array
				$gene[$i] = (string) $x1 ."#". (string) $x2 ."#".(string) $y ."#".(string) $d ."#". $row['track_shape'] ."#". $row['track_color'] ."#". $row['feature_name'];
				#echo $gene[$items[2]],"<br>";
			}
		}
		else{
			$w = round($x2 - $x1, 2);
			## height of the box
			$h =  $row['feature_value'] * 40 / 100;
			foreach($pos_arr as $x){
			$y = $base - $h;
			#echo (string) $x['x1'] . "#" . (string) $y . "#" .(string) $w . "#" .(string) $h,"<br>";
			array_push($gene, (string) $x1 . "#" . (string) $y . "#" .(string) $w . "#" .(string) $h . "#" . $row['track_color']);
			}
		}
		$i++;
	}		
}	

## Return the JSON object
echo json_encode($gene);

/**
 * Fun: To assign each gene into a level
 * Req: $start - start pixel of the gene
 * Req: $end - pixel of the gene
 * Req: level array
 * Ret: Level array and the level
 * */
function set_array( $start, $end, $pos_arr){
	## Set the level at 0
	$level = 0;
	## For each level in the level array
	for($i = 0; $i <= count($pos_arr); $i++){
		## check the level into variable
		$check = $level;
		## For each pixel from start to end
		for($j = $start; $j <= $end; $j++){
			#echo "$i -- $j <br>";
			## check for filled boxes, if positive
			if(! empty($pos_arr[$i][$j])){
				## move the level by 1
				$level = $level + 1;
				#echo "$i -- $j -- $level<br>";
				## no need to check nay further
				break;
			}
		}
		## If the level has not increased after check
		if($check == $level){
			## not need to check further
			break;
		}
	}
	
	## Since we know the level, from start to end
	for($j = $start; $j <= $end; $j++){
		## fill these empty boxes with dummy 9, main idea is to fill it
		$pos_arr[$level][$j] = 9;
	}
	
	/** Use this only for debug purpose
	 *
	for($i = 0; $i < count($pos_arr); $i++){
		echo "level ", $i, " -- (";
		for($j = 1; $j <= 37; $j++){
			if(empty($pos_arr[$i][$j])){
				echo 0;
			} else {
				echo $pos_arr[$i][$j];
			}
		}
		echo ")<br>";
	}
	* */

	return array($pos_arr, $level);
}

?>
