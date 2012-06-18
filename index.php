<?php
$bool_page = '';
$synfilename = '';
$annfilename = '';
$newsession_id = '';
$upload_dir = '';

function save_uploaded_file($id, $type){
	global $newsession_id, $upload_dir, $synfilename, $annfilename;

	## Get file handler
	$new_file = $_FILES[$id];
	## Get file name
	$file_name = $new_file['name'];
	if($file_name == ""){
		return "false";
	}
	## correct the filename
	$file_name = str_replace(' ', '_', $file_name);
	## locate the path to temp location of the file
	$file_tmp = $new_file['tmp_name'];
	## Get the file size
	$file_size = $new_file['size'];
	## If the file is uploaded
	if( is_uploaded_file( $file_tmp)){
		## Move the file from temporary location to /tmp folder 
		$bool_move_file = move_uploaded_file( $file_tmp, $upload_dir . $newsession_id . $file_name);
		if(! $bool_move_file){
			#$bool_page = "Unable to copy file to the $upload_dir folder. Please check the permissions.";
			return "Unable to copy file to the $upload_dir folder. Please check the permissions.";
		}
	}
	($type == 'syn') ? $synfilename = $file_name : $annfilename = $file_name;
	return 'true';
}

function save_url_file($id, $type){
	global $newsession_id, $upload_dir, $synfilename, $annfilename;
	
	$url_path = $_POST[$id];
	if($url_path == ''){
		return 'false';
	}
	if(
			( substr($url_path, -4) === '.txt') || 
			( substr($url_path, -4) === '.zip') || 
			( substr($url_path, -3) === '.gz')
	){
		$url = explode('/', $url_path);
		$ok = copy($url_path, $upload_dir . $newsession_id . $url[sizeof($url) - 1]);
		if(! $ok){
			return "$url_path is not correct";
		}
	}
	else {
		return "URL($url_path) doesnt end with .txt or .zip or .gz ";
	}	
	($type == 'syn') ? $synfilename = $url[sizeof($url) - 1] : $annfilename = $url[sizeof($url) - 1];
	return 'true';
}

/**
 * $_POST['pro'] = submit
 * This if works only after submission
 */
function save_files(){
	global $newsession_id, $upload_dir, $synfilename, $annfilename;

	date_default_timezone_set('America/Mexico_City');
	$newsession_id = date('tniHYsu') . getmypid();
	
	## Get the upload directory and database settings
	require_once("lib/settings.php");
	## connect to the database
	require_once("lib/database.php");
	$upload_dir = $upload_dir;

	$bool_page = save_uploaded_file('file0', 'syn');
	if($bool_page != 'true'){
		$bool_page = save_url_file('syn_url', 'syn');
		if($bool_page != 'true'){
			if((! isset($_POST['syn_default'])) && ($bool_page == 'false')){
				return "Synteny file is required"; 
			}
			return $bool_page;
		}
	}
	$out = save_uploaded_file('file1', 'ann');
	if($out != 'true'){
		$out = save_url_file('ann_url', 'ann');
		if($out != 'false'){
			return $out;
		}
	}
	
	return $bool_page;
}	
	
function req(){
	## Require the file with commands to create synteny and annotation tables
	require_once ("lib/databasetable.php");
	## Require thr email file to send email
	require_once ("lib/emailsystem.php");
	
	## If email was submitted, clean it
	if ($_POST["email"] != '') {
		$email = mysql_real_escape_string($_POST["email"]);
	}
}

function stringlenforzip($value, $trim) {
	$num1 = strlen($value);
	$totalnum = $num1 - 4;
	$stringlen = substr("$value", 0, $totalnum);
	return $stringlen;
}

function stringlen($value, $trim) {
	$num1 = strlen($value);
	$totalnum = $num1 - 3;
	$stringlen = substr("$value", 0, $totalnum);
	return $stringlen;
}
	
## To unzip the uploaded files
## for each uploaded file
function uncompress_files($filename){
	global $newsession_id, $upload_dir;
	## Get the complete path
	$path = $upload_dir . $newsession_id . $filename;
	## get the extension of the uploaded file
	$ext = strrchr($filename, ".");
	## if it is of *.gz format
	if( strtolower($ext) == '.gz'){
		## gunzip it
		exec("gunzip $path");
		## update the synteny filename
		$filename = stringlen($filename, ".");
	} 
	## if the file is of *.zip format
	elseif( strtolower($ext) == '.zip') {
		## unzip it
		exec("unzip -d $upload_dir $path");
		## update the filename
		$filename = stringlenforzip($filename, ".");
		## delete the uploaded file
		if(file_exists($path)){
			unlink($path);
		}
	}
	return $filename;
}

