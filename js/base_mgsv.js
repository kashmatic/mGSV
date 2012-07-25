/** Start jQuery when the page is loaded
 */
$(document).ready(function(){
	// Get the session id.
	session_id = $('#session_id').val();
	//console.log(session_id);
	// Get the array order to be displayed. 
	getOrderArray(session_id);
	$('div#info_bar > span').html('Organisms are arranged as provided in the uploaded synteny file. To avoid overcrowding, try alternate view.');
});

var order_array = new Array;
var template_array;
var session_id;
var genome = 70;
var synteny = 100;
var sheight;

/** Ajax request to get the list of organism
 * along with the range.
 */
function getOrderArray(session_id){
		//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/base_order.php',
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
		order_array[i]['pos_start'] = (i * genome) + (i * synteny);
		order_array[i]['pos_end'] = order_array[i]['pos_start'] + genome;
		//console.log(order_array);
	};
}

/** 
 * list of functions to be called to update the display
 */
function pageDisplay(){
	//* create the divs
	createDivs();
	drawImage();
	setSelect();
	graph_or_default();
}

function getNumber(org){
	n = 0;
	$.each(template_array, function(key, value){
		//console.log(org, value['id'], key);
		if(org == value['id']){
			n = key;
		}
	});
	return n;
}

function getOrderNumber(org){
	n = 0;
	$.each(order_array, function(key, value){
		//console.log(org, value['id'], key);
		if(org == value['id']){
			n = key;
		}
	});
	return n;
}

/** 
 * create divs
 */
function createDivs00(){
	//Before creating the table.
	$('table').empty();
	// past is null for the synteny
	var past = null;
	// initialize the divs
	sheight = order_array[order_array.length - 1]['pos_end'];
	var control_div = '<div id="CONTROL" style="height:' + sheight + 'px; overflow:auto" class="control rounded light"></div>';
	var visual_div = '<div id="SYNTENY" style="height:' + sheight + 'px" class="synteny" ></div>';
	var html = "<tr><td class='tdcontrol'>" + control_div + "</td><td>" + visual_div + "</td></tr>";
	$('table').html(html);
	console.log(order_array[order_array.length - 1]['pos_end'] + genome);
}

function createDivs(){
  //Before creating the table.
  $('table').empty();
  // past is null for the synteny
  var past = null;
  // initialize the divs
  var control_div = '';
  var visual_div = '';
  sheight = order_array[order_array.length - 1]['pos_end'];
  // For each item in the order
  $.each(order_array, function(key, value){
    // Check if there is a genome above
    if( past != null){
      syn = past + '__SEP__' + key;
      control_div += '<div id="'+ syn +'__CONTROL" style="height:99px;" ></div>';
    }
    control_div += '<div id="'+ key +'__CONTROL" class="base_control rounded" style="height:69px;padding:0px">'+ controlItems(key) + '</div>';
    // set the top genome to current
    past = key;
  });
  var visual_div = '<div id="SYNTENY" style="height:' + sheight + 'px; width:' + ($(window).width() - 250) + 'px" class="synteny"></div>';
  //var html = "<tr><td class='tdcontrol'>" + control_div + "</td><td>" + visual_div + "</td></tr>";
  $('#div_control').html(control_div);
  $('#div_synteny').html(visual_div);
  $('#control_synteny').html(controlSynteny());
  //console.log($('table').html());
  //console.log(sheight);
}

function controlItems(id){
	//var html = '<p>' + order_array[id]['id'] + '<br/>\
	var html = '<p>\
<img src="img/zoomin.png" onclick="zoomin(' + id + ')" align="top" title="Zoom in">\
<img src="img/zoomout.png" onclick="zoomout(' + id + ')" align="top" title="Zoom out">\
<img src="img/left.png" onclick="moveleft(' + id + ')" align="top" title="Move left">\
<img src="img/right.png" onclick="moveright(' + id + ')" align="top" title="Move right"> \
<img class="entire_rounded" src="img/entire.png" onclick="entire(' + id + ')" style="height:25px;width:40px; border: 2px solid #0d6dcd" align="top" title="Entire genome"> \
<br> \
<input style="width:150px" type="text" id=' + id + '_input name="range" value='+ order_array[id]['range'] + ' title="Provide coordinates">\
<img src="img/refresh.png" onclick="refresh(' + id + ')" align="top" style="width:25px" title="Set the coordinates"><br>\
</p>\
';
	return html;
}

