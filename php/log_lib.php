<?php

require_once('credentials.php');

ini_set('display_errors', '1');
error_reporting(E_ALL);

function generateQuestions($construction_ids, $construction_questions, $id){
  global $host, $db, $username, $pw;

  $questionnaire = true;
  if($id != -1){
    $questionnaire = false;
  }

  $tmp = '';

  $questions = '(';
  for($i = 0; $i < sizeof($construction_questions); $i++){
    $questions .= str_replace(constant("SPLIT"), ",", $construction_questions[$i]);
    if($i != sizeof($construction_questions) - 1){
      $questions .= ',';
    }
  }
  $questions .= ')';

  $link = mysqli_connect($host, $username, $pw, $db) or die('Error: ' . mysqli_error());

  $query = 'SELECT * FROM questions WHERE id in ' . $questions . ' AND active="1"';
  $result = mysqli_query($link, $query) or die(mysqli_error($link));

  $question_id = array();
  $question_text = array();
  $question_answers = array();
  $question_images = array();

  while ($row = mysqli_fetch_object($result)) {
    $question_id[] = $row -> id;
    $question_text[] = $row -> question;
    $question_answers[] = $row -> answers;
    $question_images[] = $row -> photo;
  }

  // iterate through all constructions
  for($i = 0; $i < sizeof($construction_questions); $i++){
    // iterate through all questions
    $constructions_questions_arr = explode(constant("SPLIT"), $construction_questions[$i]);
    $construction_data = $i + 1;
    if(!$questionnaire){
      $construction_data = $id;
    }
    for($j = 0; $j < sizeof($constructions_questions_arr); $j++){
      $construction_question_id = array_search($constructions_questions_arr[$j], $question_id);
      $question_answers_arr = explode(constant("SPLIT"), $question_answers[$construction_question_id]);
      $tmp .= '<div class="row question" data-construction="' . $construction_data . '" data-id="' . $construction_ids[$i] . '">
                  <div class="col-md-12 padding-0">
                    <img class="center-block img-responsive" src="../img/' . $question_images[$construction_question_id] .'" />
                  </div>    
                  <div class="col-md-12">
                    <h3>
                      ' . $question_text[$construction_question_id] . '
                    </h3>
                    <button type="button" class="col-xs-12 btn question-answer yes">' . $question_answers_arr[0] . '</button>
                    <button type="button" class="col-xs-12 btn question-answer no">' . $question_answers_arr[1] . '</button>
                    <div class="textfield">
                      <textarea name="description" class="form-control" rows="5" placeholder="Was stimmt nicht?" id="no-description"></textarea>
                    </div>
                  </div>
                </div>';
    }
  }
  mysqli_close($link);
  return $tmp;
}

function generateMenu($names, $id){
  $tmp = '';
  $size = sizeof($names);
  if($id != -1){
    $size = 1;
  }
  for($i = 0; $i < $size; $i++){
    $number = $i + 1;
    $name = $names[$i];
    if($id != -1){
      $number = $id;
      $name = $names;
    }
    $tmp .= '<li><a href="#" data-construction="' . $number .'" class="construction_link">BW' . $number . ': ' . $name . '</a></li>';               
  }

  $tmp .= '<li><a href="#" data-construction="0" class="construction_link"><span class="glyphicon glyphicon-ok"></span>  Abschließen</a></li>';
  return $tmp;
}

?>