/**
 * Go thru the synteny file and upload the information into 
 */
	function syn($filename) {
		$bool_page = 'true';
		#echo "($filename[0])<br>";
		## Get the global values
		global $upload_dir, $newsession_id, $email;
		#echo "($upload_dir)<br>";
		## Get the first line
		$header_line = exec( "head -n 1 tmp/".$newsession_id . $filename);
		if($header_line == ''){
			$bool_page = 'Synteny file: Unable to store in /tmp folder.';
			return $bool_page;
		}
		## convert into array
		$header_pieces = explode( "\t", $header_line);
		
		## check if the first item starts with #
		if( ! preg_match("/^#/i", $header_pieces[0])) {
			$bool_page = 'Synteny file: The # symbol at line 1 is not present.';
			return $bool_page;
		}
		
		## Replace the # for further analysis
		$header_pieces[0] = str_replace('#', '', $header_pieces[0]);
		
		## Get the fixed column names
		$fixed_org = array('org1', 'org1_start', 'org1_end', 'org2', 'org2_start', 'org2_end');
		
		## Check if the header matches our column names
		## for each fixed name
		for($i = 0; $i < sizeof($fixed_org); $i++){
			## if the fixed name doesnt match the file column name
			if( $header_pieces[$i] != $fixed_org[$i]) {
				## raise error
				$bool_page = "Synteny file: Column No." . ($i + 1) . " header should be '" . $fixed_org[$i] . "'.";
				return $bool_page;
			}
		}
		
		## Check if the number of columns is the same as column headers
		$second_line = exec( "head -n 2 tmp/" . $newsession_id . $filename . "| tail -n 1");
		$second_pieces = explode( "\t", $second_line);
		if( sizeof($header_pieces) != sizeof($second_pieces)){
			$bool_page = "Synteny file: There are " . sizeof($header_pieces) . " column names but " . sizeof($second_pieces) . " columns.";
			return $bool_page;
		}
		
		## For the non-fixed column names
		for($i = 6; $i < sizeof($header_pieces); $i++){
			## make sure they are not empty
			if( preg_match("/^\s*$/i", $header_pieces[$i])) {
				$bool_page = "Synteny file: Column No." . ($i + 1) . " header is blank.";
				return $bool_page;
			}
		}
		
		## Check if the column names are unique
		if( count( array_unique( $header_pieces)) < count( $header_pieces)) {
			$bool_page = 'Synteny file: There are more than one column with the same name';
			return $bool_page;
		}
		
		## create the table in the database
		$bool_page = synteny_table( $newsession_id, $filename);
		if($bool_page != 'true'){
			return $bool_page;
		}
		
		## Get the field names
		$cnames = desc_synteny_table($newsession_id);
		if(is_string($cnames)){
			return $cnames;
		}
		
		## Remove the id field name
		array_shift($cnames);
		## get a string from the array
		$insert_into = implode(', ', $cnames);
		
		
		## look for column name 'length'
		$check_length = array_search('length', $header_pieces);

		## define pattern
		$pattern = '/^.*\t[0-9]+\t[0-9]+\t.*\t[0-9]+\t[0-9]+';
	
		## Get the starting index of the new items
		$newitems = count($header_pieces) - 7;
		## If no addition items are provided
		for ($i = 0; $i <= $newitems; $i++) {
			## generate the regex for each additional column
			$pattern .= '\t[0-9\.Ee\-\+]*';
		}
		## close the $pattern here.
		$pattern .= '$/';
		
		## create a read file handle to the uplaoded file
		$fh = fopen($upload_dir . $newsession_id . $filename, "r");
		## Remove the first line
		fgets($fh);
		
		## initialize a line_number to know
		$line_number = 1;
		while( ! feof( $fh)){
			## increment it
			$line_number++;
			## get each line
			$line = fgets($fh);
			## continue if it is an empty line
			if($line == ''){ continue; }
			## trim for end of line characters
			$line = trim($line);
			## compare with the pattern
			$correct_format = preg_match($pattern, $line);
			if($correct_format == false){
				$bool_page = "Synteny File: line number $line_number, is not in the correct format";
				break;
			}
			#echo "($line)<br>";
			$values = "VALUES(";
			$num_of_items = explode("\t", $line);
			for ($jj = 0; $jj < count($num_of_items); $jj++) {
				$values .= "'" . $num_of_items[$jj] . "',";
			}
			## check for the first 250 characters of org1 and org2
			if(substr($num_of_items[0], 0, 250) == substr($num_of_items[3], 0, 250)){
				## If they are similar they throw an error and quit
				$bool_page = "Synteny File: Org1 and Org2 are similar at line $line_number.";
				break;
			}
			## If there is no column called length in the input synteny file
			if($check_length == false){
				## get the difference between start and end of org2
				$org2 = abs($num_of_items[4] - $num_of_items[5]) + 1;
				## get the difference between start and end of org1
				$org1 = abs($num_of_items[1] - $num_of_items[2]) + 1;
				## get the minimum of the above two lengths
				$minlength = min($org1, $org2);
				## concatenate this minimum value to the input command
				$values .= "'" . $minlength . "',";
			}
			## generate a random color to store each synteny
			$values .= "'" .  getRandomColorHex() . "')";
			
			## If everything is good
			if( $bool_page == 'true'){
				## Insert the values into the table
				$sql = "insert into {$newsession_id}_synteny ($insert_into) $values";
				## show the statement
				#echo "insert command: $sql<br>";
				## execute the statement
				$result = execute_sql($sql);
			}
		}
		## Delete the files from server
		if(file_exists($upload_dir . $newsession_id . $filename)){
			unlink($upload_dir . $newsession_id . $filename);
		}
		## return the boolean parameter
		return $bool_page;
	}

	/**
	 * Function to deal with the uploaded annotation file
	 */
	function annotation($annfilename) {
		## preset the boolean to true
		$bool_page = 'true';
		## get the global parameters
		global $upload_dir, $newsession_id, $email, $filename;
		## start the line counter at 0
		$counter = 0;
		
		## Get the field names
		$cnames = desc_annotation_table($newsession_id);
		## Remove the id field name
		array_shift($cnames);
		## get a string from the array
		$insert_into = implode(', ', $cnames);

		## define pattern
		$pattern = '/^(.*)\t([0-9]+)\t([0-9]+)\t([\+|\-|\.| ])[\t|\n]+(.*)\t([0-9eE\-\+\.|\.| ]*)\t(.*)\t(christmasarrow|pentagram|dashline|ellipse|arrow|box|xyplot)\t([a-zA-Z\s]*)?$/i';
	
		## create a read file handle to the uplaoded file
		$fh = fopen($upload_dir . $newsession_id . $annfilename, "r");
		## Remove the first line
		fgets($fh);
		
		## initialize a line_number to know
		$line_number = 1;
		while( ! feof( $fh)){
			## increment it
			$line_number++;
			## get each line
			$line = fgets($fh);
			## continue if it is an empty line
			if(preg_match("/^\s*$/i", $line)){
				continue; 
			}
			## trim for end of line characters
			$line = trim($line);
			## compare with the pattern
			$correct_format = preg_match($pattern, $line);
			if($correct_format == false){
				$bool_page = "Annotation file: line number $line_number is not in correct format";
				break;
			}
			#echo "($line)<br>";localhost
			$num_of_items = explode("\t", $line);
			$values = "VALUES('" . implode("','", $num_of_items) . "')";
			#echo "224: $bool<br>";
			if( $bool_page == 'true'){
				## Insert the values into the table
				$sql = "insert into {$newsession_id}_annotation ($insert_into) $values";
				## show the statement
				#echo "229: $sql<br>";
				## execute the statement
				$result = execute_sql($sql);
			}
		}
		## Delete the files from server
		if(file_exists($upload_dir . $newsession_id . $annfilename)){
			unlink($upload_dir . $newsession_id . $annfilename);
		}
		return $bool_page;
	}

