<?php
include 'app-load.php';

function log_import($content)
{
	$LOG_FILE = 'logs/log-import.log';
	if (!file_exists($LOG_FILE)) {
		file_put_contents($LOG_FILE, "");
	}
	$current_log = file_get_contents($LOG_FILE);
	$new_log = date('d F Y, H:i:s   ') . $content . "\n";
	file_put_contents($LOG_FILE, $current_log . $new_log);
}

$MESIN = db_fetch(" SELECT * FROM mesin ORDER BY MESIN_ID ASC ");
if (count($MESIN) > 0) {
	foreach ($MESIN as $m) {
		if (fp_connect($m->IP, $m->PORT)) {
			$ip = $m->IP;

			//echo $ip.' machine connected<br>';

			$buffer = fp_import_log($ip, "80");
			$buffer = explode("\r\n", $buffer);
			$i = 0;
			$VALUES = array();
			if (count($buffer) > 0) {
				for ($a = 0; $a < count($buffer); $a++) {
					$data = parse_data($buffer[$a], "<Row>", "</Row>");
					$PIN = parse_data($data, "<PIN>", "</PIN>");
					$DateTime = parse_data($data, "<DateTime>", "</DateTime>");
					if (!empty($PIN)) {
						$VALUES[] = "('$PIN','$DateTime')";
					}
				}
			}
			if (count($VALUES) > 0) {
				$VALUES = implode(',', $VALUES);
				db_execute(" INSERT IGNORE INTO log_mesin (`PIN`,`DATE`) VALUES {$VALUES} ");
				$affected = $DB->Affected_Rows();
			}
			if (empty($affected)) $affected = 0;

			if ($affected != 0) {
				log_mail('Attendance has been recorded from ip machine ' . $ip);
			} else {
				log_mail('Attendance cannot be recorded from ip machine ' . $ip);
			}
		}
	}
}
