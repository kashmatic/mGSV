var order_array = new Array;
var template_array;
var session_id;

/** Ajax request to get the list of organism
 * along with the range.
 */
function getOrderArray(session_id){
		//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/order.php',
		//* session_id required
		data: {
			session_id: session_id
		},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			//* assign the summary to template array
			template_array = data;
			//* determine the display array
			setupOrder();
			//* generate the display page
			pageDisplay();
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
}

/** 
 * list of functions to be called to update the display
 */
function pageDisplay(){
	//* create the divs
	createDivs();
	drawImage();
	setSelect();
}

/** Use the order from the browser to generate the display array
 * 
 */
function setupOrder(){
	//* hidden value in the php
	dorder = $('#display_order').val();
	//* split the string
	dorder_array = dorder.split("__ORDER__");
	//* For each value in the string
	for(i = 0; i < dorder_array.length; i++){
		//* clone the value from template array
		var se = $.extend(true, [], template_array);
		//* add that value to the display array
		j = getNumber(dorder_array[i]);
		order_array[i] = se[j];
		//console.log(j);
	};
}

function getNumber(org){
	n = 0;
	$.each(template_array, function(key, value){
		if(org ==  value['id']){
			//console.log(org, value['id'], key);
			n = key;
		}
	});
	return n;
}

function getID( string){
	var arr = string.split("__")
	return order_array[arr[0]]['id'];
}

function setControls(){
	$.each(order_array, function(key, value){
		$("input[id=" + key + "__CONTROL]").val( value['range']);
		$("select[id=" + key + "_ann]").val( value['ann']);
		$("select[id=" + key + "_shape]").val( value['ann_shape']);
		$("select[id=" + key + "_color]").val( value['ann_color']);
	});
}

function getControlDiv(string){
	var id = string + "__CONTROL";
	var div = $("<div>", { 'id': id, 'class': 'control'});
	$(div).html(controlItems(id));
	return div;
}

function controlSynteny(syn){
	var arr = syn.split("__SEP__");
	//console.log(arr[0]);
	//var html = '<p>Synteny between<br>' + order_array[arr[0]]['id'] + ' &<br>' + order_array[arr[1]]['id'] + '<br>\
	var html = '<p>\
<!-- <span id="' + syn + '_testing">Filter </span>  <img src="img/filter.png"><br> -->\
<span id="' + syn + '_info"></span><br>\
<script type="text/javascript">\
$("span#' + syn + '_testing").mouseover(function(){\
	$("div#' + syn + '_popup").css("display", "block");\
});\
$("span#' + syn + '_testing").mouseout(function(){\
	$("div#' + syn + '_popup").css("display", "none");\
});\
$("div.popupsynteny").mouseover(function(){\
	$(this).css("display", "block");\
});\
$("div.popupsynteny").mouseout(function(){\
	$(this).css("display", "none");\
});\
</script>\
<div>\
<select id="' + syn + '_syn"></select>\
<select id="' + syn + '_con">\
<option>>=</option>\
<option>==</option>\
<option><=</option>\
</select>\
<input style="width:100px;" type="text" id=' + syn + '_input value="">\
<img src="img/refresh.png" onclick="refreshsyn(\''+ syn +'\')">\
<input type=hidden id="' + syn + '_hide" value=""/><br>\
<span id="' + syn + '_popup_span"></span>\
</div>\
<div id="' + syn + '_popup" class="popupsynteny rounded">\
<select id="' + syn + '_syn"></select>\
<select id="' + syn + '_con">\
<option disabled value="" selected>filter by</option>\
<option><=</option>\
<option>==</option>\
<option>>=</option>\
</select>\
<input style="width:100px;" type="text" id=' + syn + '_input value=""><br>\
<img src="img/refresh.png" onclick="refreshsyn(\''+ syn +'\')">\
<input type=hidden id="' + syn + '_hide" value=""/><br>\
<span id="' + syn + '_popup_span"></span>\
</div>\
</p>';
	getSyntenyOptions(syn);
	return html;
}

