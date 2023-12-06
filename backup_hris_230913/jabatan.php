<?php
include 'app-load.php';

is_login('jabatan.view');

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
					<li><a href="jabatan-action.php?op=add"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
					<li><a href="javascript:void(0)" id="btn-edit"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
					<li><a href="jabatan-import.php"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Import</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('COMPANY_ID', array('' => '--all--') + dropdown_option('company', 'COMPANY_ID', 'COMPANY', 'ORDER BY COMPANY_ID DESC'), get_search('JABATAN', 'COMPANY_ID'), ' id="COMPANY_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID', project_option_filter(), get_search('JABATAN', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="JABATAN" value="<?php echo get_search('JABATAN', 'JABATAN') ?>" class="form-control input-sm" placeholder="Jabatan....">
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">Level Jabatan</h1>
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
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'JABATAN': $('#JABATAN').val()
			},
			url: 'jabatan-json.php',
			fit: true,
			border: true,
			nowrap: false,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'JABATAN_ID',
			sortOrder: 'asc',
			singleSelect: true,
			pagination: true,
			pageSize: 50,
			pageList: [50, 100],
			rownumbers: true,
			frozenColumns: [
				[{
						field: 'JABATAN_ID',
						title: 'ID',
						width: 50,
						sortable: true,
						align: 'center'
					},
					{
						field: 'JABATAN',
						title: 'Jabatan',
						width: 180,
						sortable: true,
						align: 'left'
					},
				]
			],
			columns: [
				[{
						field: 'PROJECT',
						title: 'Unit',
						width: 150,
						sortable: false,
						align: 'left'
					},
					{
						field: 'COMPANY',
						title: 'Company',
						width: 180,
						sortable: false,
						align: 'left'
					},
					{
						field: 'GP',
						title: 'GP',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'LM',
						title: 'LM',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'CT',
						title: 'CT',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'JHT',
						title: 'JHT',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'JP',
						title: 'JP',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'KES',
						title: 'KES',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'MED',
						title: 'MED',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'BAC',
						title: 'BAC',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'TRN',
						title: 'TRN',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'MKN',
						title: 'MKN',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'AHLI',
						title: 'AHLI',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'KMN',
						title: 'KMN',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'JAB',
						title: 'JAB',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'PENG',
						title: 'PENG',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'PROY',
						title: 'PROY',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'SHFT',
						title: 'SHFT',
						width: 40,
						sortable: false,
						align: 'center'
					},
					{
						field: 'THR_PRO',
						title: 'THR PRO',
						width: 60,
						sortable: false,
						align: 'center'
					},
					{
						field: 'THR_FULL',
						title: 'THR FULL',
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
			else window.location = 'jabatan-action.php?op=edit&id=' + sel.JABATAN_ID;
			return false;
		});
		$('#btn-delete').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) window.location = 'jabatan-action.php?op=delete&id=' + sel.JABATAN_ID;
			return false;
		});
		$('#btn-search').click(function() {
			doSearch();
			return false;
		});
		$('#COMPANY_ID, #PROJECT_ID').change(function() {
			doSearch();
			return false;
		});
		$('.input-search, #JABATAN').keypress(function(e) {
			if (e.which == 13) {
				doSearch();
				e.preventDefault();
			}
		});
		$('#btn-reset').click(function() {
			$('#COMPANY_ID').val("");
			$('#PROJECT_ID').val("");
			$('#JABATAN').val("");
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
			PROJECT_ID: $('#PROJECT_ID').val(),
			JABATAN: $('#JABATAN').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>