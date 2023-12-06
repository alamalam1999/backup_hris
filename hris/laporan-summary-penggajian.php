<?php
include 'app-load.php';
is_login('laporan-summary-penggajian.view');
$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';
include 'header.php';
$MODULE ='LAPORAN-PENGGAJIAN';


?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<input type="text" id="START_DATE" value="<?php echo get_search($MODULE,'START_DATE') ?>" class="form-control input-sm datepicker" placeholder="Start Date">
		</div>
		<div class="col-sm-2">
			<input type="text" id="FINISH_DATE" value="<?php echo get_search($MODULE,'FINISH_DATE') ?>" class="form-control input-sm datepicker" placeholder="Finish Date">
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('COMPANY_ID',dropdown_option_default('company','COMPANY_ID','COMPANY','ORDER BY COMPANY_ID DESC','-- Company --'),get_search($MODULE,'COMPANY_ID'),' id="COMPANY_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('COMPANY_ID',dropdown_option_default('project','PROJECT_ID','PROJECT','ORDER BY PROJECT_ID DESC','-- Unit --'),get_search($MODULE,'PROJECT_ID'),' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-1">
			<button class="btn btn-sm btn-primary" type="button" id="btn-search" style="width:100%;">
				<i class="fa fa-search"></i>&nbsp;&nbsp;Search
			</button>
		</div>
		<div class="col-sm-3">
			<h1 style="margin:0;text-align:right;">Summmary Penggajian</h1>
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
		queryParams: { 'START_DATE': $('#START_DATE').val(), 'FINISH_DATE': $('#FINISH_DATE').val(), 'COMPANY_ID': $('#COMPANY_ID').val(), 'PROJECT_ID': $('#PROJECT_ID').val() },
		url:'laporan-summary-penggajian-json.php',
		fit: true,
		border: true,
		nowrap: true,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'PROJECT',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: false,
		rownumbers:true,
		columns:[[
			{field:'PERIODE',title:'Periode',width:150,sortable:false,align:'left'},
			{field:'COMPANY',title:'Company',width:150,sortable:false,align:'left'},
			{field:'PROJECT',title:'Unit',width:150,sortable:false,align:'left'},
			{field:'GAJI_POKOK_NET',title:'Total Penghasilan',width:100,sortable:false,align:'right'},
			{field:'TUNJANGAN',title:'Total Tunjangan',width:100,sortable:false,align:'right'},
			{field:'TOTAL_BPJS_JHT',title:'Total BPJS JHT',width:100,sortable:false,align:'right'},
			{field:'TOTAL_BPJS_JP',title:'Total BPJS JP',width:100,sortable:false,align:'right'},
			{field:'TOTAL_BPJS_KES',title:'Total BPJS KES',width:100,sortable:false,align:'right'},
			{field:'GAJI_BERSIH',title:'Total Gaji Bersih',width:120,sortable:false,align:'right'},
		]],
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-print').click(function(){
		
	});

	$('#START_DATE').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			return false;
		}
	});

	$('#FINISH_DATE').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			return false;
		}
	});

	$('#COMPANY_ID').change(function(){
		$('#PROJECT_ID').val("");
		doSearch();
		return false;
	});

	$('#PROJECT_ID').change(function(){
		$('#COMPANY_ID').val("");
		doSearch();
		return false;
	});

	$('#btn-search').click(function(){
		doSearch();
		return false;
	});

	$('#btn-reset').click(function(){
		$('#START_DATE').val("");
		$('#FINISH_DATE').val("");
		$('#COMPANY_ID').val("");
		$('#PROJECT_ID').val("");
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
		START_DATE: $('#START_DATE').val(),
		FINISH_DATE: $('#FINISH_DATE').val(),
		COMPANY_ID: $('#COMPANY_ID').val(),
		PROJECT_ID: $('#PROJECT_ID').val(),
	});
}


</script>

<?php
include 'footer.php';
?>