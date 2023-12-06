<?php

include 'app-load.php';

is_login('thr.view');

$MODULE = 'THR';

if( isset($_GET['generate']) )
{
	is_login('thr.generate');
	
	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');
	
	$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
	
	if( ! isset($PERIODE->PERIODE_ID) )
	{
		header('location: thr.php');
		exit;
	}
	if( $PERIODE->STATUS_PERIODE == 'CLOSED' )
	{
		header('location: thr.php?m=closed');
		exit;
	}
	
	$TAHUN = $PERIODE->TAHUN;
	$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;
	$TGL_MULAI2 = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI2 = $PERIODE->TANGGAL_SELESAI;
	$THR_IDUL_FITRI = $PERIODE->THR_IDUL_FITRI;
	$TGL_IDUL_FITRI = $PERIODE->TGL_IDUL_FITRI;
	$THR_KUNINGAN = $PERIODE->THR_KUNINGAN;
	$TGL_KUNINGAN = $PERIODE->TGL_KUNINGAN;
	
	/*$PROJECT = db_first(" SELECT CUTOFF FROM project WHERE PROJECT_ID='$PROJECT_ID' ");
	$CUTOFF = isset($PROJECT->CUTOFF) ? $PROJECT->CUTOFF : 0;
	
	if( $CUTOFF == '1' )
	{
		$TGL_MULAI2 = $PERIODE->TANGGAL_MULAI2;
		$TGL_SELESAI2 = $PERIODE->TANGGAL_SELESAI2;
	}*/

	db_execute(" DELETE FROM thr WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	
	$karyawan = db_fetch("
		SELECT *
		FROM karyawan K
		LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
		WHERE J.PROJECT_ID='$PROJECT_ID' AND ST_KERJA='AKTIF'
	");

	if( count($karyawan) > 0 )
	{
		foreach($karyawan as $k)
		{
			$RANGE = 0;
			if($k->JENIS_THR=='IDUL FITRI'){
				$RANGE = strtotime($TGL_IDUL_FITRI) - strtotime($k->TGL_MASUK);
			}
			if($k->JENIS_THR=='KUNINGAN'){
				$RANGE = strtotime($TGL_KUNINGAN) - strtotime($k->TGL_MASUK);
			}
			$Y = round($RANGE/31536000,0);
			$sisa = $RANGE % 31536000;
			$M = round($sisa/2592000,0);
			$sisa = $sisa % 2592000;
			$D = round($sisa/86400,0);
			
			$MASA_KERJA = '';
			if($Y > 0) $MASA_KERJA .= $Y.' tahun ';
			if($M > 0) $MASA_KERJA .= $M.' bulan ';
			if($D > 0) $MASA_KERJA .= $D.' hari ';
			
			$TOTAL_HARI = round($RANGE/86400,0);
			
			$THR = 0;
			if($k->R_THR_PRORATA=='1')
			{
				$TGL_MASUK = strtotime($k->TGL_MASUK);
				if( $THR_IDUL_FITRI == '1' AND $k->JENIS_THR == 'IDUL FITRI' )
				{
					$TIME_IDUL_FITRI = strtotime($TGL_IDUL_FITRI);
					$RANGE = floor(($TIME_IDUL_FITRI - $TGL_MASUK)/(86400));
					if( $RANGE >= 30 AND $RANGE <= 365 ){
						$THR = ($RANGE/365) * $k->GAJI_POKOK;
					}
					if( $RANGE > 365 ){
						$THR = $k->GAJI_POKOK+$k->TUNJ_KELUARGA;
					}
				}
				if( $THR_KUNINGAN == '1' AND $k->JENIS_THR == 'KUNINGAN' )
				{
					$TIME_KUNINGAN = strtotime($TGL_KUNINGAN);
					$RANGE = floor(($TIME_KUNINGAN - $TGL_MASUK)/(86400));
					if( $RANGE >= 30 AND $RANGE <= 365 ){
						$THR = ($RANGE/365) * $k->GAJI_POKOK;
					}
					if( $RANGE > 365 ){
						$THR = $k->GAJI_POKOK+$k->TUNJ_KELUARGA;
					}
				}
			}
			
			if($k->R_THR == '1')
			{
				$THR = 0;
				$TGL_MASUK = strtotime($k->TGL_MASUK);
				if( $THR_IDUL_FITRI == '1' AND $k->JENIS_THR == 'IDUL FITRI' )
				{
					$TIME_IDUL_FITRI = strtotime($TGL_IDUL_FITRI);
					$RANGE = floor(($TIME_IDUL_FITRI - $TGL_MASUK)/(86400));
					if( $RANGE > 30 ){
						$THR = $k->GAJI_POKOK+$k->TUNJ_KELUARGA;
					}
				}
				if( $THR_KUNINGAN == '1' AND $k->JENIS_THR == 'KUNINGAN' )
				{
					$TIME_KUNINGAN = strtotime($TGL_KUNINGAN);
					$RANGE = floor(($TIME_KUNINGAN - $TGL_MASUK)/(86400));
					if( $RANGE > 30 ){
						$THR = $k->GAJI_POKOK+$k->TUNJ_KELUARGA;
					}
				}
			}

			if($k->R_THR == '0')
			{
				$THR_TAHUN = $k->GAJI_POKOK+$k->TUNJ_KELUARGA;
				$THR_BULAN = $THR_TAHUN/12;
				$BULAN_THR = ceil($TOTAL_HARI/30);
				if($BULAN_THR>12)$BULAN_THR=12;
				$THR = $THR_BULAN*$BULAN_THR;

			}
			$GAJI_POKOK = $k->GAJI_POKOK + $k->TUNJ_KELUARGA;
			db_execute("
				INSERT IGNORE thr (
					PERIODE_ID,PROJECT_ID,KARYAWAN_ID,MASA_KERJA,TOTAL_HARI,GAJI_POKOK,THR
				)
				VALUES (
					'$PERIODE_ID','$PROJECT_ID','$k->KARYAWAN_ID','$MASA_KERJA','$TOTAL_HARI','$GAJI_POKOK','$THR'
				)
			");
		}
	}
	
	header('location: thr.php?m=1');
	exit;
}

if( isset($_GET['export']) )
{
	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');
	header('location: export-gaji.php?PERIODE_ID='.$PERIODE_ID.'&PROJECT_ID='.$PROJECT_ID);
}

include 'header.php';
?>

<?php
if(get_input('m') == '1'){
	$SUCCESS = 'THR berhasil dibuat';
}
if(get_input('m') == 'closed'){
	$ERROR[] = 'Tidak dapat membuat laporan THR<br>Periode ini sudah ditutup';
}
include 'msg.php';
?>

<section class="container-fluid">

	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
				<li><a href="javascript:void(0)" id="btn-generate"><i class="fa fa-cog"></i>&nbsp;&nbsp;Generate</a></li>
				<?php /*<li><a href="javascript:void(0)" id="btn-export"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li>
				<li role="separator" class="divider"></li>*/ ?>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),get_search('THR','PERIODE_ID'),' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID',project_option_filter(0),get_search('THR','PROJECT_ID'),' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('THR','NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">THR</h1>
		</div>
	</div>
	
	<section class="content">
		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>
	</section>
</section>

<div id="ACTION_MENU" class="easyui-menu">
    <div>Edit</div>
    <div>Delete</div>
</div>

<script>
$(document).ready(function(){
	$('#t').datagrid({
		queryParams: { 'PERIODE_ID': $('#PERIODE_ID').val(), 'PROJECT_ID': $('#PROJECT_ID').val(), 'NAMA': $('#NAMA').val() },
		url:'thr-json.php',
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
			{field:'NAMA',title:'Nama',width:250,sortable:true,align:'left'},
		]],
		columns:[[
			{field:'TGL_MASUK',title:'Join Date',width:110,sortable:true,align:'center'},
			{field:'MASA_KERJA',title:'Masa Kerja',width:200,sortable:false,align:'left'},
			{field:'TOTAL_HARI',title:'Total Hari',width:100,sortable:false,align:'right'},
			{field:'GAJI_POKOK',title:'Gaji Pokok',width:100,sortable:false,align:'right'},
			{field:'THR',title:'THR',width:110,sortable:false,align:'right'},
		]],
		onRowContextMenu: function(e,index,row){
			$(this).datagrid('selectRow',index);
			e.preventDefault();
			$('#ACTION_MENU').menu('show', {
				left:e.pageX,
				top:e.pageY
			});
        }
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-generate').click(function(){
		window.location = 'thr.php?generate=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-export').click(function(){
		window.location = 'thr.php?export=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#PERIODE_ID, #PROJECT_ID').change(function(){
		doSearch();
		return false;
	});
	$('#NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	datagrid();
});
function datagrid()
{
	var wind = parseInt($(window).height());
	var top = parseInt($('.navbar').outerHeight());
	$('#t-responsive').height(wind - top - 70);
	$('#t').datagrid('resize');
}
function doSearch(){
	$('#t').datagrid('load',{
		PERIODE_ID: $('#PERIODE_ID').val(),
		PROJECT_ID: $('#PROJECT_ID').val(),
		NAMA: $('#NAMA').val(),
	});
}
</script>

<?php
include 'footer.php';
?>