function controlSynteny(){
	var html = '<p>Filter options<br>\
</script>\
<select id="_syn" style="background-color: white; width: 100px;" title="Column names"></select>\
<select id="_con" style="background-color: white; width: 50px;" title="Operator">\
<option>>=</option>\
<option value="=">==</option>\
<option><=</option>\
</select><br>\
<input style="width:100px;" type="text" id="_input" value="" style="width: 150px;" title="Provide coordinates">\
 <img src="img/refresh.png" onclick="refreshsyn()" align="top" title="Set filter" style="width: 25px">\
 <img src="img/XButton.png" onclick="reset_filter()" align="top" title="Remove filter" style="width: 25px">\
</p>';
	getSyntenyOptions();
	return html;
}

/**
 * Get the options that go into the synteny view
 */
function getSyntenyOptions(syn){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/base_data.php',
		data: { 
			org: syn, 
			data: 'fields',
			session_id: session_id 
		},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			$.each(data, function(key, value){
				$('#_syn').append($('<option></option>').text(value));
			});
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//console.log(arr);
}

function drawImage(filter){
	var past = 0;
	filter = typeof filter !== 'undefined' ? filter : '';
	// For each div in the visualization page
	$('#SYNTENY').empty();
	paper =  Raphael(document.getElementById("SYNTENY"), "100%", sheight);
	$.each(order_array, function(key, value){
		annotationTrack(paper, key);
	});
	var html = '';
	for(var i = 0; i < order_array.length; i++){
		for(var j = i + 1; j < order_array.length; j++){
			var synid =  i + "__SEP__" + j;
			//console.log("i " + i + " j " + j);
			html += syntenyTrack(paper, synid, filter);
		}
	}
	$("#visi_invisi").html("Display/Hide pairwise syntenic regions<br>" + html);
	$('#visi_invisi').css({'width': $(window).width() - 220});
}

function annotationTrack(paper, key){
	width = $('#SYNTENY').width();
	var arr = genomeSettings(key);
	paper.genome(
		order_array[key]['pos_start'], 
		width, 
		genome, 
		arr['start'], 
		arr['end'], 
		getScale(arr['start'], arr['end']), 
		arr['id']
	);
	//console.log(order_array[key]['pos_start'], width, genome, arr['start'], arr['end'], getScale(arr['start'], arr['end']));
}

function syntenyTrack(paper, synid, filter){
	width = $('#SYNTENY').width();
	// Get the synteny settings
	var arr = syntenySettings( synid);
	// Draw the synteny
	paper.synteny(
		paper, 
		arr['top_pos'], 
		arr['bot_pos'], 
		width, 
		arr['top'], 
		arr['bot'], 
		arr['id'], 
		filter, 
		synid, 
		session_id
	);
	//console.log(synid);
	//console.log(arr['id']);
	pair = synid.split("__SEP__");
	orgpair = arr['id'].split("__SEP__");
	//html = '<p class="assocShow" id="' + pair[0] + '__SEP__' + pair[1] + '" onclick="getChecked(\'' + pair[0] + '__SEP__' + pair[1] + '\')">' + orgpair[0] + ' <span style="color:red">vs.</span> ' + orgpair[1] + '</p>';
	html = '<span class="assocShow" id="' + pair[0] + '__SEP__' + pair[1] + '" onclick="getChecked(\'' + pair[0] + '__SEP__' + pair[1] + '\')" title="Click to Display/Hide Synteny pairs">' + orgpair[0] + ' vs. ' + orgpair[1] + '</span> ';
	if(orgpair[0] == orgpair[1]){
		return '';
	} else {
		return html;
	}
}

function getChecked(id){
	var arr = id.split('__SEP__');
	curClass = $('#'+id).attr('class');
	include = true;
	if(curClass == 'assocShow'){
		include = false;
		$('#'+id).attr({'class': 'assocHide'});
	} else {
		$('#'+id).attr({'class': 'assocShow'});
	}
	$('#SYNTENY').empty();
	paper =  Raphael(document.getElementById("SYNTENY"), "100%", sheight);
	$.each(order_array, function(key, value){
		annotationTrack(paper, key);
	});
	$('#visi_invisi > span').each(function(){
		var i = $(this).attr('id');
		setClass = $('#'+i).attr('class');
		if(setClass == 'assocHide'){
			return;
		}
		//console.log(i);
		syntenyTrack(paper, i, '');
	});
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
	// return the array
	return arr;
}

