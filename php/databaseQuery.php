<?php

//include_once('log_lib.php');
include_once('credentials.php');

$link = mysqli_connect($host, $username, $pw, $db)or die('could not connect to database');

if(isset($_POST['getConstructions']) && $_POST['getConstructions'] > 0){
  echo 'worked fine so far...';

  $category = $_POST['getConstructions'];

  $query = 'SELECT * FROM constructions WHERE category="' . $category . '" AND active=1 ORDER BY number ASC';
  $result = mysqli_query($link, $query) or die(mysqli_error());

  $output = '';

  while ($row = mysqli_fetch_object($result)) {

    $status = '';
    switch ($row -> last_status) {
      case 0:
        $status = 'success';
        break;
      case 1:
        $status = 'warning';
        break;
      case 2;
        $status = 'danger';
        break;
    }

    $interval = date('d.m.y', mktime(0, 0, 0, date('m'), date('d') + $row -> interval, date('y')));

    $last_checked = strtotime($row -> last_checked);
    $last_checked = date('d.m.y', $last_checked);

    $output .= '
                <tr class="' . $status . '">
                  <td>' . $row -> number .          '</td>
                  <td>' . $row -> name .            '</td>
                  <td>TODO</td>
                  <td>' . $interval .               '</td>
                  <td>' . $last_checked .           '</td>
                  <td>' . $row -> last_controller . '</td>
                  <td>TODO</td>
                  <td>TODO</td>
                  <td>TODO</td>
                  <td>TODO</td>
                  <td>TODO</td>
                  <td>TODO</td>
                  <td>TODO</td>
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