$(function() {
    $( ".items" ).draggable({
      appendTo: "body",
      helper: "clone"
    });
    $( "#cart ul" ).droppable({
      activeClass: "ui-state-default acceptable-item",
      hoverClass: "ui-state-hover",
      accept: function(ui) {
        if( ui.attr("data") == $(this).attr("data") || $(this).attr("data") == "remove"){
           return true
        } else {
          return false
        }
      },
      drop: function( event, ui ) {
        if($(this).attr("data") != "remove") {
          if($(this).children().length){
            $('#inventory ul').append($(this).children())
          }
          $("#" + ui.draggable.attr("id")).appendTo(this);
        }
        else {
          destroyItem(ui.draggable.attr("id"));
          $("#" + ui.draggable.attr("id")).remove();
        }
      }
    });
    $("#inventory ul").droppable({
      activeClass: "ui-state-default",
      hoverClass: "ui-state-hover",
      drop: function( event, ui ) {
        $("#" + ui.draggable.attr("id")).appendTo(this);
      }
      });
  });

function upSkill( skill ){
  $.ajax({
        type: "POST",
        url: root_folder+"arenas/edit",
        data: "skill=" + encodeURIComponent(skill),
      }).done(function(p) {
        actualiseStat();

      }).fail(function(result) {
          alert(result);
      });
}
function actualiseStat(){
  $.ajax({
        type: "POST",
        url: root_folder+"arenas/edit",
        data: "actualise=1",
        dataType: "json"
      }).done(function(p) {
        //alert(p);
        $("#skill-health").html(p.health + "(+" + p.health_item + ")");
        $("#skill-strength").html(p.strength + "(+" + p.strength_item + ")");
        $("#skill-speed").html(p.speed + "(+" + p.speed_item + ")");
        $("#skill-sight").html(p.sight + "(+" + p.sight_item + ")");
        $("#skill-point").html(p.skill_point);

      }).fail(function(result) {
        //alert("coucou");
      });
}

function saveInventory() {
	if ($('.stuff-presentation .items').length != 2) {
		alert("Veuillez vous assurer d'être bien équipés ;)");
		return;
	}
  var myData = '';
  var i = 0;
  $(".equipped-item").each(function() {
    if($(this).children().length) {
      i++;
      myData += i + '=' +$(this).children().attr('id') + '&'; 
      //console.log(myData);
    }
  });
    $.ajax({
        type: "POST",
        url: root_folder+"arenas/edit",
        data: myData
      }).done(function(p) {
        //alert(p);
         actualiseStat();
         $(".alert-success").show('slow');
         setTimeout(function() { $(".alert-success").hide('slow') }, 2500);
         
      }).fail(function(result) {
        //alert(result);
      });
  }

function destroyItem(tool) {
  $.ajax({
        type: "POST",
        url: root_folder+"arenas/edit",
        data: "delete_tool=" + tool,
      }).done(function(p) {
        $(".alert-danger").show('slow');
        setTimeout(function() { $(".alert-danger").hide('slow') }, 2500);
      }).fail(function(result) {
      });
}