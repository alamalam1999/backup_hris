<?php
include 'app-load.php';
$KARYAWAN_ID = get_input('karyawan');
$PERIODE_ID = get_input('periode');

$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
$date1 = $PERIODE->TANGGAL_MULAI;
$date2 = $PERIODE->TANGGAL_SELESAI;
		
$time1 = strtotime($date1);
$time2 = strtotime($date2);

$KARYAWAN = db_first(" SELECT * FROM karyawan WHERE KARYAWAN_ID='$KARYAWAN_ID' ");

$SCAN = parse_scan($KARYAWAN_ID,$date1,$date2);
$_UANG_MAKAN = isset($KARYAWAN->UANG_MAKAN) ? $KARYAWAN->UANG_MAKAN : 0;
$_UANG_KERAJINAN = isset($KARYAWAN->UANG_KERAJINAN) ? $KARYAWAN->UANG_KERAJINAN : 0;
$_UANG_PULSA = isset($KARYAWAN->UANG_PULSA) ? $KARYAWAN->UANG_PULSA : 0;
$_UANG_BERAS = isset($KARYAWAN->UANG_BERAS) ? $KARYAWAN->UANG_BERAS : 0;
$_UANG_LEMBUR = isset($KARYAWAN->UANG_LEMBUR) ? $KARYAWAN->UANG_LEMBUR : 0;
//print_r($SCAN);
$SHOW_HOLIDAY = 1;

$data = array();
$i = 0;
$att_minute_total = $attendance_total = $absent_total = 0;
$dl = $cuti = $sakit = $ijin = 0;
$total_uang_makan = $total_uang_pulsa = $total_uang_beras = $total_uang_lembur;

