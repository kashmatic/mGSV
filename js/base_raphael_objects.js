/** F: Genome
 * Requires
 * y: the y axis to draw the genome
 * start: start position of the genome
 * end: end position of the genome
 * scale: how many number of ticks we want
 */
Raphael.fn.genome = function(y, width, height, start, end, scale, org){
	//console.log(y);
	//* Create a black rectangle to denote genome
	this.rectObj = this.rect(0, y, width, height).attr({fill: '#4682B4'});
	//* Fill this rect with black color
	this.rectObj.attr({stroke: "black", fill:"#4682B4"});
	//* Math to create the ticks
	var out = 10; // length of the tick
	var ticks = this.set();
	var pixel = (end - start) / width;
	var scale_width = scale / pixel;
	var rem = start%scale;
	var first = parseInt((scale - rem) / pixel);
	var pos = (start - rem) + scale;
	
	//* Loop thru to draw the ticks
	for(var i = first; i < width; i = (i + scale_width)){
		//* call the tick function to draw tick
		this.vtick(i, (y + height - out), (y + height), pos);
		this.vtick(i, y, (y + out), pos);
		this.text(i, (y + 25), pos);
		pos = pos + scale;
	}
	this.text(width/2, y + 40, org).attr({
		'stroke':'white', 
		'fill': 'white', 
		'font-size': '18px',
		'font-family': 'Monospace',
		'opacity': 0.7
	});
	
	return this;
};

Raphael.fn.synteny = function(paper, top_pos, bot_pos, width, set1, set2, id, filter, synid, session_id){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/base_synteny.php',
		//* parameters
		data: {
				set1: set1, 
				set2: set2, 
				width: width, 
				top_pos: top_pos,
				bot_pos: bot_pos,
				id: id,
				filter: filter,
				session_id: session_id
			},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			//console.log(synid);
			var obj_arr = [];
			var obj_html = [];
			//* for each key
			$.each(data['pos'], function(key, value){
				//console.log(key);
				arr = value.split('_');
				d = paper.path("M" + arr[0] + "," + top_pos + "L" + arr[1] + "," + top_pos + "L" + arr[3] + "," + bot_pos + "L" + arr[2] + "," + bot_pos + "z");
				d.attr({
					fill: arr[4],
					opacity: 0.5
				});
				obj_arr.push(d);
			});
			$.each(data['html'], function(key, value){
				obj_html.push(value);
			});
			//console.log('total', obj_arr.length);
			
			$.each(obj_arr, function(key, value){
				//value.node.onclick = function () { value.attr("fill", "red")};
				value.mouseover(function (e) { value.attr('opacity', 1)} );
				value.mouseout(function (e) { value.attr('opacity', 0.5)} );
				value.mousedown(function (e) {
					var bbox = value.getBBox();
					$('#coord').css('display', 'block').html(obj_html[key]);
					$('#coord').css('left', e.pageX - 10);
					$('#coord').css('top', e.pageY - 10);
					$('#coord').mouseout(function(){
						$('#coord').css('display', 'none');		
					});
				});
				//console.log(value);
			});
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//group.attr({ cursor: 'pointer' });
}

/** To draw a line
 * x1,y1: start coordinate
 * x2,y2: end coordinate
 */
Raphael.fn.line = function(x1, y1, x2, y2){
	//alert("x1: "+x1+", x2: "+x2+", y1: "+y1+", y2: "+y2);
	//* Draw the line
	this.pathObj = this.path("M" + x1 + "," + y1 + "L" + x2 + "," + y2);
	return this;
}

/** To draw a tick
 * x: x position to draw the tick
 * y1, y2: y position to draw
 * text: value to be displayed
 */
Raphael.fn.vtick = function(x, y1, y2, text){
	//* create a set
	var ticks = this.set();
	//* draw line
	ticks.push(this.line(x, y1, x, y2));
	return this;
}

