<?php

include 'app-load.php';

is_login('eksepsi.view');

include 'header.php';

if (get_input('m') == 'approved') $SUCCESS = 'Data sudah di <b>Approved</b>';
if (get_input('m') == 'void') $ERROR[] = 'Data sudah di <b>Void</b>';
include 'msg.php';

?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
					<li><a href="eksepsi-action.php?op=add"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
					<li><a href="javascript:void(0)" id="btn-edit"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
					<li role="separator" class="divider"></li>
					<?php if (has_access('eksepsi.delete')) { ?>
						<li><a href="javascript:void(0)" id="btn-approved" style="color:green;"><i class="fa fa-check"></i>&nbsp;&nbsp;Approved</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-trash"></i>&nbsp;&nbsp;Void</a></li>
						<li><a href="javascript:void(0)" id="btn-hapus" style="color:red;"><i class="fa fa-close"></i>&nbsp;&nbsp;Delete</a></li>
						<li><a href="javascript:void(0)" id="btn-bulky-hapus" style="color:red;"><i class="fa fa-minus"></i>&nbsp;&nbsp;Multiple Delete</a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), get_search('EKSEPSI', 'PERIODE_ID'), ' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID', project_eksepsi_option(0), get_search('EKSEPSI', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('STATUS', array('' => '-- ALL STATUS --', 'PENDING' => 'PENDING', 'APPROVED' => 'APPROVED', 'VOID' => 'VOID'), get_search('EKSEPSI', 'STATUS'), ' id="STATUS" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('EKSEPSI', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-2">
			<h1 style="margin:0;text-align:right;">Eksepsi</h1>
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
				'PERIODE_ID': $('#PERIODE_ID').val(),
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'NAMA': $('#NAMA').val(),
				'STATUS': $('#STATUS').val()
			},
			url: 'eksepsi-json.php',
			fit: true,
			border: true,
			nowrap: true,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'TGL_MULAI',
			sortOrder: 'desc',
			singleSelect: false,
			pagination: true,
			pageSize: 50,
			pageList: [50, 100],
			rownumbers: true,
			columns: [
				[{
						field: 'ck',
						checkbox: true
					},
					{
						field: 'PERIODE',
						title: 'Periode',
						width: 110,
						sortable: true,
						align: 'center'
					},
					{
						field: 'TGL_MULAI',
						title: 'Tgl Mulai',
						width: 110,
						sortable: false,
						align: 'center'
					},
					{
						field: 'TGL_SELESAI',
						title: 'Tgl Selesai',
						width: 110,
						sortable: false,
						align: 'center'
					},
					{
						field: 'NAMA',
						title: 'Nama Karyawan',
						width: 200,
						sortable: true,
						align: 'left'
					},
					{
						field: 'POSISI',
						title: 'Jabatan',
						width: 150,
						sortable: true,
						align: 'left'
					},
					{
						field: 'JENIS',
						title: 'Jenis',
						width: 110,
						sortable: false,
						align: 'center'
					},
					{
						field: 'STATUS',
						title: 'Status',
						width: 110,
						sortable: true,
						align: 'center'
					},
					{
						field: 'SHIFT_CODE',
						title: 'Shift',
						width: 80,
						sortable: false,
						align: 'center'
					},
					{
						field: 'FILE',
						title: 'File',
						width: 100,
						sortable: false,
						align: 'center'
					},
					{
						field: 'KETERANGAN',
						title: 'Keterangan',
						width: 300,
						sortable: false,
						align: 'left'
					},
					{
						field: 'APPROVED_ON',
						title: 'Approved On',
						width: 140,
						sortable: false,
						align: 'center'
					},
					{
						field: 'APPROVED_BY',
						title: 'Approved By',
						width: 140,
						sortable: false,
						align: 'center'
					},
					{
						field: 'CREATED_ON',
						title: 'Created On',
						width: 140,
						sortable: false,
						align: 'center'
					},
					{
						field: 'CREATED_BY',
						title: 'Created By',
						width: 140,
						sortable: false,
						align: 'center'
					},
					{
						field: 'UPDATED_ON',
						title: 'Updated On',
						width: 140,
						sortable: false,
						align: 'center'
					},
					{
						field: 'UPDATED_BY',
						title: 'Updated By',
						width: 140,
						sortable: false,
						align: 'center'
					},
				]
			],
			rowStyler: function(index, row) {
				if (row.STATUS_KEY == "VOID") {
					return 'background-color:#ffaeae;';
				}
			}
		});

		$(window).resize(function() {
			datagrid();
		});

		$('#btn-edit').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else window.location = 'eksepsi-action.php?op=edit&id=' + sel.EKSEPSI_ID;
			return false;
		});

		$('#btn-delete').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) {
				var REASON = prompt("Kenapa eksepsi ini di VOID?", "");
				if (REASON != null) {
					window.location = 'eksepsi-action.php?op=delete&id=' + sel.EKSEPSI_ID + '&reason=' + REASON;
				}
			}
			return false;
		});

		/* quick approved exception */
		$('#btn-approved').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) {
				var rows = $('#t').datagrid('getSelections');
				var QS = '';
				$.each(rows, function(index, value) {
					QS += '&ids[]=' + value.EKSEPSI_ID;
				});
				window.location = 'eksepsi-action.php?op=approve' + QS;
			}
			return false;
		});
		/* end quick approved exception */

		$('#btn-hapus').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) {
				window.location = 'eksepsi-action.php?op=hapus&id=' + sel.EKSEPSI_ID;
			}
			return false;
		});

		/* quick bulky delete exception */
		$('#btn-bulky-hapus').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Are you sure?')) {
				var rows = $('#t').datagrid('getSelections');
				var QS = '';
				$.each(rows, function(index, value) {
					QS += '&ids[]=' + value.EKSEPSI_ID;
				});
				window.location = 'eksepsi-action.php?op=bulky_hapus' + QS;
			}
			return false;
		});
		/* end quick bulky delete exception */

		$('#btn-search').click(function() {
			doSearch();
			return false;
		});

		$('#PERIODE_ID, #PROJECT_ID, #STATUS').change(function() {
			doSearch();
			return false;
		});

		$('.input-search, #NAMA').keypress(function(e) {
			if (e.which == 13) {
				doSearch();
				e.preventDefault();
			}
		});

		$('#btn-reset').click(function() {
			$('#PERIODE_ID').val("");
			$('#PROJECT_ID').val("");
			$('#NAMA').val("");
			$('#STATUS').val("");
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
			PERIODE_ID: $('#PERIODE_ID').val(),
			PROJECT_ID: $('#PROJECT_ID').val(),
			NAMA: $('#NAMA').val(),
			STATUS: $('#STATUS').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>