function controlItems(id){
	var html = '<p>' + order_array[id]['id'] + '<br/>\
<input type="text" id=' + id + '_input name="range" value='+ order_array[id]['range'] + '>\
<img src="img/refresh.png" onclick="refresh(' + id + ')">\
<img src="img/zoomin.png" onclick="zoomin(' + id + ')">\
<img src="img/zoomout.png" onclick="zoomout(' + id + ')">\
<img src="img/left.png" onclick="moveleft(' + id + ')">\
<img src="img/right.png" onclick="moveright(' + id + ')">\
<img src="img/add.png" id="' + id + '_testing"><br>\
<script type="text/javascript">\
$("img#' + id + '_testing").mouseover(function(){\
	$("div#' + id + '_popup").css("display", "block");\
});\
$("img#' + id + '_testing").mouseout(function(){\
	$("div#' + id + '_popup").css("display", "none");\
});\
$("div.popup").mouseover(function(){\
	$(this).css("display", "block");\
});\
$("div.popup").mouseout(function(){\
	$(this).css("display", "none");\
});\
</script>\
<div id="' + id + '_popup" class="popup rounded">\
	<select id="' + id + '_ann" onchange="selectAnn(' + id + ')"></select>\
	<select id="' + id + '_shape" onchange="select(' + id + ')">\
		<option disabled value="" selected>Shape</option>\
		<option>arrow</option>\
		<option>box</option>\
		<option>christmasarrow</option>\
		<option>dashline</option>\
		<option>ellipse</option>\
	</select>\
	<select id="' + id + '_color" onchange="select(' + id + ')">\
		<option disabled value="" selected>Color</option>\
		<option>red</option>\
		<option>blue</option>\
		<option>green</option>\
		<option>black</option>\
		<option>gainsboro</option>\
	</select>\
</div>\
</p>\
';
	getOptions(id);
	return html;
}

/**
 * Get the options that go into the synteny view
 */
function getSyntenyOptions(syn){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/data.php',
		data: { 
			org: syn, 
			data: 'synteny',
			session_id: session_id 
		},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			$.each(data, function(key, value){
				$('#'+syn+'_syn').append($('<option></option>').text(value));
			});
			setControls();
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//console.log(arr);
}

function getOptions(id){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/data.php',
		data: { 
			org: order_array[id]['id'], 
			data: 'annotation',
			session_id: session_id
		},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			if(data.length > 0){
				$.each(data, function(key, value){
					$('#'+id+'_ann').append($('<option></option>').text(value));
				});
				setControls();
			} else {
				$("div#" + id + "_popup").remove();
			}
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//console.log(arr);
}


/** 
 * create divs
 */
function createDivs(){
	//Before creating the table.
	$('table').empty();
	// past is null for the synteny
	var past = null;
	// initialize the divs
	var control_div = '';
	var visual_div = '';
	// For each item in the order
	$.each(order_array, function(key, value){
		//console.log(value['id']);
		// Check if there is a genome above
		if( past != null){
			// if yes then, synteny between above and current genome
			syn = past + '__SEP__' + key;
			// create synteny div and put it in visual td
			//$("<div>", { 'id': syn , 'class': 'synteny'}).appendTo(visual);
			visual_div += '<div id="' + syn + '__SYNTENY" class="synteny"></div>';
			// create control div and put it in control td
			//$("<div>", { 'id': syn + "__CONTROL", 'class': 'control', 'text': 'syteny'}).appendTo(control);
			control_div += '<div id="'+ syn +'__CONTROL" class="control rounded light">' + controlSynteny(syn) + '</div>';
		}
		// create the genome div and add it to visual td
		//$("<div>", { 'id': key , 'class': 'genome'}).appendTo(visual);
		visual_div += '<div id="' + key + '__GENOME" class="genome"></div>';
		// create the control div and add it into control td
		//getControlDiv(key).appendTo(control);
		control_div += '<div id="'+ key +'__CONTROL" class="control rounded">'+ controlItems(key) + '</div>';
		// set the top genome to current
		past = key;
	});
	var html = "<tr><td class='tdcontrol'>" + control_div + "</td><td>" + visual_div + "</td></tr>";
	$('table').html(html); 
	//console.log($('table').html());
}

