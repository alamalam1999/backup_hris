<?php
include 'app-load.php';

is_login('pelatihan.view');

$STATUS = array(
	'' => '--Semua--',
	'PENGAJUAN' => 'PENGAJUAN',
	'TIDAK DISETUJUI' => 'TIDAK DISETUJUI',
	'DISETUJUI' => 'DISETUJUI',
	'TERLAKSANA' => 'TERLAKSANA',
);

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
				<li><a href="pelatihan-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('PELATIHAN','NAMA') ?>" class="form-control input-sm" autocomplete="off" placeholder="Nama....">
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('STATUS',$STATUS,get_search('STATUS','STATUS'),' id="STATUS" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-6">
			<h1 style="margin:0;text-align:right;">Pelatihan</h1>
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
		queryParams: {
			'NAMA': $('#NAMA').val(),
			'STATUS': $('#STATUS').val(),
		},
		url:'pelatihan-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'TGL_MULAI',
		sortOrder: 'desc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'NAMA_PELATIHAN',title:'Nama Pelatihan',width:300,sortable:true,align:'left'},
			{field:'TGL_MULAI',title:'Tgl Mulai',width:110,sortable:false,align:'center'},
			{field:'TGL_SELESAI',title:'Tgl Selesai',width:110,sortable:false,align:'center'},
			{field:'TEMPAT',title:'Tempat',width:200,sortable:false,align:'left'},
			{field:'PESERTA',title:'Peserta',width:100,sortable:false,align:'center'},
			{field:'PENYELENGGARA',title:'Penyelenggara',width:100,sortable:false,align:'center'},
			{field:'STATUS',title:'Status',width:120,sortable:false,align:'center'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'pelatihan-action.php?op=edit&id='+sel.PELATIHAN_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'pelatihan-action.php?op=delete&id='+sel.PELATIHAN_ID;
		return false;
	});
	$('#NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	$('#STATUS').change(function(){
		doSearch();
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
		STATUS: $('#STATUS').val(),
	});
}
</script>

<?php
include 'footer.php';
?>