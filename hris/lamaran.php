<?php
include 'app-load.php';
is_login('lamaran.view');
/*$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';*/
include 'header.php';
$MODULE = 'LAMARAN';
?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
				<li><a href="lamaran-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li><a href="javascript:void(0)" id="btn-check" style=""><i class="fa fa-check"></i>&nbsp;&nbsp;Update Status</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search($MODULE,'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-2">	
			<?php echo dropdown('POSISI_ID',dropdown_option_default('posisi','POSISI_ID','POSISI','ORDER BY POSISI ASC','-- All jabatan --'),get_search('LAMARAN','POSISI_ID'),' class="form-control input-sm" id="POSISI_ID"') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('LOWONGAN_ID',dropdown_option_default('lowongan','LOWONGAN_ID','LOWONGAN','ORDER BY LOWONGAN ASC','-- All Lowongan --'),get_search('LAMARAN','LOWONGAN_ID'),' class="form-control input-sm" id="LOWONGAN_ID"') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="USIA" value="<?php echo get_search($MODULE,'USIA') ?>" class="form-control input-sm" placeholder="Usia(< Th)....">
		</div>
		<div class="col-sm-2">
			<h1 style="margin:0;text-align:right;">Lamaran</h1>
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
		url:'lamaran-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'APPLICANT_NO',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'NAMA',title:'Nama',width:200,sortable:true,align:'left'},
			{field:'JK',title:'JK',width:50,sortable:true,align:'center'},
			{field:'APPLICANT_NO',title:'Applicant No',width:200,sortable:true,align:'left'},
			{field:'LOWONGAN',title:'Lowongan',width:200,sortable:true,align:'left'},
			{field:'POSISI',title:'Jabatan',width:200,sortable:true,align:'left'},
			{field:'STATUS_LAMARAN',title:'Status',width:150,sortable:true,align:'center'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'lamaran-action.php?op=edit&id='+sel.LAMARAN_ID;
		return false;
	});
	$('#btn-check').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		if( sel.POSISI == null) alert('Jabatan yang dilamar belum ditentukan');
		else window.location = 'lamaran-status.php?op=edit&id='+sel.CALON_KARYAWAN_ID+'&lamaran='+sel.LAMARAN_ID+'&lowongan='+sel.LOWONGAN_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'lamaran-action.php?op=delete&id='+sel.LAMARAN_ID;
		return false;
	});

	$('#NAMA, #USIA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});

	$('#POSISI_ID, #LOWONGAN_ID').change(function(){
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
		POSISI_ID: $('#POSISI_ID').val(),
		LOWONGAN_ID: $('#LOWONGAN_ID').val(),
		USIA: $('#USIA').val(),
	});
}
</script>

<?php 
include 'footer.php'; 
?>