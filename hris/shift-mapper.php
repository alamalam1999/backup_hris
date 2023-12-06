<?php
include 'app-load.php';

is_login('shift-mapper.view');

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
				<li><a href="shift-mapper-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID',project_option_filter(0),get_search('SHIFT-MAPPER','PROJECT_ID'),' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="SHIFT_CODE" value="<?php echo get_search('SHIFT-MAPPER','SHIFT_CODE') ?>" class="form-control input-sm" placeholder="Code....">
		</div>
		<div class="col-sm-6">
			<h1 style="margin:0;text-align:right;"><span style="color:#aaaaaa;">Master</span> - Shift Mapper</h1>
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
		queryParams: { 'PROJECT_ID': $('#PROJECT_ID').val(), 'SHIFT_CODE': $('#SHIFT_CODE').val() },
		url:'shift-mapper-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'MAPPER_ID',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'PROJECT',title:'Unit',width:250,sortable:true,align:'center'},
			{field:'VAR',title:'Source Code',width:100,sortable:false,align:'center'},
			{field:'VAL',title:'Dest Code',width:100,sortable:false,align:'center'},
			{field:'START_TIME',title:'In',width:100,sortable:false,align:'center'},
			{field:'FINISH_TIME',title:'Out',width:100,sortable:false,align:'center'},
			{field:'SHIFT_COLOR',title:'Color',width:80,sortable:false,align:'center'},
			{field:'OVERNIGHT',title:'Overnight',width:80,sortable:false,align:'center'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'shift-mapper-action.php?op=edit&id='+sel.MAPPER_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'shift-mapper-action.php?op=delete&id='+sel.MAPPER_ID;
		return false;
	});
	$('#btn-search').click(function(){
		doSearch();
		return false;
	});
	$('.input-search, #SHIFT_CODE').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	datagrid();
	$('#PROJECT_ID').change(function(){
		$('#t').datagrid('load',{
			PROJECT_ID: $('#PROJECT_ID').val(),
			SHIFT_CODE: $('#SHIFT_CODE').val(),
		});
	});
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
		PROJECT_ID: $('#PROJECT_ID').val(),
		SHIFT_CODE: $('#SHIFT_CODE').val(),
	});
}
</script>

<?php
include 'footer.php';
?>