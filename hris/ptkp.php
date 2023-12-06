<?php
include 'app-load.php';

is_login('ptkp.view');

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
				<li><a href="ptkp-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('PTKP','NAMA') ?>" class="form-control input-sm" autocomplete="off" placeholder="Status PTKP....">
		</div>
		<div class="col-sm-8">
			<h1 style="margin:0;text-align:right;">PTKP</h1>
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
		url:'ptkp-json.php',
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
		columns:[[
			{field:'NAMA',title:'Status PTKP',width:100,sortable:true,align:'left'},
			{field:'NILAI',title:'Besaran PTKP',width:200,sortable:false,align:'right'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'ptkp-action.php?op=edit&id='+sel.PTKP_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'ptkp-action.php?op=delete&id='+sel.PTKP_ID;
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
		NAMA: $('#NAMA').val(),
	});
}
</script>

<?php
include 'footer.php';
?>