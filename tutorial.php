<!DOCTYPE HTML>
<html>
<head>
	<title>mGSV :: Tutorial</title>
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
	<meta charset="UTF-8">

	<link rel="stylesheet" href="css/homepage_tutorial.css" />
	<style type="text/css">
		fieldset{width:718px;border:1px #B0C0D1 solid;padding:40px;}
		legend{background:#B0C0D1;padding:4px 10px;color:#000000;}
	</style>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>


	<script type="text/javascript" src="js/tabcontent.js"></script>
	<link rel="stylesheet" type="text/css" href="css/tabcontent.css">

	<!-- start jquery slideshow plugin for tutorial section -->

	<script type="text/javascript">
		$(document).ready(function(){
			var currentPosition = 0;
			var slideWidth = 1380;
			var slides = $('.slide');
			var numberOfSlides = slides.length;
			
			// Remove scrollbar in JS
			$('#slidesContainer').css('overflow', 'hidden');
			// Wrap all .slides with #slideInner div
			slides
			.wrapAll('<div id="slideInner"></div>')
			// Float left to display horizontally, readjust .slides width
			.css({
			'float' : 'left',
			'width' : slideWidth
		});
		
		// Set #slideInner width equal to total width of all slides
		$('#slideInner').css('width', slideWidth * numberOfSlides);
		
		// Insert controls in the DOM
		$('#slideshow')
		.prepend('<span class="control" id="leftControl">Clicking moves left</span>')
		.append('<span class="control" id="rightControl">Clicking moves right</span>');
		
		// Hide left arrow control on first load
		manageControls(currentPosition);
		
		// Create event listeners for .controls clicks
		$('.control')
		.bind('click', function(){
			// Determine new position
			currentPosition = ($(this).attr('id')=='rightControl') ? currentPosition+1 : currentPosition-1;
			// Hide / show controls
			manageControls(currentPosition);
			// Move slideInner using margin-left
			$('#slideInner').animate({
				'marginLeft' : slideWidth*(-currentPosition)
			});
		});
	
		// manageControls: Hides and Shows controls depending on currentPosition
		function manageControls(position){
			// Hide left arrow if position is first slide
			if(position==0){ $('#leftControl').hide() } else{ $('#leftControl').show() }
			// Hide right arrow if position is last slide
			if(position==numberOfSlides-1){ $('#rightControl').hide() } else{ $('#rightControl').show() }
		}	
		});
	</script>

	<!-- end jquery slideshow plugin for tutorial section -->
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
	
	<script type="text/javascript">
		$(document).ready(function() {
		/*
		*  Tuturial fancybox effects
		*/
		$("#SynCon").fancybox({
			'width' : '75%',
			'height' : '75%',
			'autoScale' : false,
			'transitionIn' : 'none',
			'transitionOut' : 'none',
			'type' : 'iframe'
		});
		$("#AnnCon").fancybox({
			'width' : '75%',
			'height' : '75%',
			'autoScale' : false,
			'transitionIn' : 'none',
			'transitionOut' : 'none',
			'type' : 'iframe'
		});
		$("#Upd_Img").fancybox({
			'width' : '75%',
			'height' : '75%',
			'autoScale' : false,
			'transitionIn' : 'none',
			'transitionOut' : 'none',
			'type' : 'iframe'
		});
		$("#Viz_Img").fancybox({
			'width' : '75%',
			'height' : '75%',
			'autoScale' : false,
			'transitionIn' : 'none',
			'transitionOut' : 'none',
			'type' : 'iframe'
		});
		});
	</script>
</head>
<body>
	<div id="page">
		<? include ('lib/header.php') ?>
		<div id="tutorial" class="section" style="padding:80px">
			<fieldset style="width: 70%">
			<legend>Step-by-Step Tutorial</legend>
			
			<div class="section-content">
				<?php include("lib/mtutorial.php"); ?>	
			</div>
			</fieldset>
			<? include ('lib/footer.php') ?>
		</div><!-- end .section -->
</body>
</html>