/** Get the info for each genome
 * 
 */
function genomeSettings(string){
	//console.log(order);
	// initialize an array
	var arr = new Array();
	o = string.split("__");
	order = o[0];
	//console.log(order_array, order, string);
	// id is the name of the genome
	arr['id'] = order_array[order]['id'];
	// split the range
	var range = order_array[order]['range'].split("_")
	// set the start
	arr['start'] = range[0];
	// set the end
	arr['end'] = range[1];
	// set the annotation to be displayed
	arr['ann'] = order_array[order]['ann'];
	arr['ann_shape'] = order_array[order]['ann_shape'];
	arr['ann_color'] = order_array[order]['ann_color'];
	// return the array
	return arr;
}

/** Get info regarding the synteny
 * 
 */
function syntenySettings(order){
	// split the order which has 2 numbers separated by __ORDER__
	var ids = order.split("__");
	// initialize the array
	var arr = new Array();
	// get the range of the top genome
	arr['top'] = order_array[ids[0]]['range'];
	// get the range of the bottom genome
	arr['bot'] = order_array[ids[2]]['range'];
	// get the ids separated by __SEP__
	arr['id'] = order_array[ids[0]]['id'] + "__SEP__" + order_array[ids[2]]['id'];
	// return the array
	return arr;
}

/** Start jQuery when the page is loaded
 */
$(document).ready(function(){
	// Get the session id.
	session_id = $('#session_id').val();
	// Get the array order to be displayed. 
	getOrderArray(session_id);
});

function drawImage(){
	var past = 0;
	// For each div in the visualization page
	$.each(order_array, function(key, value){
		past = parseInt(key) - 1; 
		if(past >= 0){
			var synid = past + "__SEP__" + key + "__SYNTENY";
			syntenyTrack(synid, '');
		}
		annotationTrack(key);
	});
}

function syntenyTrack(synid, filter){
	$('#' + synid).empty();
	height = $('#' + synid).height();
	width = $('#' + synid).width();
	//console.log(synid, height, width);
	// Get the synteny settings
	var arr = syntenySettings( synid);
	// Initialize the Raphael
	first =  Raphael(document.getElementById(synid), "100%", "100%");
	// Draw the synteny
	first.synteny(first, height, width, arr['top'], arr['bot'], arr['id'], filter, synid, session_id);
}

function annotationTrack(key){
	var id = $("#" + key + "__GENOME").attr('id');
	$('#' + id).empty();
	var width = $('#' + id).width();
	var height = $('#' + id).height();
	//console.log(key, id, height, width);
	// Get genome settings
	var arr = genomeSettings(id);
	// initialize the Pahael object
	first =  Raphael(document.getElementById(id), "100%", "100%");
	//For the black boxes on either side.
	first.rect(0,0,5,height).attr({fill: "#4682B4", stroke: "#4682B4"});
	first.rect(width-5,0,5,height).attr({fill: "#4682B4", stroke: "#4682B4"});
	// Draw genome on the top
	first.genome(5, width, arr['start'], arr['end'], getScale(arr['start'], arr['end']));
	// Draw genome at the bottom
	first.genome(height, width, arr['start'], arr['end'], getScale(arr['start'], arr['end']));
	// Draw the annotation
	first.geneTrack(first, height, width, arr['start'], arr['end'], arr['id'], arr['ann'], arr['ann_shape'], arr['ann_color'], session_id);
	//console.log(arr);
}


/** Determine the scale 
 */
function getScale(start, end){
	// Initialize the multiplier
	var i = 10;
	// Initialize the scale 
	var scale = i;
	// get the range
	var range = end - start;
	// Loop till
	while(true){
		// number of ticks to be drawn
		var ticks = range / scale;
		// is less then 20
		if (ticks <= 20){
			// then break
			break;
		}
		// if not increase the scale to the multiple of i
		scale = scale * i;
	}
	// return the scale
	return scale;
}


