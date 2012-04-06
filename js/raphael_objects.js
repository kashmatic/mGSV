/** F: Genome
 * Requires
 * y: the y axis to draw the genome
 * start: start position of the genome
 * end: end position of the genome
 * scale: how many number of ticks we want
 */
Raphael.fn.genome = function(height, width, start, end, scale){
	// set the thickness of the genome box
	y = height - 5;
	//* Create a black rectangle to denote genome
	this.rectObj = this.rect(0, y, width, height).attr({fill: '#4682B4'});
	//* Fill this rect with black color
	this.rectObj.attr({stroke: "#4682B4", fill:"#4682B4"});
	//* Math to create the ticks
	var out = 7; // length of the tick
	var ticks = this.set();
	var pixel = (end - start) / width;
	var scale_width = scale / pixel;
	var rem = start%scale;
	var first = parseInt((scale - rem) / pixel);
	var pos = (start - rem) + scale;
	
	//* Loop thru to draw the ticks
	for(var i = first; i < width; i = (i + scale_width)){
		//* call the tick function to draw tick
		this.vtick(i, (y - out), (y + height + out), pos);
		pos = pos + scale;
	}
	
	return this;
};

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
	//* draw text
	ticks.push(this.text( x, (y1 - 5), text));
	ticks.push(this.text( x, (y2 + 5), text));
	return this;
}

