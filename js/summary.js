var arr;
var syn;
var centerx;
var centery;
var inner_r;
var session_id;

/**
 * Try to get the information from the database
 */
function getOptions(session_id){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/data.php',
		data: { 
			org: '', 
			data: 'size',
			session_id: session_id 
		},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			arr = data;
			console.log(arr);
			draw();
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//console.log(arr);
}


function sum(arr){
	var total = 0;
	$.each(arr, function(key, value){
		total += parseInt(value);
	});
	//console.log("total", total);
	return total;
}

function eachSection(val, total){
	var s = Math.round(360*val/total);
	return s;
}

function startEndAngle(arr){
	var total = sum(arr);
	var past = 0;
	var count = 0;
	var angle_array = new Array;
	$.each(arr, function(key, value){
		current = eachSection(value, total)
		angle_array[key] = new Array;
		angle_array[key]['start'] = past;
		angle_array[key]['end'] = past + current;
		angle_array[key]['count'] = count;
		past += current;
		count += parseInt(value);
	});
	//console.log("angles", angle_array);
	return angle_array;
}

/** Start jQuery when the page is loaded
 */
$(document).ready(function(){
	// Get the session id.
	session_id = $('#session_id').val();
	getOptions(session_id);
});

function draw(){
	h = $("#canvas").height();
	w = $("#canvas").width();
	console.log(h,w);
	var r =  Raphael(document.getElementById('canvas'), w, h);
	centerx = w/2;
	centery = h/2;
	var array = startEndAngle(arr);
	for(var key in array){
		console.log(key);
		r.curve(array[key]['start'], array[key]['end'], key);
	}
	orgs = getkeys();
	//var html = '<p>';
	$('#synInfo').append('<p></p>');
	for(var i = 0; i < orgs.length; i++){
		for(var j = i + 1; j < orgs.length; j++){
			console.log(orgs[i], orgs[j]);
			r.syn(r, orgs[i], array[orgs[i]]['count'], orgs[j], array[orgs[j]]['count'], randomColor());
		}
	}
	generateOrder();
}

function generateOrder(){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/data.php',
		data: { 
			org: '', 
			data: 'order',
			session_id: session_id 
		},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			$.each(data, function(key, value){
				if(key == 'def'){
					defaultOrder(value);
				}
				else if(key == 'ran'){
					randomOrder(value);
				}
				else if(key == 'sug'){
					suggestOrder(value);
				}
			})
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//console.log(arr);
}

function defaultOrder(value){
	var html = '<form action="mgsv.php" method="POST">\
<input type="hidden" name="session_id" value="' + session_id + '">\
<input type="hidden" name="order" value="' + value + '">\
<input type="submit" class="input_button" value="Visualize">\
</form><p>';
	var oarr = value.split('__ORDER__');
	$.each(oarr, function(key, value){
		html += value + '<br>';
	});
	html += '</p>';
	showThis('def');
	$("div#default").html(html);
	//console.log(html);
}

function randomOrder(value){
	var html = '<p>';
	var oarr = value.split('__ORDER__');
	$.each(oarr, function(key, value){
		html += value + '<br>';
	});
	html += '</p><a href="mgsv.php?session_id=' + session_id + '&order=' + value + '" >Visualize</a>';
	showThis('ran');
	$("div#random").html(html);
	//console.log(html);
}

function suggestOrder(value){
	var html = '<form action="mgsv.php" method="POST">\
<input type="hidden" name="session_id" value="' + session_id + '">\
<input type="hidden" name="order" value="' + value + '">\
<input type="submit" class="input_button" value="Visualize">\
</form><p>';
	var oarr = value.split('__ORDER__');
	$.each(oarr, function(key, value){
		html += value + '<br>';
	});
	html += '</p>';
	showThis('sug');
	$("div#suggest").html(html);
	//console.log(html);
}

function randomColor(){
	var r = Math.floor(Math.random()*256);
 	var g = Math.floor(Math.random()*256);
 	var b = Math.floor(Math.random()*256);
 	color = '#'+intToHex(r)+intToHex(g)+intToHex(b);
 	return color;
}

function intToHex(n){
	n = n.toString(16);
	if( n.length < 2)
		n = "0"+n;
 	return n;
}
 
function getkeys(){
	var keys = new Array;
	for(var key in arr){
		keys.push(key);
	}
	return keys;
}

