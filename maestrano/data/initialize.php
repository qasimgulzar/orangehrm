<?php

require_once '../init.php';
require_once '../connec/init.php';

$filepath = '../var/_data_sequence';
$status = false;

if (file_exists($filepath)) {
  // Last update timestamp
  $timestamp = trim(file_get_contents($filepath));
  $current_timestamp = round(microtime(true) * 1000);
  if (empty($timestamp)) { $timestamp = 0; } 

  // Fetch updates
  $client = new Maestrano_Connec_Client('orangehrm.app.dev.maestrano.io');
  $msg = $client->get("updates/$timestamp");
  $code = $msg['code'];

  if($code != 200) {
    error_log("Cannot fetch connec updates code=$code");
  } else {
    $result = json_decode($msg['body'], true);

    // Persist employees
    $employeeMapper = new EmployeeMapper();
    $employeeMapper->persistAll($result['employees']);
  }

  // $status = true;
}

if ($status) {
  file_put_contents($filepath, $current_timestamp);
}

?>
