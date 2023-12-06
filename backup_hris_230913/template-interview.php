<?php
include 'app-load.php';
is_login('template-interview.view');
include 'header.php';
$MODULE = 'TEMPLATE-INTERVIEW';
?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
				<li><a href="template-interview-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="TEMPLATE" value="<?php echo get_search($MODULE,'TEMPLATE') ?>" class="form-control input-sm" placeholder="Template....">
		</div>
		<div class="col-sm-8">
			<h1 style="margin:0;text-align:right;">Template Interview</h1>
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
		queryParams: { 'TEMPLATE': $('#TEMPLATE').val() },
		url:'template-interview-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'TEMPLATE_ID',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		//onLoadSuccess: onLoadSuccess,
		columns:[[
			{field:'TEMPLATE',title:'Template',width:200,sortable:true,align:'left'},
			{field:'PERTANYAAN',title:'Daftar Pertanyaan',width:700,sortable:true,align:'left'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'template-interview-action.php?op=edit&id='+sel.TEMPLATE_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'template-interview-action.php?op=delete&id='+sel.TEMPLATE_ID;
		return false;
	});
	$('#TEMPLATE').keypress(function (e) {
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
		TEMPLATE: $('#TEMPLATE').val(),
	});
}

/*function onLoadSuccess(data){
	var merges = [
	{
		index: 0,
		rowspan: 5
	},
	{
		index: 5,
		rowspan: 5
	},
	];
	for(var i=0; i<merges.length; i++){
		$(this).datagrid('mergeCells',{
			index: merges[i].index,
			field: 'TEMPLATE',
			rowspan: merges[i].rowspan
		});
	}
}*/

</script>

<?php 
include 'footer.php'; 
?>