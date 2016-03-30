<?php

//include_once('log_lib.php');
include_once('credentials.php');

define("EMPTY","---");

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

}else{
  echo 'No post parameter was submitted...';
}

?>