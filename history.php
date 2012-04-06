<?php
$hash = $_GET['hash'];
require_once ("lib/settings.php");
require_once ("lib/database.php");
?>

<html>
<head>
	<title>mGSV :: History</title>
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/pt-sans.css" />
	<link rel="stylesheet" href="css/pt-serif.css" />
	<link rel="stylesheet" href="css/otherpage.css" />
	<link rel="stylesheet" href="css/homepage_shadowbox.css">
	<link rel="stylesheet" href="css/style.css">
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script src="js/metadata.js"></script>
	<script src="js/homepage_shadowbox.js"></script>
	<script src="js/homepage_main.js"></script>
</head>

<body>
	<div id="page">
		<? include ('lib/header.php') ?>
		<div id="top" class="section">
			<div class="container">
			</div>
		</div>
		<div id="intro" class="section">
			<div class="section-content">
				<div class="container">
					<div class="grid11">
						<p>
							<h3>List of previous output files from mGSV</h3>
							These files will be stored in our database for a period of 60 days. Please follow the link provided (in the last column) to view the previous submissions.
							</p>
							Please Note:
							<br /><br />
							1. If the file is not present in the designated field below, the reason could be that the file name was not provided while submitting this file.<br />
							2. If the concerned file is absent in the table, the reason could be the file was submitted more than 60 days ago. If so, the file has been removed from the database.<br />
							3. If the file is not present in the database, please  <a href="mailto:Qunfeng.Dong@unt.edu">contact us</a>.
						</p>

<?
$userinfo = "select id, synfilename,annfilename,session_id,url,create_on from userinfo where hash='$hash'";
$infouser = execute_sql($userinfo);
?>
						<table class="history">
							<tr>
								<td>Filename</td>
								<td>Date of Creation</td>
								<td>Link</td>
							</tr>
<?
$table_content = "";
while($row = mysql_fetch_array($infouser)){
	$table_content = "<tr>";
	if($row[2] != '') {
		$table_content .= "<td>$row[1] <br> $row[2]</td>";
	}
	else {
		$table_content .= "<td>$row[1]</td>";
	}
	$table_content .= "<td>$row[5]</td><td><a href=$row[4]>here</a></td></tr>";
	echo $table_content;
}
echo "</table>";

?>
						</div>
					</div>
				</div>
			</div>
			<? include ('lib/footer.php') ?>	
		</div>
</div><!-- end #page -->


</body>
</html>