/** Get info regarding the synteny
 * 
 */
function syntenySettings(order){
	//console.log(order);
	var ids = order.split("__SEP__");
	// initialize the array
	var arr = new Array();
	// get the range of the top genome
	arr['top'] = order_array[ids[0]]['range'];
	arr['top_pos'] = order_array[ids[0]]['pos_end'];
	// get the range of the bottom genome
	arr['bot'] = order_array[ids[1]]['range'];
	arr['bot_pos'] = order_array[ids[1]]['pos_start'];
	// get the ids separated by __SEP__
	arr['id'] = order_array[ids[0]]['id'] + "__SEP__" + order_array[ids[1]]['id'];
	// return the array
	return arr;
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
	// Iterate over the order to create select menus
	$.each(order_array, function(key, value){
		var select; // empty variable
		select = $("<select onchange=getorder(this) class='genome_order' title='Re-arrange the order by Select/Insert/Delete'></select>");
		//select = $("<select class='genome_order'></select>");
		select = opt(select); // Fill the select with options
		$(select).val(value['id']); // set the selected item
		$(select).appendTo('#selectlist');// append all select menu
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
			$("<option class='option'></option").attr("value", value['id']).text(value['id']) 
		);
	});
	// Include the insert option
	$(select).append(
		$("<option class='option'></option").attr("value", 'insert').text('Insert')
	);
	// include the delete option
	$(select).append(
		$("<option class='option'></option").attr("value", 'delete').text('Delete')
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

function rearrange(){
	arr = new Array;
	$("span#select_order_check").html('');
	// Iterate over the number of select menus
	for(var index=0; index < $('#selectlist select').length; index++){
		// set the id of the select menus starting with 0
		sel = $('#selectlist select:eq(' + index + ')').val();
		if($.inArray(sel, arr) >= 0){
			$("span#select_order_check").html('Each organism should appear only once.');
			return;
		}
		arr.push(sel);
	}
	console.log(arr);
	dorder = arr.join('__ORDER__');
	console.log(dorder);
	$('#display_order').val(dorder);
	setupOrder();
	pageDisplay();
}

// Actions depending on the items selected in select menu
function getorder(item){
	// Get the value selected
	var ss = $(item).val();
	dorder = $('#display_order').val();
	arr = dorder.split('__ORDER__');
	sel = $(item).attr('id');
	if (ss == 'delete'){
		if (order_array.length < 3){
			alert("minimum two genomes are needed to draw synteny");
			setSelect();
			return;
		}
		$(item).remove();
		arr.splice(sel, 1);
		order_array.splice(sel, 1);
	}
	else if( ss == "insert"){
		next = order_array[0]['id'];
		arr.splice(sel, 0, next);
	}
	else {
		//arr[sel] = ss;
		return;
	}
	dorder = arr.join('__ORDER__');
	console.log(dorder);
	$('#display_order').val(dorder);
	setupOrder();
	//pageDisplay();
	setSelect();
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

function all_zoomin(){
	for(var i = 0; i < order_array.length; i++){
		zoomin(i);
	}
	//drawImage();
}

function all_zoomout(){
	for(var i = 0; i < order_array.length; i++){
		zoomout(i);
	}
}

function all_moveright(){
	for(var i = 0; i < order_array.length; i++){
		moveright(i);
	}
}

function all_moveleft(){
	for(var i = 0; i < order_array.length; i++){
		moveleft(i);
	}
}

function all_entire(){
	for(var i = 0; i < order_array.length; i++){
		//console.log(i);
		entire(i);
	}
}

function refreshsyn(syn){
	field = $('#_syn').val();
	con = $('#_con').val();
	value = $('#_input').val();
	if(con == ''){ return; }
	var pattern = new RegExp(/^[0-9]+$/);
	if(! pattern.test(value)){ return; }
	sel = field + '@@' + con + '@@' +value;
	console.log(sel);
	drawImage(sel);
	//filterlist(sel);
}

function filterlist(syn){
	var y = $('#'+syn+'_hide').val();
	syn += "__SYNTENY";
	syntenyTrack(syn, y);
}

function reset_filter(){
	$('#'+syn+'_hide').val('');
	$('#_input').val('');
	drawImage('');
}


function zoomin(id){
	var arr = order_array[id]['range'].split("_");
	//console.log(order_array);
	arr[0] = parseInt(arr[0]);
	arr[1] = parseInt(arr[1]);	
	var section = parseInt((arr[1] - arr[0]) / 5);
	//console.log(section);
	if(section < 500){
		return;
	}
	//console.log(id);
	arr[0] = arr[0] + (section * 2);
	arr[1] = arr[0] + section;
	//console.log(arr, section);
	order_array[id]['range'] = arr[0].toString() + "_" +  arr[1].toString();
	//console.log(order_array);
	pageDisplay();
}

function zoomout(id){
	var arr = order_array[id]['range'].split("_");
	arr[0] = parseInt(arr[0]);
	arr[1] = parseInt(arr[1]);	
	var section = parseInt(arr[1] - arr[0]);
	arr[0] = arr[0] - (section * 2);
	arr[1] = arr[1] + (section * 2);
	arr = validate_values(arr, id);
	if(booleanCheck(id, arr[0] + "_" + arr[1])){
		order_array[id]['range'] = arr[0].toString() + "_" +  arr[1].toString();
		//console.log(arr, section);
		//changeSyntenyAnnotation(id);
	}
	pageDisplay();
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

function booleanCheck(id, range){
	var bool = true;
	if (order_array[id]['range'] == range){
		bool = false;
	}
	return bool;
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
	carr = $.extend(true, [], arr);
	carr = validate_values(carr, id);
	if( carr[0] > arr[0]){
		carr[1] += Math.abs(carr[0] - arr[0]);
	}
	if(booleanCheck(id, carr[0] + "_" + carr[1])){
		console.log(carr, arr, section);
		order_array[id]['range'] = carr[0].toString() + "_" +  carr[1].toString();
		//changeSyntenyAnnotation(id);
	}
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
	carr = $.extend(true, [], arr);
	carr = validate_values(carr, id);
	if( arr[1] > carr[1]){
		carr[0] -= Math.abs(carr[1] - arr[1]);
	}
	if(booleanCheck(id, carr[0] + "_" + carr[1])){
		order_array[id]['range'] = carr[0].toString() + "_" +  carr[1].toString();
		//changeSyntenyAnnotation(id);
	}
	pageDisplay();
}

function entire(id){
	oa = order_array[id]['id'];
	ta = getFromTemplate(oa)
	//console.log(ta.range);
	if(booleanCheck(id, ta.range)){
		order_array[id]['range'] = ta.range;
		//changeSyntenyAnnotation(id);
	}
	pageDisplay();
}

function refresh(id){
	var input = $('#' + id + '_input').val();
	//console.log(input, id);
	if(validate_text(input)){
		input = input.replace(/\s/g, "");
		var arr = input.split("_")
		arr = validate_values(arr, id);
		if(booleanCheck(id, arr[0] + "_" + arr[1])){
			order_array[id]['range'] = arr[0] + "_" + arr[1];
			//changeSyntenyAnnotation(id);
		}
		//pageDisplay();
	}
	pageDisplay();
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

function graph_or_default(){
	graph = $("input#graph_value").val();
	if(graph == 1){
		$('div#info_bar > span').html('Organisms are arranged in optimized order.');
	}
	else{
		$('div#info_bar > span').html('Organisms are arranged as provided in the uploaded synteny file.');
		getOptimal();
	}
}

function getOptimal(){
		//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/base_data.php',
		data: { 
			org: '', 
			data: 'sorder',
			session_id: session_id
		},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			console.log(data);
			getHref = $('div#button_opti > a').attr('href');
			$('div#button_opti > a').attr({'href': getHref + data + '&graph=1'});
			$('div#button_opti').css({'display': 'block'});
			$('div#info_bar > span').html('Organisms are arranged as provided in the uploaded synteny file. Click "Optimize order" to rearrange the organisms.');
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//console.log(arr);
}
