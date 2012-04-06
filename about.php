<!DOCTYPE HTML>
<html>
<head>
	<title>mGSV :: About</title>
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/homepage_tutorial.css" />
	<style type="text/css">
		fieldset{width:718px;border:1px #B0C0D1 solid;padding:40px;}
		legend{background:#B0C0D1;padding:4px 10px;color:#000000;}
	</style>
	<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20901299-3']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
	</script>
</head>

<body>

<div id="page">
	<? include ('lib/header.php') ?>
	<div id="about" class="section" style="padding:80px">
		<fieldset style="width: 70%">
			<legend>Information about mGSV</legend>
			<p>
				The analysis of multi genome syntenies are a common practice in comparative genomics. With the advent of DNA sequencing technologies, individual biologists can rapidly produce their genomic sequences of interest. We have developed the web-based multi Genome Synteny Viewer (mGSV) that allows users to upload two data files for synteny visualization, the mandatory synteny file for specifying genomic positions of conserved regions and the optional genome annotation file. mGSV presents multi selected genomes in a single integrated view while still retaining the browsing flexibility necessary for exploring individual genomes. Users can browse and filter for genomic regions of interest, change the color or shape of each annotation track as well as switch the annotation track dynamically. Additional features include immediate email notification and tracking of usage history. The entire mGSV package is also light-weighted which enables easy local installation.   
			</p>
		</fieldset>
		<? include ('lib/footer.php') ?>
	</div>
</div><!-- end #page -->


</body>
</html>
