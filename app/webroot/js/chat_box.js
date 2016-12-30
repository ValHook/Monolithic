// AJAX
$(function() {
	setInterval(refresh,1000);
	//refresh();
	$('.ajax').on('click', function() {
		var myMessage = $('#boiteMessage').val();
		if(myMessage != "") {
			$.post($(this).attr('href'),
	                     { message : myMessage},
	                       function( data ) {
	                          	//alert(data);
	                          	$('#boiteMessage').val('');
	                       }
	               );
		}
			return false;
		});
});

function refresh() {
    var lastId = 0;
    if( $( "#boiteDialogue > p:last-child" ).length ){
    	var lastId = $( "#boiteDialogue p:last-child" ).attr('id');
    }
    $.post('/chat/actualise_message',
	                     { dernierMessage : lastId },
	                       function( data ) {
	                       	//if(data.length > 5)alert(data);
	                       	add_dialogue(data);
	                       }
	               );
}

function add_dialogue(data){
	var myArray = jQuery.parseJSON(data);
	var i = 0;
	myArray.forEach(function(entry) {
		var arr = $.map(entry, function(el,key) {
			var tmp = [key, el];
			return tmp;
		});
		/*
		if(arr.length == 10){
			alert(arr[0] + '|||' + arr[1] + '|||' + arr[2] + '|||' + arr[3] + '|||' + arr[4] + '|||' + arr[5] + '|||' + arr[6] + '|||' + arr[7] + '|||' + arr[8] + '|||' + arr[9]);
		}
		else if (arr.length == 12) {
			alert(arr[0] + '|||' + arr[1] + '|||' + arr[2] + '|||' + arr[3] + '|||' + arr[4] + '|||' + arr[5] + '|||' + arr[6] + '|||' + arr[7] + '|||' + arr[8] + '|||' + arr[9]);
		}
		else {
			alert("un chacal fait de la merde !!")
		}*/
		
		$('#boiteDialogue').append('<p id="' + arr[1] + '"> '+ arr[0] + '|||' + arr[1] + '|||' + arr[2] + '|||' + arr[3] + '|||' + arr[4] + '|||' + arr[5] + '|||' + arr[6] + '|||' + arr[7] + '|||' + arr[8] + '|||' + arr[9] + '</p>');
	});
}