function synteny(syn, total){
	var top_start = position(eachSection(syn[0],total));
	var draw = "M" + top_start[0] + "," + top_start[1];
	var bot_end = position(eachSection(syn[3],total));
	draw += "Q" + centerx + "," + centery + " " + bot_end[0] + "," + bot_end[1] + " " + top_start[0] + "," + top_start[1];
	var bot_start = position(eachSection(syn[2], total));
	draw += "A120,120, 0, 0, 1, "+ bot_start[0] + "," + bot_start[1];
	var top_end = position(eachSection(syn[1],total));
	draw += "Q" + centerx + "," + centery + " " + top_end[0] + "," + top_end[1] + " " + bot_start[0] + "," + bot_start[1];
	draw += "A120,120, 0, 0, 1, "+ top_start[0] + "," + top_start[1];
	return draw;
}

function position(angle){
	rad = Math.PI/180;
	var x1 = centerx + inner_r * Math.cos(-angle * rad);
	var y1 = centery + inner_r * Math.sin(-angle * rad);
	return [x1, y1];
}

Raphael.fn.curve = function(angle_start, angle_end, key){
	var cur = this.set();
	//console.log("curve", angle_start, angle_end)
	outer_r = (centerx < centery) ? centerx - 50: centery - 50;
	inner_r = outer_r - 50;
	//outer_r = centerx;
	rad = Math.PI/180;
	var x1 = centerx + outer_r * Math.cos(-angle_start * rad);
	var x2 = centerx + outer_r * Math.cos(-angle_end * rad);
	
	var y1 = centery + outer_r * Math.sin(-angle_start * rad);
	var y2 = centery + outer_r * Math.sin(-angle_end * rad);
	
	var xx1 = centerx + inner_r * Math.cos(-angle_start * rad);
	var xx2 = centerx + inner_r * Math.cos(-angle_end * rad);
	
	var yy1 = centery + inner_r * Math.sin(-angle_start * rad);
	var yy2 = centery + inner_r * Math.sin(-angle_end * rad);
	
	bool = 0;
	if (angle_end - angle_start > 180)
		bool = 1;
	start = "M" + xx1 + "," + yy1;
	line1 = "L" + x1 + "," + y1;
	outer_arc = "A" + outer_r + "," + outer_r + ", 0, " + bool + ", 0, "+ x2 + "," + y2;
	line2 = "L" + xx2 + "," + yy2;
	inner_arc = "A" + inner_r + "," + inner_r + ", 0, " + bool + ", 1, "+ xx1 + "," + yy1;
	c = this.path(start + line1 + outer_arc + line2 + inner_arc).attr({fill: '#EEE8AA'});
	cur.push(c);
	ss = c.getTotalLength();
	dd = c.getPointAtLength(ss/3);
	//console.log(key);
	t = this.text(dd.x, dd.y, key);
	t.attr({"font-size": '16pt', 'font-family': 'Monospace', 'stroke': '#4682B4'});
	cur.push(t);
	//console.log(dd.x);
	return cur;
}

Raphael.fn.syn = function(paper, set1, set1_count, set2, set2_count, color){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/synteny_circle.php',
		//* parameters
		data: {
				set1: set1,
				set1_count: set1_count,
				set2: set2,
				set2_count: set2_count,
				session_id: session_id
			},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			if(data.length > 0){
				$('#synInfo > table').append( '<tr><td>' + set1 + '..' + set2 +  '</td><td>' + data.length + '</td></tr>');
			}
			var obj_list = new Array;
			total = sum(arr);
			$.each(data, function(key, value){
				syn = value.split('_');
				data = synteny(syn, total);
				//console.log(data);
				p = paper.path(data).attr({fill: color, opacity: 0.3});
				obj_list.push(p);
			});
			$.each(obj_list, function(key, value){
				//value.node.onclick = function () { value.attr("fill", "red")};
				value.mouseover(function (e) { 
					$.each(obj_list, function(key, value){
						value.attr('opacity', 1);
						value.toFront();
					});
				});
				value.mouseout(function (e) {
					$.each(obj_list, function(key, value){
						value.attr('opacity', 0.5);
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

function showThis(id){
	if(id == 'def'){
		$("span#default").css({'background-color': "#4682B4", 'color': 'white'});
		$("span#suggest").css({'background-color': 'white', 'color': 'black'});
		$("div#default").css('display', 'block');
		$("div#suggest").css('display', 'none');
	}
	else if(id == 'sug'){
		$("span#default").css({'background-color': 'white', 'color': 'black'});
		$("span#suggest").css({'background-color': "#4682B4", 'color': 'white'});
		$("div#default").css('display', 'none');
		$("div#suggest").css('display', 'block');
	}
}

