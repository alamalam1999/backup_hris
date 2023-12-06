<?php
include 'app-load.php';
is_login('karyawan.view');

$JS[] = 'static/tipsy/jquery.tipsy.js';
$JS[] = 'static/js/datagrid-export.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>
<?php
if (isset($_GET['export'])) {
	//echo "string"; die();
	$PROJECT_ID = get_input('PROJECT_ID');
	header('location: export-karyawan.php?PROJECT_ID=' . $PROJECT_ID);
	exit;
}
?>
<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
					<li><a href="karyawan-action.php?op=add"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
					<li><a href="javascript:void(0)" id="btn-edit"><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
					<li><a href="karyawan-import.php" id="btn-import"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Import from excel</a></li>

					<!-- <li><a href="javascript:void(0);" onclick="$('#t_excel').datagrid('toExcel','KARYAWAN - '+$('#COMPANY_ID option:selected').html()+' - '+$('#PROJECT_ID option:selected').html()+' ('+$('#VIEW_MODE option:selected').html()+' ).xls')" style=""><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li> -->
					<!-- <li><a href="javascript:void(0);" onclick="$('#t_excel').datagrid('toExcel','Data-Karyawan.xls')"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li> -->
					<li><a href="javascript:void(0)" id="btn-export"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li>

					<li role="separator" class="divider"></li>
					<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-trash"></i>&nbsp;&nbsp;Delete</a></li>
					<?php
					/*
					<li><a href="karyawan-action.php?op=generate_password" style="color:red;"><i class="fa fa-trash"></i>&nbsp;&nbsp;Generate Password</a></li>
					*/
					?>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('COMPANY_ID', array('' => '--all company--') + dropdown_option('company', 'COMPANY_ID', 'COMPANY', 'ORDER BY COMPANY ASC'), get_search('KARYAWAN', 'COMPANY_ID'), ' id="COMPANY_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID', project_option_filter(), get_search('KARYAWAN', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('KARYAWAN', 'NAMA') ?>" class="form-control input-sm" autocomplete="off" placeholder="Nama....">
		</div>
		<div class="col-sm-2">
			<input type="text" id="NIK" value="<?php echo get_search('KARYAWAN', 'NIK') ?>" class="form-control input-sm" autocomplete="off" placeholder="Nik....">
		</div>
		<div class="col-sm-2">
			<h1 style="margin:0;text-align:right;">Karyawan</h1>
		</div>
	</div>

	<section class="content">

		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>

	</section>

	<section class="content" style="display: none;">

		<div id="t-responsive" class="table-responsive">
			<table id="t_excel" style="min-height:200px;"></table>
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
				'NAMA': $('#NAMA').val(),
				'NIK': $('#NIK').val()
			},
			url: 'karyawan-json.php',
			fit: true,
			border: true,
			nowrap: true,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'KARYAWAN_ID',
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
						field: 'COMPLETED',
						title: 'Comp',
						width: 50,
						sortable: true,
						align: 'left'
					},

					{
						field: 'APPROVED',
						title: 'Appr',
						width: 50,
						sortable: true,
						align: 'left'
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
						field: 'EMAIL',
						title: 'Email',
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
						title: 'Jabatan',
						width: 150,
						sortable: true,
						align: 'center'
					},
					{
						field: 'PROJECT',
						title: 'Unit',
						width: 150,
						sortable: false,
						align: 'center'
					},
					// {
					// 	field: 'COMPANY',
					// 	title: 'Company',
					// 	width: 150,
					// 	sortable: false,
					// 	align: 'center'
					// },
					// {
					// 	field: 'POSISI',
					// 	title: 'Jabatan',
					// 	width: 150,
					// 	sortable: true,
					// 	align: 'center'
					// },
					<?php if (has_access('karyawan.view_gaji')) { ?> {
							field: 'GAJI_POKOK',
							title: 'Gaji Pokok',
							width: 100,
							sortable: false,
							align: 'right'
						},
					<?php } ?>
					{
						field: 'TGL_MASUK',
						title: 'Join',
						width: 110,
						sortable: false,
						align: 'center'
					},
					{
						field: 'AGAMA',
						title: 'Agama',
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
		$('#t').datagrid({
			rowStyler: function(index, row) {
				if (row.diff == 'new') {
					return 'background-color:#93c6c3;';
				}
			}
		});

		$('#t_excel').datagrid({
			queryParams: {
				'JABATAN_ID': $('#JABATAN_ID').val(),
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'COMPANY_ID': $('#COMPANY_ID').val(),
				'NAMA': $('#NAMA').val(),
				'NIK': $('#NIK').val()
			},
			url: 'karyawan-json.php',
			fit: true,
			border: true,
			nowrap: true,
			striped: true,
			collapsible: true,
			remoteSort: true,
			sortName: 'KARYAWAN_ID',
			sortOrder: 'asc',
			singleSelect: false,
			pagination: true,
			pageSize: 50,
			pageList: [50, 100],
			rownumbers: true,
			columns: [
				[

					{
						field: 'no',
						title: 'No',
						width: 30,
						sortable: true,
						align: 'center'
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
						title: 'JENIS KELAMIN',
						width: 50,
						sortable: true,
						align: 'center'
					},

					{
						title: 'UPAH TETAP',
						align: 'center',
						colspan: 5
					},
				],
				[{
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
					// {
					// 	field: 'TIPE_GAJI',
					// 	title: 'Tipe Gaji',
					// 	width: 80,
					// 	sortable: false,
					// 	align: 'center'
					// },
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
			else window.location = 'karyawan-action.php?op=edit&id=' + sel.KARYAWAN_ID;
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
		$('#JABATAN_ID, #COMPANY_ID, #PROJECT_ID').change(function() {
			doSearch();
			return false;
		});
		$('.input-search, #NAMA, #NIK').keypress(function(e) {
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
			$('#NIK').val("");
			doSearch();
			return false;
		});
		$('#btn-export').click(function() {
			window.location = 'export-karyawan.php?export=1&PROJECT_ID=' + $('#PROJECT_ID').val();
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
			NAMA: $('#NAMA').val(),
			NIK: $('#NIK').val(),
		});

		$('#t_excel').datagrid('load', {
			JABATAN_ID: $('#JABATAN_ID').val(),
			COMPANY_ID: $('#COMPANY_ID').val(),
			PROJECT_ID: $('#PROJECT_ID').val(),
			NAMA: $('#NAMA').val(),
			NIK: $('#NIK').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>