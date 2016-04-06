<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once('credentials.php');
require_once('log_lib.php');

define("EMPTY","---");
define("SPLIT","|");

if(isset($_POST['getConstructions']) && $_POST['getConstructions'] > 0){

  $link = mysqli_connect($host, $username, $pw, $db)or die('could not connect to database');

  $category = $_POST['getConstructions'];

  $query = 'SELECT * FROM constructions WHERE category="' . $category . '" AND active=1 ORDER BY con_number ASC';
  $result = mysqli_query($link, $query) or die(mysqli_error($link));

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
                  <td>' . $row -> con_number .        '</td>
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
                  <td><i><a class="btn btn-xs btn-default" href="questionnaire.html?checkConstruction=' . $row -> con_number . '&conType=' . $category . '" target="_blank">Prüfen</a></i></td>
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

  $link = mysqli_connect($host, $username, $pw, $db)or die('could not connect to database');

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

  $link = mysqli_connect($host, $username, $pw, $db)or die('could not connect to database');

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
  
  echo $result;

  mysqli_close($link);

}else{
  echo 'No post parameter was submitted...';
}

?>