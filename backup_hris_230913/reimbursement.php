<?php

include 'app-load.php';

is_login('reimbursement.view');

include 'header.php';

if(get_input('m')=='approved') $SUCCESS = 'Data sudah di <b>Approved</b>';
if(get_input('m')=='void') $ERROR[] = 'Data sudah di <b>Void</b>';
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
				<li><a href="reimbursement-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				
				<?php if(has_access('reimbursement.change_status')){ ?>
				<li><a href="javascript:void(0)" id="btn-approved" style="color:#00cf00;"><i class="fa fa-check"></i>&nbsp;&nbsp;Approved</a></li>
				<?php } ?>
				
				<?php if(has_access('reimbursement.delete')){ ?>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-trash"></i>&nbsp;&nbsp;Void</a></li>
				<?php } ?>
				
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),get_search('REIMBURSEMENT','PERIODE_ID'),' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID',project_option_filter(0),get_search('REIMBURSEMENT','PROJECT_ID'),' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('REIMBURSEMENT','NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">Reimbursement</h1>
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
		queryParams: { 'PERIODE_ID': $('#PERIODE_ID').val(), 'PROJECT_ID': $('#PROJECT_ID').val(), 'NAMA': $('#NAMA').val() },
		url:'reimbursement-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'TANGGAL',
		sortOrder: 'desc',
		singleSelect:false,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'ck',checkbox:true},
			{field:'TANGGAL',title:'Tanggal',width:110,sortable:false,align:'center'},
			{field:'NIK',title:'NIK',width:100,sortable:false,align:'center'},
			{field:'NAMA',title:'Nama Karyawan',width:200,sortable:true,align:'left'},
			{field:'TOTAL',title:'Total',width:100,sortable:false,align:'right'},
			{field:'FILE',title:'File',width:100,sortable:false,align:'center'},
			{field:'STATUS',title:'Status',width:110,sortable:false,align:'center'},
			{field:'KETERANGAN',title:'Keterangan',width:300,sortable:false,align:'left'},
			{field:'CREATED_ON',title:'Created On',width:140,sortable:false,align:'center'},
			{field:'CREATED_BY',title:'Created By',width:140,sortable:false,align:'center'},
			{field:'UPDATED_ON',title:'Updated On',width:140,sortable:false,align:'center'},
			{field:'UPDATED_BY',title:'Updated By',width:140,sortable:false,align:'center'},
		]],
		rowStyler:function(index,row){
			if (row.STATUS_KEY == "VOID"){
				return 'background-color:#ffdddd;';
			}
		}
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'reimbursement-action.php?op=edit&id='+sel.REIMBURSEMENT_ID;
		return false;
	});

	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?'))
		{
			var REASON = prompt("Kenapa reimbursement ini di VOID ?", "Alasan");
			if (REASON != null) {
				window.location = 'reimbursement-action.php?op=delete&id='+sel.REIMBURSEMENT_ID+'&reason='+REASON;
			}
		}
		return false;
	});

	/* quick approved reimbursement */
	$('#btn-approved').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')){
			var rows = $('#t').datagrid('getSelections');
			var QS = '';
			$.each(rows, function(index, value){
				QS += '&ids[]=' + value.REIMBURSEMENT_ID;
			});
			window.location = 'reimbursement-action.php?op=approve' + QS;
		}
		return false;
	});
	/* end quick approved reimbursement */

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