while($time1 <= $time2)
{
	$loop_date = date('Y-m-d',$time1);
	#$excep = isset($SCAN[$loop_date]['exception']) ? $SCAN[$loop_date]['exception'] : '';

	$HARI = array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
	$space = $space2 = '';
	if(empty($SCAN[$loop_date]['scan_in'])) $space = '&nbsp;';
	if(empty($SCAN[$loop_date]['scan_out'])) $space2 = '&nbsp;';
	$DAY = date('w',$time1);
	$data[$i]['KARYAWAN_ID'] = $KARYAWAN_ID;
	$data[$i]['day_name'] = isset($HARI[$DAY]) ? $HARI[$DAY] : '';
	$data[$i]['plain_date'] = date('Y-m-d',$time1);
	$data[$i]['plain_scan_in'] = isset($SCAN[$loop_date]['scan_in']) ? $SCAN[$loop_date]['scan_in'] : '';
	$data[$i]['plain_scan_out'] = isset($SCAN[$loop_date]['scan_out']) ? $SCAN[$loop_date]['scan_out'] : '';
	$data[$i]['date'] = date('d-M-Y',$time1);
	$data[$i]['check_in'] = isset($SCAN[$loop_date]['check_in']) ? $SCAN[$loop_date]['check_in'] : '';
	$data[$i]['check_out'] = isset($SCAN[$loop_date]['check_out']) ? $SCAN[$loop_date]['check_out'] : '';
	$data[$i]['scan_in'] = isset($SCAN[$loop_date]['scan_in']) ? '<div style="'.$bg_in.'">'.$SCAN[$loop_date]['scan_in'].$space.'</div>' : '';
	$data[$i]['scan_out'] = isset($SCAN[$loop_date]['scan_out']) ? '<div style="'.$bg_out.'">'.$SCAN[$loop_date]['scan_out'].$space2.'</div>' : '';
	$data[$i]['late'] = isset($SCAN[$loop_date]['late']) ? $SCAN[$loop_date]['late'] : '';
	$data[$i]['early'] = isset($SCAN[$loop_date]['early']) ? $SCAN[$loop_date]['early'] : '';
	$data[$i]['att_minute'] = isset($SCAN[$loop_date]['att_minute']) ? $SCAN[$loop_date]['att_minute'] : '';

	$data[$i]['attendance'] = isset($SCAN[$loop_date]['attendance']) ? $SCAN[$loop_date]['attendance'] : '';
	$data[$i]['dl'] = isset($SCAN[$loop_date]['DINAS LUAR']) ? $SCAN[$loop_date]['DINAS LUAR'] : '';
	$data[$i]['cuti'] = isset($SCAN[$loop_date]['CUTI']) ? $SCAN[$loop_date]['CUTI'] : '';
	$data[$i]['sakit'] = isset($SCAN[$loop_date]['SAKIT']) ? $SCAN[$loop_date]['SAKIT'] : '';
	$data[$i]['ijin'] = isset($SCAN[$loop_date]['IJIN']) ? $SCAN[$loop_date]['IJIN'] : '';
	$data[$i]['absent'] = isset($SCAN[$loop_date]['absent']) ? $SCAN[$loop_date]['absent'] : '';
	$data[$i]['type'] = isset($SCAN[$loop_date]['type']) ? $SCAN[$loop_date]['type'] : '';
	$data[$i]['holiday'] = isset($SCAN[$loop_date]['holiday']) ? $SCAN[$loop_date]['holiday'] : '';
	$data[$i]['exception'] = isset($SCAN[$loop_date]['exception']) ? $SCAN[$loop_date]['exception'] : '';
	$data[$i]['extra_work'] = isset($SCAN[$loop_date]['extra_work']) ? $SCAN[$loop_date]['extra_work'] : '';
	$data[$i]['plain_note'] = isset($SCAN[$loop_date]['plain_note']) ? $SCAN[$loop_date]['plain_note'] : '';
	
	$note = isset($SCAN[$loop_date]['note']) ? $SCAN[$loop_date]['note'] : '';
	if($data[$i]['type']=='0' || $data[$i]['holiday']=='1'){
		$data[$i]['note'] = $note;
	}else{
		$STATUS = '';
		if($data[$i]['dl']=='1') $STATUS = 'DINAS LUAR';
		else if($data[$i]['cuti']=='1') $STATUS = 'CUTI';
		else if($data[$i]['sakit']=='1') $STATUS = 'SAKIT';
		else if($data[$i]['ijin']=='1') $STATUS = 'IJIN';
		
		/*if(in_array('individual_report/exception',$access)){
			$data[$i]['status'] = form_dropdown('inp_status',array(''=>'','DINAS LUAR'=>'DINAS LUAR','CUTI'=>'CUTI','TUBEL'=>'TUBEL','CUTI'=>'CUTI','DIKLAT'=>'DIKLAT','SAKIT'=>'SAKIT','IJIN'=>'IJIN'),$STATUS,' class="inp_status" style="border:0;background:0;width:100%;" ');
			$data[$i]['status'] .= '<input type="hidden" class="inp_employee_id" value="'.$employee_id.'">';
			$data[$i]['status'] .= '<input type="hidden" class="inp_date" value="'.$loop_date.'">';
		}else{
			$data[$i]['status'] = $STATUS;
		}*/
		$data[$i]['status'] = $STATUS;
		
		if(isset($data[$i]['exception']) AND $data[$i]['exception']=='1')
		{
			if(in_array('individual_report/exception',$access)){
				$data[$i]['note'] = '<input type="text" class="inp_note tipff" style="border:0;background:0;width:100%;" value="'.$note.'" title="Press enter to save">';
			}else{
				$data[$i]['note'] = $note;
			}
		}
		else
		{
			$data[$i]['note'] = $note;
		}
	}
	
	$UANG_MAKAN = $UANG_PULSA = $UANG_BERAS = 0;
	if($data[$i]['attendance']=='1')
	{
		$UANG_MAKAN = $_UANG_MAKAN;
		$UANG_PULSA = $_UANG_PULSA;
		$UANG_BERAS = $_UANG_BERAS;
		if($data[$i]['late'] > 0){
			$UANG_MAKAN = ($_UANG_MAKAN/2);
		}
		if($data[$i]['early'] > 0){
			$UANG_MAKAN = ($_UANG_MAKAN/2);
		}
	}

	if( $data[$i]['late'] > 0 ) $_UANG_KERAJINAN = 0;
	if( $data[$i]['absent'] > 0 ){
		$UANG_MAKAN = 0;
		$UANG_PULSA = 0;
		$UANG_BERAS = 0;
		$_UANG_KERAJINAN = 0;
	}
	
	$UANG_LEMBUR = 0;
	if($data[$i]['extra_work'])
	{
		$UANG_LEMBUR = $_UANG_LEMBUR;
	}
		
	$data[$i]['uang_makan'] = $UANG_MAKAN;
	$data[$i]['uang_pulsa'] = $UANG_PULSA;
	$data[$i]['uang_beras'] = $UANG_BERAS;
	$data[$i]['uang_lembur'] = $UANG_LEMBUR;
	$total_uang_makan = $total_uang_makan + $UANG_MAKAN;
	$total_uang_pulsa = $total_uang_pulsa + $UANG_PULSA;
	$total_uang_beras = $total_uang_beras + $UANG_BERAS;
	$total_uang_lembur = $total_uang_lembur + $UANG_LEMBUR;

	$att_minute_total = $att_minute_total + $data[$i]['att_minute'];
	$attendance_total = $attendance_total + $data[$i]['attendance'];
	$absent_total = $absent_total + $data[$i]['absent'];

	$dl = $dl + $data[$i]['dl'];
	$cuti = $cuti + $data[$i]['cuti'];
	$sakit = $sakit + $data[$i]['sakit'];
	$ijin = $ijin + $data[$i]['ijin'];
	
	if(empty($SHOW_HOLIDAY) AND $data[$i]['holiday']=='1'){
		unset($data[$i]);
		$i--;
	}
	
	$time1 = strtotime("+1 day", $time1);
	$i++;
}

$DATA1 = array('TOTAL_UANG_MAKAN' => $total_uang_makan, 'TOTAL_UANG_PULSA' => $total_uang_pulsa, 'TOTAL_UANG_BERAS' => $total_uang_beras, 'TOTAL_LEMBUR' => $total_uang_lembur, 'TOTAL_UANG_KERAJINAN' => $_UANG_KERAJINAN,'TOTAL_TUNJANGAN' => $total_uang_makan + $total_uang_pulsa + $total_uang_beras + $total_uang_lembur + $_UANG_KERAJINAN);
$DATA2 = db_first(" SELECT K.GAJI_POKOK,P.TOTAL_POTONGAN,P.POTONGAN_ID FROM karyawan K LEFT JOIN potongan P ON (P.KARYAWAN_ID=K.KARYAWAN_ID) WHERE P.PERIODE_ID='$PERIODE_ID' AND P.KARYAWAN_ID='$KARYAWAN_ID' ");
$DATA2 = json_decode(json_encode($DATA2), True);
$DATA = array_merge($DATA1, $DATA2);
echo json_encode($DATA);