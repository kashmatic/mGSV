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
		
	</head>
	<body>
		<? include ('lib/header.php') ?>
		<br><br>
		<input type=hidden id='session_id' value='<? echo $_GET['session_id'] ?>'>
		<table id="summary">
			<tr>
				<td style="width:300px" valign=top>
					<h4 style="color: #4682B4">Associations Provided</h4>
					<div id="synInfo">
						<table id="assoc"><tr><th>Association</th><th>Syntenies</th></tr></table>
					</div>
				</td>
				<td rowspan=2>
					<div id="canvas"></div>
				</td>
			</tr>
			<tr>
				<td valign=top>
					<h4 style="color: #4682B4">Select viewing order</h4>
					<span id=default class="orderTab" onclick=showThis('def')>From input file</span>
					<span id=suggest class="orderTab" onclick=showThis('sug')>Suggested</span><br>
					<div id=default class="order">
						default
					</div>
					<div id=suggest class="order">
						suggest
					</div>
				</td>
			</tr>
		</table>
	</body>
</html>
