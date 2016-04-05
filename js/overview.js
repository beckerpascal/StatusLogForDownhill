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
    if($(this).data('value') > 0){
      $(this).parent('li').addClass("active").siblings().removeClass("active");
      getConstructions($(this).data("value")); 
      var headings = $('.navbar-brand');
      for(var i = 0; i < headings.length; i++){
        if($(headings[i]).data('value') === $(this).data("value")){
          $(headings[i]).show();
        }else{
          $(headings[i]).hide();
        }
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