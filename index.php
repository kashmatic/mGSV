<?php
$bool_page = '';
/**
 * $_POST['pro'] = submit
 * This if works only after submission
 */
if (isset($_POST['pro'])) {
	$bool_page = 'true';
	## Get the current time zone
	date_default_timezone_set('America/Mexico_City');
	## Create a session id with date and process id
	$newsession_id = date('tniHYsu') . getmypid();

	## Get the upload directory and database settings
	require_once ("lib/settings.php");
	## connect to the database
	require_once ("lib/database.php");
	
	## Initialize array to hold the filename
	$filename = array();
	## Initialize the total uploads to 2
	$total_uploads = "2";
	## Loop over the two uplaoded files
	for ($i = 0; $i < $total_uploads; $i++) {
		## Get file handler
		$new_file = $_FILES['file' . $i];
		## Get file name
		$file_name = $new_file['name'];
		## correct the filename
		$file_name = str_replace(' ', '_', $file_name);
		## locate the path to temp location of the file
		$file_tmp = $new_file['tmp_name'];
		## Get the file size
		$file_size = $new_file['size'];
		## If the file is uploaded
		if( is_uploaded_file( $file_tmp)){
			#echo $upload_dir . $file_name. '<br>';
			## Move the file from temporary location to /tmp folder 
			move_uploaded_file( $file_tmp, $upload_dir . $newsession_id . $file_name);
			## store the filename.
			array_push( $filename, $file_name);
		}
	}
	
	## Require the file with commands to create synteny and annotation tables
	require_once ("lib/databasetable.php");
	#require_once ("lib/reportinvalidfile_mod.php");
	$synfilename = $filename[0];
	## Require thr email file to send email
	require_once ("lib/emailsystem.php");
	
	## If email was submitted, clean it
	if ($_POST["email"] != '') {
		$email = mysql_real_escape_string($_POST["email"]);
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
	for( $i = 0; $i < sizeof($filename); $i++){		
		#echo $filename[$i].'<br>';
		## Get the complete path
		$path = $upload_dir . $newsession_id . $filename[$i];
		## get the extension of the uploaded file
		$ext = strrchr($filename[$i], ".");
		## if it is of *.gz format
		if( strtolower($ext) == '.gz'){
			## gunzip it
			exec("gunzip $path");
			## update the synteny filename
			$filename[$i] = stringlen($filename[$i], ".");
		} 
		## if the file is of *.zip format
		elseif( strtolower($ext) == '.zip') {
			## unzip it
			exec("unzip -d $upload_dir $path");
			## update the filename
			$filename[$i] = stringlenforzip($filename[$i], ".");
			## delete the uploaded file
			unlink($path);
		}
	} ## for
	#print_r($filename);

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
			if($check_length == false){
				$org2 = abs($num_of_items[4] - $num_of_items[5]) + 1;
				$org1 = abs($num_of_items[1] - $num_of_items[2]) + 1;
				$minlength = min($org1, $org2);
				$values .= "'" . $minlength . "',";
			}
			$values .= "'" .  getRandomColorHex() . "')";
			#echo "168: $bool<br>";
			if( $bool_page == 'true'){
				## Insert the values into the table
				$sql = "insert into {$newsession_id}_synteny ($insert_into) $values";
				## show the statement
				#echo "171: $sql<br>";
				## execute the statement
				$result = execute_sql($sql);
			}
		}
		## Delete the files from server
		unlink($upload_dir . $newsession_id . $filename);
		return $bool_page;
	}


	function annotation($annfilename) {
		$bool_page = 'true';
		global $upload_dir, $newsession_id, $email, $filename;
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
			#echo "($line)<br>";
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
		unlink($upload_dir . $newsession_id . $annfilename);
		return $bool_page;
	}

	## Load the synteny file into the database
	if( $bool_page == 'true'){
		$bool_page = syn($filename[0]);
	}
	
	## Delete the wrong files
	if( $bool_page != 'true'){
		unlink($upload_dir . $newsession_id . $filename[0]);
	}
	
	if((sizeof($filename) > 1) && ( $bool_page == 'true')){
		$bool_page = annotation_table( $newsession_id);
		$bool_page = annotation($filename[1]);
	}
	
	if(($email != "") && ( $bool_page == 'true')){
		emailsystem($email);
	}
	
	if($bool_page == 'true'){
		#$bool_page = "Location:summary.php?session_id=$newsession_id";
		header("Location:summary.php?session_id=$newsession_id");
	}
}

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
		<script src="js/jtip.js" type="text/javascript"></script>

		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-20901299-3']);
			_gaq.push(['_trackPageview']); (function() {
				var ga = document.createElement('script');
				ga.type = 'text/javascript';
				ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(ga, s);
			})();

		</script>
		<script>
			$(document).ready(function(){
				$('#MgsvForm').submit(function(){
					$('#rotate').css({'display': 'block'});
					if($('#file0').val() == ''){
						$('#file0_error').html('Synteny file is required.');
						$('#rotate').css({'display': 'none'});
						return false;
					}
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
				<table class="inputTable">
					<tr>
						<td valign="top">
							<label for="file0">Upload <a href="img/syntenyImage.jpg" class="thickbox" title="The format of synteny file">synteny</a> file (Required)&nbsp;<span class="formInfo"><a href="html/hint5.htm?width=375" class="jTip" id="six" name=''>?</a></span></label>
						</td>
						<td valign="top" width="300px">
							<input type="file" id="file0" name="file0" /><br>
							<span id="file0_error" style="color: red;"></span>
						</td>
						<td valign="top">
							<a href="sample_synteny.txt">Download synteny file</a>
						</td>
					</tr>
					<tr>
						<td>
							<label for="annotation">Upload <a href="img/annotationImage.jpg" class="thickbox" title="The format of annotation file">annotation</a> file (Optional)&nbsp;<span class="formInfo"><a href="html/hint6.htm?width=375" class="jTip" id="seven" name=''>?</a></span></label>
						</td>
						<td>
							<input type="file" id="file1" name="file1" class="{validate:{required:false,accept:'gz|txt|zip'}}" />
						</td>
						<td>
							<a href="sample_annotation.txt">Download annotation file</a></td>
					</tr>
					<tr>
						<td>
							<label for="email">Email (Optional)&nbsp;<span class="formInfo"><a href="html/hint1.htm?width=375" class="jTip" id="five" name=''>?</a></span></label>
						</td>
						<td>
							<input id="email" name="email" /><br>
							<span id="email_error" style="color: red;"></span>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input class="submit" type="submit" id="pro" name="pro" value="Submit" /><br>
							<span id="rotate" style="display:none">Loading... <img src="img/rotating_arrow.gif" /></span>
						</td>
					</tr>
				</table>
			</form>
			<div style="border 1px solid red; color: red">
			<? 
				if(($bool_page != 'true') && ($bool_page != '')){
					echo "ERROR.<br>";
					echo $bool_page;
				} 
			?>
			</div>
		</div>
		<? include ('lib/footer.php') ?>
	</body>
</html>
