<?php

include 'app-load.php';

is_login('absensi.view');

if (isset($_GET['generate'])) {
	is_login('absensi.generate');

	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');

	$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");

	if (!isset($PERIODE->PERIODE_ID)) {
		header('location: absensi.php');
		exit;
	}

	if ($PERIODE->STATUS_PERIODE == 'CLOSED') {
		header('location: absensi.php?m=closed');
		exit;
	}

	$TAHUN = $PERIODE->TAHUN;
	$BULAN = $PERIODE->BULAN;
	$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;

	$rs = db_fetch(" SELECT KARYAWAN_ID 
		FROM karyawan 
		WHERE PROJECT_ID = '$PROJECT_ID' AND ST_KERJA = 'AKTIF' 
	");

	$KARYAWAN_ID = array();
	if (count($rs) > 0) {
		foreach ($rs as $row) {
			$KARYAWAN_ID[] = $row->KARYAWAN_ID;
		}
	}

	if (count($KARYAWAN_ID) > 0) {
		$rs = db_fetch(" SELECT *, K.SHIFT_CODE
			FROM shift_karyawan K
			LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
			WHERE K.PROJECT_ID='$PROJECT_ID' AND KARYAWAN_ID IN (" . implode(',', $KARYAWAN_ID) . ") AND (DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
		");

		$SHIFT = array();
		if (count($rs) > 0) {
			foreach ($rs as $row) {
				$SHIFT[$row->KARYAWAN_ID][$row->DATE] = $row;
			}
		}

		$SCAN = parse_scan($row->KARYAWAN_ID, $TGL_MULAI, $TGL_SELESAI, $SHIFT);

		print_r($SCAN);

		/* 
		if (count($SCAN) > 0) {
			foreach ($SCAN as $key => $row) {
				if ($row['scan_in'] != '') {
					echo $row['scan_in'];
				}
			}
		} 
		*/
		
		die();
	}
}

/*
$PERIODE_ID = (int) get_input('PERIODE_ID');
$VIEW_MODE = get_input('VIEW_MODE') ? get_input('VIEW_MODE') : 'ALL';
if(empty($PERIODE_ID))
{
	$PERIODE_ID = get_search('ABSENSI','PERIODE_ID');
	$VIEW_MODE = get_search('ABSENSI','VIEW_MODE');
	if(empty($PERIODE_ID))
	{
		$periode = db_first(" SELECT * FROM periode ORDER BY PERIODE_ID DESC ");
		set_search('ABSENSI', array('PERIODE_ID','VIEW_MODE'));
		header('location: '.$_SERVER['PHP_SELF'].'?PERIODE_ID='.$periode->PERIODE_ID.'&VIEW_MODE=ALL');
		exit;
	}
	else
	{
		set_search('ABSENSI', array('PERIODE_ID','VIEW_MODE'));
		header('location: '.$_SERVER['PHP_SELF'].'?PERIODE_ID='.$PERIODE_ID.'&VIEW_MODE='.$VIEW_MODE);
		exit;
	}
}

$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
$TANGGAL = array();
$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;

$RANGE = date_range($TGL_MULAI,$TGL_SELESAI);
$DAY = array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');

$HD = db_fetch(" SELECT * FROM holiday WHERE (DATE >= '$TGL_MULAI') AND (DATE <= '$TGL_SELESAI') ");
$HOLIDAY = array();
if(count($HD)>0)
{
	foreach($HD as $row)
	{
		$HOLIDAY[$row->DATE] = $row->HOLIDAY;
	}
}
*/

$JS[] = 'static/tipsy/jquery.tipsy.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<section class="container-fluid">
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET">
		<div class="row" style="margin:10px 0;">
			<div class="col-sm-2">
				<div class="dropdown">
					<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
						<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dd1">
						<li><a href="javascript:void(0)" id="btn-generate"><i class="fa fa-cog"></i>&nbsp;&nbsp;Generate</a></li>
						<li><a href="absensi-import.php"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Import</a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-2">
				<?php echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), get_search('ABSENSI', 'PERIODE_ID'), ' id="PERIODE_ID" class="form-control input-sm" ') ?>
			</div>
			<div class="col-sm-2">
				<?php echo dropdown('PROJECT_ID', project_option_filter(0), get_search('ABSENSI', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
			</div>
			<div class="col-sm-2">
				<?php echo dropdown('VIEW', array('ABSENSI' => 'Cutoff Absensi', 'PAYROLL' => 'Cutoff Payroll'), get_search('ABSENSI', 'VIEW_MODE'), ' id="VIEW_MODE" class="form-control input-sm" ') ?>
			</div>
			<div class="col-sm-2">
				<input type="text" id="NAMA" value="<?php echo get_search('ABSENSI', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
			</div>
			<div class="col-sm-2">
				<h1 style="margin:0;text-align:right;">Absensi</h1>
			</div>
		</div>
	</form>

	<section class="content">

		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>

	</section>
</section>

<script>
	$(document).ready(function() {

		$('#btn-generate').click(function() {
			window.location = 'absensi.php?generate=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
			//console.log('test');
			return false;
		});

		<?php /*$('#t').datagrid({
		queryParams: {  'PROJECT_ID': $('#PROJECT_ID').val(),
						'PERIODE_ID': '<?php echo $PERIODE_ID ?>',
						'VIEW_MODE': '<?php echo $VIEW_MODE ?>',
						'TGL_MULAI': '<?php echo $TGL_MULAI ?>',
						'TGL_SELESAI': '<?php echo $TGL_SELESAI ?>',
						'NAMA': $('#NAMA').val(),
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
		]],
		columns:[[
			<?php
			$WEEKEND = array();
			if($VIEW_MODE=='ALL'){ foreach($RANGE as $tgl){
			$day = date('w',strtotime($tgl));
			if(in_array($day,array('0','6'))) $WEEKEND[] = 'TGL_'.date('Ymd',strtotime($tgl));
			$D = '<span class="tip" title="'.$DAY[$day].', '.date('d F Y',strtotime($tgl)).'">'.date('d/m',strtotime($tgl)).'</span>';
			$D = isset($HOLIDAY[$tgl]) ? '<span class="tip" style="color:red;font-weight:bold;" title="'.$DAY[$day].', '.date('d F Y',strtotime($tgl)).' : '.$HOLIDAY[$tgl].'">'.date('d/m',strtotime($tgl)).'</span>' : $D;
			?>
			{field:'TGL_<?php echo date('Ymd',strtotime($tgl)) ?>',title:'<?php echo $D ?>',width:80,sortable:false,align:'center'},
			<?php }} ?>
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
			{field:'TOTAL_BACKUP',title:'BACKUP',width:70,sortable:false,align:'center'},
			{field:'TOTAL_TS',title:'TS',width:70,sortable:false,align:'center'},
			{field:'TOTAL_TO',title:'TO',width:70,sortable:false,align:'center'},
			{field:'TOTAL_BM',title:'BM',width:70,sortable:false,align:'center'},
			{field:'TOTAL_R',title:'R',width:70,sortable:false,align:'center'},
			{field:'TOTAL_UL',title:'UL',width:70,sortable:false,align:'center'},
			{field:'TOTAL_DINAS',title:'DINAS',width:70,sortable:false,align:'center'},
			{field:'TOTAL_OFF',title:'OFF',width:70,sortable:false,align:'center'},
		]],
		onLoadSuccess: function(data){
			$('.tip').tipsy({
				opacity : 1,
				//delayOut : 200
			});
		}
	});
	$(window).resize(function(){ datagrid(); });

	var dg = $('#t');
	<?php
	$AR = array(
		'DAY','ATT','ABS','LATE','EARLY','SAKIT','IJIN','SKD','BACKUP','CT','CI','LEMBUR','OFF','TO','TS','R','BM','CM','DINAS','UL'
	);
	foreach($AR as $val){
	?>
	var col = dg.datagrid('getColumnOption','<?php echo 'TOTAL_'.$val ?>');
	col.styler = function(){ return 'background-color:#ffffe7'; };
	<?php } ?>

	var dg = $('#t');
	dg.datagrid();
	<?php foreach($AR as $val){ ?>
	td = dg.datagrid('getPanel').find('div.datagrid-header td[field="TOTAL_<?php echo $val ?>"]');
	td.css('background-color','<?php echo empty(get_option('C_'.$val)) ? '#ffffc7' : get_option('C_'.$val) ?>');
	td.css('color','<?php echo empty(get_option('F_'.$val)) ? '#000000' : get_option('F_'.$val) ?>');
	<?php } ?>

	<?php if(count($WEEKEND)>0){ foreach($WEEKEND as $val){ ?>
	<?php /*td = dg.datagrid('getPanel').find('div.datagrid-header td[field="<?php echo $val ?>"]');
	td.css('background-color','#ffdfdf');* / ?>
	var col = dg.datagrid('getColumnOption','<?php echo $val ?>');
	col.styler = function(){ return 'background-color:#ffeeee'; };
	<?php }}*/ ?>

		make_dg();
		$('#PERIODE_ID, #PROJECT_ID, #VIEW_MODE').change(function() {
			make_dg();
			return false;
		});
		$('#NAMA').keypress(function(e) {
			if (e.which == 13) {
				doSearch();
				e.preventDefault();
			}
		});
		datagrid();
	});

	function make_dg() {
		$.ajax({
			url: 'absensi-header-json.php',
			data: {
				'PERIODE_ID': $('#PERIODE_ID').val(),
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'VIEW_MODE': $('#VIEW_MODE').val(),
				'NAMA': $('#NAMA').val(),
			},
			dataType: 'script',
			method: 'GET',
			/*
			success : function(res){
				lib();
			}
			*/
		});
	}

	function datagrid() {
		var wind = parseInt($(window).height());
		var top = parseInt($('.navbar').outerHeight());
		$('#t-responsive').height(wind - top - 70);
		//$('#t').datagrid('resize');
	}

	function doSearch() {
		make_dg();
		$('#t').datagrid('load', {
			PERIODE_ID: $('#PERIODE_ID').val(),
			PROJECT_ID: $('#PROJECT_ID').val(),
			VIEW_MODE: $('#VIEW_MODE').val(),
			NAMA: $('#NAMA').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>