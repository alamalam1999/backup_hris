<?php

include 'app-load.php';

is_login('approval-absensi.view');

$ST = array(
  'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
  'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
  'VOID' => '<span style="color:#ff0000;">VOID</span>',
);

set_search('APPROVAL_ABSENSI', array('sort', 'order', 'PERIODE_ID', 'PROJECT_ID', 'NAMA'));
if (get_input('clear')) clear_search('APPROVAL_ABSENSI', array('PERIODE_ID', 'PROJECT_ID', 'NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'TANGGAL_ABSEN';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';

if ($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE - 1) * $PER_PAGE;

$where = '';
$wh = array();
if ($PERIODE_ID = get_input('PERIODE_ID') and !empty($PERIODE_ID)) $wh[] = " E.PERIODE_ID = '$PERIODE_ID' ";
if ($PROJECT_ID = get_input('PROJECT_ID') and !empty($PROJECT_ID)) $wh[] = " J.PROJECT_ID = '$PROJECT_ID' ";
if ($STATUS = get_input('STATUS') and !empty($STATUS)) $wh[] = " E.STATUS = '$STATUS' ";
if ($NAMA = get_input('NAMA') and !empty($NAMA)) $wh[] = " UPPER(NAMA) LIKE UPPER('%$NAMA%') ";
if (count($wh) > 0) $where = " WHERE " . implode(' AND ', $wh);

$rs = db_first("
	SELECT COUNT(1) as cnt 
	FROM log_online E 
	LEFT JOIN periode R ON (R.PERIODE_ID=E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.PIN) 
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	{$where}
");

$NUM_ROWS = $rs->cnt;

$rs = db_fetch_limit("
	SELECT E.*, R.PERIODE, K.NAMA 
	FROM log_online E 
	LEFT JOIN periode R ON (R.PERIODE_ID = E.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID = E.PIN) 
	LEFT JOIN jabatan J ON (J.JABATAN_ID = K.JABATAN_ID)
	LEFT JOIN posisi P ON (P.POSISI_ID = K.POSISI_ID)
	{$where}
	ORDER BY STATUS DESC, $SORT $ORDER
	", $PER_PAGE, $OFFSET);

$rows = $t = array();
$key = 0;
if (count($rs) > 0) {
  foreach ($rs as $row) {
    $t['TANGGAL_ABSEN'] = tgl($row->TANGGAL_ABSEN);
    $t['STATUS'] = isset($ST[$row->STATUS]) ? '<div style="font-weight:bold;">' . $ST[$row->STATUS] . '</div>' : '';
    $t['FOTO'] = empty($row->FOTO) ? '' : '<a href="' . base_url() . 'uploads/absen/' . $row->FOTO . '" target="_blank">view</a>';
    $t['APPROVED_ON'] = tgl($row->APPROVED_ON, 1);
    $t['CREATED_ON'] = tgl($row->CREATED_ON, 1);
    $t['UPDATED_ON'] = tgl($row->UPDATED_ON, 1);
    $rows[$key] = (object) array_merge((array) $row, $t);
    $key++;
  }
}

$tmp = array();
$tmp['total'] = $NUM_ROWS;
$tmp['rows'] = $rows;
echo json_encode($tmp);
