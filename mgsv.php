<doctype>
<html>
	<head>
		<title>mGSV :: Display</title>
		<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
		<script type="text/javascript" src="js/raphael-min.js"></script>
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="js/my_script.js"></script>
		<script type="text/javascript" src="js/raphael_objects.js"></script>
		<script type="text/javascript" src="js/data.js"></script>
		<LINK href="css/style.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<?
			$order = '';
			if(isset($_GET['order'])){
				$order = $_GET['order'];
			}
		?>
		<section>
		<? include ('lib/header.php') ?>
		<br><br>
		<? include ('lib/flip.php') ?>
		<div style="clear:both">
			<div style="float:left; padding: 10px;">
				
				<img src="img/zoomin.png" onclick="all_zoomin(' + id + ')" align="top">
				<img src="img/zoomout.png" onclick="all_zoomout(' + id + ')" align="top">
				<img src="img/left.png" onclick="all_moveleft(' + id + ')" align="top">
				<img src="img/right.png" onclick="all_moveright(' + id + ')" align="top">
				<img class="entire_rounded" src="img/entire.png" onclick="all_entire(' + id + ')" style="height:25px;width:40px; border: 3px solid #0d6dcd" align="top">
			</div>
				<div style="float:left; padding: 10px;">
			<span id="selectlist"></span>
				</div>
		</div>
			<table></table>
			<input type=hidden id='display_order' value="<? echo $order ?>" > 
			<input type=hidden id='session_id' value='<? echo $_GET['session_id'] ?>'>
			<div id="coord"></div>
		</section>
	</body>
</html>
