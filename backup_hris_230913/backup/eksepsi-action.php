<?php

include 'app-load.php';

is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {

	is_login('eksepsi.edit');
	$EDIT = db_first(" SELECT * FROM eksepsi WHERE EKSEPSI_ID='$ID' ");
	if (isset($EDIT->STATUS) and $EDIT->STATUS == 'APPROVED') {

		header('location: eksepsi.php?m=approved');
		exit;
	}
	if (isset($EDIT->STATUS) and $EDIT->STATUS == 'VOID') {

		header('location: eksepsi.php?m=void');
		exit;
	}
}
// echo "<pre>";
// print_r($EDIT); die();

if ($OP == 'delete') {
	is_login('eksepsi.delete');
	$REASON = db_escape(get_input('reason'));
	db_execute(" UPDATE eksepsi SET STATUS='VOID',KETERANGAN='Alasan Void : $REASON' WHERE EKSEPSI_ID='$ID' ");
	header('location: eksepsi.php');
	exit;
}

if ($OP == 'hapus') {
	is_login('eksepsi.delete');
	db_execute(" DELETE FROM eksepsi WHERE EKSEPSI_ID='$ID' ");
	header('location: eksepsi.php');
	exit;
}

if ($OP == 'bulky_hapus') {
	is_login('eksepsi.delete');
	$IDS = get_input('ids');
	if (is_array($IDS)) {
		$CU = current_user();
		$TIME = date('Y-m-d H:i:s');
		db_execute(" DELETE FROM eksepsi WHERE EKSEPSI_ID IN (" . implode(',', $IDS) . ")");
	}
	header('location: eksepsi.php');
	exit;
}

