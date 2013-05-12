
    /**
     * @copyright Copyright (C) René Martin, 2012. All rights reserved.
     * @license   GNU General Public License version 2 or later; see LICENSE.txt
     **/

// This file if pretty poorly refactored due to time issues
var flatdata, detaildata;
var allmarkers;
var dateindex = 0, maxdateindex = 0;
$(window).load(function(){
  // Show Ajax loader upon requests
  $(document).ajaxStart(function(){ $("#loader").fadeIn(); });
  $(document).ajaxComplete(function(){ $("#loader").fadeOut(); });

  $("#fromdateinput").change(function() { $("#todateinput").val(""); $("#todateinput").attr("min",$("#fromdateinput").val()); $("#todatetablerow").fadeIn(); });

  // Flat list and detail list sliding is initialized here
  $("#openflatlist").click(function(){
    $("#possibleflats li:not(.active)").slideDown();
    $("#openflatlist").fadeOut();
    $("#alldetails").slideUp();
    $("#opendetails").fadeIn();
  });
  $("#opendetails").click(function(){
    $("#alldetails").slideDown();
    $("#opendetails").fadeOut();
    $("#possibleflats li:not(.active)").slideUp();
    $("#openflatlist").fadeIn();
  });

  if(!codebase) codebase = "";

  // Set up Ajax requests
  updateFlats();
  $("#submitBtn").click(updateFlats);
  $("#acceptbook").click(function(){
    $("#myModal").modal("hide");
    $.ajax({
      url: codebase+"book.php",
      dataType: "json",
      type: "POST",
      data: {dow:$("#possibleflats .active a").attr("name"), date:$(this).val(), name: $("#username").val()}
    }).done(recieveBookAnswer);
  });
  $("#loginforbook").click(function(){
    $("#loginModal").modal("hide");
    $.ajax({
      url: codebase+"book.php",
      dataType: "json",
      type: "POST",
      data: {dow:$("#possibleflats .active a").attr("name"), date:$("#acceptbook").val(), name: $("#username").val(), usr:$("#googleusername").val(), pwd:$("#googlepassword").val()}
    }).done(recieveBookAnswer);
  });
});

function updateFlats() {
	$.getJSON(codebase+"?city="+$("#cityinput").val()+"&date="+$("#fromdateinput").val()+"&todate="+$("#todateinput").val(), recieveFlatUpdate);
}

function recieveBookAnswer(inputdata)
{
  // Interpret the result from the book request and show result to the user
  switch(inputdata["result"])
  {
    case "Success":
      $("#answerModal #message").html("Ihre Buchung wurde erfolgreich durchgeführt.");
      $("#answerModal").modal();
      $.getJSON("?city="+$("#cityinput").val()+"&date="+$("#fromdateinput").val()+"&todate="+$("#todateinput").val()+"&dow="+$("#possibleflats .active a").attr("name")+"&detail=true", recieveFlatDetail);
      break;
    case "Error-0":
      $("#answerModal #message").html("Ihre Buchung war fehlerhaft.<br>Einer der Parameter war inkorrekt.");
      $("#answerModal").modal();
      break;
    case "Error-1":
      $("#loginModal").modal();
      break;
    case "Error-2":
      $("#answerModal #message").html("Ihre Buchung war fehlerhaft.<br>Das Datum existierte nicht mehr.");
      $("#answerModal").modal();
      break;
    case "Error-3":
      $("#answerModal #message").html("Ihre Buchung war fehlerhaft.<br>Das Objekt ist zu diesem Datum bereits belegt.");
      $("#answerModal").modal();
      break;
    case "Error-4":
      $("#answerModal #message").html("Ihre Buchung war fehlerhaft.<br>Nutzername oder Passwort sind inkorrekt.");
      $("#answerModal").modal();
      break;
  }
}