// Set the select menu
function setSelect(){
	// Clear all the select menus
	$("#selectlist").empty();
	// initialize a select item
	var select_template = $("<select onchange=getorder(this)></select>");
	// Iterate over the order to create select menus
	$.each(order_array, function(key, value){
		var select; // empty variable
		select = select_template.clone() // clone ok
		select = opt(select); // Fill the select with options
		$(select).val(value['id']); // set the selected item
		$('#selectlist').append(select); // append all select menu
	});
	// set the ids of the select menu
	setSelectId();
}

// To fill the select with options
function opt(select){
	// Iterate over the fixed array of items
	$.each(template_array, function(key, value){
		// append the options to select
		$(select).append(
			$("<option></option").attr("value", value['id']).text(value['id']) 
		);
	});
	// Include the insert option
	$(select).append(
		$("<option></option").attr("value", 'insert').text('Insert')
	);
	// include the delete option
	$(select).append(
		$("<option></option").attr("value", 'delete').text('Delete')
	);
	// return the options;
	return select;
}

// To set the id of the select menu
function setSelectId(){
	// Iterate over the number of select menus
	for(var index=0; index < $('#selectlist select').length; index++){
		// set the id of the select menus starting with 0
		$('#selectlist select:eq(' + index + ')').attr('id', index )
	}
}


// Actions depending on the items selected in select menu
function getorder(item){
	// Get the value selected
	var ss = $(item).val();
	//console.log(ss);
	// Get the id of the select menu
	sel = $(item).attr('id');
	//console.log(sel);
	//console.log(order_array);
	// If user selected 'delete'
	if (ss == 'delete'){
		// To make sure there is at least 2 organisms present
		console.log(order_array.length);
		if (order_array.length < 3){
			alert("minimum two genomes are needed to draw synteny");
			setSelect();
			return;
		}
		// Remove the select menu
		$(item).remove();
		// Remove the item from the order
		order_array.splice(sel, 1)
	} 
	// If user select insert
	else if( ss == "insert"){
		next = order_array[0]['id'];
		// Insert the first item in the arr
		order_array.splice(sel, 0, getFromTemplate(order_array[0]['id']));
	} 
	else {
		// Replace the selected value with current value 
		order_array.splice(sel, 1, getFromTemplate(ss));
	}
	//console.log(order_array);
	pageDisplay();
	// Update the order
	//updateArray();
	// Reset the select menus
	//setSelect();
}

function getFromTemplate(org){
	var ret;
	$.each(template_array, function(key, value){
		if (value['id'] == org){
			ret = value;
		}
	});
	return ret;
}

function refresh(id){
	var input = $('#' + id + '_input').val();
	console.log(input, id);
	if(validate_text(input)){
		input = input.replace(/\s/g, "");
		var arr = input.split("_")
		arr = validate_values(arr, id);
		order_array[id]['range'] = arr[0] + "_" + arr[1];
		pageDisplay();
	}
}

function validate_values(arr, id){
	org = order_array[id]['id'];
	full = getFromTemplate(org);
	check = full['range'].split('_');
	//console.log(arr);
	if(arr[0] < check[0]){
		arr[0] = check[0];
	}
	if(arr[1] > check[1]){
		arr[1] = check[1];
	}
	//console.log(arr);
	return arr;
}

function validate_text(string){
	var bool = true;
	var pattern = new RegExp(/^[0-9]+_[0-9]+$/);
	bool = pattern.test(string);
	var arr = string.split("_");
	if( parseInt(arr[1]) < parseInt(arr[0])){
		bool = false;
	}
	//console.log(bool);
	return bool;
}

function zoomin(id){
	var arr = order_array[id]['range'].split("_");
	arr[0] = parseInt(arr[0]);
	arr[1] = parseInt(arr[1]);	
	var section = parseInt((arr[1] - arr[0]) / 5);
	if(section < 500){
		return;
	}
	arr[0] = arr[0] + (section * 2);
	arr[1] = arr[0] + section;
	console.log(arr, section);
	order_array[id]['range'] = arr[0].toString() + "_" +  arr[1].toString();
	pageDisplay();
}