if ($OP == 'approve') {
	is_login('eksepsi.change_status');
	$IDS = get_input('ids');
	if (is_array($IDS)) {

		$CU = current_user();
		$TIME = date('Y-m-d H:i:s');
		db_execute(" UPDATE eksepsi SET STATUS='APPROVED',UPDATED_BY='$CU->NAMA',APPROVED_BY='$CU->NAMA',APPROVED_ON='$TIME' WHERE EKSEPSI_ID IN (" . implode(',', $IDS) . ")");
	}
	header('location: eksepsi.php');
	exit;
}

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('PERIODE_ID', 'KARYAWAN_ID', 'TGL_MULAI');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$EKSEPSI_ID = db_escape(get_input('EKSEPSI_ID'));
	$PERIODE_ID = get_input('PERIODE_ID');
	$KARYAWAN_ID = get_input('KARYAWAN_ID');
	$TGL_MULAI = get_input('TGL_MULAI');
	$TGL_SELESAI = get_input('TGL_SELESAI');

	$allow_ext = array('pdf', 'jpg', 'jpeg', 'png');
	$file_name = isset($_FILES['FILE']['name']) ? $_FILES['FILE']['name'] : '';
	$file_tmp = isset($_FILES['FILE']['tmp_name']) ? $_FILES['FILE']['tmp_name'] : '';
	$file_ext = strtolower(substr(strrchr($file_name, "."), 1));
	$file_new = rand(11111, 99999) . '_' . $file_name;
	$file_dest = 'uploads/skd/' . $file_new;

	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom <strong>' . implode(', ', $REQUIRE) . '</strong> wajib di isi';
	} else if ((!empty($file_name)) and !in_array($file_ext, $allow_ext)) {
		$ERROR[] = 'File yang diijinkan bertipe : ' . implode(', ', $allow_ext);
	} else {
		$FIELDS = array(
			'PERIODE_ID', 'KARYAWAN_ID', 'JENIS', 'KETERANGAN', 'TGL_MULAI', 'TGL_SELESAI'
		);

		$NEW_FILE = 0;
		if (is_uploaded_file($file_tmp)) {
			if (move_uploaded_file($file_tmp, $file_dest)) {
				$FIELDS[] = 'FILE';
				$NEW_FILE = 1;
			}
		}

		$d = array();
		foreach ($FIELDS as $F) {
			if ($F == 'FILE') {
				if ($NEW_FILE == '1') {
					$INSERT_VAL[$F] = "'" . db_escape($file_new) . "'";
					$UPDATE_VAL[$F] = $F . "='" . db_escape($file_new) . "'";
				} else {
					$INSERT_VAL[$F] = "'" . db_escape(get_input('CURRENT_FILE')) . "'";
					$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input('CURRENT_FILE')) . "'";
				}
			} else {
				$INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
				$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
			}
		}

		if (has_access('eksepsi.change_status')) {
			//EDIT AGUNG
			if (get_input('PROSES_APPROVED_1') == 1) {
				$UPDATE_VAL['PROSES_APPROVED'] = "PROSES_APPROVED='" . get_input('PROSES_APPROVED_1') . "'";
			}

			if (get_input('PROSES_APPROVED_2') == 2) {
				$UPDATE_VAL['PROSES_APPROVED'] = "PROSES_APPROVED='" . get_input('PROSES_APPROVED_2') . "'";
				$FIELDS[] = 'STATUS';
				$INSERT_VAL['STATUS'] = 'APPROVED';
				$UPDATE_VAL['STATUS'] = "STATUS='APPROVED'";
			}
		} else {
			$FIELDS[] = 'STATUS';
			$INSERT_VAL['STATUS'] = "'PENDING'";
		}

		$JENIS = get_input('JENIS');
		$SHIFT_CODE = get_input('SHIFT_CODE');
		if ($JENIS == 'TO_IN') {
			#db_execute(" UPDATE shift_karyawan SET SHIFT_CODE='$SHIFT_CODE' WHERE KARYAWAN_ID='$KARYAWAN_ID' AND DATE='$TGL_MULAI' ");
			$FIELDS['SHIFT_CODE'] = 'SHIFT_CODE';
			$INSERT_VAL['SHIFT_CODE'] = "'" . db_escape($SHIFT_CODE) . "'";
			$UPDATE_VAL['SHIFT_CODE'] = "SHIFT_CODE='" . db_escape($SHIFT_CODE) . "'";

			$INSERT_VAL['TGL_SELESAI'] = "'" . db_escape($TGL_MULAI) . "'";
			$UPDATE_VAL['TGL_SELESAI'] = "TGL_SELESAI='" . db_escape($TGL_MULAI) . "'";
			$TGL_SELESAI = $TGL_MULAI;
		}
		if ($JENIS == 'TO_OUT') {
			$SHIFT_CODE = 'X';
			#db_execute(" UPDATE shift_karyawan SET SHIFT_CODE='$SHIFT_CODE' WHERE KARYAWAN_ID='$KARYAWAN_ID' AND DATE='$TGL_MULAI' ");
			$FIELDS['SHIFT_CODE'] = 'SHIFT_CODE';
			$INSERT_VAL['SHIFT_CODE'] = "'" . db_escape($SHIFT_CODE) . "'";
			$UPDATE_VAL['SHIFT_CODE'] = "SHIFT_CODE='" . db_escape($SHIFT_CODE) . "'";

			$INSERT_VAL['TGL_SELESAI'] = "'" . db_escape($TGL_MULAI) . "'";
			$UPDATE_VAL['TGL_SELESAI'] = "TGL_SELESAI='" . db_escape($TGL_MULAI) . "'";
			$TGL_SELESAI = $TGL_MULAI;
		}
		if ($JENIS == 'TS') {
			$FIELDS['SHIFT_CODE'] = 'SHIFT_CODE';
			$INSERT_VAL['SHIFT_CODE'] = "'" . db_escape($SHIFT_CODE) . "'";
			$UPDATE_VAL['SHIFT_CODE'] = "SHIFT_CODE='" . db_escape($SHIFT_CODE) . "'";

			$INSERT_VAL['TGL_SELESAI'] = "'" . db_escape($TGL_MULAI) . "'";
			$UPDATE_VAL['TGL_SELESAI'] = "TGL_SELESAI='" . db_escape($TGL_MULAI) . "'";
			$TGL_SELESAI = $TGL_MULAI;
		}
		if ($JENIS == 'TS_IN') {
			## db_execute(" UPDATE shift_karyawan SET SHIFT_CODE='$SHIFT_CODE' WHERE KARYAWAN_ID='$KARYAWAN_ID' AND DATE='$TGL_MULAI' ");
			$FIELDS['SHIFT_CODE'] = 'SHIFT_CODE';
			$INSERT_VAL['SHIFT_CODE'] = "'" . db_escape($SHIFT_CODE) . "'";
			$UPDATE_VAL['SHIFT_CODE'] = "SHIFT_CODE='" . db_escape($SHIFT_CODE) . "'";

			$INSERT_VAL['TGL_SELESAI'] = "'" . db_escape($TGL_MULAI) . "'";
			$UPDATE_VAL['TGL_SELESAI'] = "TGL_SELESAI='" . db_escape($TGL_MULAI) . "'";
			$TGL_SELESAI = $TGL_MULAI;
		}
		if ($JENIS == 'TS_OUT') {
			$SHIFT_CODE = 'X';
			##db_execute(" UPDATE shift_karyawan SET SHIFT_CODE='$SHIFT_CODE' WHERE KARYAWAN_ID='$KARYAWAN_ID' AND DATE='$TGL_MULAI' ");
			$FIELDS['SHIFT_CODE'] = 'SHIFT_CODE';
			$INSERT_VAL['SHIFT_CODE'] = "'" . db_escape($SHIFT_CODE) . "'";
			$UPDATE_VAL['SHIFT_CODE'] = "SHIFT_CODE='" . db_escape($SHIFT_CODE) . "'";

			$INSERT_VAL['TGL_SELESAI'] = "'" . db_escape($TGL_MULAI) . "'";
			$UPDATE_VAL['TGL_SELESAI'] = "TGL_SELESAI='" . db_escape($TGL_MULAI) . "'";
			$TGL_SELESAI = $TGL_MULAI;
		}
		if ($JENIS == 'BACKUP') {
			$FIELDS['SHIFT_CODE'] = 'SHIFT_CODE';
			$INSERT_VAL['SHIFT_CODE'] = "'" . db_escape($SHIFT_CODE) . "'";
			$UPDATE_VAL['SHIFT_CODE'] = "SHIFT_CODE='" . db_escape($SHIFT_CODE) . "'";

			$INSERT_VAL['TGL_SELESAI'] = "'" . db_escape($TGL_MULAI) . "'";
			$UPDATE_VAL['TGL_SELESAI'] = "TGL_SELESAI='" . db_escape($TGL_MULAI) . "'";
			$TGL_SELESAI = $TGL_MULAI;
		}
		if ($JENIS == 'IJIN_LE') {
			$JAM_MULAI = get_input('JAM_MULAI');
			$JAM_SELESAI = get_input('JAM_SELESAI');

			$FIELDS['JAM_MULAI'] = 'JAM_MULAI';
			$INSERT_VAL['JAM_MULAI'] = "'" . db_escape($JAM_MULAI) . "'";
			$UPDATE_VAL['JAM_MULAI'] = "JAM_MULAI='" . db_escape($JAM_MULAI) . "'";

			$FIELDS['JAM_SELESAI'] = 'JAM_SELESAI';
			$INSERT_VAL['JAM_SELESAI'] = "'" . db_escape($JAM_SELESAI) . "'";
			$UPDATE_VAL['JAM_SELESAI'] = "JAM_SELESAI='" . db_escape($JAM_SELESAI) . "'";

			$INSERT_VAL['TGL_SELESAI'] = "'" . db_escape($TGL_MULAI) . "'";
			$UPDATE_VAL['TGL_SELESAI'] = "TGL_SELESAI='" . db_escape($TGL_MULAI) . "'";
			$TGL_SELESAI = $TGL_MULAI;
		}

		$CU = current_user();
		$TIME = date('Y-m-d H:i:s');
		$FIELDS[] = 'CREATED_BY';
		$INSERT_VAL['CREATED_BY'] = "'" . $CU->NAMA . "'";
		/*
		$FIELDS[] = 'CREATED_ON';
		$INSERT_VAL['CREATED_ON'] = "'" . $TIME . "'";
		*/
		$FIELDS[] = 'UPDATED_BY';
		$INSERT_VAL['UPDATED_BY'] = "'" . $CU->NAMA . "'";
		$UPDATE_VAL['UPDATED_BY'] = "UPDATED_BY='" . $CU->NAMA . "'";
		/*
		$FIELDS[] = 'UPDATED_ON';
		$INSERT_VAL['UPDATED_ON'] = "'" . $TIME . "'";
		$UPDATE_VAL['UPDATED_ON'] = "UPDATED_ON='" . $TIME . "'";
			
		if (get_input('STATUS') == 'APPROVED') {
			$FIELDS[] = 'APPROVED_BY';
			$INSERT_VAL['APPROVED_BY'] = "'" . $CU->NAMA . "'";
			$UPDATE_VAL['APPROVED_BY'] = "APPROVED_BY='" . $CU->NAMA . "'";
			$FIELDS[] = 'APPROVED_ON';
			$INSERT_VAL['APPROVED_ON'] = "'" . $TIME . "'";
			$UPDATE_VAL['APPROVED_ON'] = "APPROVED_ON='" . $TIME . "'";
		}
		*/

		if ($OP == '' or $OP == 'add') {
			is_login('eksepsi.add');
			db_execute(" INSERT INTO eksepsi (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			insert_eksepsi_detail($ID, $TGL_MULAI, $TGL_SELESAI);

			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		} else {
			db_execute(" UPDATE eksepsi SET " . implode(',', $UPDATE_VAL) . " WHERE EKSEPSI_ID='$ID' ");
			insert_eksepsi_detail($ID, $TGL_MULAI, $TGL_SELESAI);

			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		}
	}
}


