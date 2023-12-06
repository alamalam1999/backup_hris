<?php
include 'app-load.php';

is_login('struktur.view');

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
				<li><a href="struktur-view.php" style=""><i class="fa fa-eye"></i>&nbsp;&nbsp;View</a></li>
				<li><a href="struktur-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-1">
			<div class="button-group">
				<a href="javascript:void(0)" id="btn-coll" class="btn btn-default btn-sm" style="text-align:center;"><i class="fa fa-arrow-up"></i></a>
				<a href="javascript:void(0)" id="btn-exp" class="btn btn-default btn-sm" style="text-align:center;"><i class="fa fa-arrow-down"></i></a>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('STRUKTUR','NAMA') ?>" class="form-control input-sm" autocomplete="off" placeholder="Struktur Organisasi....">
		</div>
		<div class="col-sm-7">
			<h1 style="margin:0;text-align:right;">Struktur Organisasi</h1>
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
	$('#t').treegrid({
		queryParams: { 'STRUKTUR': $('#STRUKTUR').val() },
		url:'struktur-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		idField:'STRUKTUR_ID',
		treeField:'STRUKTUR',
		lines:true,
		animate:true,
		sortName: 'ORD',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'STRUKTUR',title:'Struktur',width:500,sortable:true,align:'left'},
			{field:'ORD',title:'Order',width:80,sortable:true,align:'center'},
		]]
	});
	$(window).resize(function(){ treegrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').treegrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'struktur-action.php?op=edit&id='+sel.STRUKTUR_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').treegrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'struktur-action.php?op=delete&id='+sel.STRUKTUR_ID;
		return false;
	});
	$('#btn-coll').click(function(){
		$('#t').treegrid('collapseAll');
		return false;
	});
	$('#btn-exp').click(function(){
		$('#t').treegrid('expandAll');
		return false;
	});
	
	$('#STRUKTUR').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	treegrid();
});
function treegrid()
{
	var wind = parseInt($(window).height());
	var top = parseInt($('.navbar').outerHeight());
	$('#t-responsive').height(wind - top - 70);
	$('#t').treegrid('resize');
}
function doSearch(){
	$('#t').treegrid('load',{
		STRUKTUR: $('#STRUKTUR').val(),
	});
}
</script>

<?php
include 'footer.php';
?>