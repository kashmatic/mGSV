<?php
#You can change user,pass,database's name, tables' names
#Here I use gsv for my databse, synteny,and annotaton are my tables.

function synteny_table($newsession_id, $synfilename) {
	## initialize bool
	$bool = 'true';
	
	## Get the first line of the file
	$header_line = exec("head -n 1 tmp/" . $newsession_id . $synfilename);
	## split the line into items
	$header_pieces = explode("\t",$header_line);
	
	## the file header line is good
	if( $bool == 'true'){
		## Initialize a string to create fields for additional columns
		$userdef = "";
		## Iterate from the 7th column till the end
		for( $i = 6; $i < count( $header_pieces); $i++){
			$userdef .= "`".$header_pieces[$i]."`"."varchar(100) DEFAULT NULL,";
		}
		## Check if there is a length function
		$check_length = array_search('length', $header_pieces);
		## if not create a column
		if($check_length == '') {
			$userdef .= "`length` varchar(100) DEFAULT NULL,";
		}
		## get the create statement
		$createTable = "CREATE TABLE `{$newsession_id}_synteny` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`org1` varchar(250) NOT NULL,
			`org1_start` int(10) unsigned NOT NULL,
			`org1_end` bigint(20) unsigned NOT NULL,
			`org2` varchar(250) NOT NULL,
			`org2_start` int(10) unsigned NOT NULL,
			`org2_end` bigint(20) unsigned NOT NULL,
			$userdef
			`SYNcolor` varchar(8) DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `org1_start` (`org1_start`),
			KEY `org1_end` (`org1_end`),
			KEY `org2_start` (`org2_start`),
			KEY `org2_end` (`org2_end`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		## execute the 
		$result = mysql_query($createTable);
		if(! $result){
			$bool = "Unable to create the synteny table.<br>" . mysql_error();
		}
	}
	## return the boolean information
	return $bool;
}

/**
 * To create the annotation table
 */
function annotation_table($newsession_id) {
	$bool = 'true';
	$createTable = "CREATE TABLE IF NOT EXISTS `{$newsession_id}_annotation` (
		`id` int(10) NOT NULL AUTO_INCREMENT,
		`org_id` varchar(250) NOT NULL,
		`start` int(20) unsigned NOT NULL,
		`end` bigint(20) unsigned NOT NULL,
		`strand` varchar(1) DEFAULT NULL,
		`feature_name` text DEFAULT NULL,
		`feature_value` real DEFAULT NULL, 
		`track_name` varchar(100) NOT NULL,
		`track_shape` varchar(100) NOT NULL,
		`track_color` varchar(100) DEFAULT NULL,
		PRIMARY KEY (`id`),
		KEY `start` (`start`),
		KEY `end` (`end`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
	$result = mysql_query($createTable);
	if(! $result){
		$bool = "Unable to create annotation table.<br>.".mysql_error();
	}
	return $bool;
}

/**
 * Get the field names from the table
 */
function desc_synteny_table($newsession_id) {
	$cnames = array();
	$sql = "DESCRIBE `{$newsession_id}_synteny`";
	$result = mysql_query($sql);
	if( ! $result){
		return mysql_error();
	}
	while( $row = mysql_fetch_assoc( $result)){
		array_push($cnames, $row['Field']);
	}
	return $cnames;
}

/**
 * Get the field names from the table
 */
function desc_annotation_table($newsession_id) {
	$cnames = array();
	$sql = "DESCRIBE `{$newsession_id}_annotation`";
	$result = mysql_query($sql);
	if( ! $result){
		return mysql_error();
	}
	while( $row = mysql_fetch_assoc( $result)){
		array_push($cnames, $row['Field']);
	}
	return $cnames;
}


?>
