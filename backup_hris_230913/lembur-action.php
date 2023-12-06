<?php

include 'app-load.php';

is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {
	is_login('lembur.edit');
	$EDIT = db_first(" SELECT * FROM lembur WHERE LEMBUR_ID='$ID' ");
	if (isset($EDIT->STATUS) and $EDIT->STATUS == 'APPROVED') {
		header('location: lembur.php?m=approved');
		exit;
	}
	if (isset($EDIT->STATUS) and $EDIT->STATUS == 'VOID') {
		header('location: lembur.php?m=void');
		exit;
	}
}

if ($OP == 'delete') {
	is_login('lembur.delete');
	$REASON = db_escape(get_input('reason'));
	db_execute(" UPDATE lembur SET STATUS='VOID',KETERANGAN='Alasan Void : $REASON' WHERE LEMBUR_ID='$ID' ");
	header('location: lembur.php');
	exit;
}

if ($OP == 'approve') {
	is_login('lembur.change_status');
	$IDS = get_input('ids');
	if (is_array($IDS)) {
		$CU = current_user();
		$TIME = date('Y-m-d H:i:s');
		db_execute(" UPDATE lembur SET STATUS='APPROVED',UPDATED_BY='$CU->NAMA',UPDATED_ON='$TIME',APPROVED_BY='$CU->NAMA',APPROVED_ON='$TIME' WHERE LEMBUR_ID IN (" . implode(',', $IDS) . ")");
	}
	header('location: lembur.php');
	exit;
}

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('PERIODE_ID', 'KARYAWAN_ID', 'TANGGAL', 'JAM_MULAI', 'JAM_SELESAI', 'JENIS');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$LEMBUR_ID = db_escape(get_input('LEMBUR_ID'));
	$PERIODE_ID = get_input('PERIODE_ID');
	$KARYAWAN_ID = get_input('KARYAWAN_ID');

	$allow_ext = array('pdf', 'jpg', 'jpeg', 'png');
	$file_name = isset($_FILES['FILE']['name']) ? $_FILES['FILE']['name'] : '';
	$file_tmp = isset($_FILES['FILE']['tmp_name']) ? $_FILES['FILE']['tmp_name'] : '';
	$file_ext = strtolower(substr(strrchr($file_name, "."), 1));
	$file_new = rand(11111, 99999) . '_' . $file_name;
	$file_dest = 'uploads/skd/' . $file_new;

	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
	} else if ((!empty($file_name)) and !in_array($file_ext, $allow_ext)) {
		$ERROR[] = 'File yang diijinkan bertipe : ' . implode(', ', $allow_ext);
	} else {
		$FIELDS = array(
			'PERIODE_ID', 'KARYAWAN_ID', 'KETERANGAN', 'TANGGAL', 'JAM_MULAI', 'JAM_SELESAI', 'JENIS'
		);

		$NEW_FILE = 0;
		if (is_uploaded_file($file_tmp)) {
			if (move_uploaded_file($file_tmp, $file_dest)) {
				$FIELDS[] = 'FILE';
				$NEW_FILE = 1;
			}
		}

		$TANGGAL = get_input('TANGGAL');
		$JAM_MULAI = strtotime($TANGGAL . ' ' . get_input('JAM_MULAI'));
		$JAM_SELESAI = strtotime($TANGGAL . ' ' . get_input('JAM_SELESAI'));
		if ($JAM_MULAI <= $JAM_SELESAI) {
			$T = $JAM_SELESAI - $JAM_MULAI;
		} else {
			$p = explode('-', $TANGGAL);
			$tmp_y = $p[0];
			$tmp_m = $p[1];
			$tmp_d = $p[1];
			$TANGGAL2 = date('Y-m-d', mktime(0, 0, 0, $tmp_m, $tmp_d + 1, $tmp_y));
		}
		$TOTAL_JAM = round($T / 3600, 1);
		#$SISA_JAM = ($T % 3600);
		#$TOTAL_MENIT = round($SISA_JAM/60);
		#$TOTAL_JAM = $TOTAL_JAM.'.'.$TOTAL_MENIT
		#echo $TOTAL_JAM;die;

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
				$INSERT_VAL[] = "'" . db_escape(get_input($F)) . "'";
				$UPDATE_VAL[] = $F . "='" . db_escape(get_input($F)) . "'";
			}
		}

		$FIELDS[] = 'TOTAL_JAM';
		$INSERT_VAL[] = "'" . db_escape($TOTAL_JAM) . "'";
		$UPDATE_VAL[] = "TOTAL_JAM='" . db_escape($TOTAL_JAM) . "'";
		//print_r($TOTAL_JAM); die();
		
		$DATA_KARYAWAN = db_first(" SELECT GAJI_POKOK, TUNJ_KELUARGA FROM KARYAWAN WHERE KARYAWAN_ID='$KARYAWAN_ID' ");
		$GAJI_POKOK = $DATA_KARYAWAN->GAJI_POKOK;
		$TUNJ_KELUARGA = $DATA_KARYAWAN->TUNJ_KELUARGA;
		$JENIS = get_input('JENIS');
		
		if ($JENIS == 'LHK'){
			if($TOTAL_JAM <= 1){
				$UANG_LEMBUR = 1 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 1.5);
			}
			if($TOTAL_JAM > 1){
				$JAM_2 = $TOTAL_JAM - 1;
				$UANG_LEMBUR_1 = 1 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 1.5);
				$UANG_LEMBUR_2 = $JAM_2 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 2);
				$UANG_LEMBUR = $UANG_LEMBUR_1 + $UANG_LEMBUR_2;
			}
		}

		if ($JENIS == 'LHL'){
			if($TOTAL_JAM <= 7){
				$UANG_LEMBUR = $TOTAL_JAM *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 2);
				
				
			}
			if($TOTAL_JAM > 7 && $TOTAL_JAM <= 8){
				$UANG_LEMBUR_1 = 7 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 2);
				$JAM_2 = $TOTAL_JAM - 7;
				$UANG_LEMBUR_2 = $JAM_2 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 3);
				$UANG_LEMBUR = $UANG_LEMBUR_1 + $UANG_LEMBUR_2;
			}
			if($TOTAL_JAM > 8){
				$UANG_LEMBUR_1 = 7 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 2);
				$JAM_2 = $TOTAL_JAM - 7;
				$UANG_LEMBUR_2 = $JAM_2 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 3);
				$JAM_3 = $TOTAL_JAM - 8;
				$UANG_LEMBUR_3 = $JAM_3 *( (1/173 * ($GAJI_POKOK+$TUNJ_KELUARGA) ) * 4);

				$UANG_LEMBUR = $UANG_LEMBUR_1 + $UANG_LEMBUR_2 + $UANG_LEMBUR_3;
			}		
		}

		$FIELDS[] = 'UANG_LEMBUR';
		$INSERT_VAL[] = "'" . db_escape($UANG_LEMBUR) . "'";
		$UPDATE_VAL[] = "UANG_LEMBUR='" . db_escape($UANG_LEMBUR) . "'";
		
		$ADJUSMENT = get_input('ADJUSMENT');
		if (empty($ADJUSMENT)) {
			if ($JENIS == 'LHK') $L = hitung_lembur_LHK($TOTAL_JAM, '0');
			if ($JENIS == 'LHL') $L = hitung_lembur_LHL($TOTAL_JAM, '0');
			if ($JENIS == 'IHB') $L = hitung_lembur_IHB($TOTAL_JAM, '0');
			$ADJUSMENT = isset($L['ADJ']) ? $L['ADJ'] : 0;
			//print_r($ADJUSMENT); die();
		}

		$FIELDS[] = 'ADJUSMENT';
		$INSERT_VAL[] = "'" . db_escape($ADJUSMENT) . "'";
		$UPDATE_VAL[] = "ADJUSMENT='" . db_escape($ADJUSMENT) . "'";

		if (has_access('lembur.change_status')) {
			$STATUS = get_input('STATUS');
			$FIELDS[] = 'STATUS';
			$INSERT_VAL['STATUS'] = "'" . $STATUS . "'";
			$UPDATE_VAL['STATUS'] = "STATUS='" . $STATUS . "'";
		} else {
			$FIELDS[] = 'STATUS';
			$INSERT_VAL['STATUS'] = "'PENDING'";
		}

		$CU = current_user();
		$TIME = date('Y-m-d H:i:s');
		$FIELDS[] = 'CREATED_BY';
		$INSERT_VAL['CREATED_BY'] = "'" . $CU->NAMA . "'";
		$FIELDS[] = 'CREATED_ON';
		$INSERT_VAL['CREATED_ON'] = "'" . $TIME . "'";
		$FIELDS[] = 'UPDATED_BY';
		$INSERT_VAL['UPDATED_BY'] = "'" . $CU->NAMA . "'";
		$UPDATE_VAL['UPDATED_BY'] = "UPDATED_BY='" . $CU->NAMA . "'";
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

		if ($OP == '' or $OP == 'add') {
			is_login('lembur.add');
			db_execute(" INSERT INTO lembur (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();

			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		} else {
			db_execute(" UPDATE lembur SET " . implode(',', $UPDATE_VAL) . " WHERE LEMBUR_ID='$ID' ");

			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		}
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Lembur
		<a href="lembur.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if ($OP == 'edit') {
			echo '<a href="lembur-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="lembur-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<input type="hidden" name="CURRENT_FILE" value="<?php echo $EDIT->FILE ?>">
		<div class="row">
			<div class="col-md-5">
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
					<label for="" class="col-sm-4 control-label">Tanggal</label>
					<div class="col-sm-8">
						<input type="text" name="TANGGAL" value="<?php echo set_value('TANGGAL', $EDIT->TANGGAL) ?>" class="form-control datepicker2" id="TANGGAL" autocomplete="off">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Jenis</label>
					<div class="col-sm-8">
						<?php echo dropdown('JENIS', array('LHK' => 'LHK - Lembur Hari Kerja', 'LHL' => 'LHL - Lembur Hari Libur'), set_value('JENIS', $EDIT->JENIS), ' id="JENIS" class="form-control" ') ?>
						<!-- , 'IHB' => 'IHB - Insentif Hari Besar' -->
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Jam</label>
					<div class="col-sm-8">
						<div class="input-group">
							<input type="text" name="JAM_MULAI" value="<?php echo set_value('JAM_MULAI', $EDIT->JAM_MULAI) ?>" id="JAM_MULAI" class="form-control time">
							<div class="input-group-addon">to</div>
							<input type="text" name="JAM_SELESAI" value="<?php echo set_value('JAM_SELESAI', $EDIT->JAM_SELESAI) ?>" id="JAM_SELESAI" class="form-control time">
						</div>
					</div>
				</div>

				<?php if (has_access('lembur.change_adjusment')) { ?>
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Total Jam</label>
						<div class="col-sm-8">
							<input type="text" name="TOTAL_JAM" value="<?php echo set_value('TOTAL_JAM', $EDIT->TOTAL_JAM) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Adjusment</label>
						<div class="col-sm-8">
							<input type="text" name="ADJUSMENT" value="<?php echo set_value('ADJUSMENT', $EDIT->ADJUSMENT) ?>" class="form-control">
							<p style="margin-top:10px;font-size:11px;color:#ff0000;">Biarkan kosong atau isi angka 0 untuk dihitung oleh sistem</p>
						</div>
					</div>
				<?php } else { ?>
					<input type="hidden" name="TOTAL_JAM" value="<?php echo set_value('TOTAL_JAM', $EDIT->TOTAL_JAM) ?>">
					<input type="hidden" name="ADJUSMENT" value="<?php echo set_value('ADJUSMENT', $EDIT->ADJUSMENT) ?>">
				<?php } ?>

			</div>
			<div class="col-md-5">

				<?php
				$SHIFT = array();
				$SHIFT['START_TIME'] = isset($EDIT->JAM_MULAI) ? $EDIT->JAM_MULAI : '';
				$SHIFT['FINISH_TIME'] = isset($EDIT->JAM_SELESAI) ? $EDIT->JAM_SELESAI : '';

				$SCAN = simple_manual_scan($EDIT->KARYAWAN_ID, $EDIT->TANGGAL, $SHIFT);
				$SC = isset($SCAN[$EDIT->TANGGAL]) ? $SCAN[$EDIT->TANGGAL] : '';
				$OVERNIGHT = isset($SC['overnight']) ? $SC['overnight'] : '';

				$where = " AND DATE(DATE)=DATE('$EDIT->TANGGAL') ";
				if ($OVERNIGHT == '1') {
					$NEW_DATE = date('Y-m-d', strtotime($EDIT->TANGGAL . ' +1 day'));
					$where = " AND (DATE(DATE) BETWEEN '$EDIT->TANGGAL' AND '$NEW_DATE') ";
				}

				$LOG = db_fetch("
				SELECT *
				FROM log_mesin
				WHERE PIN='$EDIT->KARYAWAN_ID' $where
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

				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Keterangan</label>
					<div class="col-sm-9">
						<input type="text" name="KETERANGAN" value="<?php echo set_value('KETERANGAN', $EDIT->KETERANGAN) ?>" class="form-control">
					</div>
				</div>

				<?php if (has_access('lembur.change_status')) { ?>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Status</label>
						<div class="col-sm-9">
							<?php echo dropdown('STATUS', array('PENDING' => 'PENDING', 'APPROVED' => 'APPROVED'), set_value('STATUS', $EDIT->STATUS), ' class="form-control" ') ?>
						</div>
					</div>
				<?php } ?>

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
		$('input').keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$('#form').submit();
			}
		});

		$('.datepicker2').datepick({
			dateFormat: 'yyyy-mm-dd',
			monthsToShow: 2,
			monthsOffset: 1,
			onSelect: function(dates) {
				value = $('#JENIS').val();
				if (value == 'IHB') {
					load_shift();
				}
			}
		});

		load_date();
		$('#PERIODE_ID').change(function() {
			load_date();
		});

		$('#KARYAWAN_ID').select2({
			theme: "bootstrap",
			ajax: {
				url: 'karyawan-ac.php',
				dataType: 'json',
			}
		});

		$('#TANGGAL, #JENIS').change(function() {
			value = $(this).val();
			if (value == 'IHB') {
				load_shift();
			}
		});
	});

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
					maxDate: r.TANGGAL_SELESAI
				});
			}
		});
	}

	function load_shift() {
		$.ajax({
			url: 'shift-karyawan-ajax.php',
			data: {
				'KARYAWAN_ID': $('#KARYAWAN_ID').val(),
				'TANGGAL': $('#TANGGAL').val()
			},
			dataType: 'json',
			method: 'POST',
			success: function(r) {
				$('#JAM_MULAI').val(r.START_TIME);
				$('#JAM_SELESAI').val(r.FINISH_TIME);
				if (r.START_TIME == null || r.FINISH_TIME == null) {
					alert('Jadwal karyawan ini tidak ditemukan !!!');
				}
			}
		});
	}
</script>

<?php
include 'footer.php';
?>