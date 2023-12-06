<?php

include 'app-load.php';

is_login('jadwal.view');

/*$PERIODE_ID = (int) get_input('PERIODE_ID');
$VIEW = get_input('VIEW') ? get_input('VIEW') : 'TIME';
if(empty($PERIODE_ID))
{
	$PERIODE_ID = get_search('JADWAL','PERIODE_ID');
	if(empty($PERIODE_ID))
	{
		$jadwal = db_first(" SELECT * FROM periode ORDER BY PERIODE_ID DESC ");
		set_search('JADWAL', array('PERIODE_ID'));
		header('location: '.$_SERVER['PHP_SELF'].'?PERIODE_ID='.$jadwal->PERIODE_ID);
		exit;
	}
	else
	{
		set_search('JADWAL', array('PERIODE_ID'));
		$jadwal = db_first(" SELECT * FROM periode ORDER BY PERIODE_ID DESC ");
		header('location: '.$_SERVER['PHP_SELF'].'?PERIODE_ID='.$PERIODE_ID);
		exit;
	}
}*/

$JS[] = 'static/tipsy/jquery.tipsy.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<section class="container-fluid">
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET" style="margin:10px 0;">
		<div class="row">
			<div class="col-sm-2">
				<div class="dropdown">
					<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
						<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dd1">
						<li><a href="jadwal-import.php"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Import</a></li>
						<li><a href="jadwal-action.php?op=add"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Jadwal</a></li>
					</ul>
				</div>
			</div>
			<div class="col-sm-2">
				<?php echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), get_search('JADWAL', 'PERIODE_ID'), ' id="PERIODE_ID" class="form-control input-sm" ') ?>
			</div>
			<div class="col-sm-2">
				<?php echo dropdown('PROJECT_ID', project_option_filter(0), get_search('JADWAL', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
			</div>
			<div class="col-sm-2">
				<?php echo dropdown('VIEW', array('ABSENSI' => 'Cutoff Absensi', 'PAYROLL' => 'Cutoff Payroll'), get_search('JADWAL', 'VIEW_MODE'), ' id="VIEW_MODE" class="form-control input-sm" ') ?>
			</div>
			<div class="col-sm-2">
				<input type="text" id="NAMA" value="<?php echo get_search('JADWAL', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
			</div>
			<div class="col-sm-2">
				<h1 style="margin:0;text-align:right;">Jadwal</h1>
			</div>
		</div>
	</form>

	<section class="content">

		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>

	</section>
</section>

<script>
	$(document).ready(function() {

		make_dg();
		$('#PERIODE_ID, #PROJECT_ID, #VIEW_MODE').change(function() {
			make_dg();
			return false;
		});

		$('#PROJECT_ID').change(function() {
			doSearch();
			return false;
		});
		$('#NAMA').keypress(function(e) {
			if (e.which == 13) {
				doSearch();
				e.preventDefault();
			}
		});
		datagrid();
		$('#VIEW_MODE').change(function() {
			doSearch()
		});
	});

	function make_dg() {
		$.ajax({
			url: 'jadwal-header-json.php',
			data: {
				'PERIODE_ID': $('#PERIODE_ID').val(),
				'PROJECT_ID': $('#PROJECT_ID').val(),
				'VIEW_MODE': $('#VIEW_MODE').val(),
				'NAMA': $('#NAMA').val(),
			},
			dataType: 'script',
			method: 'GET',
			success: function(res) {}
		});
	}

	function datagrid() {
		var wind = parseInt($(window).height());
		var top = parseInt($('.navbar').outerHeight());
		$('#t-responsive').height(wind - top - 70);
		$('#t').datagrid('resize');
	}

	function doSearch() {
		make_dg();
		$('#t').datagrid('load', {
			PERIODE_ID: $('#PERIODE_ID').val(),
			PROJECT_ID: $('#PROJECT_ID').val(),
			VIEW_MODE: $('#VIEW_MODE').val(),
			NAMA: $('#NAMA').val(),
		});
	}
</script>

<?php
include 'footer.php';
?>