var cur_question = 0;
var questions = $('.question');

var send = true;

$(document).ready(function() {

  window.onbeforeunload = function() {
    if(!send){
      return "Möchtest du die Seite wirklich verlassen? Alle bisher getätigten Eingaben gehen verloren!";
    }
  }

  $(questions.get(cur_question)).show(400);

  $('#next').click(function(){
    nextQuestion();
  });

  $('#previous').click(function(){
    previousQuestion();
  });

  $('.construction_link').click(function(){
    $('li.active').removeClass('active');
    $(this).parent().addClass('active');
    var con_nr = $(this).data("construction");
    var goTo = 0;
    for(var i = 0; i < questions.length; i++){
      if($(questions.get(i)).data('construction') == con_nr){
        goTo = i;
        break;
      }
    }
    showQuestion(goTo);
    $('#navbar').collapse('hide');
  });

  $('.question-answer').click(function(){
    send = false;
    if($(this).hasClass('yes')){
      $(this).addClass('btn-success');
      $(this).nextAll('.textfield').hide();
      $(this).next('.no').removeClass('btn-danger');
    }else{
      $(this).addClass('btn-danger');
      $(this).nextAll('.textfield').show();
      $(this).prev('.yes').removeClass('btn-success');
    }
  });

});

function nextQuestion(){
  showQuestion(cur_question + 1);
}

function previousQuestion(){
  showQuestion(cur_question - 1);
}

function showQuestion(number){
  $(questions.get(cur_question)).hide(400);
  cur_question = (number) % questions.length;
  $(questions.get(cur_question)).show(400);
  setCurrentQuestion(cur_question);
  setCurrentQuestionLinkActive(cur_question);
}

function setCurrentQuestion(number){
  var cur_number = $(questions.get(cur_question)).data("construction");
  var cur_name = $(".construction_link[data-construction='" + cur_number + "']").text();
  $(".navbar-brand").text(cur_name);
}

function setCurrentQuestionLinkActive(number){
  $('li.active').removeClass('active');
  var cur_number = $(questions.get(cur_question)).data("construction");
  $(".construction_link[data-construction='" + cur_number + "']").parent().addClass('active');

}