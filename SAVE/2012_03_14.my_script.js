/** Start jQuery when the page is loaded
 */
$(document).ready(function(){
	var order_array = {
		'Organism_A': "2000_100000",
		'Organism_B': "1_100000",
		'Organism_C': "2000_100000"
	}
	//console.log(order_array);
	
	var past = null;
	
	$.each(order_array, function(key, value){
		//console.log(key);
		var syn = past + '__SEP__' + key;
		if( past != null){
			$("<div>", { 'id': syn , 'class': 'synteny'}).appendTo("section");
		}
		$("<div>", { 'id': key , 'class': 'genome' }).appendTo("section");
		past = key;
	});
	
	$.each($("section").children("div"), function(key,value){
		var id = $(value).attr('id');
		//console.log(id);
		if ($("section").children(":first").attr('id') == id){
			first =  Raphael(document.getElementById(id), "100%", "100%");
			var arr = order_array[id].split("_")
			var pos_start = arr[0];
			var pos_end = arr[1];
			first.genome($(value).height(), $(value).width(), pos_start, pos_end, getScale(pos_start, pos_end));
			first.geneTrack(first, $(value).height(), $(value).width(), pos_start, pos_end, id, 'gene');
		} 
		else if (id.indexOf('SEP') > 0 ) {
			first =  Raphael(document.getElementById(id), "100%", "100%");
			var arr = id.split("__SEP__");
			var set1 = order_array[arr[0]];
			var set2 = order_array[arr[1]];
			first.synteny(first,$(value).height(), $(value).width(), set1, set2, id);
		} 
		else if ($("section").children(":last").attr('id') == id){
			var arr = order_array[id].split("_")
			var pos_start = arr[0];
			var pos_end = arr[1];
			//console.log('last');
			first =  Raphael(document.getElementById(id), "100%", "100%");
			first.genome(5, $(value).width(), pos_start, pos_end, getScale(pos_start, pos_end));
			first.geneTrack(first, $(value).height(), $(value).width(), pos_start, pos_end, id, 'gene');
		} 
		else {
			var arr = order_array[id].split("_")
			var pos_start = arr[0];
			var pos_end = arr[1];
			//console.log(id);
			first =  Raphael(document.getElementById(id), "100%", "100%");
			first.genome($(value).height(), $(value).width(), pos_start, pos_end, getScale(pos_start, pos_end));
			first.genome(5, $(value).width(), pos_start, pos_end, getScale(pos_start, pos_end));
			first.geneTrack(first, $(value).height(), $(value).width(), pos_start, pos_end, id, 'gene');
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