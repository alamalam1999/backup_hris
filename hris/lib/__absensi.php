<?php

	function periode_option()
	{	
		$rs = db_fetch(" SELECT PERIODE_ID,PERIODE FROM periode WHERE STATUS_PERIODE='OPEN' ORDER BY TANGGAL_MULAI DESC ");
		$t = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$t[$row->PERIODE_ID] = $row->PERIODE;
			}
		}
		return $t;
	}
	
	function project_option($ALL = 1)
	{
		$CU = current_user();
		$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

		$where = '';
		if( ! empty($PROJECT_ID))
		{
			$where = " WHERE PROJECT_ID='$PROJECT_ID' ";
			$t = array();
		}
		else
		{
			$t = array(''=>'--all project--');
			if(empty($ALL)) $t = array();
		}
		
		$rs = db_fetch(" SELECT PROJECT_ID,PROJECT FROM project $where ORDER BY PROJECT ASC ");
		
		if(count($rs)>0){
			foreach($rs as $row){
				$t[$row->PROJECT_ID] = $row->PROJECT;
			}
		}
		return $t;
	}

	function project_eksepsi_option($ALL = 1)
	{
		$CU = current_user();
		$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

		$where = '';
		if( ! empty($PROJECT_ID))
		{
			$where = " WHERE PROJECT_ID='$PROJECT_ID' ";
			$t = array();
		}
		else
		{
			$t = array(''=>'--all project--');
			if(empty($ALL)) $t = array();
		}
		
		$ek = db_fetch("
			SELECT PROJECT_ID,COUNT(1) as cnt FROM eksepsi A
			LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.KARYAWAN_ID
			WHERE STATUS='PENDING' GROUP BY PROJECT_ID
		");
		$EX = array();
		if( count($ek) > 0 ){
			foreach($ek as $row){
				$EX[$row->PROJECT_ID] = $row->cnt;
			}
		}
		
		$rs = db_fetch(" SELECT PROJECT_ID,PROJECT FROM project $where ORDER BY PROJECT ASC ");
		
		if(count($rs)>0){
			foreach($rs as $row){
				$ek = isset($EX[$row->PROJECT_ID]) ? ' ----- ('.$EX[$row->PROJECT_ID].')' : '';
				$t[$row->PROJECT_ID] = $row->PROJECT . $ek;
			}
		}
		return $t;
	}
	
	function project_lembur_option($ALL = 1)
	{
		$CU = current_user();
		$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';

		$where = '';
		if( ! empty($PROJECT_ID))
		{
			$where = " WHERE PROJECT_ID='$PROJECT_ID' ";
			$t = array();
		}
		else
		{
			$t = array(''=>'--all project--');
			if(empty($ALL)) $t = array();
		}
		
		$ek = db_fetch("
			SELECT PROJECT_ID,COUNT(1) as cnt FROM lembur A
			LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.KARYAWAN_ID
			WHERE STATUS='PENDING' GROUP BY PROJECT_ID
		");
		$EX = array();
		if( count($ek) > 0 ){
			foreach($ek as $row){
				$EX[$row->PROJECT_ID] = $row->cnt;
			}
		}
		
		$rs = db_fetch(" SELECT PROJECT_ID,PROJECT FROM project $where ORDER BY PROJECT ASC ");
		
		if(count($rs)>0){
			foreach($rs as $row){
				$ek = isset($EX[$row->PROJECT_ID]) ? ' ---- ('.$EX[$row->PROJECT_ID].')' : '';
				$t[$row->PROJECT_ID] = $row->PROJECT . $ek;
			}
		}
		return $t;
	}
	
	function simple_scan($PIN,$date1,$date2,$SHIFT)
	{
		$PIN = db_escape($PIN);
		$date1 = db_escape($date1);
		$LOG = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND (DATE(DATE) BETWEEN '$date1' AND '$date2') ");
		
		$scan_array = array();
		foreach($LOG as $sc)
		{
			$scan_array[] = strtotime($sc->unix_date);
		}
		
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		
		$result = array();
		$overnight = 0;
		
		while($time1 <= $time2)
		{
			$loop_date = date('Y-m-d',$time1);
			
			$SHIFT_CODE = isset($SHIFT[$PIN][$loop_date]->SHIFT_CODE) ? $SHIFT[$PIN][$loop_date]->SHIFT_CODE : '';
			$OVERNIGHT = isset($SHIFT[$PIN][$loop_date]->OVERNIGHT) ? $SHIFT[$PIN][$loop_date]->OVERNIGHT : '';
			
			$check_in = isset($SHIFT[$PIN][$loop_date]->START_TIME) ? $SHIFT[$PIN][$loop_date]->START_TIME : '';
			$START_BEGIN = isset($SHIFT[$PIN][$loop_date]->START_BEGIN) ? $SHIFT[$PIN][$loop_date]->START_BEGIN : '';
			$START_END = isset($SHIFT[$PIN][$loop_date]->START_END) ? $SHIFT[$PIN][$loop_date]->START_END : '';
			
			$check_out = isset($SHIFT[$PIN][$loop_date]->FINISH_TIME) ? $SHIFT[$PIN][$loop_date]->FINISH_TIME : '';
			$FINISH_BEGIN = isset($SHIFT[$PIN][$loop_date]->FINISH_BEGIN) ? $SHIFT[$PIN][$loop_date]->FINISH_BEGIN : '';
			$FINISH_END = isset($SHIFT[$PIN][$loop_date]->FINISH_END) ? $SHIFT[$PIN][$loop_date]->FINISH_END : '';
			
			$check_in_date = strtotime($loop_date.' '.$check_in);
			$check_out_date = strtotime($loop_date.' '.$check_out);
			
			/* Overnight trigger */
			if(($check_in_date > $check_out_date) OR $OVERNIGHT=='YES')
			{
				$overnight = 1;
				$new_loop_date = date('Y-m-d',strtotime($loop_date . ' +1 day'));
				$check_out_date = strtotime($new_loop_date.' '.$check_out);
				
				$NEW_LOG = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND DATE(DATE)= '$new_loop_date' ");
				$new_scan_array = array();
				foreach($NEW_LOG as $sc)
				{
					$new_scan_array[] = strtotime($sc->unix_date);
				}
				
				$scan_array = array_merge($scan_array, $new_scan_array);
			}
			
			$scan_in_start_date = $check_in_date - (3600 * $START_BEGIN);
			$scan_in_finish_date = $check_in_date + (3600 * $START_END);
			$scan_out_start_date = $check_out_date - (3600 * $FINISH_BEGIN);
			$scan_out_finish_date = $check_out_date + (3600 * $FINISH_END);
			
			if(valid_time($check_in) AND valid_time($check_out) AND $time1 <= time())
			{
				$working_day = 1;
				$scan_in_candidate = $scan_out_candidate = array();
				if(count($scan_array)>0){
					foreach($scan_array as $sa){
						if($sa >= $scan_in_start_date AND $sa <= $scan_in_finish_date){
							$scan_in_candidate[] = $sa;
						}
						if($sa >= $scan_out_start_date AND $sa <= $scan_out_finish_date){
							$scan_out_candidate[] = $sa;
						}
					}
					if(count($scan_in_candidate)>0) $scan_in = min($scan_in_candidate);
					if(count($scan_out_candidate)>0) $scan_out = max($scan_out_candidate);
					
					if(!empty($scan_in) AND !empty($scan_out))
					{
						if($scan_in > $check_in_date){
							$late = $scan_in - $check_in_date;
						}
						$late_minute = empty($late) ? '' : floor($late/60);
						
						if($scan_out < $check_out_date){
							$early = $check_out_date - $scan_out;
						}
						$early_minute = empty($early) ? '' : round($early/60,0);
					
						$minute = floor(($check_out_date - $check_in_date)/60);
						$att_minute = $minute - $late_minute - $early_minute - $rest_time;
					}
				}
			}
			$result[$loop_date]['check_in'] = $check_in;
			$result[$loop_date]['check_out'] = $check_out;
			$result[$loop_date]['scan_in'] = empty($scan_in) ? '' : date('H:i',$scan_in);
			$result[$loop_date]['scan_out'] = empty($scan_out) ? '' : date('H:i',$scan_out);
			$result[$loop_date]['late'] = $late_minute;
			$result[$loop_date]['early'] = $early_minute;
			$result[$loop_date]['overnight'] = $overnight;
			
			$time1 = strtotime("+1 day", $time1);
		}
		return $result;
	}

	function simple_manual_scan($PIN,$date1,$SHIFT)
	{
		$PIN = db_escape($PIN);
		$date1 = db_escape($date1);
		$LOG = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND (DATE(DATE) BETWEEN '$date1' AND '$date1') ");
		
		$scan_array = array();
		foreach($LOG as $sc)
		{
			$scan_array[] = strtotime($sc->unix_date);
		}
		
		$time1 = strtotime($date1);
		$time2 = strtotime($date1);
		
		$result = array();
		$overnight = 0;
		
		while($time1 <= $time2)
		{
			$loop_date = date('Y-m-d',$time1);
			
			$check_in = isset($SHIFT['START_TIME']) ? $SHIFT['START_TIME'] : '';
			$START_BEGIN = 0.5;
			$START_END = 0;
			
			$check_out = isset($SHIFT['FINISH_TIME']) ? $SHIFT['FINISH_TIME'] : '';
			$FINISH_BEGIN = 0;
			$FINISH_END = 1;
			
			$check_in_date = strtotime($loop_date.' '.$check_in);
			$check_out_date = strtotime($loop_date.' '.$check_out);
			
			/* Overnight trigger */
			if($check_in_date > $check_out_date)
			{
				$overnight = 1;
				$new_loop_date = date('Y-m-d',strtotime($loop_date . ' +1 day'));
				$check_out_date = strtotime($new_loop_date.' '.$check_out);
				
				$NEW_LOG = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND DATE(DATE)= '$new_loop_date' ");
				$new_scan_array = array();
				foreach($NEW_LOG as $sc)
				{
					$new_scan_array[] = strtotime($sc->unix_date);
				}
				
				$scan_array = array_merge($scan_array, $new_scan_array);
			}
			
			$scan_in_start_date = $check_in_date - (3600 * $START_BEGIN);
			$scan_in_finish_date = $check_in_date + (3600 * $START_END);
			$scan_out_start_date = $check_out_date - (3600 * $FINISH_BEGIN);
			$scan_out_finish_date = $check_out_date + (3600 * $FINISH_END);
			
			if(valid_time($check_in) AND valid_time($check_out) AND $time1 <= time())
			{
				$working_day = 1;
				$scan_in_candidate = $scan_out_candidate = array();
				if(count($scan_array)>0){
					foreach($scan_array as $sa){
						if($sa >= $scan_in_start_date AND $sa <= $scan_in_finish_date){
							$scan_in_candidate[] = $sa;
						}
						if($sa >= $scan_out_start_date AND $sa <= $scan_out_finish_date){
							$scan_out_candidate[] = $sa;
						}
					}
					if(count($scan_in_candidate)>0) $scan_in = min($scan_in_candidate);
					if(count($scan_out_candidate)>0) $scan_out = max($scan_out_candidate);
					
					if(!empty($scan_in) AND !empty($scan_out))
					{
						if($scan_in > $check_in_date){
							$late = $scan_in - $check_in_date;
						}
						$late_minute = empty($late) ? '' : floor($late/60);
						
						if($scan_out < $check_out_date){
							$early = $check_out_date - $scan_out;
						}
						$early_minute = empty($early) ? '' : round($early/60,0);
					
						$minute = floor(($check_out_date - $check_in_date)/60);
						$att_minute = $minute - $late_minute - $early_minute - $rest_time;
					}
				}
			}
			$result[$loop_date]['check_in'] = $check_in;
			$result[$loop_date]['check_out'] = $check_out;
			$result[$loop_date]['scan_in'] = empty($scan_in) ? '' : date('H:i',$scan_in);
			$result[$loop_date]['scan_out'] = empty($scan_out) ? '' : date('H:i',$scan_out);
			$result[$loop_date]['late'] = $late_minute;
			$result[$loop_date]['early'] = $early_minute;
			$result[$loop_date]['overnight'] = $overnight;
			
			$time1 = strtotime("+1 day", $time1);
		}
		return $result;
	}
	
	function parse_scan($PIN,$date1,$date2,$SHIFT)
	{
		$PIN = db_escape($PIN);
		$date1 = db_escape($date1);
		$date2 = db_escape($date2);
		$LOG = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND (DATE(DATE) BETWEEN '$date1' AND '$date2') ");
		
		#$JADWAL = db_first(" SELECT * FROM jadwal WHERE JADWAL_ID='1' ");
		#$ROLE = isset($JADWAL->DATA) ? json_decode(stripslashes($JADWAL->DATA)) : array();
		#$HOLIDAY = $this->holiday($date1,$date2);
		#$NOTE = $this->note($employee_id,$date1,$date2);
		
		$EXCEPTION = get_eksepsi($PIN,$date1,$date2);
		$LEMBUR = get_lembur($PIN,$date1,$date2);

		$scan_array = array();
		foreach($LOG as $sc)
		{
			$scan_array[] = strtotime($sc->unix_date);
		}
		
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		$result = array();
		
		$total_day = 0;
		$total_working_day = $total_attendance = $total_absent = $total_late = $total_early = $total_holiday = $total_lembur = 0;
		$total_sakit = $total_ijin = $total_skd = $total_ci = $total_ct = $total_to = $total_ts = $total_bm = $total_r = 0;
		$total_backup = $total_cm = $total_dinas = $total_ul = $total_ijin_le = $total_sm = 0;

		while($time1 <= $time2)
		{
			/*if($time1 > time()){
				$time1 = strtotime("+1 day", $time1);
				continue;
			}*/
			
			$total_day = $total_day + 1;
			$loop_day = date('w',$time1);
			$loop_date = date('Y-m-d',$time1);

			$SHIFT_CODE = isset($SHIFT[$PIN][$loop_date]->SHIFT_CODE) ? $SHIFT[$PIN][$loop_date]->SHIFT_CODE : '';
			$OVERNIGHT = isset($SHIFT[$PIN][$loop_date]->OVERNIGHT) ? $SHIFT[$PIN][$loop_date]->OVERNIGHT : '';
			
			$check_in = isset($SHIFT[$PIN][$loop_date]->START_TIME) ? $SHIFT[$PIN][$loop_date]->START_TIME : '';
			$START_BEGIN = isset($SHIFT[$PIN][$loop_date]->START_BEGIN) ? $SHIFT[$PIN][$loop_date]->START_BEGIN : '';
			$START_END = isset($SHIFT[$PIN][$loop_date]->START_END) ? $SHIFT[$PIN][$loop_date]->START_END : '';
			
			$check_out = isset($SHIFT[$PIN][$loop_date]->FINISH_TIME) ? $SHIFT[$PIN][$loop_date]->FINISH_TIME : '';
			$FINISH_BEGIN = isset($SHIFT[$PIN][$loop_date]->FINISH_BEGIN) ? $SHIFT[$PIN][$loop_date]->FINISH_BEGIN : '';
			$FINISH_END = isset($SHIFT[$PIN][$loop_date]->FINISH_END) ? $SHIFT[$PIN][$loop_date]->FINISH_END : '';
			
			/* change shift via exception */
			foreach(array('TO_IN','TO_OUT','TS','TS_OUT') as $EX)
			{
				if(isset($EXCEPTION[$loop_date][$EX]))
				{
					$SHIFT_CODE = $SHIFT_CODE_OVERRIDE = $EXCEPTION[$loop_date][$EX]->SHIFT_CODE;
					$sc = db_first(" SELECT * FROM shift WHERE SHIFT_CODE='$SHIFT_CODE' ");
					$OVERNIGHT = isset($sc->OVERNIGHT) ? $sc->OVERNIGHT : '';
					
					$check_in = isset($sc->START_TIME) ? $sc->START_TIME : '';
					$START_BEGIN = isset($sc->START_BEGIN) ? $sc->START_BEGIN : '';
					$START_END = isset($sc->START_END) ? $sc->START_END : '';
					
					$check_out = isset($sc->FINISH_TIME) ? $sc->FINISH_TIME : '';
					$FINISH_BEGIN = isset($sc->FINISH_BEGIN) ? $sc->FINISH_BEGIN : '';
					$FINISH_END = isset($sc->FINISH_END) ? $sc->FINISH_END : '';
				}
			}
			
			if(isset($EXCEPTION[$loop_date]['IJIN_LE']))
			{
				$LE = $EXCEPTION[$loop_date]['IJIN_LE'];
				$check_in = isset($LE->JAM_MULAI) ? $LE->JAM_MULAI : '';
				$START_BEGIN = 2;
				$START_END = 2;
					
				$check_out = isset($LE->JAM_SELESAI) ? $LE->JAM_SELESAI : '';
				$FINISH_BEGIN = 2;
				$FINISH_END = 2;
			}
			
			$check_in_date = strtotime($loop_date.' '.$check_in);
			$check_out_date = strtotime($loop_date.' '.$check_out);
			
			/* Overnight trigger */
			if(($check_in_date > $check_out_date) OR $OVERNIGHT=='YES')
			{
				$new_loop_date = date('Y-m-d',strtotime($loop_date . ' +1 day'));
				$check_out_date = strtotime($new_loop_date.' '.$check_out);
				
				$NEW_LOG = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND DATE(DATE)= '$new_loop_date' ");
				$new_scan_array = array();
				foreach($NEW_LOG as $sc)
				{
					$new_scan_array[] = strtotime($sc->unix_date);
				}
				$scan_array = array_merge($scan_array, $new_scan_array);
			}
			
			$scan_in_start_date = $check_in_date - (3600 * $START_BEGIN);
			$scan_in_finish_date = $check_in_date + (3600 * $START_END);
			$scan_out_start_date = $check_out_date - (3600 * $FINISH_BEGIN);
			$scan_out_finish_date = $check_out_date + (3600 * $FINISH_END);

			#if($PIN==28){
			#echo 'Scan In : '.date('Y-m-d H:i',$check_in_date).'<br>';
			#echo 'Scan In Begin : '.date('Y-m-d H:i',$scan_in_start_date).'<br>';
			#echo 'Scan In End : '.date('Y-m-d H:i',$scan_in_finish_date).'<br>';
			#echo 'Scan Out : '.date('Y-m-d H:i',$check_out_date).'<br>';
			#echo 'Scan Out Begin : '.date('Y-m-d H:i',$scan_out_start_date).'<br>';
			#echo 'Scan Out End : '.date('Y-m-d H:i',$scan_out_finish_date).'<br>';
			#die;
			#}
			
			
			$scan_in = $scan_out = $late = $early = $late_minute = $early_minute = '';
			$extra_work_time = $extra_work_time_minute = '';
			$att_minute = $no_att_minute = $working_day = $attendance = $absent = $holiday = $note = $plain_note = '';
			$exception = $lembur = '';
			
			$rest_time = 60;
			//if($loop_day=='5') $rest_time = 90;
				
			if(valid_time($check_in) AND valid_time($check_out)) /* AND $time1 <= time() */
			{
				$working_day = 1;
				$scan_in_candidate = $scan_out_candidate = array();
				if(count($scan_array)>0){
					foreach($scan_array as $sa){
						if($sa >= $scan_in_start_date AND $sa <= $scan_in_finish_date){
							$scan_in_candidate[] = $sa;
						}
						if($sa >= $scan_out_start_date AND $sa <= $scan_out_finish_date){
							$scan_out_candidate[] = $sa;
						}
					}
					if(count($scan_in_candidate)>0) $scan_in = min($scan_in_candidate);
					if(count($scan_out_candidate)>0) $scan_out = max($scan_out_candidate);
					
					if(!empty($scan_in) AND !empty($scan_out))
					{
						if($scan_in > $check_in_date){
							$late = $scan_in - $check_in_date;
						}
						$late_minute = empty($late) ? '' : floor($late/60);
						
						if($scan_out < $check_out_date){
							$early = $check_out_date - $scan_out;
						}
						$early_minute = empty($early) ? '' : round($early/60,0);
						
						// extra work time
						$extra_work_time = 0;
						if($scan_out > $check_out_date){
							$extra_work_time = $scan_out - $check_out_date;
						}
						$extra_work_time_minute = empty($extra_work_time) ? '' : floor($extra_work_time/60);
						if($extra_work_time_minute < 40) $extra_work_time_minute = 0;
						
						if($late_minute <= 5){
							//$late_minute = 0;
							/*if($extra_work_time_minute >= $late_minute){
								$late_minute = 0;
							} else {
								$late_minute = $late_minute - $extra_work_time_minute;
							}*/
						}
						
						$minute = floor(($check_out_date - $check_in_date)/60);
						$att_minute = $minute - $late_minute - $early_minute - $rest_time;
						
						$attendance = 1;
					}
					else
					{
						
						$minute = floor(($check_out_date - $check_in_date)/60);
						$no_att_minute = $minute - $rest_time;

						$absent = 1;
					}
				}else{
					$absent = 1;
				}
			}
			else if( in_array($SHIFT_CODE,array('X','OFF')))
			{
				$holiday = 1;
				$absent = '';
			}
			
			#if($PIN==28){
			#echo 'Scan In : '.date('H:i',$scan_in).'<br>';
			#echo 'Scan Out : '.date('H:i',$scan_out).'<br>';
			#die;
			#}
			
			$tmp_absent = $absent;
			foreach(array('SAKIT','IJIN','IJIN_LE','SKD','BACKUP','CI','CT','CM','DINAS','UL','TO_IN','TO_OUT','TS','TS_IN','TS_OUT','BM','R','SM') as $EX){
				if( ! isset(${'total_'.strtolower($EX)}) ) ${'total_'.strtolower($EX)} = 0;
				if(isset($EXCEPTION[$loop_date][$EX]) /*AND ($holiday!='1')*/){
					
					$attendance = 0;
					$exception = 1;
					$absent = '';
					$no_att_minute = 0;
					$result[$loop_date][$EX] = 1;
					$note = $EXCEPTION[$loop_date][$EX]->KETERANGAN;
					if( isset($SHIFT_CODE_OVERRIDE) ){
						$note .= " ## Jadwal : ".$SHIFT_CODE_OVERRIDE.' ('.$check_in.'-'.$check_out.')';
					}
					${'total_'.strtolower($EX)} = ${'total_'.strtolower($EX)} + 1;
					if($EX=='TO_OUT'){
						$total_to = $total_to + 1;
					}
					if($EX=='TS_OUT'){
						$total_ts = $total_ts + 1;
					}
					if($EX=='IJIN_LE' AND $tmp_absent=='1'){
						$absent = 1;
						$exception = 0;
						$result[$loop_date][$EX] = 0;
					}
				}
			}
			
			if(isset($LEMBUR[$loop_date])){
				$lembur = 1;
				$absent = '';
				$no_att_minute = 0;
				$result[$loop_date]['lembur'] = 1;
				$note = $LEMBUR[$loop_date]->JENIS.' : '.$LEMBUR[$loop_date]->KETERANGAN;
			}

			$result[$loop_date]['day'] = $loop_day;
			$result[$loop_date]['check_in'] = $check_in;
			$result[$loop_date]['check_out'] = $check_out;
			$result[$loop_date]['scan_in'] = empty($scan_in) ? '' : date('H:i',$scan_in);
			$result[$loop_date]['scan_out'] = empty($scan_out) ? '' : date('H:i',$scan_out);
			$result[$loop_date]['late'] = $late_minute;
			$result[$loop_date]['early'] = $early_minute;
			$result[$loop_date]['extra_work'] = $extra_work_time_minute;
			$result[$loop_date]['att_minute'] = $att_minute;
			$result[$loop_date]['no_att_minute'] = $no_att_minute;
			$result[$loop_date]['working_day'] = $working_day;
			$result[$loop_date]['attendance'] = $attendance;
			$result[$loop_date]['absent'] = $absent;
			$result[$loop_date]['type'] = $type;
			$result[$loop_date]['holiday'] = $holiday;
			$result[$loop_date]['exception'] = $exception;
			$result[$loop_date]['plain_note'] = $plain_note;
			$result[$loop_date]['note'] = $note;
			
			$total_working_day = $total_working_day + $working_day;
			$total_attendance = $total_attendance + $attendance;
			if( $late_minute > 0 ) $total_late = $total_late + 1;
			if( $early_minute > 0 ) $total_early = $total_early + 1;
			$total_absent = $total_absent + intval($absent);
			$total_holiday = $total_holiday + intval($holiday);
			$total_lembur = $total_lembur + intval($lembur);
				
			$time1 = strtotime("+1 day", $time1);
		}
		
		$result['total_day'] = $total_day;
		$result['total_working_day'] = $total_working_day;
		$result['total_attendance'] = $total_attendance;
		$result['total_late'] = $total_late;
		$result['total_early'] = $total_early;
		$result['total_absent'] = $total_absent;
		$result['total_holiday'] = $total_holiday;
		$result['total_lembur'] = $total_lembur;
		
		$result['total_sakit'] = isset($total_sakit) ? $total_sakit : 0;
		$result['total_ijin'] = isset($total_ijin) ? $total_ijin : 0;
		$result['total_ijin_le'] = isset($total_ijin_le) ? $total_ijin_le : 0;
		$result['total_skd'] = isset($total_skd) ? $total_skd : 0;
		$result['total_ci'] = isset($total_ci) ? $total_ci : 0;
		$result['total_ct'] = isset($total_ct) ? $total_ct : 0;
		$result['total_to'] = isset($total_to) ? $total_to : 0;
		$result['total_ts'] = isset($total_ts) ? $total_ts : 0;
		$result['total_bm'] = isset($total_bm) ? $total_bm : 0;
		$result['total_r'] = isset($total_r) ? $total_r : 0;
		$result['total_backup'] = isset($total_backup) ? $total_backup : 0;
		$result['total_cm'] = isset($total_cm) ? $total_cm : 0;
		$result['total_dinas'] = isset($total_dinas) ? $total_dinas : 0;
		$result['total_ul'] = isset($total_ul) ? $total_ul : 0;
		$result['total_sm'] = isset($total_sm) ? $total_sm : 0;

		return $result;
	}

	function insert_eksepsi_detail($ID,$date1,$date2)
	{
		$RANGE = date_range($date1,$date2);
		db_execute(" DELETE FROM eksepsi_detail WHERE EKSEPSI_ID='$ID' ");
		foreach($RANGE as $tgl){
			db_execute(" INSERT INTO eksepsi_detail (EKSEPSI_ID,TGL) VALUES ('$ID','$tgl') ");
		}
	}
	
	function get_eksepsi($PIN,$date1,$date2)
	{
		$rs = db_fetch("
			SELECT *
			FROM eksepsi A
			LEFT JOIN eksepsi_detail B ON B.EKSEPSI_ID=A.EKSEPSI_ID
			WHERE
				STATUS='APPROVED' AND
				KARYAWAN_ID='$PIN' AND
				(TGL >= '$date1' AND TGL <= '$date2')
		");
		
		$EKSEPSI = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$EKSEPSI[$row->TGL][$row->JENIS] = $row;
			}
		}
		return $EKSEPSI;
	}

	function get_lembur($PIN,$date1,$date2)
	{
		$rs = db_fetch("
			SELECT *
			FROM lembur A
			WHERE
				STATUS='APPROVED' AND
				KARYAWAN_ID='$PIN' AND
				(TANGGAL >= '$date1' AND TANGGAL <= '$date2')
		");
		
		$LEMBUR = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$LEMBUR[$row->TANGGAL] = $row;
			}
		}
		return $LEMBUR;
	}

	function hitung_lembur_LHK($JAM,$GAJI_POKOK,$ADJ=0)
	{
		$LHK = 0;
		
		if($ADJ == 0)
		{
			if($JAM >= 16)
			{
				$ADJ = $JAM - 2;
			}
			else if($JAM >= 12)
			{
				$ADJ = $JAM - 1.5;
			}
			else if($JAM >= 8)
			{
				$ADJ = $JAM - 1;
			}
			else if($JAM >= 4)
			{
				$ADJ = $JAM - 0.5;
			}
			else if($JAM < 4)
			{
				$ADJ = $JAM;
			}
		}
						
		$PENGALI1 = $PENGALI2 = $POINT1 = $POINT2 = $TOTAL_POINT = 0;
		$RUNNING = $ADJ;
		if( $RUNNING > 0 )
		{
			$PENGALI1 = 1;
			$POINT1 = $PENGALI1 * 1.5;
			$RUNNING = $RUNNING - $PENGALI1;
		}
		if( $RUNNING > 0 )
		{
			$PENGALI2 = $RUNNING;
			$POINT2 = $PENGALI2 * 2;
		}
		$TOTAL_POINT = $POINT1 + $POINT2;
		$LHK = $TOTAL_POINT * round((1/173)*$GAJI_POKOK,0);
		
		$ret = array(
			'ADJ' => $ADJ,
			'PENGALI1' => $PENGALI1,
			'PENGALI2' => $PENGALI2,
			'POINT1' => $POINT1,
			'POINT2' => $POINT2,
			'TOTAL_POINT' => $TOTAL_POINT,
			'TOTAL' => $LHK,
		);
		
		return $ret;
	}
	
	function hitung_lembur_LHL($TOTAL_JAM,$GAJI_POKOK,$ADJ=0)
	{
		if($ADJ == 0)
		{
			$ADJ = round($TOTAL_JAM,2);
		}
		$LHL = 0;
		$PENGALI1 = 0;
		$PENGALI2 = 2;
		$POINT1 = 0;
		$POINT2 = $ADJ * $PENGALI2;
		$TOTAL_POINT = $POINT1 + $POINT2;
		$LHL = $TOTAL_POINT * round((1/173)*$GAJI_POKOK,0);
		
		$ret = array(
			'ADJ' => $ADJ,
			'PENGALI1' => $PENGALI1,
			'PENGALI2' => $PENGALI2,
			'POINT1' => $POINT1,
			'POINT2' => $POINT2,
			'TOTAL_POINT' => $TOTAL_POINT,
			'TOTAL' => $LHL,
		);
		
		return $ret;
	}

	function hitung_lembur_IHB($TOTAL_JAM,$GAJI_POKOK,$ADJ=0)
	{
		if($ADJ == 0)
		{
			$ADJ = round($TOTAL_JAM,2);
		}
		$LHL = 0;
		$PENGALI1 = 0;
		$PENGALI2 = 0;
		$POINT1 = 0;
		$POINT2 = 0;
		$TOTAL_POINT = $ADJ;
		$LHL = $TOTAL_POINT * round((1/173)*$GAJI_POKOK,0);
		
		$ret = array(
			'ADJ' => $ADJ,
			'PENGALI1' => $PENGALI1,
			'PENGALI2' => $PENGALI2,
			'POINT1' => $POINT1,
			'POINT2' => $POINT2,
			'TOTAL_POINT' => $TOTAL_POINT,
			'TOTAL' => $LHL,
		);
		
		return $ret;
	}
	
	function date_range($date1, $date2)
	{
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);

		$date = array();
		while($time1 <= $time2)
		{
			$date[] = date('Y-m-d',$time1);
			$time1 = strtotime("+1 day", $time1);
		}
		
		return $date;
	}

	function valid_time($time)
	{
		if(preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $time)) return TRUE;
		else return FALSE;
	}
	
	function valid_date($date, $strict = TRUE)
	{
		$dateTime = DateTime::createFromFormat('m/d/Y H:i', $date);
		if ($strict) {
			$errors = DateTime::getLastErrors();
			if (!empty($errors['warning_count'])) {
				return FALSE;
			}
		}
		if($dateTime==FALSE)
		{
			$dateTime = DateTime::createFromFormat('m/d/Y H:i:s', $date);
			if ($strict) {
				$errors = DateTime::getLastErrors();
				if (!empty($errors['warning_count'])) {
					return FALSE;
				}
			}
		}
		
		return $dateTime !== FALSE;
	}

	function cuti_berjalan($JOIN_DATE)
	{
		$START = strtotime($JOIN_DATE);
		$END = strtotime(date('Y-m-d', strtotime($JOIN_DATE . " +1 year ")));
		$MAPPER = array(0,0,3,4,5,6,7,8,9,10,11,12);
		$CUTI = array();
		$i = 0;
		while ($START < $END)
		{
			$KUOTA = isset($MAPPER[$i]) ? $MAPPER[$i] : 0;
			$DATE = date('Y-m-d',$START);
			$CUTI[$DATE] = $KUOTA;
			$START = strtotime("+1 month", $START);
			$i++;
		}
		return $CUTI;
	}

	function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = penyebut($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
 
	function terbilang($nilai) {
		if($nilai<0) {
			$hasil = "minus ". trim(penyebut($nilai));
		} else {
			$hasil = trim(penyebut($nilai));
		}     		
		return $hasil;
	}