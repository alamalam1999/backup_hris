<?php
include 'app-load.php';

is_login('project.view');

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
					<li><a href="project-action.php?op=add"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
					<li><a href="javascript:void(0)" id="btn-edit"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('COMPANY_ID', array('' => '--all--') + dropdown_option('company', 'COMPANY_ID', 'COMPANY', 'ORDER BY COMPANY ASC'), get_search('PROJECT', 'COMPANY_ID'), ' id="COMPANY_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('PROJECT', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-6">
			<h1 style="margin:0;text-align:right;">Unit</h1>
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
				'COMPANY_ID': $('#COMPANY_ID').val(),
				'NAMA': $('#NAMA').val()
			},
			url: 'project-json.php',
			fit: true,
			border: true,
			nowrap: false,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'PROJECT_ID',
			sortOrder: 'asc',
			singleSelect: true,
			pagination: true,
			pageSize: 50,
			pageList: [50, 100],
			rownumbers: true,
			columns: [
				[{
						field: 'PROJECT_ID',
						title: 'ID',
						width: 50,
						sortable: true,
						align: 'center'
					},
					{
						field: 'PROJECT',
						title: 'Unit',
						width: 180,
						sortable: true,
						align: 'left'
					},
					{
						field: 'COMPANY',
						title: 'Company',
						width: 150,
						sortable: false,
						align: 'left'
					},
					{
						field: 'CUTOFF',
						title: 'Cut-Off',
						width: 180,
						sortable: false,
						align: 'left'
					},
					{
						field: 'START_DATE',
						title: 'Tgl Mulai',
						width: 100,
						sortable: false,
						align: 'center'
					},
					{
						field: 'FINISH_DATE',
						title: 'Tgl Selesai',
						width: 100,
						sortable: false,
						align: 'center'
					},
					{
						field: 'STATUS',
						title: 'Status',
						width: 100,
						sortable: false,
						align: 'center'
					},
					{
						field: 'SHOWING',
						title: 'Tampilkan',
						width: 100,
						sortable: false,
						align: 'center'
					},
					{
						field: 'NOTE',
						title: 'Keterangan',
						width: 300,
						sortable: false,
						align: 'left'
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
			else window.location = 'project-action.php?op=edit&id=' + sel.PROJECT_ID;
			return false;
		});
		$('#btn-delete').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) window.location = 'project-action.php?op=delete&id=' + sel.PROJECT_ID;
			return false;
		});
		$('#btn-search').click(function() {
			doSearch();
			return false;
		});
		$('#COMPANY_ID').change(function() {
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
			$('#COMPANY_ID').val("");
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
			COMPANY_ID: $('#COMPANY_ID').val(),
			NAMA: $('#NAMA').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>