<?php

include 'app-load.php';

is_login('laporan-pph-perusahaan-bulanan.view');

$JS[] = 'static/tipsy/jquery.tipsy.js';
$JS[] = 'static/js/datagrid-export.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<?php
if(get_input('m') == '1'){
	$SUCCESS = 'Penggajian berhasil dibuat';
}
if(get_input('m') == 'closed'){
	$ERROR[] = 'Tidak dapat membuat laporan gaji<br>Periode penggajian sudah di tutup';
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
				<li><a href="javascript:;" onclick="$('#t').datagrid('toExcel','LAPORAN PENGGAJIAN - '+$('#PERIODE_ID option:selected').html()+' - '+$('#PROJECT_ID option:selected').html() +'.xls')" style=""><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),get_search('LAPORAN-PENGGAJIAN','PERIODE_ID'),' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID',project_option_filter(1),get_search('LAPORAN-PENGGAJIAN','PROJECT_ID'),' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<?php /*
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('PENGGAJIAN','NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		*/ ?>
		<div class="col-sm-6">
			<h1 style="margin:0;text-align:right;">Summary PPH Masa Perusahaan</h1>
		</div>
	</div>
	
	<section class="content">
		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>
	</section>
</section>

<script>
$(document).ready(function(){
	$('#t').datagrid({
		queryParams: { 'PERIODE_ID': $('#PERIODE_ID').val(), 'PROJECT_ID': $('#PROJECT_ID').val() },
		url:'laporan-pph-perusahaan-bulanan-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'NAMA',
		sortOrder: 'asc',
		singleSelect:false,
		pagination: false,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		frozenColumns:[[
			//{field:'ck',checkbox:true},
			//{field:'PERIODE',title:'Periode',width:100,sortable:true,align:'center'},
			{field:'UNIT',title:'Unit',width:200,sortable:false,align:'left'},
			{field:'PERIODE',title:'Periode',width:100,sortable:false,align:'center'},
			{field:'TOTAL_PENGHASILAN_',title:'Total Penghasilan',width:150,sortable:false,align:'center'},
			{field:'TOTAL_PPH',title:'Total PPH21',width:140,sortable:false,align:'center'},
		]],
		columns:[[
			
			{title:'TOTAL UPAH TETAP',align:'center',colspan:4},
			{title:'TOTAL BPJS',align:'center',colspan:5},
			
			
		],[
			{field:'GAJI_POKOK_',title:'GAPOK',width:80,sortable:false,align:'right'},
			{field:'GAJI_PRORATA_',title:'GP Prorata',width:80,sortable:false,align:'right'},
			{field:'TUNJANGAN_KELUARGA_',title:'Tunj Keluarga',width:80,sortable:false,align:'right'},
			{field:'THR_',title:'THR',width:80,sortable:false,align:'right'},
			{field:'TOTAL_POTONGAN_',title:'TOTAL',width:80,sortable:false,align:'right'},
			
			{field:'JKK_KARYAWAN_',title:'JKK',width:80,sortable:false,align:'right'},
			{field:'JHT_KARYAWAN_',title:'JHT',width:80,sortable:false,align:'right'},
			{field:'JKM_KARYAWAN_',title:'JKM',width:80,sortable:false,align:'right'},
			{field:'BPJS_KESEHATAN_KARYAWAN_',title:'KESEHATAN',width:80,sortable:false,align:'right'},
			
			
			
		]],
		onLoadSuccess: function(data){
			$('.tip').tipsy({
				opacity : 1,
			});
		}
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-print').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'lembur-action.php?op=edit&id='+sel.LEMBUR_ID;
		return false;
	});
	
	$('#btn-generate').click(function(){
		window.location = 'penggajian.php?generate=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-export').click(function(){
		window.location = 'penggajian.php?export=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-dbank').click(function(){
		window.location = 'penggajian-dbank.php?PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-slip').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else {
			var rows = $('#t').datagrid('getSelections');
			var QS = '';
			$.each(rows, function(index, value){
				QS += '&ids[]=' + value.KARYAWAN_ID;
			});
			window.open('penggajian-slip.php?PERIODE_ID='+ $('#PERIODE_ID').val() + '&PROJECT_ID='+$('#PROJECT_ID').val() + QS,'_blank');
		}
		return false;
	});

	$('#btn-search').click(function(){
		doSearch();
		return false;
	});

	$('#PERIODE_ID, #PROJECT_ID').change(function(){
		doSearch();
		return false;
	});

	$('.input-search, #NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	
	$('#btn-reset').click(function(){
		$('#PERIODE_ID').val("");
		$('#PROJECT_ID').val("");
		$('#NAMA').val("");
		doSearch();
		return false;
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