function zoomout(id){
	var arr = order_array[id]['range'].split("_");
	arr[0] = parseInt(arr[0]);
	arr[1] = parseInt(arr[1]);	
	var section = parseInt((arr[1] - arr[0]) / 5);
	arr[0] = arr[0] - (section * 2);
	arr[1] = arr[1] + section;
	arr = validate_values(arr, id);
	order_array[id]['range'] = arr[0].toString() + "_" +  arr[1].toString();
	pageDisplay();
}

function moveleft(id){
	var arr = order_array[id]['range'].split("_");
	arr[0] = parseInt(arr[0]);
	arr[1] = parseInt(arr[1]);	
	var section = parseInt((arr[1] - arr[0]) / 5);
	if(section < 500){
		return;
	}
	arr[0] = arr[0] - (section * 2);
	arr[1] = arr[1] - (section * 2);
	arr = validate_values(arr, id);
	if((arr[1] - arr[0]) < section){
		return;
	}
	console.log(arr, section);
	order_array[id]['range'] = arr[0].toString() + "_" +  arr[1].toString();
	pageDisplay();
}

function moveright(id){
	var arr = order_array[id]['range'].split("_");
	arr[0] = parseInt(arr[0]);
	arr[1] = parseInt(arr[1]);	
	var section = parseInt((arr[1] - arr[0]) / 5);
	if(section < 500){
		return;
	}
	arr[0] = arr[0] + (section * 2);
	arr[1] = arr[1] + (section * 2);
	arr = validate_values(arr, id);
	if((arr[1] - arr[0]) < section){
		return;
	}
	order_array[id]['range'] = arr[0].toString() + "_" +  arr[1].toString();
	pageDisplay();
}

function select(id){
	shape = $('#'+id+'_shape').val();
	color = $('#'+id+'_color').val();
	order_array[id]['ann_shape'] = shape;
	order_array[id]['ann_color'] = color;
	annotationTrack(id);
}

function selectAnn(id){
	ann = $('#'+id+'_ann').val();
	order_array[id]['ann'] = ann;
	order_array[id]['ann_shape'] = '';
	order_array[id]['ann_color'] = '';
	shape = $('#'+id+'_shape').val('');
	shape = $('#'+id+'_color').val('');
	annotationTrack(id);
}

function refreshsyn(syn){
	field = $('#'+syn+'_syn').val();
	con = $('#'+syn+'_con').val();
	value = $('#'+syn+'_input').val();
	if(con == ''){ return; }
	var pattern = new RegExp(/^[0-9]+$/);
	if(! pattern.test(value)){ return; }
	sel = field + '@@' + con + '@@' +value;
	if($('#'+syn+'_hide').val() != ''){
		sel = $('#'+syn+'_hide').val() + '@@@' + sel;
	}
	$('#'+syn+'_hide').val(sel);
	console.log(pattern.test(value));
	//console.log($('#'+syn+'_hide').val());
	filterlist(syn);
}

function filterlist(syn){
	var y = $('#'+syn+'_hide').val();
	if(y != ''){
		$('#'+ syn + '_popup_span').empty();
		var arr = y.split('@@@');
		$.each(arr, function(key, value){
			v = value.replace(/@@/ig, " ");
			//console.log(v);
			$('#'+ syn + '_popup_span').append("<br><span id='removespan' onclick='remove(\""+ syn + "\",\"" + value +"\")'>" + v + " <span style='color:red;'>remove</span><span>");
		});
	} else {
		$('#'+ syn + '_popup_span').empty();
	}
	syn += "__SYNTENY";
	//console.log("filterlist y - " + y);
	syntenyTrack(syn, y);
}

function remove(syn, term){
	var y = $('#'+syn+'_hide').val();
	var arr = y.split('@@@');
	var removeItem = term;
	//console.log(arr.length);
	arr = jQuery.grep(arr, function(value) {
		return value != removeItem;
	});
	if(arr.length == 0){
		$('#'+syn+'_hide').val('');
	} else if (arr.length == 1){
		$('#'+syn+'_hide').val(arr[0]);
	} else {
		$('#'+syn+'_hide').val(arr.join('@@@'));
	}
	filterlist(syn);
}