if (isset($_POST['pro'])) {
	if($_POST['syn_default'] == 'syn_default'){
		if($_POST['ann_default'] == 'ann_default'){
			header("Location:summary.php?session_id=306241420125500000022322");
		} else {
			header("Location:summary.php?session_id=306271420125100000024316");
		}
	}
	
	$bool_page = save_files();
	
	if( $bool_page == 'true'){
		$synfilename = uncompress_files($synfilename);
		$annnfilename = uncompress_files($annfilename);
	}

	req();

	## Load the synteny file into the database
	if( $bool_page == 'true'){
		$bool_page = syn($synfilename);
	}

	## Delete the wrong files
	if(( $bool_page != 'true') && (file_exists($upload_dir . $newsession_id . $synfilename))){
		unlink($upload_dir . $newsession_id . $synfilename);
	}
	
	if(($annfilename != '') && ( $bool_page == 'true')){
		$bool_page = annotation_table($newsession_id);
		$bool_page = annotation($annfilename);
	}
	
	if(($email != "") && ( $bool_page == 'true')){
		emailsystem($email);
	}
	
	if($bool_page == 'true'){
		#$bool_page = "Location:summary.php?session_id=$newsession_id";
		header("Location:summary.php?session_id=$newsession_id");
	}
}

/**
 * Function to generate a random color.
 */
function getRandomColorHex($max_r = 255, $max_g = 255, $max_b = 255) {
	return sprintf( '#%02X%02X%02X', rand(0,$max_r), rand(0,$max_g), rand(0,$max_b) );
}

