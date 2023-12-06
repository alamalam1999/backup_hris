<?php
include 'app-load.php';

is_login('periode.view');

include 'header.php';
?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
				<li><a href="periode-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('PERIODE','NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-8">
			<h1 style="margin:0;text-align:right;">Periode</h1>
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
		queryParams: { 'NAMA': $('#NAMA').val() },
		url:'periode-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'TANGGAL_MULAI',
		sortOrder: 'desc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'PERIODE_ID',title:'ID Periode',width:80,sortable:true,align:'center',rowspan:2},
			{field:'PERIODE',title:'Periode',width:120,sortable:true,align:'left',rowspan:2},
			{field:'BULAN',title:'Bulan',width:80,sortable:true,align:'center',rowspan:2},
			{field:'TAHUN',title:'Tahun',width:80,sortable:true,align:'center',rowspan:2},
			{title:'Cut-Off <b>Absen=Payroll</b>',align:'center',colspan:2},
			{title:'Cut-Off <b>Payroll</b>',align:'center',colspan:2},
			{title:'THR',align:'center',colspan:4},
			{field:'STATUS_PERIODE',title:'Status',width:110,sortable:false,align:'center',rowspan:2},
		],[
			{field:'TANGGAL_MULAI',title:'Tgl Mulai',width:110,sortable:false,align:'center'},
			{field:'TANGGAL_SELESAI',title:'Tgl Selesai',width:110,sortable:false,align:'center'},
			{field:'TANGGAL_MULAI2',title:'Tgl Mulai',width:110,sortable:false,align:'center'},
			{field:'TANGGAL_SELESAI2',title:'Tgl Selesai',width:110,sortable:false,align:'center'},
			{field:'THR_IDUL_FITRI',title:'THR Idul Fitri',width:100,sortable:false,align:'center'},
			{field:'TGL_IDUL_FITRI',title:'Tgl Idul Fitri',width:100,sortable:false,align:'center'},
			{field:'THR_KUNINGAN',title:'THR Kuningan',width:100,sortable:false,align:'center'},
			{field:'TGL_KUNINGAN',title:'Tgl Kuningan',width:100,sortable:false,align:'center'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'periode-action.php?op=edit&id='+sel.PERIODE_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'periode-action.php?op=delete&id='+sel.PERIODE_ID;
		return false;
	});
	$('#btn-search').click(function(){
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
		NAMA: $('#NAMA').val(),
	});
}
</script>

<?php
include 'footer.php';
?>