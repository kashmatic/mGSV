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
		'range': "1_100000",
		'ann': 'gene'
	},
	3: {
		'id': 'Organism_C',
		'range': "1_100000",
		'ann': 'gene'		
	}
}

/** create divs
 * 
 */
function createDivs(){
	var past = null;
	$.each(order_array, function(key, value){
		//console.log(value['id']);
		if( past != null){
			syn = past + '__SEP__' + key;
			$("<div>", { 'id': syn , 'class': 'synteny'}).appendTo("section");
		}
		$("<div>", { 'id': key , 'class': 'genome' }).appendTo("section");
		past = key;
	});
}

function genomeSettings(order){
	var arr = new Array();
	arr['id'] = order_array[order]['id'];
	var range = order_array[order]['range'].split("_")
	arr['start'] = range[0];
	arr['end'] = range[1];
	arr['ann'] = order_array[order]['ann'];
	return arr;
}

function syntenySettings(order){
	var ids = order.split("__SEP__");
	var arr = new Array();
	arr['top'] = order_array[ids[0]]['range'];
	arr['bot'] = order_array[ids[1]]['range'];
	arr['id'] = order_array[ids[0]]['id'] + "__SEP__" + order_array[ids[1]]['id']
	return arr;
}

/** Start jQuery when the page is loaded
 */
$(document).ready(function(){
	// Get the order and generate the divs to be displayed
	createDivs();
	
	// For each div in the visualization page
	$.each($("section").children("div"), function(key,value){
		// Get the id of the div
		var id = $(value).attr('id');
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
});

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