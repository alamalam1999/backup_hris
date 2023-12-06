<?php
include 'app-load.php';

is_login('holiday.view');

$rs = db_fetch(" SELECT YEAR FROM holiday GROUP BY YEAR ORDER BY YEAR DESC ");
$YEAR = array();
if(count($rs)>0)
{
	foreach($rs as $row)
	{
		$YEAR[$row->YEAR] = $row->YEAR;
	}
}

if( get_input('copy')=='1' )
{
	$YEAR = get_input('year');
	
	$rs = db_fetch(" SELECT * FROM holiday WHERE YEAR='$YEAR' ");
	if(count($rs)>0)
	{
		foreach($rs as $row)
		{
			$DATE = date('Y-m-d',strtotime('+1 year', strtotime($row->DATE)));
			$YEAR = date('Y',strtotime($DATE));
			$HOLIDAY = db_escape($row->HOLIDAY);
			db_execute(" INSERT INTO holiday (DATE,YEAR,HOLIDAY) VALUES ('$DATE','$YEAR','$HOLIDAY') ");
		}
	}
	
	header('location: holiday.php');
	exit;
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
				<li><a href="holiday-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li><a href="javascript:void(0)" id="btn-copy" style=""><i class="fa fa-copy"></i>&nbsp;&nbsp;Copy Next Year</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('YEAR',$YEAR,get_search('HOLIDAY','YEAR'),' id="YEAR" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('HOLIDAY','NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-6">
			<h1 style="margin:0;text-align:right;">Holiday</h1>
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
		queryParams: { 'YEAR': $('#YEAR').val(),'NAMA': $('#NAMA').val() },
		url:'holiday-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'DATE',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:100,
		pageList: [100],
		rownumbers:true,
		columns:[[
			{field:'DATE',title:'Date',width:110,sortable:false,align:'center'},
			{field:'HOLIDAY',title:'Description',width:400,sortable:false,align:'left'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-edit').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'holiday-action.php?op=edit&id='+sel.HOLIDAY_ID;
		return false;
	});
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else if(confirm('Are you sure?')) window.location = 'holiday-action.php?op=delete&id='+sel.HOLIDAY_ID;
		return false;
	});
	$('#btn-copy').click(function(){
		window.location = 'holiday.php?copy=1&year='+$('#YEAR').val();
		return false;
	});
	$('#btn-search').click(function(){
		doSearch();
		return false;
	});
	$('#YEAR').change(function(){
		doSearch();
		return false;
	});
	$('.input-search, #NAMA').keypress(function (e) {
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
		YEAR: $('#YEAR').val(),
		NAMA: $('#NAMA').val(),
	});
}
</script>

<?php
include 'footer.php';
?>