?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>mGSV :: Home</title>
		<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
		<meta charset="UTF-8">
		<link rel="stylesheet" href="css/homepage.css" />
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
		<script type="text/javascript" src="js/thickbox.js"></script>
		<link rel="stylesheet" href="css/thickbox.css" />
		<script src="js/jtip.js" type="text/javascript"></script>
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<script>
			$(document).ready(function(){
				if($.browser.msie){
					$('#msie').css({'display': 'block'});
				}
				console.log($.browser);
				$('#MgsvForm').submit(function(){
					$('#rotate').css({'display': 'block'});
					$('div#error_div').html('');
					var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
					emailAddress = $('#email').val();
					if((emailAddress != '') && (! pattern.test(emailAddress))){
						$('#email_error').html('wrong email format.');
						$('#rotate').css({'display': 'none'});
						return false;
					}
				});
			});
		</script>

	</head>
	<body>
		<? include ('lib/header.php') ?>
		<br><br>
		<div align="center">
			<img src="img/mGSV_logo_600px.png" style="">
			<form id="MgsvForm" method="post" action="" enctype="multipart/form-data">
				<table class="inputTable round">
					<tr>
						<td valign="top" class="round" rowspan="2" style="height:250px">
							<H4 align="center" style="border-bottom: 1px solid black; padding: 0px 0px 10px 0px;">
								<a href="img/syntenyImage.png" class="thickbox" title="The format of synteny file">Synteny File (required)</a> 
								<span class="formInfo"><a href="html/hint5.htm?width=375" class="jTip" id="six" name=''>?</a></span>
							</H4>
							<br>
							<label for="file0">Upload synteny file</label>
							<br>
							<input type="file" id="file0" name="file0" />
							<br><br>
							<h5 style="color:#008080">or</h5>
							<label>Provide URL to Synteny file</label>
							<br>
							<input type="text" id="syn_url" name="syn_url" style="width:400px"/>
							<br><br>
							<h5 style="color:#008080">or</h5>
							<label>Use the sample synteny file</label>
							<br>
							<input type="checkbox" name="syn_default" value="syn_default">  sample_synteny.txt (<a href="sample_synteny.txt">download</a>)
						</td>
						<td valign="top" class="round" rowspan="2">
							<H4 align="center" style="border-bottom: 1px solid black; padding: 0px 0px 10px 0px;">
								<a href="img/annotation.png" class="thickbox" title="The format of annotation file">Annotation File	(optional)</a>
								<span class="formInfo"><a href="html/hint6.htm?width=375" class="jTip" id="seven" name=''>?</a></span>
							</H4>
							<br>
							<label for="annotation">Upload annotation file</label>
							<br>
							<input type="file" id="file1" name="file1" class="{validate:{required:false,accept:'gz|txt|zip'}}" />
							<br><br>
							<h5 style="color:#008080">or</h5>
							<label>Provide URL to Annotation file</label>
							<br>
							<input type="text" id="ann_url" name="ann_url" style="width:400px"/>
							<br><br>
							<h5 style="color:#008080">or</h5>
							<label>Use the sample annotation file</label>
							<br>
							<input type="checkbox" name="ann_default" value="ann_default">  sample_annotation.txt (<a href="sample_annotation.txt">download</a>)
						</td>
						<td valign="top" class="round">
							<H4 align="center" style="border-bottom: 1px solid black; padding: 0px 0px 10px 0px;">
								Email (Optional)
								<span class="formInfo"><a href="html/hint1.htm?width=375" class="jTip" id="five" name=''>?</a></span>
							</H4>
							<br>
							<label>Valid Email address.</lable>
							<br>
							<input id="email" name="email" /><br>
							<span id="email_error" style="color: red;"></span>
						</td>
					</tr>
					<tr>
						<td class="round" align="center" style="height:200px">
							<input class="large button blue round" type="submit" id="pro" name="pro" value="Submit" /><br>
							<span id="rotate" style="display:none; color: green">Loading... <img src="img/rotating_arrow.gif" /></span>
							<br>
							<input class="large button blue round" type="reset" value="Reset" /><br>
						</td>
					</tr>
				</table>
			</form>
			<div style="border 5px solid red; color: red" id="error_div">
			<? 
				if(($bool_page != 'true') && ($bool_page != '')){
					echo "ERROR.<br>";
					echo $bool_page;
				} 
			?>
			</div>
			<div id='msie' style="display:none">
				Current IE browser is not equipped to use mGSV. Please use Mozilla Firefox, Chrome, Safari.
			</div>
		</div>
		<? include ('lib/footer.php') ?>
	</body>
</html>
