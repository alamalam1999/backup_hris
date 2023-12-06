<?php
include 'app-load.php';

is_login('user.view');

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
					<li><a href="user-action.php?op=add"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
					<li><a href="javascript:void(0)" id="btn-edit"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('LEVEL_ID', array('' => '--all--') + dropdown_option('user_level', 'LEVEL_ID', 'LEVEL', 'ORDER BY LEVEL ASC'), get_search('USER', 'LEVEL_ID'), ' id="LEVEL_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID', array('' => '--all--') + dropdown_option('project', 'PROJECT_ID', 'PROJECT', 'ORDER BY PROJECT ASC'), get_search('USER', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('USER', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">User</h1>
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
				'LEVEL_ID': $('#LEVEL_ID').val(),
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'NAMA': $('#NAMA').val()
			},
			url: 'user-json.php',
			fit: true,
			border: true,
			nowrap: true,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'NAMA',
			sortOrder: 'asc',
			singleSelect: true,
			pagination: true,
			pageSize: 50,
			pageList: [50, 100],
			rownumbers: true,
			columns: [
				[{
						field: 'EMAIL',
						title: 'Email',
						width: 250,
						sortable: false,
						align: 'left'
					},
					{
						field: 'NAMA',
						title: 'Nama',
						width: 200,
						sortable: true,
						align: 'left'
					},
					{
						field: 'LEVEL',
						title: 'Level',
						width: 150,
						sortable: false,
						align: 'center'
					},
					{
						field: 'PROJECT',
						title: 'Unit',
						width: 150,
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
			else window.location = 'user-action.php?op=edit&id=' + sel.USER_ID;
			return false;
		});
		$('#btn-delete').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) window.location = 'user-action.php?op=delete&id=' + sel.USER_ID;
			return false;
		});
		$('#LEVEL_ID, #PROJECT_ID').change(function() {
			doSearch();
			return false;
		});
		$('#NAMA').keypress(function(e) {
			if (e.which == 13) {
				doSearch();
				e.preventDefault();
			}
		});
		$('#btn-reset').click(function() {
			$('#LEVEL_ID').val("");
			$('#PROJECT_ID').val("");
			$('#NAMA').val("");
			doSearch();
			return false;
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
			LEVEL_ID: $('#LEVEL_ID').val(),
			PROJECT_ID: $('#PROJECT_ID').val(),
			NAMA: $('#NAMA').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>