<?php

//include_once('log_lib.php');
include_once('credentials.php');

define("EMPTY","---");
define("SPLIT","|");

$link = mysqli_connect($host, $username, $pw, $db)or die('could not connect to database');

if(isset($_POST['getConstructions']) && $_POST['getConstructions'] > 0){

  $category = $_POST['getConstructions'];

  $query = 'SELECT * FROM constructions WHERE category="' . $category . '" AND active=1 ORDER BY number ASC';
  $result = mysqli_query($link, $query) or die(mysqli_error());

  $output = '';

  while ($row = mysqli_fetch_object($result)) {

    $status = '';
    $status_text = '';
    switch ($row -> current_status) {
      case 0:
        // $status = 'success';
        $status = '';
        $status_text = 'fahrbereit';
        break;
      case 1:
        $status = 'info';
        $status_text = 'Prüf. fällig';
        break;
      case 2;
        $status = 'danger';
        $status_text = 'Rep. fällig';
        break;
      case 3;
        $status = 'warning';
        $status_text = 'Freig. fällig';
        break;
    }

    $interval = date('d.m.y', mktime(0, 0, 0, date('m'), date('d') + $row -> interval, date('y')));


    if(isset($row -> last_checked)){
      $last_checked = strtotime($row -> last_checked);
      $last_checked = date('d.m.y', $last_checked);
    }else{
      $last_checked = constant("EMPTY");
    }

    if(isset($row -> last_controller)){
      $last_controller = $row -> last_controller;
    }else{
      $last_controller = constant("EMPTY");    
    }

    if(isset($row -> last_text)){
      $last_text = $row -> last_text;
    }else{
      $last_text = constant("EMPTY");    
    }


    if(isset($row -> maintain_checked)){
      $maintain_checked = strtotime($row -> maintain_checked);
      $maintain_checked = date('d.m.y', $maintain_checked);  
    }else{
      $maintain_checked = constant("EMPTY");
    }

    if(isset($row -> maintain_controller)){
      $maintain_controller = $row -> maintain_controller;
    }else{
      $maintain_controller = constant("EMPTY");    
    }

    if(isset($row -> maintain_text)){
      $maintain_text = $row -> maintain_text;
    }else{
      $maintain_text = constant("EMPTY");    
    }

    
    if(isset($row -> authorize_checked)){
      $authorize_checked = strtotime($row -> authorize_checked);
      $authorize_checked = date('d.m.y', $authorize_checked);
    }else{
      $authorize_checked = constant("EMPTY");
    }

    if(isset($row -> authorize_controller)){
      $authorize_controller = $row -> authorize_controller;
    }else{
      $authorize_controller = constant("EMPTY");    
    }

    if(isset($row -> authorize_text)){
      $authorize_text = $row -> authorize_text;
    }else{
      $authorize_text = constant("EMPTY");    
    }

    $output .= '
                <tr class="' . $status . '">
                  <td>' . $row -> number .        '</td>
                  <td>' . $row -> name .          '</td>
                  <td>' . $status_text .          '</td>
                  <td>' . $interval .             '</td>
                  <td>' . $last_checked .         '</td>
                  <td>' . $last_controller .      '</td>
                  <td>' . $last_text .            '</td>
                  <td>' . $maintain_checked .     '</td>
                  <td>' . $maintain_controller .  '</td>
                  <td>' . $maintain_text .        '</td>
                  <td>' . $authorize_checked .    '</td>
                  <td>' . $authorize_controller . '</td>
                  <td>' . $authorize_text .       '</td>
                  <td><i>TODO</i></td>
                </tr>
              ';
  }

  mysqli_close();

  echo $output;

}else if(isset($_POST['newQuestionnaire']) && $_POST['newQuestionnaire'] > 0){
  $category = $_POST['newQuestionnaire'];
  $construction_ids = '';
  $questions = '(';
  $html = '';

  $query = 'SELECT * FROM questionnaires WHERE category="' . $category . '" AND active="1"';
  $result = mysqli_query($link, $query) or die(mysqli_error());

  while ($row = mysqli_fetch_object($result)) {
    $construction_ids = $row -> construction_ids;
  }

  $query = 'SELECT * FROM constructions WHERE id in (' . str_replace(constant("SPLIT"), ",", $construction_ids) . ') AND active="1"';
  $result = mysqli_query($link, $query) or die(mysqli_error());

  $construction_names = array();
  $construction_questions = array();
  $construction_questions_amount = array();

  while ($row = mysqli_fetch_object($result)) {
    $construction_names[] = $row -> name;
    $construction_questions[] = $row -> question_ids;
    $construction_questions_amount[] = sizeof(explode(constant("SPLIT"), $row -> question_ids));
  }

  for($i = 0; $i < sizeof($construction_names); $i++){
    $html .= '<li><a href="#" data-construction="' . ($i + 1) .'" class="construction_link">Bauwerk ' . ($i + 1) . ' (' . $construction_names[$i] . ')</a></li>';               
  }

  $html .= '<li><a href="#" data-construction="0" class="construction_link">Abschließen</a></li>';

  $html .= '~??~??~';

  for($i = 0; $i < sizeof($construction_questions); $i++){
    $questions .= str_replace(constant("SPLIT"), ",", $construction_questions[$i]);
    if($i != sizeof($construction_questions) - 1){
      $questions .= ',';
    }
  }
  $questions .= ')';

  $query = 'SELECT * FROM questions WHERE id in ' . $questions . ' AND active="1"';
  $result = mysqli_query($link, $query) or die(mysqli_error());

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
  for($i = 0; $i < sizeof($construction_names); $i++){
    // iterate through all questions
    $constructions_questions_arr = explode(constant("SPLIT"), $construction_questions[$i]);
    for($j = 0; $j < sizeof($constructions_questions_arr); $j++){
      $construction_question_id = array_search($constructions_questions_arr[$j], $question_id);
      $question_answers_arr = explode(constant("SPLIT"), $question_answers[$construction_question_id]);
      $html .= '<div class="row question" data-construction="' . ($i + 1) .'">
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
                      <textarea name="description" class="form-control" rows="5">Was stimmt nicht?</textarea>
                    </div>
                  </div>
                </div>';
    }
  }

  echo $html;

}else{
  echo 'No post parameter was submitted...';
}

?>