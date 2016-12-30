
/* Menus */
$(document).ready(function() {
	$('.go-left').on('click', function(e) {
		if ($('body').hasClass('lefty') || $('body').hasClass('righty')) {
			return true;
		}
		$(this).addClass('active');
		$('.close-left').addClass('close-visible');
		$('body').addClass('lefty');
		e.stopPropagation();
	});
	$('.go-right').on('click', function(e) {
		if ($('body').hasClass('lefty') || $('body').hasClass('righty')) {
			return true;
		}
		$('.close-right').addClass('close-visible');
		$(this).addClass('active');
		$('body').addClass('righty');
		$('.go-right').removeClass('fa-comment fa-comment-o');
  		$('.go-right').addClass('fa-comment-o');
		e.stopPropagation();
	});
	$('main').on('click', function() {
		$('.close-left').removeClass('close-visible');
		$('.close-right').removeClass('close-visible');
		$('.go-left').removeClass('active');
		$('.go-right').removeClass('active');
		$('body').removeClass('lefty');
		$('body').removeClass('righty');
	});
	
	/* Guild*/
	$('.button_guildeFC').on('click', function(e) {
		if ($(this).hasClass('unSelect')) {
			$('.button_guildeFC').removeClass('select');
			$('.button_guildeFC').addClass('unSelect');
			$(this).removeClass('unSelect');
			$(this).addClass('select');
		}
	});
});

function testestest() {
	/*$('#findGuild').css('display', 'none');
	$('#creatGuild').css('display', 'inline');*/
	$("#findGuild").hide();
	$("#creatGuild").show();
}

function buttonFindGuildDidTuch() {
	/*$('#creatGuild').css('display', 'none');
	$('#findGuild').css('display', 'inline');*/
	$("#findGuild").show();
	$("#creatGuild").hide();
}

function color( id ) {
	$.ajax({
        type: "POST",
        url: root_folder+"arenas/myfighters",
        data: "fighter=" + id,
        //dataType: "json"
      }).done(function(p) {
        //alert(p);
        window.location = "edit"; 
      }).fail(function(result) {
        alert(result);
      });
}

function SelectForMessage(name) {
	$('#PlayerRecherche').val(name);
	$('#search_for').submit();
}

function searchFighter() {
	$('#search_for').submit();
}

function redirectionMessages() {
	window.location = "messages"; 
}