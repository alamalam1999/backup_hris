<?php

include 'app-load.php';

#is_login('rekap.view');

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
				<?php /*<li><a href="rekap-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>*/ ?>
				
				<?php if(has_access('rekap.change_status')){ ?>
				<li><a href="javascript:void(0)" id="btn-approved" style="color:#00cf00;"><i class="fa fa-check"></i>&nbsp;&nbsp;Approved</a></li>
				<?php } ?>
				
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),get_search('REKAP','PERIODE_ID'),' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('REKAP','NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">Rekap Total</h1>
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
		queryParams: { 'PERIODE_ID': $('#PERIODE_ID').val(), 'NAMA': $('#NAMA').val() },
		url:'rekap-json.php',
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
		//pageSize:50,
		//pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'PROJECT',title:'Unit',width:150,sortable:false,align:'center'},
			{field:'GAJI_POKOK_NET',title:'Penghasilan',width:100,sortable:false,align:'right'},
			{field:'TUNJANGAN',title:'Tunjangan',width:100,sortable:false,align:'right'},
			{field:'TOTAL_BPJS_JHT',title:'BPJS JHT',width:100,sortable:false,align:'right'},
			{field:'TOTAL_BPJS_JP',title:'BPJS JP',width:100,sortable:false,align:'right'},
			{field:'TOTAL_BPJS_KES',title:'BPJS KES',width:100,sortable:false,align:'right'},
			{field:'GAJI_BERSIH',title:'Gaji Bersih',width:120,sortable:false,align:'right'},
		]],
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'rekap-action.php?op=edit&id='+sel.REKAP_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?'))
		{
			var REASON = prompt("Kenapa rekap ini di VOID ?", "Alasan");
			if (REASON != null) {
				window.location = 'rekap-action.php?op=delete&id='+sel.REKAP_ID+'&reason='+REASON;
			}
		}
		return false;
	});
	$('#PERIODE_ID').change(function(){
		doSearch();
		return false;
	});
	$('#NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	$('#btn-reset').click(function(){
		$('#PERIODE_ID').val("");
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
		NAMA: $('#NAMA').val(),
	});
}
</script>

<?php
include 'footer.php';
?>