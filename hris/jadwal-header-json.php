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

if( $VIEW_MODE == 'PAYROLL' )
{
	$PROJECT = db_first(" SELECT CUTOFF FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
	$CUTOFF = isset($PROJECT->CUTOFF) ? $PROJECT->CUTOFF : 0;
	if( $CUTOFF == '1' )
	{
		$TGL_MULAI = $PERIODE->TANGGAL_MULAI2;
		$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI2;
	}
}

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

?>
	$('#t').datagrid({
		queryParams: {
				'PERIODE_ID': '<?php echo $PERIODE_ID ?>',
				'PROJECT_ID': '<?php echo $PROJECT_ID ?>',
				'TGL_MULAI': '<?php echo $TGL_MULAI ?>',
				'TGL_SELESAI': '<?php echo $TGL_SELESAI ?>',
				'VIEW_MODE': '<?php echo $VIEW_MODE ?>',
				'NAMA': '<?php echo $NAMA ?>'
			},
		url:'jadwal-json.php',
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
			{field:'NIK',title:'NIK',width:100,sortable:true,align:'center'},
			{field:'NAMA',title:'Nama',width:200,sortable:true,align:'left'},
			{field:'POSISI',title:'Jabatan',width:200,sortable:true,align:'left'},
		]],
		columns:[[
			<?php
			$WEEKEND = array();
			foreach($RANGE as $tgl){ $day = date('w',strtotime($tgl));
			if(in_array($day,array('0','6'))) $WEEKEND[] = 'TGL_'.date('Ymd',strtotime($tgl));
			$D = '<span class="tip" title="'.$DAY[$day].', '.date('d F Y',strtotime($tgl)).'">'.date('d/m',strtotime($tgl)).'</span>';
			$D = isset($HOLIDAY[$tgl]) ? '<span class="tip" style="color:red;font-weight:bold;" title="'.$DAY[$day].', '.date('d F Y',strtotime($tgl)).' : '.$HOLIDAY[$tgl].'">'.date('d/m',strtotime($tgl)).'</span>' : $D;
			?>
			{field:'TGL_<?php echo date('Ymd',strtotime($tgl)) ?>',title:'<?php echo $D ?>',width:80,sortable:false,align:'center'},
			<?php } ?>
		]],
		onLoadSuccess: function(data){
			$('.tip').tipsy({
				opacity : 1,
			});
		}
	});
	$(window).resize(function(){ datagrid(); });
	
	var dg = $('#t');
	dg.datagrid();
	
	<?php if(count($WEEKEND)>0){ foreach($WEEKEND as $val){ ?>
	td = dg.datagrid('getPanel').find('div.datagrid-header td[field="<?php echo $val ?>"]');
	td.css('background-color','#ffdfdf');
	var col = dg.datagrid('getColumnOption','<?php echo $val ?>');
	col.styler = function(){ return 'background-color:#ffeeee'; };
	<?php }} ?>