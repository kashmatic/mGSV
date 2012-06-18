<ul id="tutorialtabs" class="shadetabs">
	<li><a href="#" rel="tab1" class="selected">Synteny file</a></li>
	<li><a href="#" rel="tab2">Annotation file</a></li>
	<li><a href="#" rel="tab3">Upload Synteny</a></li>
	<li><a href="#" rel="tab4">Summary page</a></li>
	<li><a href="#" rel="tab5">Pairwise page</a></li>
	<li><a href="#" rel="tab6">Multiple page</a></li>
	<!--
		<li><a href="#" rel="tab6">Video tutorial</a></li>
	-->
</ul>

<div style="border-top:1px solid gray; width:100%; overflow:auto; padding: 5px">
	<div id="tab1" class="tabcontent">
		<?php include("html/synteny_tutorial.html"); ?> 
	</div>
	<div id="tab2" class="tabcontent">
		<?php include("html/annotation_tutorial.html"); ?>
	</div>
	<div id="tab3" class="tabcontent">
		<?php include("html/upload_synteny.html"); ?>
	</div>
	<div id="tab4" class="tabcontent">
		<a href="img/summary-figure.png" target="_blank">
			<img src="img/summary-figure.png" width="1200px" border="0px">
		</a>
	</div>
	<div id="tab5" class="tabcontent">
		<a href="img/pairwise-figure.png" target="_blank">
			<img src="img/pairwise-figure.png" width="1200px" border="0px">
		</a>
	</div>
	<div id="tab6" class="tabcontent">
		<a href="img/multiple-figure.png" target="_blank">
			<img src="img/multiple-figure.png" width="1200px" border="0px">
		</a>
	</div>
	<div id="tab7" class="tabcontent">
		<br>
		The video can be downloaded <a href="GSVTutorial.m4v" target="_blank">here</a> 
		<br>
		<br>
		<iframe title="YouTube video player" width="750" height="600" src="http://www.youtube.com/embed/l5MmnVF25o0" frameborder="0" allowfullscreen></iframe>
	</div>	
</div>

<script type="text/javascript">
	var tutorial=new ddtabcontent("tutorialtabs")
	tutorial.setpersist(true)
	tutorial.setselectedClassTarget("link") //"link" or "linkparent"
	tutorial.init()
</script>

<map name="viz">
	<area alt="panleft" title="Click to Pan towards the left" shape="rect" coords="27,55,51,92">
	<area alt="rearrange" title="Click to move this element up/down" shape="rect" coords="447,8,535,38">
	<area alt="evalfilter" title="Enter an e-value that you would like to filter your results on" shape="rect" coords="152,58,280,84">
	<area alt="zoominout" title="Controls to Zoom in/out" shape="rect" coords="443,59,547,92">
	<area alt="range" title="Enter the range (start,end) that you wish to navigate to" shape="rect" coords="618,57,839,84">
	<area alt="refresh" title="Click to refresh the image" shape="rect" coords="913,14,992,38">
	<area alt="panright" title="Click to Pan towards the right" shape="rect" coords="940,58,965,91">
	<area alt="genometrack" title="Track representing the genome" shape="rect" coords="13,112,982,146">
	<area alt="gfftrack" title="Track displaying the GFF annotations aligned against the genome" shape="rect" coords="9,150,982,200">
	<area alt="querytrack" title="Track displaying the query sequences aligned against the genome" shape="rect" coords="10,204,981,235">
	<area alt="popup" title="Informative popup with links to view more information and zoom into the neighborhood" shape="rect" coords="354,238,684,319">
</map>

