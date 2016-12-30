var received_chat_sound = new Audio(root_folder+'audio/Received_Message.mp3'); 
var sent_chat_sound = new Audio(root_folder+'audio/Sent_Message.mp3'); 

$(document).ready(function() {
	chatSelectGuild();
	chatSelectAll();
	setInterval(refreshAll,1000);
	guildCheck();
	persoCheck();
	var myvar = "<?php echo $myVarValue;?>";
	$('form#send-chat-message-all').on('submit', prepareSendAll);
	$('form#send-chat-message-guild').on('submit', prepareSendGuild);
	$('form#send-chat-message-private').on('submit', prepareSendPerso);
	$('#checkmarkAll').on('click', prepareSendAll);
	$('#checkmarkGuild').on('click', prepareSendGuild);
	$('#checkmarkPrivate').on('click', prepareSendPerso);
	$('input[name=message]').on('focus keypress', function() {$(this).removeClass('error')})
});
function guildCheck() {
	$.ajax({
  			type: "POST",
  			url: root_folder+"api/get_guild_chat"
  		}).done(function(guildId) {
  			if(parseInt(guildId)) {
  				setInterval(refreshGuild,1000);
  			}
  			else{
  				$(".chat-button-guild").attr("onClick", "");
  				$(".chat-button-guild").addClass("disabled");
  			}
  		}).fail(function(result) {
  			alert('Erreur lors de l\'envoi du message.');
  		});
}
function persoCheck() {
	var id = $('#send-chat-message-private').attr('data');
	if(id) {
		setInterval(refreshPerso,1000);
	}
}
function prepareSendAll(e) {
	e.preventDefault();
	var message = $('#send-chat-message-all input[name=message]').val();
	if (message.length < 3) {
		$('input[name=message]').addClass('error');
	} else {
		$('input[name=message]').removeClass('error');
		send(message);
	}
}
function prepareSendGuild(e) {
	e.preventDefault();
	var message = $('#send-chat-message-guild input[name=message]').val();
	if (message.length < 3) {
		$('input[name=message]').addClass('error');
	} else {
		$('input[name=message]').removeClass('error');
		sendGuild(message);
	}
}
function prepareSendPerso(e) {
	e.preventDefault();
	var message = $('#send-chat-message-private input[name=message]').val();
	if (message.length < 3) {
		$('input[name=message]').addClass('error');
	} else {
		$('input[name=message]').removeClass('error');
		sendPerso(message);
	}
}
function send(message) {
	$.ajax({
  			type: "POST",
  			url: root_folder+"api/envoyer_message",
  			data: "message="+message
  		}).done(function(messages) {
  			sent_chat_sound.play();
  			$('input[name=message]').val('');
  		}).fail(function(result) {
  			alert('Erreur lors de l\'envoi du message.');
  		});
}
function sendGuild(message) {
	$.ajax({
  			type: "POST",
  			url: root_folder+"api/envoyer_message",
  			data: "message="+message+"&guild=true"
  		}).done(function(messages) {
  			sent_chat_sound.play();
  			$('input[name=message]').val('');
  		}).fail(function(result) {
  			alert('Erreur lors de l\'envoi du message.');
  		});
}
function sendPerso(message) {
	var id = $('#send-chat-message-private').attr('data');
	$.ajax({
  			type: "POST",
  			url: root_folder+"api/envoyer_message",
  			data: "message="+message+"&fighter_id_to="+id
  		}).done(function(messages) {
  			sent_chat_sound.play();
  			$('input[name=message]').val('');
  		}).fail(function(result) {
  			alert('Erreur lors de l\'envoi du message.');
  		});
}
function refreshAll() {
	var last_msg = $('#chat-messages-all .chat-row:last-child');
	var last_id = last_msg && last_msg.attr('message-id') ? last_msg.attr('message-id') : 0;
	$.ajax({
  			type: "POST",
  			url: root_folder+"api/actualiser_message",
  			data: "dernierMessage="+last_id,
  			dataType: "json"
  		}).done(function(messages) {
  			if (messages.length == 0) {return}
  			// Add messages
  			//alert(messages);
  			messages.forEach(function(m) {
  				appendMessage(m,0);
  			});
  			// Play sound
  			if (messages[messages.length - 1].name != fighter_name ) {
  				received_chat_sound.play();
   				$('.go-right').removeClass('fa-comment fa-comment-o');
	   			$('body').hasClass('righty') || $('body').innerWidth() >= 1700 ? $('.go-right').addClass('fa-comment-o') : $('.go-right').addClass('fa-comment');
  			}
  			// Clear and scroll chat
  			var chat = $('#chat-messages-all');
   			var height = chat.prop('scrollHeight');
  			chat.animate({
    		  scrollTop: height
   			}, 400, "linear");
  		}).fail(function(result) {
  		});
}
function refreshGuild() {
	var last_msg = $('#chat-messages-guild .chat-row:last-child');
	var last_id = last_msg && last_msg.attr('message-id') ? last_msg.attr('message-id') : 0;
	$.ajax({
  			type: "POST",
  			url: root_folder+"api/actualiser_message",
  			data: "dernierMessage="+last_id+"&guild=true",
  			dataType: "json"
  		}).done(function(messages) {
  			if (messages.length == 0) {return}
  			// Add messages
  			if (messages.length > 3){
  			//alert(messages);
  				
  			}
  			messages.forEach(function(m) {
  				appendMessage(m,1);
  			});
  			// Play sound
  			if (messages[messages.length - 1].name != fighter_name ) {
  				received_chat_sound.play();
   				$('.go-right').removeClass('fa-comment fa-comment-o');
	   			$('body').hasClass('righty') || $('body').innerWidth() >= 1700 ? $('.go-right').addClass('fa-comment-o') : $('.go-right').addClass('fa-comment');
  			}
  			// Clear and scroll chat
  			var chat = $('#chat-messages-guild');
   			var height = chat.prop('scrollHeight');
  			chat.animate({
    		  scrollTop: height
   			}, 400, "linear");
  		}).fail(function(result) {
  		});
}
function refreshPerso() {
	var id = $('#send-chat-message-private').attr('data');
	var last_msg = $('#chat-messages-private .chat-row:last-child');
	var last_id = last_msg && last_msg.attr('message-id') ? last_msg.attr('message-id') : 0;
	$.ajax({
  			type: "POST",
  			url: root_folder+"api/actualiser_message",
  			data: "dernierMessage="+last_id+"&fighter_id_to="+id,
  			dataType: "json"
  		}).done(function(messages) {
  			if (messages.length == 0) {return}
  			// Add messages
  			if (messages.length > 3){
  			//alert(messages);
  				
  			}
  			messages.forEach(function(m) {
  				appendMessagePrivate(m);
  			});
  			// Play sound
  			if (messages[messages.length - 1].name != fighter_name ) {
  				received_chat_sound.play();
  			}
  			// Clear and scroll chat
  			var chat = $('#chat-messages-private');
   			var height = chat.prop('scrollHeight');
  			chat.animate({
    		  scrollTop: height
   			}, 400, "linear");
  		}).fail(function(result) {
  		});
}
function appendMessage(m,guild) {
	if (m.name != fighter_name) {
		var msg_row = ''
		+'<div class="chat-row row" message-id="'+m.id+'">'
			+'<div class="col-xs-3">'
				+'<div class="sender">'
					+'<img src="'+root_folder+'img/avatars/'+m.avatar_url+'" class="img-responsive sender-avatar">'
					+'<p class="sender-name">'+m.name+'</p>'
				+'</div>'
			+'</div>'
			+'<div class="col-xs-9">'
				+'<div class="chat-message chat-message-other">'+m.message+'</div>'
			+'</div>'
		+'</div>';
	}
	else {
		var msg_row = ''
		+'<div class="chat-row row" message-id="'+m.id+'">'
			+'<div class="col-xs-9">'
				+'<div class="chat-message chat-message-me">'+m.message+'</div>'
			+'</div>'
			+'<div class="col-xs-3">'
				+'<div class="sender">'
					+'<img src="'+root_folder+'img/avatars/'+m.avatar_url+'" class="img-responsive sender-avatar">'
					+'<p class="sender-name rtl">'+m.name+'</p>'
				+'</div>'
			+'</div>'
		+'</div>';
	}
	if(guild==1){
		$('#chat-messages-guild').append($.parseHTML(msg_row));
	}
	else if(guild==2){
    msg_row += '<div class="chat-row row" message-id="'+m.id+'><hr></div>';
		$('#chat-messages-private').append($.parseHTML(msg_row));
	}
	else
		$('#chat-messages-all').append($.parseHTML(msg_row));
}
function appendMessagePrivate (m) {
  if (m.name != fighter_name) {
    var msg_row = ''
    +'<div class="chat-row row margin-bot-chat" message-id="'+m.id+'">'
      +'<div class="col-xs-2">'
        +'<div class="sender">'
          +'<img src="'+root_folder+'img/avatars/'+m.avatar_url+'" class="img-responsive sender-avatar">'
          +'<p class="sender-name">'+m.name+'</p>'
        +'</div>'
      +'</div>'
      +'<div class="col-xs-10">'
        +'<div class="chat-message chat-message-other taille-chat-box-right">'+m.message+'</div>'
      +'</div>'
    +'</div>';
  }
  else {
    var msg_row = ''
    +'<div class="chat-row row margin-bot-chat" message-id="'+m.id+'">'
      +'<div class="col-xs-10">'
        +'<div class="chat-message chat-message-me taille-chat-box-left">'+m.message+'</div>'
      +'</div>'
      +'<div class="col-xs-2">'
        +'<div class="sender margin-left-img-chat">'
          +'<img src="'+root_folder+'img/avatars/'+m.avatar_url+'" class="img-responsive sender-avatar">'
          +'<p class="sender-name rtl">'+m.name+'</p>'
        +'</div>'
      +'</div>'
    +'</div>';
  }
  $('#chat-messages-private').append($.parseHTML(msg_row));
}
function chatSelectGuild() {
	if(!$('.chat-button-guild').hasClass( "active")){
		$('.chat-button-guild').addClass("active");
		$('.chat-button-all').removeClass("active");
		$('.chat-messages-all').hide();
		$('.chat-messages-guild').show();
	}
}
function chatSelectAll() {
	if(!$('.chat-button-all').hasClass( "active")){
		$('.chat-button-all').addClass("active");
		$('.chat-button-guild').removeClass("active");
		$('.chat-messages-guild').hide();
		$('.chat-messages-all').show();
	}
}