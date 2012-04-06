var arr;
var syn;

function getOptions(){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/data.php',
		data: { org: '', data: 'size' },
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			arr = data;
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
	getOptions();
});

function draw(){
	var r =  Raphael(document.getElementById('canvas'), 1000, 800);
	var array = startEndAngle(arr);
	for(var key in array){
		//console.log(key);
		r.curve(array[key]['start'], array[key]['end']);
	}
	orgs = getkeys();
	for(var i = 0; i < orgs.length; i++){
		for(var j = i + 1; j < orgs.length; j++){
			console.log(orgs[i], orgs[j]);
			r.synteny(r, orgs[i], array[orgs[i]]['count'], orgs[j], array[orgs[j]]['count'], randomColor());
		}
	}
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

function drawSynteny(){
	//r.path("M420,200Q300,200 479,206 420,200");
	//r.circle(196,260,20);
	/*
	total = sum(arr);
	s = eachSection(10,total);
	e = eachSection(210,total);
	ps = position(s);
	pe = position(e);
	console.log(ps, pe);
	//r.path("M" + ps[0] + "," + ps[1] + "Q300,200 " + pe[0] + "," + pe[1] + " " + ps[0] + "," + ps[1]).attr({'stroke': 'red'});
	//r.path("M418,179Q300,200 196,260 418,179").attr({'stroke': 'red'});
	r.path(synteny(syn, total)).attr({stroke:'red', fill:'red'});
	console.log(s);
	*/
}

function synteny(syn, total){
	var top_start = position(eachSection(syn[0],total));
	var draw = "M" + top_start[0] + "," + top_start[1];
	var bot_end = position(eachSection(syn[3],total));
	draw += "Q300,200 " + bot_end[0] + "," + bot_end[1] + " " + top_start[0] + "," + top_start[1];
	var bot_start = position(eachSection(syn[2], total));
	draw += "A120,120, 0, 0, 1, "+ bot_start[0] + "," + bot_start[1];
	var top_end = position(eachSection(syn[1],total));
	draw += "Q300,200 " + top_end[0] + "," + top_end[1] + " " + bot_start[0] + "," + bot_start[1];
	draw += "A120,120, 0, 0, 1, "+ top_start[0] + "," + top_start[1];
	return draw;
}

function position(angle){
	center_x = 300;
	center_y = 200;
	inner_r = 120;
	rad = Math.PI/180;
	var x1 = center_x + inner_r * Math.cos(-angle * rad);
	var y1 = center_y + inner_r * Math.sin(-angle * rad);
	return [x1, y1];
}

Raphael.fn.curve = function(angle_start, angle_end){
	console.log("curve", angle_start, angle_end)
	center_x = 300;
	center_y = 200;
	inner_r = 120;
	outer_r = 180;
	rad = Math.PI/180;
	var x1 = center_x + outer_r * Math.cos(-angle_start * rad);
	var x2 = center_x + outer_r * Math.cos(-angle_end * rad);
	
	var y1 = center_y + outer_r * Math.sin(-angle_start * rad);
	var y2 = center_y + outer_r * Math.sin(-angle_end * rad);
	
	var xx1 = center_x + inner_r * Math.cos(-angle_start * rad);
	var xx2 = center_x + inner_r * Math.cos(-angle_end * rad);
	
	var yy1 = center_y + inner_r * Math.sin(-angle_start * rad);
	var yy2 = center_y + inner_r * Math.sin(-angle_end * rad);
	
	//console.log(xx1, yy1, x2, y2);	
	//r.path("M",x1,y1, "A", inner_r, inner_r, 0, 1, 0, x2, y2);
	//r.path("M" + x1 + "," + y1 + ", A" + outer_r + "," + outer_r + ", 0, +" + (angle_end - angle_start > 180) +", 0, "+ x2 + "," + y2);
	bool = 0;
	if (angle_end - angle_start > 180)
		bool = 1;
	start = "M" + xx1 + "," + yy1;
	line1 = "L" + x1 + "," + y1;
	outer_arc = "A" + outer_r + "," + outer_r + ", 0, " + bool + ", 0, "+ x2 + "," + y2;
	line2 = "L" + xx2 + "," + yy2;
	inner_arc = "A" + inner_r + "," + inner_r + ", 0, " + bool + ", 1, "+ xx1 + "," + yy1;
	this.path(start + line1 + outer_arc + line2 + inner_arc).attr({fill: '#808000'});
	return this;
}

Raphael.fn.synteny = function(paper, set1, set1_count, set2, set2_count, color){
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
			},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			total = sum(arr);
			$.each(data, function(key, value){
				syn = value.split('_');
				data = synteny(syn, total);
				//console.log(data);
				paper.path(data).attr({fill: color});
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