if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

if (has_access('eksepsi.manual_scan')) {
	$JENIS_EKSEPSI = array(
		'SAKIT' => 'SAKIT',
		'IJIN' => 'IJIN',
		'IJIN_LE' => 'IJIN LATE/EARLY',
		'SKD' => 'SURAT KETERANGAN DOKTER',
		'CT' => 'CUTI TAHUNAN',
		'CI' => 'CUTI ISTIMEWA',
		/*
		'BACKUP' => 'BACKUP',
		'TO_IN' => 'TUKAR OFF (IN)',
		'TO_OUT' => 'TUKAR OFF (OUT)',
		'TS' => 'TUKAR SHIFT (SAME DAY)',
		'TS_IN' => 'TUKAR SHIFT (IN)',
		'TS_OUT' => 'TUKAR SHIFT (OUT)', 
		*/
		'R' => 'RESIGN',
		'BM' => 'BELUM MASUK',
		'CM' => 'CUTI MELAHIRKAN',
		'DINAS' => 'DINAS',
		'UL' => 'UNPAID LEAVE',
		'SM' => 'SCAN MANUAL',
	);
} else {
	$JENIS_EKSEPSI = array(
		'SAKIT' => 'SAKIT',
		'IJIN' => 'IJIN',
		'IJIN_LE' => 'IJIN LATE/EARLY',
		'SKD' => 'SURAT KETERANGAN DOKTER',
		'CT' => 'CUTI TAHUNAN',
		'CI' => 'CUTI ISTIMEWA',
		/*
		'BACKUP' => 'BACKUP',
		'TO_IN' => 'TUKAR OFF (IN)',
		'TO_OUT' => 'TUKAR OFF (OUT)',
		'TS' => 'TUKAR SHIFT (SAME DAY)',
		'TS_IN' => 'TUKAR SHIFT (IN)',
		'TS_OUT' => 'TUKAR SHIFT (OUT)',
		*/
		'R' => 'RESIGN',
		'BM' => 'BELUM MASUK',
		'CM' => 'CUTI MELAHIRKAN',
		'DINAS' => 'DINAS',
		'UL' => 'UNPAID LEAVE',
	);
}

