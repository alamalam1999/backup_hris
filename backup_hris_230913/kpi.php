<?php
include 'app-load.php';

is_login('kpi.view');

$DATA = db_fetch("
	SELECT *
	FROM struktur
	ORDER BY ORD ASC
");
$list = array();
if( count($DATA) > 0 ){
	foreach($DATA as $row){
		$thisref = & $refs[ $row->STRUKTUR_ID ];
		$thisref = array_merge((array) $thisref,(array) $row);
		if ($row->PARENT_ID == 0) {
			$list[] = & $thisref;
		} else {
			$refs[$row->PARENT_ID]['child'][] = & $thisref;
		}
	}
}
$TREE_CHAR = '_____';
$RS = hirearchy($list);
$PARENT_OPTION = array('0' => ' -- JABATAN --');
if(count($RS)>0){
	foreach($RS as $row){
		$TREE = '';
		for($i=1; $i<$row->DEPTH; $i++){
			$TREE .= $TREE_CHAR;
		}
		$PARENT_OPTION[$row->STRUKTUR_ID] = '<span style="color:#cccccc;">'.$TREE.'</span>' . ' ' . strtoupper($row->STRUKTUR);
	}
}

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
				<li><a href="kpi-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('KPI','NAMA') ?>" class="form-control input-sm" autocomplete="off" placeholder="Indicator....">
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('STRUKTUR_ID',$PARENT_OPTION,get_search('KPI','STRUKTUR_ID'),' id="STRUKTUR_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-6">
			<h1 style="margin:0;text-align:right;">Key Performance Index</h1>
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
			'STRUKTUR_ID': $('#STRUKTUR_ID').val(),
		},
		url:'kpi-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'INDICATOR',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'INDICATOR',title:'Indicator',width:700,sortable:true,align:'left'},
			{field:'UNIT',title:'Unit',width:100,sortable:false,align:'center'},
			{field:'STRUKTUR',title:'Struktur',width:150,sortable:false,align:'center'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'kpi-action.php?op=edit&id='+sel.KPI_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'kpi-action.php?op=delete&id='+sel.KPI_ID;
		return false;
	});
	$('#NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	$('#STRUKTUR_ID').change(function(){
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
		STRUKTUR_ID: $('#STRUKTUR_ID').val(),
	});
}
</script>

<?php
include 'footer.php';
?>