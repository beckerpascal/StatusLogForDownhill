<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once('credentials.php');
require_once('log_lib.php');


if(isset($_POST['getConstructions']) && $_POST['getConstructions'] > 0){

  $link = mysqli_connect($host, $username, $pw, $db)or die('could not connect to database');

  $category = $_POST['getConstructions'];

  $query = 'SELECT * FROM constructions WHERE category="' . $category . '" AND active=1 ORDER BY con_number ASC';
  $result = mysqli_query($link, $query) or die(mysqli_error($link));

  $output = '';

  while ($row = mysqli_fetch_object($result)) {

    $construction_status = '';
    $construction_status_text = '';
    $button_text = '';
    switch ($row -> current_status) {
      case 0:
        $construction_status = '';
        $construction_status_text = 'fahrbereit';
        $button_text = 'prüfen';
        $button_status = 'btn-default';
        break;
      case 1;
        $construction_status = 'danger';
        $construction_status_text = 'Rep. fällig';
        $button_text = 'warten';
        $button_status = 'btn-danger';
        break;
      case 2;
        $construction_status = 'warning';
        $construction_status_text = 'Freig. fällig';
        $button_text = 'freigeben';
        $button_status = 'btn-warning';
        break;
      case 3: // TODO Cronjob for testing date and mail
        $construction_status = 'info';
        $construction_status_text = 'Prüf. fällig';
        $button_text = 'warten';
        $button_status = 'btn-info';
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

    if(isset($row -> last_comment)){
      $last_comment = $row -> last_comment;
    }else{
      $last_comment = constant("EMPTY");    
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

    if(isset($row -> maintain_comment)){
      $maintain_comment = $row -> maintain_comment;
    }else{
      $maintain_comment = constant("EMPTY");    
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

    if(isset($row -> authorize_comment)){
      $authorize_comment = $row -> authorize_comment;
    }else{
      $authorize_comment = constant("EMPTY");    
    }

    $output .= '
                <tr class="' . $construction_status . '">
                  <td>' . $row -> con_number .        '</td>
                  <td>' . $row -> name .          '</td>
                  <td>' . $construction_status_text .          '</td>
                  <td class="table-border-right">' . $interval .             '</td>
                  <td>' . $last_checked .         '</td>
                  <td>' . $last_controller .      '</td>
                  <td class="table-border-right">' . $last_comment .         '</td>
                  <td>' . $maintain_checked .     '</td>
                  <td>' . $maintain_controller .  '</td>
                  <td class="table-border-right">' . $maintain_comment .     '</td>
                  <td>' . $authorize_checked .    '</td>
                  <td>' . $authorize_controller . '</td>
                  <td class="table-border-right">' . $authorize_comment .    '</td>
                  <td><i><a class="btn btn-xs ' . $button_status . '" href="questionnaire.html?checkConstruction=' . $row -> con_number . '&conType=' . $category . '" target="_blank">' . $button_text . '</a></i></td>
                </tr>
              ';
  }

  mysqli_close($link);

  echo $output;

}else if(isset($_GET['newQuestionnaire']) && $_GET['newQuestionnaire'] > 0){

  $link = mysqli_connect($host, $username, $pw, $db)or die('could not connect to database');

  $category = $_GET['newQuestionnaire'];
  $construction_ids = '';
  $html = '';

  $query = 'SELECT * FROM questionnaires WHERE category="' . $category . '" AND active="1"';
  $result = mysqli_query($link, $query) or die(mysqli_error());

  if(mysqli_num_rows($result) > 0){
    while ($row = mysqli_fetch_object($result)) {
      $construction_ids = $row -> construction_ids;
    }
  }else{
    echo 'Error';
    return;
  }

  $query = 'SELECT * FROM constructions WHERE id in (' . str_replace(constant("SPLIT"), ",", $construction_ids) . ') AND active="1"';
  $result = mysqli_query($link, $query) or die(mysqli_error());

  $construction_names = array();
  $construction_questions = array();

  if(mysqli_num_rows($result) > 0)
  {
    while ($row = mysqli_fetch_object($result)) {
      $construction_names[] = $row -> name;
      $construction_questions[] = $row -> question_ids;
    }

    $html .= generateMenu($construction_names, -1);

    $html .= '~??~??~';

    $html .= generateQuestions(explode(constant("SPLIT"), $construction_ids), $construction_questions, -1);

    echo $html;
  }else{
    echo 'Error';
    return;
  }
  mysqli_close($link);

}else if(isset($_GET['checkConstruction']) && $_GET['checkConstruction'] > 0){

  $link = mysqli_connect($host, $username, $pw, $db) or die('could not connect to database');

  $construction_number = $_GET['checkConstruction'];
  $construction_type = $_GET['conType'];
  $html = '';

  $query = 'SELECT * FROM constructions WHERE con_number="' . $construction_number . '" AND active="1" AND category="' . $construction_type . '"';
  $result = mysqli_query($link, $query) or die(mysqli_error($link));

  $construction_id = 0;
  $construction_name = '';
  $construction_questions = '';
  $construction_number = 0;

  if(mysqli_num_rows($result) > 0)
  {
    while ($row = mysqli_fetch_object($result)) {
      $construction_id = $row -> id;
      $construction_number = $row -> con_number;
      $construction_name = $row -> name;
      $construction_questions = $row -> question_ids;
    }

    $html .= generateMenu($construction_name, $construction_number);
    $html .= '~??~??~';
    $html .= generateQuestions($construction_id, $construction_questions, $construction_number);

    echo $html;
  }else{
    echo 'Error';
    return;
  }

  mysqli_close($link);

}else if(isset($_GET['sendQuestionnaire']) && $_GET['sendQuestionnaire'] > 0){

  $link = mysqli_connect($host, $username, $pw, $db) or die('could not connect to database');

  $sendQuestionnaire = $_GET['sendQuestionnaire'];
  $name = $_GET['name'];
  $type = $_GET['type'];
  $conNr = $_GET['conNr'];
  $answers = $_GET['answers'];

  $isConstruction = 0;
  if($conNr != -1){
    $isConstruction = 1;
    $type = $conNr;
  }

  $insert = "INSERT INTO reports (quest_id, isConstruction, last_checked, last_controller, answers) VALUES (".$type.", ".$isConstruction.", NOW(), '".$name."', '".$answers."')";
  echo $insert;
  $result = mysqli_query($link, $insert) or die(mysqli_error($link));

  // iterate through constructions
  $answers_arr = explode('~~', $answers);
  for($i = 0; $i < sizeof($answers_arr); $i++){
    $question_arr = explode('||', $answers_arr[$i]);
    $construction_id = $question_arr[0];
    $question_ids = '';
    $current_status = -1;
    $query = "SELECT question_ids, current_status FROM constructions WHERE id=" . $construction_id;
    $result = mysqli_query($link, $query) or die(mysqli_error($link));
    while ($row = mysqli_fetch_object($result)) {
      $question_ids = $row -> question_ids;
      $current_status = $row -> current_status;
    }

    if($current_status == 0){
      resetConstruction($construction_id);
    }

    $current_target = 'last';
    switch ($current_status) {
      case 1:
        $current_target = 'maintain';
        break;
      case 2:
        $current_target = 'authorize';
        break;
      default:
        $current_target = 'last';
        break;
    }
    
    // iterate through questions
    $problems = '';
    $construction_status = 0;
    $question_ids_arr = explode('|', $question_ids);
    for($j = 1; $j < sizeof($question_arr); $j++){ // 0 is construction id
      if($question_arr[$j] != '0'){
        echo $i . '/' . $j . ':' . $question_arr[$j] . ' - ';
        $construction_status = 1;
        $question_text = getQuestionText($question_ids[$j - 1]);
        $problems .= '<i>' . $question_text . '</i><br/>' . $question_arr[$j] . '<br/>';
      }
    }

    $next_status = 0;
    if($construction_status == 1){
      $next_status = $current_status + 1;
    }else{
      $next_status = 0;
      $problems = 'i.O.';
    }

    // TODO: format answers + get question text
    // TODO: switch between check, maintain and authorize
    $update = "UPDATE constructions SET current_status=" . $next_status . ", " . $current_target ."_checked=NOW(), " . $current_target ."_controller='" . $name . "', " . $current_target ."_comment='" . $problems . "' WHERE id='" . $construction_id . "'";
    echo 'Update: ' . $update;
    mysqli_query($link, $update);
  }
  
  mysqli_close($link);

}
else if(isset($_GET['resetConstruction']) && $_GET['resetConstruction'] > 0){

  resetConstruction($_GET['resetConstruction']);
  echo 'Success';

}else{
  echo 'No post parameter was submitted...';
}

?>