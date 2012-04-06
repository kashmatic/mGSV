<!DOCTYPE HTML>
<?php
require_once("path.php");
$db = $_GET['db'];
?>
<html>
<head>
	<title>MGSV</title>
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
<div id="page">
	<div id="top" class="section">
		<div class="container">
		</div>
	</div>
	
	<div id="intro" class="section">

		<div class="section-content">
			<div class="container">
			<div class="grid11">
					
<?php
if($db == 1) {
echo "<p><h1>The # symbol is required in the synteny file.</h1></p>";
}
elseif($db == 2) {
echo "<p><h1>There are duplications in the first line of synteny file.</h1></p>";
}
elseif($db == 3) {
echo "<p><h1>There was an error during the file upload. Please try again.</h1></p>";
}
?>

				</div>
				

	
			</div>
			</div>
		</div>
	</div>
	
	<div id="nav">
		<div class="section-content">
			<div class="container">

				<ul>
					<li><a href="../mgsvhomepage.php#top" class="active"><span>Home</span></a></li>
					<li><a href="../mgsvhomepage.php#about"><span>About</span></a></li>
					<li><a href="../mgsvhomepage.php#tutorial"><span>Tutorial</span></a></li>
					<li><a href="../mgsvhomepage.php#download"><span>Download</span></a></li>
					<li><a href="../mgsvhomepage.php#contact"><span>Contact</span></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div id="footer" class="section">
		<div class="container">
			<div class="grid12 first" id="bottom">
				<br />
				<br />
				<br />
				<br />
				<br />
				<br />
				<br />
				<p class="small"><a href= "http://copyright.unt.edu/content/unt-copyright-resources" title="Copyright">Copyright</a>&copy;&nbsp;UNT</p>

			</div>
		</div>
	</div><!-- end .section -->

</div><!-- end #page -->


</body>
</html>
