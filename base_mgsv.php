<doctype>
<html>
	<head>
		<title>mGSV :: Display</title>
		<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
		<script type="text/javascript" src="js/raphael-min.js"></script>
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="js/base_mgsv.js"></script>
		<script type="text/javascript" src="js/base_raphael_objects.js"></script>
		<script type="text/javascript" src="js/data.js"></script>
		<LINK href="css/style.css" rel="stylesheet" type="text/css">
		<style type="text/css">
			.assocShow {
				background-color: #5F9EA0;
				color: white;
				padding: 0px 10px 0px 10px;
				//border: 1px solid black;
			}
			.assocHide {
				background-color: gainsboro;
				padding: 0px 10px 0px 10px;
			}
		</style>
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
				
				<img src="img/zoomin.png" onclick="all_zoomin(' + id + ')" align="top" title="Zoom in (all genomes)">
				<img src="img/zoomout.png" onclick="all_zoomout(' + id + ')" align="top" title="Zoom out (all genomes)">
				<img src="img/left.png" onclick="all_moveleft(' + id + ')" align="top" title="Move left (all genomes)">
				<img src="img/right.png" onclick="all_moveright(' + id + ')" align="top" title="Move right (all genomes)">
				<img class="entire_rounded" src="img/entire.png" onclick="all_entire(' + id + ')" style="height:25px;width:40px; border: 3px solid #0d6dcd" align="top" title="Entire region (all genomes)">
			</div>
			<div style="float:left; padding: 10px;">
				<span id="selectlist"></span>
				<img class="entire_rounded" src="img/view.png" onclick="rearrange()" style="height:25px;width:80px; border: 3px solid #0d6dcd" align="top" title="Rearrange the genomes">
				<span id="select_order_check" style="color:red"></span>
			</div>
		</div>
		<br>
		<div style="border: 0px; clear: both;margin-bottom: 20px;">
			<div style="padding:5px; width:170px; height:80px; float:left" id="control_synteny"></div>
			<div style="border: 0px solid #4682B4; padding:5px; height:80px; overflow:auto; float:left;" id="visi_invisi"></div>
		</div>
		<div id='synteny_container' style="border: 0px; clear: both;padding-top: 10px;">
			<div id='div_control' style="float:left"></div>
			<div id='div_synteny' style="float:left"></div>
		</div>
		<input type=hidden id='display_order' value="<? echo $order ?>" > 
		<input type=hidden id='session_id' value='<? echo $_GET['session_id'] ?>'>
		<div id="coord"></div>
		</section>
	</body>
</html>
