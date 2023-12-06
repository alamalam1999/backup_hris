<?php

include 'app-load.php';

is_login();

$OP = get_input('op');
$ID = get_input('id');

if ($OP == 'approve') {
  is_login('approval-absensi.change_status');
  $IDS = get_input('ids');

  $rs = db_fetch(" SELECT LOG_ONLINE_ID  
		FROM log_online 
		WHERE LOG_ONLINE_ID IN (" . implode(',', $IDS) . ") AND STATUS = 'PENDING'
	");

  $LOG_ONLINE_ID = array();
  if (count($rs) > 0) {
    foreach ($rs as $row) {
      $LOG_ONLINE_ID[] = $row->LOG_ONLINE_ID;
    }
  }

  if (is_array($LOG_ONLINE_ID)) {
    $CU = current_user();
    $TIME = date('Y-m-d H:i:s');
    db_execute(" UPDATE log_online SET STATUS='APPROVED', APPROVED_BY='$CU->NAMA', APPROVED_ON='$TIME' WHERE LOG_ONLINE_ID IN (" . implode(',', $LOG_ONLINE_ID) . ")");
  }
  header('location: approval-absensi.php');
  exit;
}

if ($OP == 'unapprove') {
  is_login('approval-absensi.change_status');
  $IDS = get_input('ids');

  $rs = db_fetch(" SELECT LOG_ONLINE_ID  
		FROM log_online 
		WHERE LOG_ONLINE_ID IN (" . implode(',', $IDS) . ") AND STATUS = 'PENDING'
	");

  $LOG_ONLINE_ID = array();
  if (count($rs) > 0) {
    foreach ($rs as $row) {
      $LOG_ONLINE_ID[] = $row->LOG_ONLINE_ID;
    }
  }

  if (is_array($LOG_ONLINE_ID)) {
    $CU = current_user();
    $TIME = date('Y-m-d H:i:s');
    db_execute(" UPDATE log_online SET STATUS='UNAPPROVED', APPROVED_BY='$CU->NAMA', APPROVED_ON='$TIME' WHERE LOG_ONLINE_ID IN (" . implode(',', $LOG_ONLINE_ID) . ")");
  }
  header('location: approval-absensi.php');
  exit;
}