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
		height: 33px;
		width:1000px;
	}
</style>
<div class="divline">
	<div style="float:left;">
	<a href="summary.php?session_id=<? echo $_GET['session_id'] ?>" style="text-decoration: none;">
		<span class="large button blue round">&larr; Summary</span>
	</a>
	</div>
	<div id="info_bar" style="float:left; margin-left:10px;">
		<span style="padding-top:10px"></span>
	</div>
	<div id="button_default" style="float:left; margin-left:10px; display: none;">
	<a href="mgsv.php?session_id=<? echo $_GET['session_id'] ?>&order=<? echo $order ?>" style="text-decoration: none;">
		<span class="large button blue round">Pairwise view</span>
	</a>
	</div>
	<!-- <button class="large button blue">Suggested</button> -->
	<input type=hidden id=graph_value name=graph_value value="<? echo $_GET['graph'] ?>">
	<input type=hidden id=default_order name=default_order value="<? echo $_GET['order'] ?>">
	<div id="button_brute" style="float:left; margin-left:10px; display: none;">
	<a href="base_mgsv.php?session_id=<? echo $_GET['session_id'] ?>&order=<? echo $order ?>" style="text-decoration: none;left: 0px">
		<span class="large button blue round">Multiple view</span>
	</a>
	</div>
	<div id="button_graph" style="float:left; margin-left:10px; display: none;">
	<a href="mgsv.php?session_id=<? echo $_GET['session_id'] ?>&order=" style="text-decoration: none;left: 0px">
		<span class="large button blue round">Optimize order</span>
	</a>
	</div>
</div>
