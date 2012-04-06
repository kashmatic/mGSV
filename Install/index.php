<?php

ob_start();
session_start();
$rootURL = "../";
$cssURL = "../css";
$imgURL = "../img";
$jsURL = "../js";

?>
<html>
<head>
	<title>mGSV :: Install</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../css/pt-sans.css" />
	<link rel="stylesheet" href="../css/pt-serif.css" />
	<link rel="stylesheet" href="../css/otherpage.css" />
	<link rel="stylesheet" href="../css/homepage_shadowbox.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script src="../js/metadata.js"></script>
	<script src="../js/homepage_shadowbox.js"></script>
	<script src="../js/homepage_main.js"></script>
</head>

<body>
	<img src="../img/mGSV_logo_200px.png" alt="mGSV">
	<div id="page">
		<div id="top" class="section">
			<div class="container">
			</div>
		</div>
		<div id="intro" class="section">
			<div class="section-content">
				<div class="container">
					<div class="grid11">
						<h3>Verifying mGSV Installation.</h3>
						<?php
							$step = $_GET["step"];
							if($step != 2) {
								echo "<p>This page allows you to verify installation of mGSV. Please input your MySQL login details below.</p>";
							}
							echo $stepTitle;
							$step = $_GET["step"];
							if (isset($step)){
							} else {
								$step =1;
							}
							require("../lib/settings.php");
							require("install.php");
						?>
					</div>
				</div>
			</div>
			
			<div id="footer" class="section">
				<div class="container">
					<div class="grid12 first" id="bottom">
						<p class="small"><a href= "http://copyright.unt.edu/content/unt-copyright-resources" title="Copyright">Copyright</a>&copy;&nbsp;UNT</p>
					</div>
				</div>
			</div>
		</div><!-- end .section -->
	</div><!-- end #page -->
</body>
</html>
<?php ob_end_flush();?>

