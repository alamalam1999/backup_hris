<?php
include 'app-load.php';

$ip = get_input('ip');
//echo($ip); die();
$buffer = fp_import_log($ip, "80");
$buffer = explode("\r\n", $buffer);
$i = 0;
$VALUES = array();
if (count($buffer) > 0) {
	for ($a = 0; $a < count($buffer); $a++) {
		$data = parse_data($buffer[$a], "<Row>", "</Row>");
		$PIN = parse_data($data, "<PIN>", "</PIN>");
		$DateTime = parse_data($data, "<DateTime>", "</DateTime>");
		//$Verified = parse_data($data,"<Verified>","</Verified>");
		//$Status = parse_data($data,"<Status>","</Status>");
		if (!empty($PIN)) {
			$VALUES[] = "('$PIN','$DateTime')";
		}
	}
}
$affected = '';
if (count($VALUES) > 0) {
	$VALUES = implode(',', $VALUES);
	db_execute(" INSERT IGNORE INTO log_mesin (`PIN`,`DATE`) VALUES {$VALUES} ");
	$affected = $DB->Affected_Rows();
}
if (empty($affected)) $affected = 0;
echo json_encode(array('status' => $affected));