Raphael.fn.synteny = function(paper, height, width, set1, set2, id, filter, synid, session_id){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/synteny.php',
		//* parameters
		data: {
				set1: set1, 
				set2: set2, 
				width: width, 
				height: height,
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
				d = paper.path("M" + arr[0] + ",0L" + arr[1] + ",0L" + arr[3] + "," + height + "L" + arr[2] + "," + height + "z").attr({fill: arr[4], opacity: 0.5});
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

/** To draw the genes
 * paper: raphael object
 * start: position
 * end: position
 * canvas_width: pixel width of the canvas
 */
Raphael.fn.geneTrack = function(paper, height, width, start, end, org, ann, shape, setColor, session_id){
	arrowHeight = 10;
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/annotation.php',
		//* parameters
		data: {
				start: start, 
				end: end, 
				width: width,
				height: height,
				org: org,
				ann: ann,
				session_id: session_id
			},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			//* for each key
			$.each(data, function(key, value){
				var item = new Array;
				var name = new Array;
				var color;
				if(setColor != ''){ color = setColor }
				//console.log(value);
				if (ann == 'expression'){
						//* split the value at #
						var arr = value.split("#");
						if(setColor == ''){
							color = arr[4];
						}
						//* draw rectangle
						paper.rect(arr[0], arr[1], arr[2], arr[3]).attr({stroke: color, fill:color});
						//paper.text(width/2,height - 30, ann + " of " + org).attr({'font-family': 'Monospace', 'fill': '#4682B4', 'font-size': 12});
				}
				else{
					var arr = value.split("#");
					if(shape == ''){
						shape = arr[4];
					}
					if(setColor == ''){
						color = arr[5];
					}
					//console.log(key, color, arr[5]);
					switch(shape){
						case 'dashline':
							i = paper.dottedLine(parseFloat(arr[0]), parseFloat(arr[2]), parseFloat(arr[1]), parseFloat(arr[2]), arrowHeight, color);
							n = paper.text(parseFloat(arr[0]), parseFloat(arr[2]) - 15, arr[6]).attr({'font-family': 'Monospace', 'text-anchor': 'start', fill: color}).hide();
							item.push(i);
							name.push(n);				
							break;
						case 'ellipse':
							i = paper.drawEllipse(parseFloat(arr[0]), parseFloat(arr[2]), parseFloat(arr[1]), parseFloat(arr[2]), arrowHeight, color);
							n = paper.text(parseFloat(arr[0]), parseFloat(arr[2]) - 15, arr[6]).attr({'font-family': 'Monospace', 'text-anchor': 'start', fill: color}).hide();
							item.push(i);
							name.push(n);				
							break;
						case 'arrow':
							if(arr[3] == '+'){
								//* draw right sided arrow
								i = paper.rightArrow(parseFloat(arr[0]), parseFloat(arr[2]), parseFloat(arr[1]), parseFloat(arr[2]), arrowHeight, color);
								n = paper.text(parseFloat(arr[0]), parseFloat(arr[2]) - 15, arr[6]).attr({'font-family': 'Monospace', 'text-anchor': 'start', fill: color}).hide();
								item.push(i);
								name.push(n);
							} 
							else {
								//* draw left sided arrow
								i = paper.leftArrow(parseFloat(arr[0]), parseFloat(arr[2]), parseFloat(arr[1]), parseFloat(arr[2]), arrowHeight, color);
								n = paper.text(parseFloat(arr[0]), parseFloat(arr[2]) - 15, arr[6]).attr({'font-family': 'Monospace', 'text-anchor': 'start', fill: color}).hide();
								item.push(i);
								name.push(n);
							}
							break;
						case 'christmasarrow':
							if(arr[3] == '+'){
								i = paper.christmasRightArrow(parseFloat(arr[0]), parseFloat(arr[2]), parseFloat(arr[1]), parseFloat(arr[2]), arrowHeight, color);
								n = paper.text(parseFloat(arr[0]), parseFloat(arr[2]) - 15, arr[6]).attr({'font-family': 'Monospace', 'text-anchor': 'start', fill: color}).hide();
								item.push(i);
								name.push(n);
							}
							else {
								i = paper.christmasLeftArrow(parseFloat(arr[0]), parseFloat(arr[2]), parseFloat(arr[1]), parseFloat(arr[2]), arrowHeight, color);
								n = paper.text(parseFloat(arr[0]), parseFloat(arr[2]) - 15, arr[6]).attr({'font-family': 'Monospace', 'text-anchor': 'start', fill: color}).hide();
								item.push(i);
								name.push(n);
							}
							break;
						case 'box':
							i = paper.box(parseFloat(arr[0]), parseFloat(arr[2]), parseFloat(arr[1]), parseFloat(arr[2]), arrowHeight, color);
							n = paper.text(parseFloat(arr[0]), parseFloat(arr[2]) - 15, arr[6]).attr({'font-family': 'Monospace', 'text-anchor': 'start', fill: color}).hide();
							item.push(i);
							name.push(n);							
							break;
					} // switch	
				} // else
				$.each(item, function(k, v){
					v.mouseover(function (e) { name[k].show() });
					v.mouseout(function (e) { name[k].hide() });
				});
				//paper.text(width/2,height - 30, ann + " oft " + org).attr({'font-family': 'Monospace', 'fill': '#4682B4', 'font-size': 12});
			});
			if(ann == ''){
				paper.text(width/2,height - 30, org).attr({'font-weight': 'bold', 'font-family': 'Monospace', 'fill': '#4682B4', 'font-size': 12});
			}
			else {
				paper.text(width/2,height - 30, ann + " of " + org).attr({'font-weight': 'bold', 'font-family': 'Monospace', 'fill': '#4682B4', 'font-size': 12});
			}
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
}

Raphael.fn.drawEllipse = function(x1, y1, x2, y2, size, color){
	item = this.ellipse( x1+((x2-x1)/2), y1, (x2-x1)/2, 3).attr({stroke: color, fill: color});
	return item;
}


Raphael.fn.dottedLine = function(x1, y1, x2, y2, size, color){
	item = this.path("M" + x1 + "," + y1 + "L" + x2 + "," + y2).attr({stroke: color, 'stroke-dasharray': "-", 'stroke-width': 2});
	return item;
}

Raphael.fn.box = function(x1, y1, x2, y2, size, color){
	item = this.rect(x1, y1, x2-x1, size).attr({stroke: color, fill: color});
	return item;
}

/** To draw a left sided arrow
 * x1, y1: start position
 * x2, y2: end position
 * size: size of the arrow
 */
Raphael.fn.leftArrow = function(x1, y1, x2, y2, size, color){
	//console.log("x1: "+x1+", x2: "+x2+", y1: "+y1+", y2: "+y2);
	//* upper half of the arrow
	var half = size / 2;
	//* width of the arrowhead
	var topX = x1 + size;
	//* for smaller arrows, arrowhead is 40% of the size
	if (topX >= x2 - 10)
		topX = x1 + (0.4 * (x2-x1));
	//* Draw the arrow
	item = this.path("M" + x1 + "," + y1 + "L" + topX + "," + (y1 - size) + "L" + topX + "," + (y1 - half) + "L" + x2 + "," + (y2 - half) + "L" + x2 + "," + (y2 + half) + "L" + topX + "," + (y1 + half) + "L" + topX + "," + (y1 + size) + "z").attr({stroke: color, fill: color}); 
	return item;
}

/** To draw a right sided arrow
 * x1, y1: start position
 * x2, y2: end position
 * size: size of the arrow
 */
Raphael.fn.rightArrow = function(x1, y1, x2, y2, size, color){
	//* upper half of the arrow
	var half = size / 2;
	//* width of the arrowhead
	var topX = x2 - size;
	//* for smaller arrows, arrowhead is 40% of the size
	if (topX <= x1 + 10)
		topX = x2 - (0.4 * (x2-x1));
	//* Draw the arrow
	item = this.path("M" + x2 + "," + y2 + "L" + topX + "," + (y1 - size) + "L" + topX + "," + (y1 - half) + "L" + x1 + "," + (y2 - half) + "L" + x1 + "," + (y2 + half) + "L" + topX + "," + (y1 + half) + "L" + topX + "," + (y1 + size) + "z").attr({stroke: color, fill: color});
	return item;
}

/** To draw a right sided arrow
 * x1, y1: start position
 * x2, y2: end position
 * size: size of the arrow
 */
Raphael.fn.christmasRightArrow = function(x1, y1, x2, y2, size, color){
	var item = this.set();
	//* upper half of the arrow
	var half = size / 2;
	//* width of the arrowhead
	var topX = x2 - size;
	//* for smaller arrows, arrowhead is 40% of the size
	if (topX <= x1 + 10){
		topX = x2 - (0.4 * (x2-x1));
	} 
	if((x2-x1) > 6) {
		item.push(this.circle(x1 + 3, y1, 3).attr({stroke: color, fill: color}));
	}
	//* Draw the arrow
	item.push(this.path("M" + x2 + "," + y2 + "L" + topX + "," + (y1 - size)).attr({stroke: color}));
	item.push(this.path("M" + x2 + "," + y2 + "L" + topX + "," + (y1 + size)).attr({stroke: color}));
	item.push(this.path("M" + x2 + "," + y2 + "L" + x1 + "," + y1).attr({stroke: color}));
	return item;
}

Raphael.fn.christmasLeftArrow = function(x1, y1, x2, y2, size, color){
	var item = this.set();
	//console.log("x1: "+x1+", x2: "+x2+", y1: "+y1+", y2: "+y2);
	//* upper half of the arrow
	var half = size / 2;
	//* width of the arrowhead
	var topX = x1 + size;
	//* for smaller arrows, arrowhead is 40% of the size
	if (topX >= x2 - 10)
		topX = x1 + (0.4 * (x2-x1));
	//* Draw the arrow
	item.push(this.path("M" + x1 + "," + y1 + "L" + topX + "," + (y1 - size)).attr({stroke: color}));
	item.push(this.path("M" + x1 + "," + y1 + "L" + topX + "," + (y1 + size)).attr({stroke: color}));
	item.push(this.path("M" + x2 + "," + y2 + "L" + x1 + "," + y1).attr({stroke: color}));
	if((x2-x1) > 6) {
		item.push(this.circle(x2 - 3, y2, 3).attr({stroke: color, fill: color}));
	}
	return item;
}
