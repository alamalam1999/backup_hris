<?php
include 'app-load.php';
is_login('karyawan-pasif.view');
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
					<?php /*
				<li><a href="karyawan-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				*/ ?>
					<li><a href="javascript:void(0)" id="btn-edit"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Activate</a></li>
					<li>
						<a href="karyawan-pasif-import.php" id="btn-import"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Import from excel</a>
					</li>
					<li role="separator" class="divider"></li>
					<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('COMPANY_ID', array('' => '--all company--') + dropdown_option('company', 'COMPANY_ID', 'COMPANY', 'ORDER BY COMPANY ASC'), get_search('KARYAWAN_PASIF', 'COMPANY_ID'), ' id="COMPANY_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID', project_option_filter(), get_search('KARYAWAN_PASIF', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('ST_KERJA', array('' => '--all status--', 'PASIF' => 'PASIF', 'RESIGN' => 'RESIGN', 'PENSIUN' => 'PENSIUN'), get_search('KARYAWAN_PASIF', 'ST_KERJA'), ' class="form-control input-sm" id="ST_KERJA"') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('KARYAWAN_PASIF', 'NAMA') ?>" class="form-control input-sm" autocomplete="off" placeholder="Nama....">
		</div>
		<div class="col-sm-2">
			<h1 style="margin:0;text-align:right;">Karyawan Pasif</h1>
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
				'JABATAN_ID': $('#JABATAN_ID').val(),
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'COMPANY_ID': $('#COMPANY_ID').val(),
				'NAMA': $('#NAMA').val()
			},
			url: 'karyawan-pasif-json.php',
			fit: true,
			border: true,
			nowrap: true,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'NAMA',
			sortOrder: 'asc',
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
						field: 'KARYAWAN_ID',
						title: 'PIN',
						width: 60,
						sortable: true,
						align: 'center'
					},
					{
						field: 'NIK',
						title: 'NIK',
						width: 90,
						sortable: true,
						align: 'center'
					},
					{
						field: 'NAMA',
						title: 'Nama Karyawan',
						width: 180,
						sortable: true,
						align: 'left'
					},
					{
						field: 'JK',
						title: 'JK',
						width: 50,
						sortable: true,
						align: 'center'
					},
					{
						field: 'JABATAN',
						title: 'Level Jabatan',
						width: 150,
						sortable: true,
						align: 'center'
					},
					{
						field: 'POSISI',
						title: 'Jabatan',
						width: 150,
						sortable: true,
						align: 'center'
					},
					<?php if (has_access('karyawan.view_gaji')) { ?> {
							field: 'GAJI_POKOK',
							title: 'Gaji Pokok',
							width: 100,
							sortable: false,
							align: 'right'
						},
					<?php } ?> {
						field: 'PROJECT',
						title: 'Unit',
						width: 150,
						sortable: false,
						align: 'center'
					},
					{
						field: 'COMPANY',
						title: 'Company',
						width: 150,
						sortable: false,
						align: 'center'
					},
					{
						field: 'TGL_MASUK',
						title: 'Join',
						width: 110,
						sortable: false,
						align: 'center'
					},
					{
						field: 'TIPE_GAJI',
						title: 'Tipe Gaji',
						width: 80,
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
		/*$('#t').datagrid({
			rowStyler: function(index,row){
				if (row.STATUS=='VOID'){
					return 'background-color:#ff0000;';
				}
			}
		});*/

		$(window).resize(function() {
			datagrid();
		});

		$('#btn-edit').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else window.location = 'karyawan-action.php?op=edit_pasif&id=' + sel.KARYAWAN_ID;
			return false;
		});
		$('#btn-delete').click(function() {
			var sel = $('#t').datagrid('getSelected');
			if (sel == undefined) alert('No item selected');
			else if (confirm('Yakin data dihapus? menghapus data karyawan juga akan menghilangkan semua riwayat.')) {
				var rows = $('#t').datagrid('getSelections');
				var QS = '';
				$.each(rows, function(index, value) {
					QS += '&ids[]=' + value.KARYAWAN_ID;
				});
				window.location = 'karyawan-action.php?op=delete' + QS;
				//var REASON = prompt("Kenapa karyawan ini di VOID?", "");
				//if (REASON != null) {
				//}
			}
			return false;
		});
		$('#btn-search').click(function() {
			doSearch();
			return false;
		});
		$('#JABATAN_ID, #COMPANY_ID, #PROJECT_ID, #ST_KERJA').change(function() {
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
			$('#COMPANY_ID').val("");
			$('#PROJECT_ID').val("");
			$('#JABATAN_ID').val("");
			$('#NAMA').val("");
			$('#ST_KERJA').val("");
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
			JABATAN_ID: $('#JABATAN_ID').val(),
			COMPANY_ID: $('#COMPANY_ID').val(),
			PROJECT_ID: $('#PROJECT_ID').val(),
			ST_KERJA: $('#ST_KERJA').val(),
			NAMA: $('#NAMA').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>