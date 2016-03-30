$(document).ready(function() {
  getConstructions(1);

  $(".nav > li > a").click(function(){
    $(this).parent('li').addClass("active").siblings().removeClass("active");
    getConstructions($(this).data("value"));
  });

});

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