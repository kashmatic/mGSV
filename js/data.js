function a(org){
	//* Use ajax to get JSON object
	$.ajax({
		//* file
		url: 'lib/data.php',
		//* parameters
		data: {
				org: org
			},
		//* datatype returned
		dataType: 'json',
		//* request method
		method: 'GET',
		//* If success
		success: function(data){
			var arr = new Array;
			$.each(data, function(key, value){
				console.log(value);
				arr.push(value);
			});
			return arr;
		},
		//* If error. show the error in console.log
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			console.log(textStatus+" - "+errorThrown);
			console.log(XMLHttpRequest.responseText);
		}
	});
	//group.attr({ cursor: 'pointer' });
}