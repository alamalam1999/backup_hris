<?php

function scan_tmp($PIN, $date1, $date2, $SHIFT)
{
    $PIN        = db_escape($PIN);
    $date1      = db_escape($date1);
    $date2      = db_escape($date2);
    $LOG        = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND (DATE(DATE) BETWEEN '$date1' AND '$date2') ");
    $EXCEPTION  = get_eksepsi($PIN, $date1, $date2);
    $LEMBUR     = get_lembur($PIN, $date1, $date2);
    $ONLINE     = get_online_absent($PIN, $date1, $date2);

    $check_rule  = db_first(" SELECT R_9JAM FROM jabatan J LEFT JOIN karyawan K ON K.JABATAN_ID = J.JABATAN_ID WHERE K.KARYAWAN_ID='$PIN' ");
    $NINE_OUR    = empty($check_rule->R_9JAM) ? 0 : $check_rule->R_9JAM;

    $scan_array = array();
    foreach ($LOG as $key => $sc) {
        $scan_array[$key]['scan_time'] = strtotime($sc->unix_date);
        $scan_array[$key]['log_id'] = $sc->id_log_mesin;
    }

    $time1             = strtotime($date1);
    $time2             = strtotime($date2);
    $result            = array();
    $total_day         = 0;
    $total_working_day = $total_attendance = $total_absent = $total_late = $total_early = $total_holiday = $total_lembur = $total_online = 0;
    $total_sakit       = $total_ijin = $total_skd = $total_ci = $total_ct = $total_to = $total_ts = $total_bm = $total_r = 0;
    $total_backup      = $total_cm = $total_dinas = $total_ul = $total_ijin_le = $total_sm = 0;
    while ($time1 <= $time2) {
        $total_day    = $total_day + 1;
        $loop_day     = date('w', $time1);
        $loop_date    = date('Y-m-d', $time1);
        $SHIFT_CODE   = isset($SHIFT[$PIN][$loop_date]->SHIFT_CODE) ? $SHIFT[$PIN][$loop_date]->SHIFT_CODE : '';
        $OVERNIGHT    = isset($SHIFT[$PIN][$loop_date]->OVERNIGHT) ? $SHIFT[$PIN][$loop_date]->OVERNIGHT : '';
        $check_in     = isset($SHIFT[$PIN][$loop_date]->START_TIME) ? $SHIFT[$PIN][$loop_date]->START_TIME : '';
        $START_BEGIN  = isset($SHIFT[$PIN][$loop_date]->START_BEGIN) ? $SHIFT[$PIN][$loop_date]->START_BEGIN : '';
        $START_END    = isset($SHIFT[$PIN][$loop_date]->START_END) ? $SHIFT[$PIN][$loop_date]->START_END : '';
        $check_out    = isset($SHIFT[$PIN][$loop_date]->FINISH_TIME) ? $SHIFT[$PIN][$loop_date]->FINISH_TIME : '';
        $FINISH_BEGIN = isset($SHIFT[$PIN][$loop_date]->FINISH_BEGIN) ? $SHIFT[$PIN][$loop_date]->FINISH_BEGIN : '';
        $FINISH_END   = isset($SHIFT[$PIN][$loop_date]->FINISH_END) ? $SHIFT[$PIN][$loop_date]->FINISH_END : '';

        foreach (array(
            'TO_IN',
            'TO_OUT',
            'TS',
            'TS_OUT'
        ) as $EX) {
            if (isset($EXCEPTION[$loop_date][$EX])) {
                $SHIFT_CODE   = $SHIFT_CODE_OVERRIDE = $EXCEPTION[$loop_date][$EX]->SHIFT_CODE;
                $sc           = db_first(" SELECT * FROM shift WHERE SHIFT_CODE='$SHIFT_CODE' ");
                $OVERNIGHT    = isset($sc->OVERNIGHT) ? $sc->OVERNIGHT : '';
                $check_in     = isset($sc->START_TIME) ? $sc->START_TIME : '';
                $START_BEGIN  = isset($sc->START_BEGIN) ? $sc->START_BEGIN : '';
                $START_END    = isset($sc->START_END) ? $sc->START_END : '';
                $check_out    = isset($sc->FINISH_TIME) ? $sc->FINISH_TIME : '';
                $FINISH_BEGIN = isset($sc->FINISH_BEGIN) ? $sc->FINISH_BEGIN : '';
                $FINISH_END   = isset($sc->FINISH_END) ? $sc->FINISH_END : '';
            }
        }

        if (isset($EXCEPTION[$loop_date]['IJIN_LE'])) {
            $LE           = $EXCEPTION[$loop_date]['IJIN_LE'];
            $check_in     = isset($LE->JAM_MULAI) ? $LE->JAM_MULAI : '';
            $START_BEGIN  = 2;
            $START_END    = 2;
            $check_out    = isset($LE->JAM_SELESAI) ? $LE->JAM_SELESAI : '';
            $FINISH_BEGIN = 2;
            $FINISH_END   = 2;
        }

        $check_in_date  = strtotime($loop_date . ' ' . $check_in);
        $check_out_date = strtotime($loop_date . ' ' . $check_out);
        if (($check_in_date > $check_out_date) or $OVERNIGHT == 'YES') {
            $new_loop_date  = date('Y-m-d', strtotime($loop_date . ' +1 day'));
            $check_out_date = strtotime($new_loop_date . ' ' . $check_out);
            $NEW_LOG        = db_fetch(" SELECT DATE as unix_date FROM log_mesin WHERE PIN='$PIN' AND DATE(DATE)= '$new_loop_date' ");
            $new_scan_array = array();
            foreach ($NEW_LOG as $key => $sc) {
                $new_scan_array[$key]['scan_time'] = strtotime($sc->unix_date);
                $new_scan_array[$key]['log_id'] = $sc->id_log_mesin;
            }
            $scan_array = array_merge($scan_array, $new_scan_array);
        }

        $scan_in_start_date   = $check_in_date - (3600 * $START_BEGIN);
        $scan_in_finish_date  = $check_in_date + (3600 * $START_END);
        $scan_out_start_date  = $check_out_date - (3600 * $FINISH_BEGIN);
        $scan_out_finish_date = $check_out_date + (3600 * $FINISH_END);
        $scan_in              = $scan_out = $late = $early = $late_minute = $early_minute = '';
        $extra_work_time      = $extra_work_time_minute = '';
        $att_minute           = $no_att_minute = $working_day = $attendance = $absent = $holiday = $note = $plain_note = $online = $real_minute          = '';
        $exception            = $lembur = '';
        $rest_time            = 60;
        if (valid_time($check_in) and valid_time($check_out)) {
            $working_day       = 1;
            $scan_in_candidate = $scan_out_candidate = array();
            if (count($scan_array) > 0) {
                foreach ($scan_array as $sa) {
                    if ($sa['scan_time'] >= $scan_in_start_date and $sa['scan_time'] <= $scan_in_finish_date) {
                        $scan_in_candidate[] = $sa['scan_time'];
                        $log_id_in_candidate[] = $sa['log_id'];
                    }
                    if ($sa['scan_time'] >= $scan_out_start_date and $sa['scan_time'] <= $scan_out_finish_date) {
                        $scan_out_candidate[] = $sa['scan_time'];
                        $log_id_out_candidate[] = $sa['log_id'];
                    }
                }

                if (count($scan_in_candidate) > 0) {
                    $scan_in = min($scan_in_candidate);
                    $log_id_in = min($log_id_in_candidate);
                }

                if (count($scan_out_candidate) > 0) {
                    $scan_out = max($scan_out_candidate);
                    $log_id_out = max($log_id_out_candidate);
                }

                if (!empty($scan_in) and !empty($scan_out)) {

                    if ($scan_in > $check_in_date) {
                        if ($NINE_OUR != 1) {
                            $late = $scan_in - $check_in_date;
                        } else {
                            $real_minutes  = floor(($scan_out - $scan_in) / 60);
                            if ($real_minutes < 540) {
                                $late = $scan_in - $check_in_date;
                            } else {
                                $late = 0;
                            }
                        }
                    }
                    $late_minute = empty($late) ? '' : floor($late / 60);


                    if ($scan_out < $check_out_date) {
                        if ($NINE_OUR != 1) {
                            $early = $check_out_date - $scan_out;
                        } else {
                            $real_minutes  = floor(($scan_out - $scan_in) / 60);
                            if ($real_minutes < 540) {
                                $early = $check_out_date - $scan_out;
                            } else {
                                $early = 0;
                            }
                        }
                    }
                    $early_minute = empty($early) ? '' : round($early / 60, 0);

                    $extra_work_time = 0;
                    if ($scan_out > $check_out_date) {
                        $extra_work_time = $scan_out - $check_out_date;
                    }
                    $extra_work_time_minute = empty($extra_work_time) ? '' : floor($extra_work_time / 60);
                    if ($extra_work_time_minute < 40)
                        $extra_work_time_minute = 0;
                    if ($late_minute <= 5) {
                    }
                    $minute       = floor(($check_out_date - $check_in_date) / 60);
                    $real_minute  = floor(($scan_out - $scan_in) / 60);
                    $att_minute   = $minute - $late_minute - $early_minute - $rest_time;
                    $attendance   = 1;
                } else {
                    $minute         = floor(($check_out_date - $check_in_date) / 60);
                    $real_minute    = 0;
                    $no_att_minute  = $minute - $rest_time;
                    $absent         = 1;
                }
            } else {
                $absent = 1;
            }
        } else if (in_array($SHIFT_CODE, array(
            'X',
            'OFF'
        ))) {
            $holiday = 1;
            $absent  = '';
        }
        $tmp_absent = $absent;
        foreach (array(
            'SAKIT',
            'IJIN',
            'IJIN_LE',
            'SKD',
            'BACKUP',
            'CI',
            'CT',
            'CM',
            'DINAS',
            'UL',
            'TO_IN',
            'TO_OUT',
            'TS',
            'TS_IN',
            'TS_OUT',
            'BM',
            'R',
            'SM'
        ) as $EX) {
            if (!isset(${'total_' . strtolower($EX)}))
                ${'total_' . strtolower($EX)} = 0;
            if (isset($EXCEPTION[$loop_date][$EX])) {
                $attendance              = 0;
                $exception               = 1;
                $absent                  = '';
                $no_att_minute           = 0;
                $result[$loop_date][$EX] = 1;
                $note                    = $EXCEPTION[$loop_date][$EX]->KETERANGAN;
                if (isset($SHIFT_CODE_OVERRIDE)) {
                    $note .= " ## Jadwal : " . $SHIFT_CODE_OVERRIDE . ' (' . $check_in . '-' . $check_out . ')';
                }
                ${'total_' . strtolower($EX)} = ${'total_' . strtolower($EX)} + 1;
                if ($EX == 'TO_OUT') {
                    $total_to = $total_to + 1;
                }
                if ($EX == 'TS_OUT') {
                    $total_ts = $total_ts + 1;
                }
                if ($EX == 'IJIN_LE' and $tmp_absent == '1') {
                    $absent                  = 1;
                    $exception               = 0;
                    $result[$loop_date][$EX] = 0;
                }
            }
        }

        if (isset($LEMBUR[$loop_date])) {
            $lembur                       = 1;
            $absent                       = '';
            $no_att_minute                = 0;
            $result[$loop_date]['lembur'] = 1;
            $note                         = $LEMBUR[$loop_date]->JENIS . ' : ' . $LEMBUR[$loop_date]->KETERANGAN;
        }

        if (isset($ONLINE[$loop_date]['IN'])) {
            $absent                       = '';
            $no_att_minute                = 0;
            $result[$loop_date]['online'] = 1;
            $note                         = 'Online Absent Via Hadir';

            $scan_in = strtotime($ONLINE[$loop_date]['IN']->TANGGAL_ABSEN);
            $LATITUDE_IN = $ONLINE[$loop_date]['IN']->LATITUDE;
            $LONGITUDE_IN = $ONLINE[$loop_date]['IN']->LONGITUDE;
            $FOTO_IN = $ONLINE[$loop_date]['IN']->FOTO;
        }

        if (isset($ONLINE[$loop_date]['OUT'])) {
            $absent                       = '';
            $no_att_minute                = 0;
            $result[$loop_date]['online'] = 1;
            $note                         = 'Online Absent Via Hadir';

            $scan_out = strtotime($ONLINE[$loop_date]['OUT']->TANGGAL_ABSEN);
            $LATITUDE_OUT = $ONLINE[$loop_date]['OUT']->LATITUDE;
            $LONGITUDE_OUT = $ONLINE[$loop_date]['OUT']->LONGITUDE;
            $FOTO_OUT = $ONLINE[$loop_date]['OUT']->FOTO;
        }

        if (isset($ONLINE[$loop_date]['IN']) && isset($ONLINE[$loop_date]['OUT'])) {
            $online     = 1;
            $attendance = 1;
        }


        $result[$loop_date]['day']              = $loop_day;
        $result[$loop_date]['check_in']         = $check_in;
        $result[$loop_date]['check_out']        = $check_out;
        $result[$loop_date]['scan_in']          = empty($scan_in) ? '' : date('H:i', $scan_in);
        $result[$loop_date]['scan_out']         = empty($scan_out) ? '' : date('H:i', $scan_out);
        $result[$loop_date]['scan_in_date']     = empty($scan_in) ? '' : date('Y-m-d H:i:s', $scan_in);
        $result[$loop_date]['scan_out_date']    = empty($scan_out) ? '' : date('Y-m-d H:i:s', $scan_out);
        $result[$loop_date]['late']             = $late_minute;
        $result[$loop_date]['early']            = $early_minute;
        $result[$loop_date]['extra_work']       = $extra_work_time_minute;
        $result[$loop_date]['att_minute']       = $att_minute;
        $result[$loop_date]['no_att_minute']    = $no_att_minute;
        $result[$loop_date]['real_minute']      = $real_minute;
        $result[$loop_date]['working_day']      = $working_day;
        $result[$loop_date]['attendance']       = $attendance;
        $result[$loop_date]['absent']           = $absent;
        $result[$loop_date]['type']             = $type;
        $result[$loop_date]['holiday']          = $holiday;
        $result[$loop_date]['exception']        = $exception;
        $result[$loop_date]['plain_note']       = $plain_note;
        $result[$loop_date]['note']             = $note;
        $result[$loop_date]['id_log_mesin_in']  = $log_id_in;
        $result[$loop_date]['id_log_mesin_out'] = $log_id_out;
        $result[$loop_date]['latitude']         = $LATITUDE;
        $result[$loop_date]['longitude']        = $LONGITUDE;
        $result[$loop_date]['foto']             = $FOTO;
        $result[$loop_date]['latitude_ol_in']   = $LATITUDE_IN;
        $result[$loop_date]['longitude_ol_in']  = $LONGITUDE_IN;
        $result[$loop_date]['foto_ol_in']       = $FOTO_IN;
        $result[$loop_date]['latitude_ol_out']  = $LATITUDE_OUT;
        $result[$loop_date]['longitude_ol_out'] = $LONGITUDE_OUT;
        $result[$loop_date]['foto_ol_out']      = $FOTO_OUT;
        $result[$loop_date]['karyawan_id']      = $PIN;

        $total_working_day                   = $total_working_day + $working_day;
        $total_attendance                    = $total_attendance + $attendance;

        if ($late_minute > 0) {
            $total_late = $total_late + 1;
            $date_late = substr($result[$loop_date]['scan_in_date'], 0, -9);
            $late_notes .= '[Terlambat] ' . $date_late . '\n';
        }

        if ($early_minute > 0) {
            $total_early = $total_early + 1;
            $date_early = substr($result[$loop_date]['scan_out_date'], 0, -9);
            $early_notes .= '[Pulang Cepat] ' . $date_early . '\n';
        }

        $total_absent  = $total_absent + intval($absent);
        $total_holiday = $total_holiday + intval($holiday);
        $total_lembur  = $total_lembur + intval($lembur);
        $total_online  = $total_online + intval($online);
        $time1         = strtotime("+1 day", $time1);
    }

    $result['total_day']            = $total_day;
    $result['total_working_day']    = $total_working_day;
    $result['total_attendance']     = $total_attendance;
    $result['total_late']           = $total_late;
    $result['total_early']          = $total_early;
    $result['total_late_early']     = $result['total_late'] + $result['total_early'];
    $result['total_absent']         = $total_absent;
    $result['total_holiday']        = $total_holiday;
    $result['total_lembur']         = $total_lembur;
    $result['total_online']         = $total_online;
    $result['total_sakit']          = isset($total_sakit) ? $total_sakit : 0;
    $result['total_ijin']           = isset($total_ijin) ? $total_ijin : 0;
    $result['total_ijin_le']        = isset($total_ijin_le) ? $total_ijin_le : 0;
    $result['total_skd']            = isset($total_skd) ? $total_skd : 0;
    $result['total_all_sakit']      = $result['total_sakit'] + $result['total_skd'];
    $result['total_ci']             = isset($total_ci) ? $total_ci : 0;
    $result['total_ct']             = isset($total_ct) ? $total_ct : 0;
    $result['total_to']             = isset($total_to) ? $total_to : 0;
    $result['total_ts']             = isset($total_ts) ? $total_ts : 0;
    $result['total_bm']             = isset($total_bm) ? $total_bm : 0;
    $result['total_r']              = isset($total_r) ? $total_r : 0;
    $result['total_backup']         = isset($total_backup) ? $total_backup : 0;
    $result['total_cm']             = isset($total_cm) ? $total_cm : 0;
    $result['total_all_ijin_cuti']  = $result['total_ijin'] + $result['total_ci'] + $result['total_ct'] + $result['total_cm'];
    $result['total_dinas']          = isset($total_dinas) ? $total_dinas : 0;
    $result['total_ul']             = isset($total_ul) ? $total_ul : 0;
    $result['total_sm']             = isset($total_sm) ? $total_sm : 0;
    $result['total_fp1']            = '0';
    $result['late_notes']           = $late_notes;
    $result['early_notes']          = $early_notes;
    return $result;
}
