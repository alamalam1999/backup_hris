<?php

include 'app-load.php';

is_login('penggajian.view');

$MODULE = 'PENGGAJIAN';
set_search($MODULE, array('sort','order','PERIODE_ID','PROJECT_ID','NAMA'));
if( get_input('clear') ) clear_search($MODULE, array('PERIODE_ID','PROJECT_ID','NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PERIODE_ID = get_search($MODULE,'PERIODE_ID') AND !empty($PERIODE_ID)) $wh[] = " P.PERIODE_ID = '$PERIODE_ID' ";
if($PROJECT_ID = get_search($MODULE,'PROJECT_ID') AND !empty($PROJECT_ID)) $wh[] = " P.PROJECT_ID = '$PROJECT_ID' ";
if($NAMA = get_search($MODULE,'NAMA') AND !empty($NAMA)) $wh[] = " UCASE(K.NAMA) LIKE UCASE('$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM penggajian P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) 
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK, K.TIPE_GAJI, K.STATUS_PTKP,PO.POSISI,J.JABATAN
	FROM penggajian P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	LEFT JOIN posisi PO ON (PO.POSISI_ID=K.POSISI_ID)
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	{$where}
	ORDER BY $SORT $ORDER", $PER_PAGE, $OFFSET);

/*echo "SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK, K.TIPE_GAJI, K.STATUS_PTKP,PO.POSISI FROM penggajian P LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) LEFT JOIN posisi PO ON (PO.POSISI_ID=K.POSISI_ID) {$where} ORDER BY $SORT $ORDER";*/

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['NIK'] = $row->NIK;
	$t['NAMA'] = '<span class="tip" title="NIK : '.$row->NIK.', Tipe Gaji : '.$row->TIPE_GAJI.'">'.$row->NAMA.'</span>';
	$t['TGL_MASUK'] = tgl($row->TGL_MASUK);
	$t['SISA_CUTI'] = ($row->SISA_CUTI <= 0) ? '<div style="color:#ff0000;background:#ffdfdf;">'.$row->SISA_CUTI.'</div>' : $row->SISA_CUTI;
	$t['SALDO_CUTI'] = $row->SISA_CUTI - $row->CUTI_TERPAKAI;
	$t['GAJI_POKOK'] = currency($row->GAJI_POKOK);
	$t['GAJI_POKOK_PRORATA'] = currency($row->GAJI_POKOK_PRORATA);
	$t['TIDAK_MASUK'] = currency($row->TIDAK_MASUK);
	$t['GAJI_POKOK_NET'] = currency($row->GAJI_POKOK_NET);
	$t['TUNJ_BACKUP'] = currency($row->TUNJ_BACKUP);
	$t['TUNJ_KEHADIRAN'] = currency($row->TUNJ_KEHADIRAN);
	$t['TUNJ_JABATAN'] = currency($row->TUNJ_JABATAN);
	$t['TUNJ_KEAHLIAN'] = currency($row->TUNJ_KEAHLIAN);
	$t['TUNJ_KOMUNIKASI'] = currency($row->TUNJ_KOMUNIKASI);
	$t['TUNJ_MAKAN'] = currency($row->TUNJ_MAKAN);
	$t['TUNJ_TRANSPORT'] = currency($row->TUNJ_TRANSPORT);
	$t['TUNJ_MAKAN_TRANSPORT'] = currency($row->TUNJ_TRANSPORT + $row->TUNJ_MAKAN);

	$t['TUNJ_PROYEK'] = currency($row->TUNJ_PROYEK);
	$t['TUNJ_SHIFT'] = currency($row->TUNJ_SHIFT);
	$t['LHK'] = currency($row->LHK);
	$t['LHL'] = currency($row->LHL);
	$t['IHB'] = currency($row->IHB);
	$t['TOTAL_TUNJANGAN_TDK_TETAP'] = currency($row->LHK + $row->LHL + $row->IHB + $row->TUNJ_MAKAN + $row->TUNJ_TRANSPORT + $row->TUNJ_KEHADIRAN);

	$t['MEDICAL'] = currency($row->MEDICAL);
	$t['ADJUSMENT_PLUS'] = currency($row->ADJUSMENT_PLUS);
	$t['ADJUSMENT_MINUS'] = currency($row->ADJUSMENT_MINUS);
	$t['THR'] = currency($row->THR);
	$t['TOTAL_TUNJANGAN'] = currency($row->TOTAL_TUNJANGAN);
	$t['KELEBIHAN_JAM_AJAR'] = $row->KELEBIHAN_JAM_AJAR;
	$t['NILAI_KELEBIHAN_JAM_AJAR'] = currency($row->NILAI_KELEBIHAN_JAM_AJAR);
	$t['TOTAL_GAJI_KOTOR'] = currency($row->TOTAL_GAJI_KOTOR);
	$t['BPJS_JHT'] = currency($row->BPJS_JHT);
	$t['BPJS_JKK'] = currency($row->BPJS_JKK);
	$t['BPJS_JKM'] = currency($row->BPJS_JKM);
	$t['BPJS_JP'] = currency($row->BPJS_JP);
	$t['BPJS_KES'] = currency($row->BPJS_KES);
	$t['TOTAL_BPJS'] = currency($row->BPJS_KES+$row->BPJS_JHT+$row->BPJS_JKK+$row->BPJS_JKM);
	$t['ANGSURAN'] = currency($row->ANGSURAN);
	$t['PINJAMAN'] = currency($row->PINJAMAN);
	$t['PINJAMAN_KOPERASI_DINATERA'] = currency($row->PINJAMAN_KOPERASI_DINATERA);
	$t['PINJAMAN_KOPERASI_AVICENNA'] = currency($row->PINJAMAN_KOPERASI_AVICENNA);
	$t['IURAN_KOPERASI_DINATERA'] = currency($row->IURAN_KOPERASI_DINATERA);
	$t['IURAN_KOPERASI_AVICENNA'] = currency($row->IURAN_KOPERASI_AVICENNA);
	$t['PINJAMAN_BANK_BWS'] = currency($row->PINJAMAN_BANK_BWS);
	$t['EKSES_KLAIM'] = currency($row->EKSES_KLAIM);
	$t['BIAYA_LAPTOP'] = currency($row->BIAYA_LAPTOP);
	$t['BIAYA_PEND_ANAK'] = currency($row->BIAYA_PEND_ANAK);
	$t['TOTAL_POTONGAN'] = currency($row->TOTAL_POTONGAN);
	$t['TOTAL_GAJI_BERSIH'] = currency($row->TOTAL_GAJI_BERSIH);
	$t['TUNJ_KELUARGA'] = currency($row->TUNJ_KELUARGA);
	$UPAH_TETAP = currency(($row->GAJI_POKOK_PRORATA+$row->GAJI_POKOK+$row->TUNJ_KELUARGA)-$row->TIDAK_MASUK);
	$t['UPAH_TETAP'] = $UPAH_TETAP;
	$t['TUNJ_OTTW'] = currency($row->TUNJ_OTTW);
	$t['TUNJ_LAINNYA_1'] = currency($row->TUNJ_LAINNYA_1);
	$t['TUNJ_LAINNYA_2'] = currency($row->TUNJ_LAINNYA_2);
	$t['TUNJ_LAINNYA_3'] = currency($row->TUNJ_LAINNYA_3);
	$t['TUNJ_LAINNYA_4'] = currency($row->TUNJ_LAINNYA_4);
	$t['TUNJ_LAINNYA_5'] = currency($row->TUNJ_LAINNYA_5);
	$t['TOTAL_TUNJANGAN_TETAP'] = currency($row->TUNJ_OTTW+$row->TUNJ_LAINNYA_1+$row->TUNJ_LAINNYA_2+$row->TUNJ_LAINNYA_3+$row->TUNJ_LAINNYA_4+$row->TUNJ_LAINNYA_5);
	
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);