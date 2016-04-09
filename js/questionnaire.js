var cur_question = 0;
var questions;
var questions_amount;
var questions_answered = 0;

var send = true;

var name = '';
var type = -1;
var conNr = -1;

$(document).ready(function() {
  getData();

  window.onbeforeunload = function() {
    if(!send){
      return "Möchtest du die Seite wirklich verlassen? Alle bisher getätigten Eingaben gehen verloren!";
    }
  };

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
      $('#overlay_username').hide();
      $('#main_content').show();
    }else{
      $('#username_error').show();
    }
  });

  $('#username').keypress(function (e) {
    if (e.which === 13) {
      $('#btn-username').trigger('click');
      return false;
    }
  });

});

function init(){
  setMenuButtons();
  setAnswerButtons();
  questions = $('.question');
  questions_amount = questions.length - 1; // excludes confirmation page
  showQuestion(0);
  setProgressBarStatus();
  setSendButton('');
}

function setMenuButtons(){
  $('.construction_link').click(function(){
    $('li.active').removeClass('active');
    $(this).parent().addClass('active');
    var con_nr = $(this).data("construction");
    var goTo = 0;
    for(var i = 0; i < questions.length; i++){
      if($(questions.get(i)).data('construction') === con_nr){
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

    // user pressed yes or no
    if($(this).hasClass('yes')){
      $(this).addClass('btn-success');
      $(this).nextAll('.textfield').hide();
      $(this).next('.no').removeClass('btn-danger');
      questionAnswered();
      nextQuestion();
    }else{
      $(this).addClass('btn-danger');
      var textfield = $(this).nextAll('.textfield');
      textfield.show();
      textfield.on('input',function(){
        questionAnswered();
      });
      $("html, body").animate({ scrollTop: $(document).height() }, "slow");
      $(this).prev('.yes').removeClass('btn-success');
    }
  });
}

function questionAnswered(){
  if(!$('div.question:visible').hasClass('answered')){
    $('div.question:visible').addClass('answered');
    questions_answered++;
    setProgressBarStatus();
    if(checkIfAllQuestionsOfConstructionHaveBeenAnswered()){
      setQuestionLinkDone(cur_question);
    }
  }
}

function checkIfAllQuestionsOfConstructionHaveBeenAnswered(){
  var cur_number = $(questions.get(cur_question)).data("construction");
  var group = $(".question[data-construction='" + cur_number + "']");
  for (var i = 0; i < group.length; i++) {
    if(!$(group[i]).hasClass('answered')){
      return false;
    }
  }
  return true;  
}

function setProgressBarStatus(){
    var progress_bar = $('.progress-bar');
    progress_bar.width((questions_answered/questions_amount) * 100 + '%');
    $('#progressbar-text').text(questions_answered + '/' + questions_amount + ' Fragen beantwortet');

    if(questions_answered === questions_amount){
      progress_bar.addClass('progress-bar-success');
      $('#btn-send').addClass('btn-success');
    }
}

function nextQuestion(){
  showQuestion(cur_question + 1);
}

function previousQuestion(){
  showQuestion(cur_question - 1);
}

function showQuestion(number){
  $(questions.get(cur_question)).hide(400);
  cur_question = number % questions.length;
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

function setQuestionLinkDone(){
  var cur_number = $(questions.get(cur_question)).data("construction");
  var link = $(".construction_link[data-construction='" + cur_number + "']");
  link.html('<span class="glyphicon glyphicon-ok"></span>  ' + link.text());
}

function setSendButton(){
  $('#btn-send').on('click', function(){
    if(questions_answered !== questions_amount){
      $('#btn-send-msg').show();      
    }else{
      sendData();  
    }
  });
}

function getData(){
  var data = '&' + window.location.search.substring(1);
  if(data === '&'){
    // TODO show error message here
    data = 'newQuestionnaire=1';
  }
  console.log('data: ' + data);
  conNr = getQueryVariable('checkConstruction');
  if(getQueryVariable('conType') !== -1){
    type = getQueryVariable('conType');
  }else{
    type = getQueryVariable('newQuestionnaire');
  }

  jQuery.ajax({
    type: "GET",
    url: '../php/databaseQuery.php',
    dataType: "text",
    data: data,
    success:function(response){
      //console.log("received questionnaire...");
      //console.log("response: " + response);
      var response_arr = response.split('~??~??~');
      if(response_arr[0] !== 'Error' && response_arr[1] !== 'Error'){
        $('#navbar-entries').html(response_arr[0]);
        $('#questions').html(response_arr[1]);
        init(); 
      }else{
        $('#overlay_error').show();
        $('#overlay_username').hide();
        $('#main_content').hide();
      }

    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      console.log("Status: " + textStatus); 
      console.log("Error: " + errorThrown); 
    } 
  });
}

function sendData(){
  var sep_groups = '~~';
  var sep_elements = '||';
  var data = '&sendQuestionnaire=1';
  data += '&name=' + name;
  data += '&type=' + type;
  data += '&conNr=' + conNr;
  data += '&answers=';

  var last_q = -1;

  for(var i = 0; i < questions_amount; i++){
    var cur_q = $(questions[i]);
    var id = cur_q.data('id');
    if(id !== last_q){
      if(last_q !== -1){
        data += sep_groups;
      }
      last_q = id;
      data += id + sep_elements;
    }
    var answers = cur_q.find('.question-answer');
    if($(answers[0]).hasClass('btn-success')){
      // Yes 
      data += '0' + sep_elements;
    }else{
      // No
      var textfield = cur_q.find('#no-description');
      data += $(textfield).val() + sep_elements;
    }
  }
  // TODO: remove last sep_element when group changes

  console.log('Data for Backend: ' + data);

  jQuery.ajax({
    type: "GET",
    url: '../php/databaseQuery.php',
    dataType: "text",
    data: data,
    success:function(response){
      console.log("success: " + response);
      $('#btn-send-msg').html('Erfolgreich verschickt!');
      $('#btn-send-msg').show();    
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) { 
      console.log("Status: " + textStatus); 
      console.log("Error: " + errorThrown); 
    } 
  });

  send = true;
}

function getQueryVariable(variable){
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i=0;i<vars.length;i++){ 
    var pair = vars[i].split("=");
    if(pair[0] === variable){
      return pair[1];
    }
  }
  return -1;
}