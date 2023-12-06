<?php

include_once 'app-load.php';

$PERIODE_ID = get_input('PERIODE_ID');
$PROJECT_ID = get_input('PROJECT_ID');
$VIEW_MODE = get_input('VIEW_MODE');
$NAMA = get_input('NAMA');

$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
$TANGGAL = array();
$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;

if ($VIEW_MODE == 'PAYROLL') {
	$PROJECT = db_first(" SELECT CUTOFF FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
	$CUTOFF = isset($PROJECT->CUTOFF) ? $PROJECT->CUTOFF : 0;
	if ($CUTOFF == '1') {
		$TGL_MULAI = $PERIODE->TANGGAL_MULAI2;
		$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI2;
	}
}

$RANGE = date_range($TGL_MULAI, $TGL_SELESAI);
$DAY = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');

$HD = db_fetch(" SELECT * FROM holiday WHERE (DATE >= '$TGL_MULAI') AND (DATE <= '$TGL_SELESAI') ");
$HOLIDAY = array();
if (count($HD) > 0) {
	foreach ($HD as $row) {
		$HOLIDAY[$row->DATE] = $row->HOLIDAY;
	}
}

?>

$('#t').datagrid({
queryParams: { 'PROJECT_ID': '<?php echo $PROJECT_ID ?>',
'PERIODE_ID': '<?php echo $PERIODE_ID ?>',
'VIEW_MODE': '<?php echo $VIEW_MODE ?>',
'TGL_MULAI': '<?php echo $TGL_MULAI ?>',
'TGL_SELESAI': '<?php echo $TGL_SELESAI ?>',
'NAMA': '<?php echo $NAMA ?>',
},
url:'absensi-json.php',
fit: true,
border: true,
nowrap: false,
striped: true,
collapsible:true,
remoteSort: true,
sortName: 'NAMA',
sortOrder: 'asc',
singleSelect:true,
pagination: true,
pageSize:50,
pageList: [50,100],
rownumbers:true,
frozenColumns:[[
{field:'KARYAWAN_ID',title:'ID',width:40,sortable:false,align:'center'},
{field:'NIK',title:'NIK',width:80,sortable:true,align:'center'},
{field:'NAMA',title:'Nama',width:200,sortable:true,align:'left'},
{field:'POSISI',title:'Jabatan',width:200,sortable:true,align:'left'},
]],
columns:[[
<?php if ($VIEW_MODE == 'PAYROLL') { ?>
	{field:'TGL_MASUK',title:'Join Date',width:110,sortable:true,align:'center'},
	{field:'VALID_UNTIL',title:'Tgl Valid Cuti',width:110,sortable:true,align:'center'},
	{field:'KUOTA_CUTI',title:'Kuota<br>Cuti',width:70,sortable:false,align:'center'},
	{field:'CUTI_PERIODE_SEBELUMNYA',title:'Pot. Cuti<br>Sblmnya',width:70,sortable:false,align:'center'},
	{field:'CUTI_PERIODE_INI',title:'Pot. Cuti<br>Sekarang',width:70,sortable:false,align:'center'},
	{field:'SISA_CUTI',title:'Sisa<br>Cuti',width:70,sortable:false,align:'center'},
<?php } ?>
<?php
$WEEKEND = array();
foreach ($RANGE as $tgl) {
	$day = date('w', strtotime($tgl));
	if (in_array($day, array('0', '6'))) $WEEKEND[] = 'TGL_' . date('Ymd', strtotime($tgl));
	$D = '<span class="tip" title="' . $DAY[$day] . ', ' . date('d F Y', strtotime($tgl)) . '">' . date('d/m', strtotime($tgl)) . '</span>';
	$D = isset($HOLIDAY[$tgl]) ? '<span class="tip" style="color:red;font-weight:bold;" title="' . $DAY[$day] . ', ' . date('d F Y', strtotime($tgl)) . ' : ' . $HOLIDAY[$tgl] . '">' . date('d/m', strtotime($tgl)) . '</span>' : $D;
?>
	{field:'TGL_<?php echo date('Ymd', strtotime($tgl)) ?>',title:'<?php echo $D ?>',width:80,sortable:false,align:'center'},
<?php } ?>
{field:'TOTAL_DAY',title:'HARI',width:70,sortable:false,align:'center'},
{field:'TOTAL_ATT',title:'HADIR',width:70,sortable:false,align:'center'},
{field:'TOTAL_LATE',title:'LATE',width:70,sortable:false,align:'center'},
{field:'TOTAL_EARLY',title:'EARLY',width:70,sortable:false,align:'center'},
{field:'TOTAL_SKD',title:'SKD',width:70,sortable:false,align:'center'},
{field:'TOTAL_SAKIT',title:'SAKIT',width:70,sortable:false,align:'center'},
{field:'TOTAL_IJIN',title:'IJIN',width:70,sortable:false,align:'center'},
{field:'TOTAL_ABS',title:'ABS',width:70,sortable:false,align:'center'},
{field:'TOTAL_CT',title:'CT',width:70,sortable:false,align:'center'},
{field:'TOTAL_CI',title:'CI',width:70,sortable:false,align:'center'},
{field:'TOTAL_CM',title:'CM',width:70,sortable:false,align:'center'},
{field:'TOTAL_LEMBUR',title:'LEMBUR',width:70,sortable:false,align:'center'},
<?php
/*
{field:'TOTAL_BACKUP',title:'BACKUP',width:70,sortable:false,align:'center'},
{field:'TOTAL_TS',title:'TS',width:70,sortable:false,align:'center'},
{field:'TOTAL_TO',title:'TO',width:70,sortable:false,align:'center'},
*/
?>
{field:'TOTAL_BM',title:'BM',width:70,sortable:false,align:'center'},
{field:'TOTAL_R',title:'R',width:70,sortable:false,align:'center'},
{field:'TOTAL_UL',title:'UL',width:70,sortable:false,align:'center'},
{field:'TOTAL_DINAS',title:'DINAS',width:70,sortable:false,align:'center'},
{field:'TOTAL_SM',title:'SM',width:70,sortable:false,align:'center'},
{field:'TOTAL_OFF',title:'OFF',width:70,sortable:false,align:'center'},
]],
onLoadSuccess: function(data){
$('.tip').tipsy({
opacity : 1,
});
}
});
$(window).resize(function(){ datagrid(); });

var dg = $('#t');
<?php
/*
	$AR = array(
		'DAY','ATT','ABS','LATE','EARLY','SAKIT','IJIN','SKD','BACKUP','CT','CI','LEMBUR','OFF','TO','TS','R','BM','CM','DINAS','UL'
	);
	*/

$AR = array(
	'DAY', 'ATT', 'ABS', 'LATE', 'EARLY', 'SAKIT', 'IJIN', 'SKD', 'LEMBUR', 'CT', 'CI', 'OFF', 'R', 'BM', 'CM', 'DINAS', 'SM', 'UL'
);
foreach ($AR as $val) {
?>
	var col = dg.datagrid('getColumnOption','<?php echo 'TOTAL_' . $val ?>');
	col.styler = function(){ return 'background-color:#ffffe7'; };
<?php } ?>

var dg = $('#t');
dg.datagrid();
<?php foreach ($AR as $val) { ?>
	td = dg.datagrid('getPanel').find('div.datagrid-header td[field="TOTAL_<?php echo $val ?>"]');
	td.css('background-color','<?php echo empty(get_option('C_' . $val)) ? '#ffffc7' : get_option('C_' . $val) ?>');
	td.css('color','<?php echo empty(get_option('F_' . $val)) ? '#000000' : get_option('F_' . $val) ?>');
<?php } ?>

<?php if (count($WEEKEND) > 0) {
	foreach ($WEEKEND as $val) { ?>
		<?php /*td = dg.datagrid('getPanel').find('div.datagrid-header td[field="<?php echo $val ?>"]');
	td.css('background-color','#ffdfdf');*/ ?>
		var col = dg.datagrid('getColumnOption','<?php echo $val ?>');
		col.styler = function(){ return 'background-color:#ffeeee'; };
<?php }
} ?>