<doctype>
<html>
	<head>
		<title>mGSV :: Summary</title>
		<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
		<script type="text/javascript" src="js/raphael-min.js"></script>
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="js/summary.js"></script>
		<script type="text/javascript" src="js/data.js"></script>
		<LINK href="css/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="css/homepage_tutorial.css" />
		<style type="text/css">
	.button {
		background: #222 url('/mgsv/img/overlay.png') repeat-x;
		display: inline-block;
		padding: 5px 10px 5px;
		color: #fff;
		text-decoration: none;
		text-shadow: 0 -1px 1px rgba(0,0,0,0.25);
		border: 1px solid rgba(0,0,0,0.25);
	}
	.large.button {
		font-size: 14px;                                          
		padding: 5px 14px 5px 14px; 				
	}
	.blue.button { 
		background-color: #2981e4; 
	}
	.round {
		-moz-border-radius: 6px;
		-webkit-border-radius: 6px;
		-moz-box-shadow: 0 1px 3px rgba(0,0,0,0.6);
		-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.6);
	}
	.divline {
		width: 100%;
		background-color: gainsboro;
		height: 33px;
		min-width:500px;
	}
</style>
	</head>
	<body>
		<? include ('lib/header.php') ?>
		<br><br>
		<input type=hidden id='session_id' value='<? echo $_GET['session_id'] ?>'>
		<div style="width:1000px; text-align:center; color:#4682B4; font-weight:bold">Summary</div>
		<table id="summary">
			<tr>
				<td style="width:300px" valign=top>
					<h4 style="color: #4682B4">Associations Provided</h4>
					<div id="synInfo">
						<table id="assoc"><tr><th>Association</th><th>Regions</th></tr></table>
					</div>
				</td>
				<td rowspan=2>
					<div id="canvas"></div>
				</td>
			</tr>
			<tr>
				<td valign=top>
					<h4 style="color: #4682B4">Select viewing mode 
						<span id="rotate" style="display:block; color: black;">Loading... <img src="img/rotating_arrow.gif" /></span>
					</h4>
					<div id=default class="order" style="border:0px">
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>
