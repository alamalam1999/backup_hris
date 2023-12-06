<?php
include 'app-load.php';

is_login('shift.view');

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
					<li><a href="shift-action.php?op=add"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
					<li><a href="javascript:void(0)" id="btn-edit"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
					<li><a href="shift-import.php"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Import</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="SHIFT_CODE" value="<?php echo get_search('SHIFT', 'SHIFT_CODE') ?>" class="form-control input-sm" placeholder="Code....">
		</div>
		<div class="col-sm-8">
			<h1 style="margin:0;text-align:right;"><span style="color:#aaaaaa;">Master</span> - Shift</h1>
		</div>
	</div>
	<section class="content">

		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>

	</section>
</section>

<script>
	$(document).ready(function() {
		$('#t').datagrid({
			queryParams: {
				'SHIFT_CODE': $('#SHIFT_CODE').val()
			},
			url: 'shift-json.php',
			fit: true,
			border: true,
			nowrap: false,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'SHIFT_CODE',
			sortOrder: 'asc',
			singleSelect: true,
			pagination: true,
			pageSize: 50,
			pageList: [50, 100],
			rownumbers: true,
			columns: [
				[{
						field: 'SHIFT_CODE',
						title: 'Shift Code',
						width: 100,
						sortable: true,
						align: 'center',
						rowspan: 2
					},
					{
						title: 'In',
						colspan: 3,
						align: 'center'
					},
					{
						title: 'Out',
						colspan: 3,
						align: 'center'
					},
					{
						field: 'SHIFT_COLOR',
						title: 'Color',
						width: 80,
						sortable: false,
						align: 'center',
						rowspan: 2
					},
					{
						field: 'OVERNIGHT',
						title: 'Overnight',
						width: 80,
						sortable: false,
						align: 'center',
						rowspan: 2
					},
					{
						field: 'STATUS',
						title: 'Type',
						width: 80,
						sortable: false,
						align: 'center',
						rowspan: 2
					},
				],
				[{
						field: 'START_TIME',
						title: 'Time',
						width: 100,
						sortable: false,
						align: 'center'
					},
					{
						field: 'START_BEGIN',
						title: 'Begin',
						width: 60,
						sortable: false,
						align: 'center'
					},
					{
						field: 'START_END',
						title: 'End',
						width: 60,
						sortable: false,
						align: 'center'
					},
					{
						field: 'FINISH_TIME',
						title: 'Time',
						width: 100,
						sortable: false,
						align: 'center'
					},
					{
						field: 'FINISH_BEGIN',
						title: 'Begin',
						width: 60,
						sortable: false,
						align: 'center'
					},
					{
						field: 'FINISH_END',
						title: 'End',
						width: 60,
						sortable: false,
						align: 'center'
					},
				]
			]
		});
		$(window).resize(function() {
			datagrid();
		});

		$('#btn-edit').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else window.location = 'shift-action.php?op=edit&id=' + sel.SHIFT_CODE;
			return false;
		});
		$('#btn-delete').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) window.location = 'shift-action.php?op=delete&id=' + sel.SHIFT_CODE;
			return false;
		});
		$('#btn-search').click(function() {
			doSearch();
			return false;
		});
		$('.input-search, #SHIFT_CODE').keypress(function(e) {
			if (e.which == 13) {
				doSearch();
				e.preventDefault();
			}
		});
		datagrid();
	});

	function datagrid() {
		var wind = parseInt($(window).height());
		var top = parseInt($('.navbar').outerHeight());
		$('#t-responsive').height(wind - top - 70);
		$('#t').datagrid('resize');
	}

	function doSearch() {
		$('#t').datagrid('load', {
			SHIFT_CODE: $('#SHIFT_CODE').val(),
		});
	}
</script>

<?php include 'footer.php'; ?>