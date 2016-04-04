var cur_question = 0;
var questions;
var questions_amount;
var questions_answered = 0;

var send = true;
var name = '';

$(document).ready(function() {

  getQuestionnaire(1);

  setSendButton('');

  window.onbeforeunload = function() {
    if(!send){
      return "Möchtest du die Seite wirklich verlassen? Alle bisher getätigten Eingaben gehen verloren!";
    }
  }

  $('#next').click(function(){
    nextQuestion();
  });

  $('#previous').click(function(){
    previousQuestion();
  });

  $('#btn-username').click(function(){
    name = $('#username').val();
    console.log(name);
    if(name != ''){
      $('#overlay').hide(500);
      $('#main_content').show();
    }else{
      $('#username_error').show();
    }
  });

  $('#username').keypress(function (e) {
    if (e.which == 13) {
      $('#btn-username').trigger('click');
      return false;    //<---- Add this line
    }
  });

});

function setMenuButtons(){
  $('.construction_link').click(function(){
    console.log('CLICK!');
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
}

function setAnswerButtons(){
  $('.question-answer').click(function(){
    send = false;
    var progress_bar = $('.progress-bar');

    // check whether question was already answered
    if(!$('div.question:visible').hasClass('answered')){
      $('div.question:visible').addClass('answered');
      questions_answered++;
      console.log('amount: ' + questions_amount + ' answered: ' + questions_answered);
      progress_bar.width((questions_answered/questions_amount)*100 + '%');
      progress_bar.text(questions_answered + '/' + questions_amount + ' Fragen beantwortet');
      if(questions_answered == questions_amount){
        progress_bar.addClass('progress-bar-success');
        $('#btn-send').addClass('btn-success');
      }
    }

    // user pressed yes or no
    if($(this).hasClass('yes')){
      $(this).addClass('btn-success');
      $(this).nextAll('.textfield').hide();
      $(this).next('.no').removeClass('btn-danger');
      nextQuestion();
    }else{
      $(this).addClass('btn-danger');
      $(this).nextAll('.textfield').show();
      $(this).nextAll('.textfield').focus()
      $(this).prev('.yes').removeClass('btn-success');
    }
  });
}

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

function setSendButton(text){
  $('#btn-send').on('click', function(){
    if(questions_answered != questions_amount){
      $('#btn-send-msg').show();      
    }else{
      $('#btn-send-msg').html('Erfolgreich verschickt!');
      $('#btn-send-msg').show();      
    }
  });
}

function getQuestionnaire(type){
  var data = '&newQuestionnaire=' + type;

  jQuery.ajax({
    type: "POST",
    url: '../php/databaseQuery.php',
    dataType: "text",
    data: data,
    success:function(response){
      console.log("received questionnaire...");
      console.log("response: " + response);
      var response_arr = response.split('~??~??~');
      $('#navbar-entries').html(response_arr[0]);
      setMenuButtons();
      $('#questions').html(response_arr[1]);
      setAnswerButtons();
      questions = $('.question');
      questions_amount = questions.length - 1; // excludes confirmation page
      showQuestion(0);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      console.log("Status: " + textStatus); 
      console.log("Error: " + errorThrown); 
    } 
  });
}