var type = 1;

$(document).ready(function() {
  getConstructions(type);

  init();

});

function init(){
  setNavBarLinks();
}

function setNavBarLinks(){
  $(".nav > li > a").click(function(){
    var value = $(this).data('value');
    if(value > 0){
      $(this).parent('li').addClass("active").siblings().removeClass("active");
      if(value < 3){
        getConstructions(value);
        $('#construction_list').show();
        $('#reports').hide();
        $("#btn-new-report").removeClass('disabled');
        $("#btn-new-report").attr("href", "questionnaire.html?newQuestionnaire=" + value);
        var headings = $('.navbar-brand');
        for(var i = 0; i < headings.length; i++){
          if($(headings[i]).data('value') === value){
            $(headings[i]).show();
          }else{
            $(headings[i]).hide();
          }
        }
      }else if(value === 3){
        $('#construction_list').hide();
        $('#reports').show();
        $("#btn-new-report").addClass('disabled');
        $("#btn-new-report").attr("href", "");      
      }      
    }
  });
}

function getConstructions(category_id){

  if(3 < category_id || category_id === undefined){
    return;
  }

  var data = '&getConstructions=' + category_id;

  console.log(data);
  jQuery.ajax({
    type: "POST",
    url: '../php/databaseQuery.php',
    dataType: "text",
    data: data,
    success:function(response){
      // console.log("received constructions...");
      $('#overview > tbody').html(response);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      console.log("Status: " + textStatus); 
      console.log("Error: " + errorThrown); 
    } 
  });

}