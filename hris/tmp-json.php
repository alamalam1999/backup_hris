<?php

include 'app-load.php';

is_login('tmp.view');

$MODULE = 'TMP';
set_search($MODULE, array('sort', 'order', 'PERIODE_ID', 'PROJECT_ID', 'NAMA'));
if (get_input('clear')) clear_search($MODULE, array('PERIODE_ID', 'PROJECT_ID', 'NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if ($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE - 1) * $PER_PAGE;

$where = '';
$wh = array();
if ($PERIODE_ID = get_search($MODULE, 'PERIODE_ID') and !empty($PERIODE_ID)) $wh[] = " P.PERIODE_ID = '$PERIODE_ID' ";
if ($PROJECT_ID = get_search($MODULE, 'PROJECT_ID') and !empty($PROJECT_ID)) $wh[] = " P.PROJECT_ID = '$PROJECT_ID' ";
if ($NAMA = get_search($MODULE, 'NAMA') and !empty($NAMA)) $wh[] = " UCASE(K.NAMA) LIKE UCASE('$NAMA%') ";
if (count($wh) > 0) $where = " WHERE " . implode(' AND ', $wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM tmp P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) 
	{$where}
");
$NUM_ROWS = $rs->cnt;

$rs = db_fetch_limit("
	SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK, K.TIPE_GAJI, K.STATUS_PTKP,PO.POSISI,J.JABATAN
	FROM tmp P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	LEFT JOIN posisi PO ON (PO.POSISI_ID=K.POSISI_ID)
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	{$where}
	ORDER BY $SORT $ORDER", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if (count($rs) > 0) {
    foreach ($rs as $row) {

        $t['NIK'] = $row->NIK;
        $t['PERIODE_ID'] = $row->PERIODE;
        $t['PROJECT_ID'] = $row->PROJECT_ID;
        $t['KARYAWAN_ID'] = $row->KARYAWAN_ID;
        $t['TOTAL_HK'] = $row->TOTAL_HK;
        $t['TOTAL_ATT'] = $row->TOTAL_ATT;
        $t['TOTAL_ALL_SAKIT'] = $row->TOTAL_ALL_SAKIT;
        $t['TOTAL_ALL_IJIN_CUTI'] = $row->TOTAL_ALL_IJIN_CUTI;
        $t['TOTAL_ABS'] = $row->TOTAL_ABS;
        $t['TOTAL_FP1'] = $row->TOTAL_FP1;
        $t['TOTAL_LATE_EARLY'] = $row->TOTAL_LATE_EARLY;
        $t['TOTAL_REAL_ATT'] = $row->TOTAL_REAL_ATT;
        $t['TOTAL_DINAS'] = $row->TOTAL_DINAS;
        $t['TUNJ_KOMUNIKASI'] = currency($row->TUNJ_KOMUNIKASI);
        $t['TUNJ_MAKAN'] = currency($row->TUNJ_MAKAN);
        $t['TUNJ_TRANSPORT'] = currency($row->TUNJ_TRANSPORT);
        $t['TOTAL_TUNJ_MAKAN'] = currency($row->TOTAL_TUNJ_MAKAN);
        $t['TOTAL_TUNJ_TRANSPORT'] = currency($row->TOTAL_TUNJ_TRANSPORT);
        $t['TOTAL_TUNJ_KOMUNIKASI'] = currency($row->TOTAL_TUNJ_KOMUNIKASI);
        $t['TOTAL_TUNJANGAN_AWAL'] = currency($row->TOTAL_TUNJANGAN_AWAL);
        $t['TOTAL_PELANGGARAN'] = $row->TOTAL_PELANGGARAN;
        $t['TRANSPORT_DINAS'] = currency($row->TRANSPORT_DINAS);
        $t['LEMBUR'] = currency($row->LEMBUR);
        $t['DITRANSFER'] = currency($row->DITRANSFER);
        $t['KETERANGAN'] = $row->KETERANGAN;

        $rows[$key] = (object) array_merge((array) $row, $t);
        $key++;
    }
}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);
