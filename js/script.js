$(document).ready(function() {
  getConstructions(1);

  //TODO: Nicer!
  $('#button_dh').click(function(){
    $(this).addClass('btn-active');
    $(this).removeClass('btn-default');
    $('#button_bp').removeClass('btn-active');
    $('#button_bp').addClass('btn-default');
    getConstructions(1);
  });

  $('#button_bp').click(function(){
    console.log('clicked..');
    $(this).addClass('btn-active');
    $(this).removeClass('btn-default');
    $('#button_dh').removeClass('btn-active');
    $('#button_dh').addClass('btn-default');
    getConstructions(2);
  });


});

function getConstructions(category_id){
  var data = '&getConstructions=' + category_id;

  console.log(data);
  jQuery.ajax({
    type: "POST",
    url: '../php/databaseQuery.php',
    dataType: "text",
    data: data,
    success:function(response){
      console.log("received constructions...");
      $('#overview > tbody').html(response);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      console.log("Status: " + textStatus); 
      console.log("Error: " + errorThrown); 
    } 
  });

}