function recieveFlatUpdate(inputdata)
{
  flatdata = inputdata;
  $(".activateOnDetail, #possibleflats, #alldetails, #openflatlist").fadeOut();
  $(".activateOnDetail, #possibleflats").promise().done(function(){
    // Reset markers on the map and set bounds
    resetMarkers();
    zoomMapToBounds(inputdata["camerazoom"]["ymin"], inputdata["camerazoom"]["ymax"], inputdata["camerazoom"]["xmin"], inputdata["camerazoom"]["xmax"]);

    // Refill the flat list woth the retrieved objects
    $("#possibleflats").html("");
    $.each(inputdata["obj"], function(ws, val) {
      $("#possibleflats").append("<li class=\""+((val["fitsfilter"]==0)?"disabled ":"")+"\"><a href=\"#\" name=\""+ws+"\">"+val["name"]+"</a></li>");

      // Add Markers on the map
      if(val["fitsfilter"]==0) addInactiveMarkerAt(val["position"][1], val["position"][0]);
      addMarkerAt(val["position"][1], val["position"][0]);
    });

    // Set up Ajay requests for details
    $("#possibleflats a").click(clickedOnFlat);
    $("#possibleflats").fadeIn();
  });
}

function clickedOnFlat() {
  $("#possibleflats .active").removeClass("active");
  $(this).parent().addClass("active");
  $.getJSON(codebase+"?city="+$("#cityinput").val()+"&date="+$("#fromdateinput").val()+"&todate="+$("#todateinput").val()+"&dow="+this.name+"&detail=true", recieveFlatDetail);
}

function recieveFlatDetail(inputdata)
{
  $(".activateOnDetail").fadeOut();
  $(".activateOnDetail").promise().done(function(){
    // Set map bounds and active marker
    zoomMapToBounds(inputdata["camerazoom"]["ymin"], inputdata["camerazoom"]["ymax"], inputdata["camerazoom"]["xmin"], inputdata["camerazoom"]["xmax"]);
    addActiveMarkerAt(inputdata["obj"][$("#possibleflats .active").find("a").attr("name")]["position"][1],
      inputdata["obj"][$("#possibleflats .active").find("a").attr("name")]["position"][0]);

    // Animations
    $("#alldetails").slideDown();
    $("#possibleflats li:not(.active)").slideUp();
    $("#opendetails").fadeOut();
    $("#openflatlist").fadeIn();
    detaildata = inputdata["obj"][$("#possibleflats .active").find("a").attr("name")];
    $("#adress").html(detaildata["adress"]);
    $("#dates").html("");

    // Insert data into detail list
    var i = -1;
    dateindex = 0;
    $("#dates").append("<li id=\"showolderdates\" style=\"display:none\"><i class=\"icon-circle-arrow-up\"></i> <span class=\"count\"></span> ältere Termine</li>");
    $.each(detaildata["dates"],function(date, occupied){
      i++;
      if(occupied == "") {
        $("#dates").append("<li class=\"label label-success date_"+(i-i%5)/5+"\"><span class=\"datetext\">"+date+": noch frei</span><button style=\"float:right\" value=\""+date+"\">jetzt buchen</button></li>");
      } else {
        $("#dates").append("<li class=\"label label-important date_"+(i-i%5)/5+"\"><span class=\"datetext\">"+date+": leider schon belegt</span></li>") ;
      }
    })
    $("#dates").append("<li id=\"shownewerdates\" style=\"display:none\"><i class=\"icon-circle-arrow-down\"></i> <span class=\"count\"> neure Termine</span></li>");
    maxdateindex = (i - i%5)/5;
    $(".label-success button").click(bookBtnClicked);
    $("#shownewerdates").click(function(){changeDateIndex(1)});
    $("#showolderdates").click(function(){changeDateIndex(-1)});
    changeDateIndex(0);
    $(".date_0").promise().done(function(){ $(".activateOnDetail").fadeIn(); });
  });
}

function bookBtnClicked() {
  // Show booking modal
  $("#modaldetails").html("<li>Beschreibung: "+detaildata["name"]+"</li><li>Adresse: "+detaildata["adress"]+"</li><li>Datum: "+$(this).val()+"</li>");
  $("#acceptbook").attr("value",$(this).val());
  $("#myModal").modal();
}

// This function is used for cycling through all the retrieved dates in the detail view
function changeDateIndex(offset)
{
  $("#dates li").fadeOut();
  $("#dates li").promise().done(function(){
    dateindex+=offset;
    $("#showolderdates .count").html(5 * dateindex);
    var num = $("#dates li").length - 2 - 5 * (dateindex + 1);
    $("#shownewerdates .count").html(num+ ( num == 1 ? " neurer Termin" : " neure Termine"));
    $(".date_"+dateindex+(dateindex>0?", #showolderdates":"")+(dateindex<maxdateindex?", #shownewerdates":"")).fadeIn();
  });
}