$ST = array(
	'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
	'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
	'VOID' => '<span style="color:#ff0000;">VOID</span>',
);

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top:25px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Eksepsi
		<a href="eksepsi.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if ($OP == 'edit') {
			echo '<a href="eksepsi-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="eksepsi-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="proses" value="1">
		<input type="hidden" name="CURRENT_FILE" value="<?php echo $EDIT->FILE ?>">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<?php
		/*
		if(isset($EDIT->EKSEPSI_ID))
		{
			echo '<input type="hidden" name="PERIODE_ID" value="'.$EDIT->PERIODE_ID.'">';
			echo '<input type="hidden" name="KARYAWAN_ID" value="'.$EDIT->KARYAWAN_ID.'">';
		}
		*/
		?>

		<div class="row">
			<div class="col-md-5">

				<?php
				/*
				if(isset($EDIT->EKSEPSI_ID)){ ?>
				<?php
				$P = db_first(" SELECT PERIODE FROM periode WHERE PERIODE_ID='$EDIT->PERIODE_ID' ");
				$K = db_first(" SELECT NAMA FROM karyawan WHERE KARYAWAN_ID='$EDIT->KARYAWAN_ID' "); ?>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Periode</label>
					<div class="col-sm-8">
						<p class="form-control-static"><?php echo isset($P->PERIODE) ? $P->PERIODE : '' ?></p>
					</div>
				</div>
				<?php }else{
				*/
				?>

				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Periode</label>
					<div class="col-sm-8">
						<?php echo dropdown('PERIODE_ID', periode_option(), set_value('PERIODE_ID', $EDIT->PERIODE_ID), ' id="PERIODE_ID" class="form-control" ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Karyawan</label>
					<div class="col-sm-8">
						<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
							<?php
							$K = db_first(" SELECT KARYAWAN_ID,NIK,NAMA FROM karyawan WHERE KARYAWAN_ID='" . db_escape(set_value('KARYAWAN_ID', $EDIT->KARYAWAN_ID)) . "' ");
							if (isset($K->KARYAWAN_ID)) {
								echo '<option value="' . $K->KARYAWAN_ID . '" selected="selected">' . $K->NIK . ' - ' . $K->NAMA . '</option>';
							}
							?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Jenis</label>
					<div class="col-sm-8">
						<?php echo dropdown('JENIS', $JENIS_EKSEPSI, set_value('JENIS', $EDIT->JENIS), ' id="JENIS" class="form-control" ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Tgl Mulai</label>
					<div class="col-sm-8">
						<input type="text" name="TGL_MULAI" value="<?php echo set_value('TGL_MULAI', $EDIT->TGL_MULAI) ?>" class="form-control datepicker2" autocomplete="off">
					</div>
				</div>
				<div id="SH_TGL_SELESAI" class="form-group">
					<label for="" class="col-sm-4 control-label">Tgl Selesai</label>
					<div class="col-sm-8">
						<input type="text" name="TGL_SELESAI" value="<?php echo set_value('TGL_SELESAI', $EDIT->TGL_SELESAI) ?>" class="form-control datepicker2" autocomplete="off">
					</div>
				</div>
				<div id="SH_SHIFT_CODE" class="form-group" style="display:none;">
					<label for="" class="col-sm-4 control-label">Shift</label>
					<div class="col-sm-8">
						<select name="SHIFT_CODE" id="SHIFT_CODE" class="form-control">
							<?php
							$K = db_first(" SELECT SHIFT_CODE,START_TIME,FINISH_TIME FROM shift WHERE SHIFT_CODE='" . db_escape(set_value('SHIFT_CODE', $EDIT->SHIFT_CODE)) . "' ");
							if (isset($K->SHIFT_CODE)) {
								echo '<option value="' . $K->SHIFT_CODE . '" selected="selected">' . $K->SHIFT_CODE . ' (' . $K->START_TIME . '-' . $K->FINISH_TIME . ')</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div id="SH_TIME" class="form-group" style="display:none;">
					<label for="" class="col-sm-4 control-label">Jam</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" name="JAM_MULAI" value="<?php echo set_value('JAM_MULAI', $EDIT->JAM_MULAI) ?>" id="JAM_MULAI" class="form-control time">
							<div class="input-group-addon">to</div>
							<input type="text" name="JAM_SELESAI" value="<?php echo set_value('JAM_SELESAI', $EDIT->JAM_SELESAI) ?>" id="JAM_SELESAI" class="form-control time">
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">

				<?php /*if(isset($EDIT->STATUS) AND $EDIT->STATUS=='VOID'){ ?>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Status</label>
				<div class="col-sm-8">
					<p class="form-control-static"><?php echo isset($ST[$EDIT->STATUS]) ? $ST[$EDIT->STATUS] : '' ?></p>
				</div>
			</div>
			<?php }else{*/ ?>

				<?php if ($EDIT->SHIFT_CODE != "") { ?>
					<?php
					$SHIFT = array();
					$rs = db_fetch("
				SELECT *
				FROM shift
				WHERE SHIFT_CODE='$EDIT->SHIFT_CODE'
			");
					if (count($rs) > 0) {
						foreach ($rs as $row) {
							$SHIFT[$EDIT->KARYAWAN_ID][$EDIT->TGL_MULAI] = $row;
						}
					}
					$SCAN = simple_scan($EDIT->KARYAWAN_ID, $EDIT->TGL_MULAI, $EDIT->TGL_SELESAI, $SHIFT);
					$SC = isset($SCAN[$EDIT->TGL_MULAI]) ? $SCAN[$EDIT->TGL_MULAI] : '';

					$LOG = db_fetch("
				SELECT *
				FROM log_mesin
				WHERE PIN='$EDIT->KARYAWAN_ID' AND DATE(DATE)=DATE('$EDIT->TGL_MULAI')
			");

					?>
					<div class="row">
						<div class="col-sm-6">
							<div style="border:1px solid #ccc;margin-bottom:15px;">
								<div style="padding:6px 10px;">
									<h4 style="margin:0;">Actual Scan</h4>
									<table class="table table-condensed" style="width:100%;margin:0;">
										<thead>
											<tr>
												<th>Scan In</th>
												<th>Scan Out</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><?php echo isset($SC['scan_in']) ? $SC['scan_in'] : '' ?></td>
												<td><?php echo isset($SC['scan_out']) ? $SC['scan_out'] : '' ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div style="border:1px solid #ccc;margin-bottom:15px;">
								<div style="padding:6px 10px;">
									<h4 style="margin:0;">Log Scan</h4>
									<table class="table table-condensed" style="width:100%;margin:0;">
										<thead>
											<tr>
												<th>Scan Log</th>
											</tr>
										</thead>
										<tbody>
											<?php if (count($LOG) > 0) {
												foreach ($LOG as $log) { ?>
													<tr>
														<td><?php echo date('d F Y, H:i:s', strtotime($log->DATE)) ?></td>
													</tr>
											<?php }
											} ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Keterangan</label>
					<div class="col-sm-9">
						<input type="text" name="KETERANGAN" value="<?php echo set_value('KETERANGAN', $EDIT->KETERANGAN) ?>" class="form-control">
					</div>
				</div>

				<?php if (has_access('eksepsi.change_status')) { ?>
					<?php if ($EDIT->PROSES_APPROVED == 0) { ?>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">APPROVED 1</label>
							<div class="col-sm-9">
								<?php echo dropdown('PROSES_APPROVED_1', array('0' => 'REJECT', '1' => 'APPROVED'), set_value('PROSES_APPROVED', $EDIT->PROSES_APPROVED), ' class="form-control" ') ?>
							</div>
						</div>
					<?php } ?>

					<?php if ($EDIT->PROSES_APPROVED == 1) {
						echo '
									<div class="form-group">
										<label for="" class="col-sm-3 control-label">APPROVED 1</label>
										<div class="col-sm-9">
											APPROVED
										</div>
									</div>
									';

					?>


						<div class="form-group">
							<label for="" class="col-sm-3 control-label">APPROVED 2</label>
							<div class="col-sm-9">
								<?php echo dropdown('PROSES_APPROVED_2', array('0' => 'REJECT', '2' => 'APPROVED'), set_value('PROSES_APPROVED', $EDIT->PROSES_APPROVED), ' class="form-control" ') ?>
							</div>
						</div>
					<?php } ?>

				<?php if ($EDIT->PROSES_APPROVED == 2) {

						echo '
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">APPROVED 1</label>
						<div class="col-sm-9">
							APPROVED
						</div>
					</div>

					<div class="form-group">
						<label for="" class="col-sm-3 control-label">APPROVED 2</label>
						<div class="col-sm-9">
							APPROVED
						</div>
					</div>
								';
					}
				}
				?>

				<div class="form-group">
					<label for="" class="col-sm-3 control-label">File</label>
					<div class="col-sm-9">
						<?php if (!empty($EDIT->FILE) and url_exists(base_url() . 'uploads/skd/' . $EDIT->FILE)) { ?>
							<p><a href="<?php echo base_url() . 'uploads/skd/' . $EDIT->FILE; ?>" class="btn btn-sm btn-success" target="_blank">Download File</a></p>
						<?php } ?>
						<input type="file" name="FILE" class="form-control">
					</div>
				</div>

			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		$('input[name=EKSEPSI]').focus();
		$('input').keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$('#form').submit();
			}
		});
		$('#KARYAWAN_ID').select2({
			theme: "bootstrap",
			ajax: {
				url: 'karyawan-ac.php',
				dataType: 'json',
			}
		});

		$('.datepicker2').datepick({
			dateFormat: 'yyyy-mm-dd',
			monthsToShow: 2,
			monthsOffset: 1
		});

		load_date();
		$('#PERIODE_ID').change(function() {
			load_date();
		});

		jenis();
		$('#JENIS').change(function() {
			jenis();
		});
	});

	function jenis() {
		JENIS = $('#JENIS').val();
		$('#SH_TGL_SELESAI').hide();
		$('#SH_SHIFT_CODE').hide();
		$('#SH_TIME').hide();
		if (JENIS == 'TS' || JENIS == 'TO_IN' || JENIS == 'TS_IN') {
			$('#SH_SHIFT_CODE').show();
			$('#SH_TGL_SELESAI').hide();
			ac();
		} else if (JENIS == 'TO_OUT' || JENIS == 'TS_OUT') {
			$('#SH_SHIFT_CODE').hide();
			$('#SH_TGL_SELESAI').hide();
		} else if (JENIS == 'BACKUP') {
			$('#SH_SHIFT_CODE').show();
			$('#SH_TGL_SELESAI').hide();
			ac();
		} else if (JENIS == 'IJIN_LE') {
			$('#SH_TGL_SELESAI').hide();
			$('#SH_TIME').show();
			ac();
		} else {
			$('#SH_TGL_SELESAI').show();
			$('#SH_SHIFT_CODE').hide();
		}
	}

	function ac() {
		$('#SHIFT_CODE').select2({
			theme: "bootstrap",
			ajax: {
				url: 'shift-ac.php',
				dataType: 'json',
			}
		});
	}

	function load_date() {
		$.ajax({
			url: 'periode-ajax.php',
			data: {
				'PERIODE_ID': $('#PERIODE_ID').val()
			},
			dataType: 'json',
			method: 'POST',
			success: function(r) {
				$('.datepicker2').datepick('option', {
					minDate: r.TANGGAL_MULAI,
					maxDate: r.TANGGAL_SELESAI2
				});
			}
		});
	}
</script>

<?php
include 'footer.php';
?>