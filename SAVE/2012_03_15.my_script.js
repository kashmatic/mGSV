var order_array = {
	0: {
		'id': 'Organism_A',
		'range': "1_100000",
		'ann': 'gene'
	},
	1: {
		'id': 'Organism_B',
		'range': "1_100000",
		'ann': 'gene'
	},
	2: {
		'id': 'Organism_A',
		'range': "50000_100000",
		'ann': 'gene'
	},
	3: {
		'id': 'Organism_C',
		'range': "1_100000",
		'ann': 'gene'		
	}
}

function getOrderArray(){
		//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/order.php',
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			console.log(data);
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
}

function getID( string){
	var arr = string.split("__")
	return order_array[arr[0]]['id'];
}

function setControls(){
	$.each(order_array, function(key, value){
		$("input[id=" + key + "__CONTROL]").val( value['range']);
	});
}

function getControlDiv(string){
	var id = string + "__CONTROL";
	var div = $("<div>", { 'id': id, 'class': 'control'});
	$(div).html(controlItems(id));
	return div;
}

function controlItems(id){
	var html = '<p>';
	html += getID(id) + "<br/>";
	html += '<input type="text" id=' + id + ' name="range" value='+ id + '>';
	html += '<img src="img/refresh.png" onclick="refresh(id)">';
	html += '<img src="img/zoomin.png" onclick="zoomin(id)">';
	html += '<img src="img/zoomout.png" onclick="zoomout(id)">';
	html += '<img src="img/left.png" onclick="moveleft(id)">';
	html += '<img src="img/right.png" onclick="moveright(id)"><br>';
	html += '</p>';
	return html;
}

/** create divs
 * 
 */
function createDivs(){
	// past is null for the synteny
	var past = null;
	// create a td element for image
	var visual = $("<td>");
	// create a td element for controls
	var control = $("<td>").addClass('tdcontrol');
	// For each item in the order
	$.each(order_array, function(key, value){
		//console.log(value['id']);
		// Check if there is a genome above
		if( past != null){
			// if yes then, synteny between above and current genome
			syn = past + '__SEP__' + key;
			// create synteny div and put it in visual td
			$("<div>", { 'id': syn , 'class': 'synteny'}).appendTo(visual);
			// create control div and put it in control td
			$("<div>", { 'id': syn + "__CONTROL", 'class': 'control', 'text': 'syteny'}).appendTo(control);
		}
		// create the genome div and add it to visual td
		$("<div>", { 'id': key , 'class': 'genome' }).appendTo(visual);
		// create the control div and add it into control td
		getControlDiv(key).appendTo(control);
		// set the top genome to current
		past = key;
	});
	// create the tr element
	trow = $('<tr>');
	// Add control td element to tr
	$(control).appendTo(trow);
	// Add visual td element to tr
	$(visual).appendTo(trow);
	// add the tr element to the table
	trow.appendTo($('table'));
	//console.log($('table').html());
}

/** Get the info for each genome
 * 
 */
function genomeSettings(order){
	// initialize an array
	var arr = new Array();
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
	// return the array
	return arr;
}

/** Get info regarding the synteny
 * 
 */
function syntenySettings(order){
	// split the order which has 2 numbers separated by __ORDER__
	var ids = order.split("__SEP__");
	// initialize the array
	var arr = new Array();
	// get the range of the top genome
	arr['top'] = order_array[ids[0]]['range'];
	// get the range of the bottom genome
	arr['bot'] = order_array[ids[1]]['range'];
	// get the ids separated by __SEP__
	arr['id'] = order_array[ids[0]]['id'] + "__SEP__" + order_array[ids[1]]['id']
	// return the array
	return arr;
}

/** Start jQuery when the page is loaded
 */
$(document).ready(function(){
	// Get the order and generate the divs to be displayed
	//getOrderArray();
	createDivs();
	drawImage();
	setControls();
	setSelect();
});

function drawImage(){
	// For each div in the visualization page
	$.each($('div').get(), function(key, value){
		var id = $(value).attr('id')
		if (id.indexOf('__CONTROL') > 0 ) {
			return;
		}
		//console.log(id);
		// If this is the first div
		if ($("section").children(":first").attr('id') == id){
			// Get the genome settings
			var arr = genomeSettings(id);
			// create the Raphael object
			first =  Raphael(document.getElementById(id), "100%", "100%");
			// Draw the genome on the bottom of the canvas
			first.genome($(value).height(), $(value).width(), arr['start'], arr['end'], getScale(arr['start'], arr['end']));
			// Draw the annotation for this organism
			first.geneTrack(first, $(value).height(), $(value).width(), arr['start'], arr['end'], arr['id'], arr['ann']);
		}
		// If this is the synteny canvas
		else if (id.indexOf('SEP') > 0 ) {
			// Get the synteny settings
			var arr = syntenySettings(id);
			// Initialize the Raphael
			first =  Raphael(document.getElementById(id), "100%", "100%");
			// Draw the synteny
			first.synteny(first,$(value).height(), $(value).width(), arr['top'], arr['bot'], arr['id']);
		} 
		// If this is the last canvas
		else if ($("section").children(":last").attr('id') == id){
			// Get the genome settings
			var arr = genomeSettings(id);
			// initialize Raphael object
			first =  Raphael(document.getElementById(id), "100%", "100%");
			// Draw genome on top of the canvas
			first.genome(5, $(value).width(), arr['start'], arr['end'], getScale(arr['start'], arr['end']));
			// Draw the annotation
			first.geneTrack(first, $(value).height(), $(value).width(), arr['start'], arr['end'], arr['id'], arr['ann']);
		}
		// If genome is in the middle
		else {
			// Get genome settings
			var arr = genomeSettings(id);
			// initialize the Pahael object
			first =  Raphael(document.getElementById(id), "100%", "100%");
			// Draw genome on the top
			first.genome(5, $(value).width(), arr['start'], arr['end'], getScale(arr['start'], arr['end']));
			// Draw the annotation
			first.geneTrack(first, $(value).height(), $(value).width(), arr['start'], arr['end'], arr['id'], arr['ann']);
			// Draw genome at the bottom
			first.genome($(value).height(), $(value).width(), arr['start'], arr['end'], getScale(arr['start'], arr['end']));
		}
		//console.log($(value).attr('id'));
	})
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
	$.each(order_array, function(key, value){
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
	console.log(JSON.stringify(order_array));
	return select;
}

// To set the id of the select menu
function setSelectId(){
	// Iterate over the number of select menus
	for(var index=0; index < $('#selectlist select').length; index++){
		// set the id of the select menus starting with 0
		$('#selectlist select:eq(' + index + ')').attr('id', index)
	}
}

