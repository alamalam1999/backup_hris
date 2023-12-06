<?php

include 'app-load.php';

is_login('pph.view');

$MODULE = 'PPH';
set_search($MODULE, array('sort','order','PERIODE_ID','COMPANY_ID','NAMA'));
if( get_input('clear') ) clear_search($MODULE, array('PERIODE_ID','COMPANY_ID','NAMA'));

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
if($COMPANY_ID = get_search($MODULE,'COMPANY_ID') AND !empty($COMPANY_ID)) $wh[] = " P.COMPANY_ID = '$COMPANY_ID' ";
if($NAMA = get_search($MODULE,'NAMA') AND !empty($NAMA)) $wh[] = " UCASE(K.NAMA) LIKE UCASE('$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

$rs = db_first("
	SELECT COUNT(1) as cnt
	FROM pph P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) 
	LEFT JOIN ptkp PP ON (PP.NAMA=K.STATUS_PTKP)
	{$where}
");
$NUM_ROWS = $rs->cnt;
	
$rs = db_fetch_limit("
	SELECT P.*, PE.PERIODE, K.NAMA, K.NIK, K.TGL_MASUK, K.TIPE_GAJI, K.STATUS_PTKP, PP.NILAI
	FROM pph P 
	LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	LEFT JOIN ptkp PP ON (PP.NAMA=K.STATUS_PTKP)
	{$where}
	ORDER BY $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if(count($rs)>0){ foreach($rs as $row){
	
	$t['NAMA'] = '<span class="tip" title="NIK : '.$row->NIK.', Tipe Gaji : '.$row->TIPE_GAJI.'">'.$row->NAMA.'</span>';
	$t['TGL_MASUK'] = tgl($row->TGL_MASUK);
	/*$t['SISA_CUTI'] = ($row->SISA_CUTI <= 0) ? '<div style="color:#ff0000;background:#ffdfdf;">'.$row->SISA_CUTI.'</div>' : $row->SISA_CUTI;
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
	$t['TUNJ_PROYEK'] = currency($row->TUNJ_PROYEK);
	$t['TUNJ_SHIFT'] = currency($row->TUNJ_SHIFT);
	$t['LHK'] = currency($row->LHK);
	$t['LHL'] = currency($row->LHL);
	$t['IHB'] = currency($row->IHB);
	$t['MEDICAL'] = currency($row->MEDICAL);
	$t['ADJUSMENT'] = currency($row->ADJUSMENT);
	$t['THR'] = currency($row->THR);
	$t['TOTAL_TUNJANGAN'] = currency($row->TOTAL_TUNJANGAN);
	$t['TOTAL_GAJI_KOTOR'] = currency($row->TOTAL_GAJI_KOTOR);
	$t['BPJS_JHT'] = currency($row->BPJS_JHT);
	$t['BPJS_JP'] = currency($row->BPJS_JP);
	$t['BPJS_KES'] = currency($row->BPJS_KES);
	$t['ANGSURAN'] = currency($row->ANGSURAN);
	$t['PINJAMAN'] = currency($row->PINJAMAN);
	$t['TOTAL_POTONGAN'] = currency($row->TOTAL_POTONGAN);
	$t['TOTAL_GAJI_BERSIH'] = currency($row->TOTAL_GAJI_BERSIH);*/

	$t['GAJI_POKOK'] = currency($row->GAJI_POKOK);
	$t['TOTAL_TUNJAGAN'] = currency($row->TOTAL_TUNJAGAN);
	$t['BPJS_JKK'] = currency($row->BPJS_JKK);
	$t['BPJS_JKM'] = currency($row->BPJS_JKM);
	$t['BPJS_KES'] = currency($row->BPJS_KES);
	$t['PLUS_BRUTO'] = currency($row->PLUS_BRUTO);
	$t['BIAYA_JABATAN'] = currency($row->BIAYA_JABATAN);
	$t['BPJS_JHT'] = currency($row->BPJS_JHT);
	$t['BPJS_JP'] = currency($row->BPJS_JP);
	$t['MINUS_BRUTO'] = currency($row->MINUS_BRUTO);
	$t['NETTO_SEBULAN'] = currency($row->NETTO_SEBULAN);
	$t['NETTO_SETAHUN'] = currency($row->NETTO_SETAHUN);
	$row->NILAI = empty($row->NILAI) ? 54000000 : $row->NILAI;
	$t['NILAI_PTKP'] = currency($row->NILAI);
	$t['KENA_PAJAK_SETAHUN'] = currency($row->KENA_PAJAK_SETAHUN);
	$t['PPH_SETAHUN'] = currency($row->PPH_SETAHUN);
	$t['PPH'] = currency($row->PPH);
	
	$rows[$key] = (object) array_merge((array) $row, $t);
	$key++;
}}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);