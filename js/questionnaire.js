var cur_question = 0;
var questions = $('.question');

$(document).ready(function() {

  $(questions.get(cur_question)).show(400);

  $('#next').click(function(){
    nextQuestion();
  });

  $('#previous').click(function(){
    previousQuestion();
  });

  

});

function nextQuestion(){
  $(questions.get(cur_question)).hide(400);
  cur_question = (cur_question + 1) % questions.length;
  $(questions.get(cur_question)).show(400);
  console.log(cur_question);
}

function previousQuestion(){
  $(questions.get(cur_question)).hide(400);
  cur_question = (cur_question - 1) % questions.length;
  $(questions.get(cur_question)).show(400);
  console.log(cur_question);
}