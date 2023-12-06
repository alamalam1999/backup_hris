<?php

include 'app-load.php';

is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {
	is_login('pinjaman.edit');
	$EDIT = db_first(" SELECT * FROM pinjaman_master WHERE PINJAMAN_MASTER_ID='$ID' ");
	if (isset($EDIT->STATUS) and $EDIT->STATUS == 'APPROVED') {
		header('location: pinjaman.php?m=approved');
		exit;
	}
	if (isset($EDIT->STATUS) and $EDIT->STATUS == 'VOID') {
		header('location: pinjaman.php?m=void');
		exit;
	}
}

if ($OP == 'approve') {
	is_login('pinjaman.change_status');
	$IDS = get_input('ids');
	if (is_array($IDS)) {
		$CU = current_user();
		$TIME = date('Y-m-d H:i:s');
		db_execute(" UPDATE pinjaman_master SET STATUS='APPROVED',UPDATED_BY='$CU->NAMA',UPDATED_ON='$TIME',APPROVED_BY='$CU->NAMA',APPROVED_ON='$TIME' WHERE PINJAMAN_MASTER_ID IN (" . implode(',', $IDS) . ")");
		db_execute(" UPDATE pinjaman_angsuran SET STATUS_PINJAMAN='APPROVED' WHERE PINJAMAN_MASTER_ID IN (" . implode(',', $IDS) . ")");
	}
	header('location: pinjaman.php');
	exit;
}

//void pinjaman
if ($OP == 'delete') {
	is_login('pinjaman.delete');
	$REASON = db_escape(get_input('reason'));
	db_execute(" UPDATE pinjaman_master SET STATUS='VOID',KETERANGAN='Alasan Void : $REASON' WHERE PINJAMAN_MASTER_ID='$ID' ");
	db_execute(" UPDATE angsuran_master SET STATUS='VOID' WHERE PINJAMAN_MASTER_ID='$ID' ");
	header('location: pinjaman.php');
	exit;
}

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('KARYAWAN_ID', 'GRAND_TOTAL');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
	} else {
		$FIELDS = array(
			'PERIODE_MULAI', 'PERIODE_SELESAI', 'KARYAWAN_ID', 'KETERANGAN'
		);

		$d = array();
		foreach ($FIELDS as $F) {
			$INSERT_VAL[$F] = "'" . db_escape(get_input($F)) . "'";
			$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
		}

		if (has_access('pinjaman.change_status')) {
			$STATUS = get_input('STATUS');
			$FIELDS[] = 'STATUS';
			$INSERT_VAL['STATUS'] = "'" . $STATUS . "'";
			$UPDATE_VAL['STATUS'] = "STATUS='" . $STATUS . "'";
		} else {
			$FIELDS[] = 'STATUS';
			$INSERT_VAL['STATUS'] = "'PENDING'";
		}

		$KARYAWAN_ID = get_input('KARYAWAN_ID');
		$rs = db_first(" SELECT J.PROJECT_ID FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID WHERE KARYAWAN_ID='$KARYAWAN_ID'  ");
		$PROJECT_ID = isset($rs->PROJECT_ID) ? $rs->PROJECT_ID : '';
		$FIELDS['PROJECT_ID'] = 'PROJECT_ID';
		$INSERT_VAL['PROJECT_ID'] = "'" . $PROJECT_ID . "'";
		$UPDATE_VAL['PROJECT_ID'] = "PROJECT_ID='" . $PROJECT_ID . "'";

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

		$GRAND_TOTAL = get_input('GRAND_TOTAL');
		$FIELDS[] = 'GRAND_TOTAL';
		$INSERT_VAL[] = "'" . input_currency($GRAND_TOTAL) . "'";
		$UPDATE_VAL[] = "GRAND_TOTAL='" . input_currency($GRAND_TOTAL) . "'";

		$ANGSURAN = get_input('ANGSURAN');
		$FIELDS[] = 'ANGSURAN';
		$INSERT_VAL[] = "'" . input_currency($ANGSURAN) . "'";
		$UPDATE_VAL[] = "ANGSURAN='" . input_currency($ANGSURAN) . "'";

		$JENIS_POTONGAN = get_input('JENIS_POTONGAN');
		$FIELDS[] = 'JENIS_POTONGAN';
		$INSERT_VAL[] = "'" . input_currency($JENIS_POTONGAN) . "'";
		$UPDATE_VAL[] = "JENIS_POTONGAN='" . input_currency($JENIS_POTONGAN) . "'";



		if ($OP == '' or $OP == 'add') {
			is_login('pinjaman.add');

			db_execute(" INSERT INTO pinjaman_master (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();

			$periode1 = get_input('PERIODE_MULAI');
			$periode2 = get_input('PERIODE_SELESAI');
			$STATUS = get_input('STATUS');

			$PERIODE1 = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$periode1' ");
			$PERIODE2 = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$periode2' ");

			$date1 = $PERIODE1->TANGGAL_SELESAI;
			$date2 = $PERIODE2->TANGGAL_SELESAI;

			$PERIODE = db_fetch(" SELECT PERIODE_ID FROM periode WHERE TANGGAL_SELESAI >= '$date1' and TANGGAL_SELESAI <= '$date2'");
			//echo count($PERIODE); die();
			if (count($PERIODE) > 0) {
				foreach ($PERIODE as $key => $value) {
					$PINJAMAN_MASTER_ID = $ID;
					$PERIODE_ID = $value->PERIODE_ID;
					$KARYAWAN_ID = $KARYAWAN_ID;
					$PROJECT_ID = $PROJECT_ID;
					$STATUS_PINJAMAN = $STATUS;
					$ANGSURAN = input_currency(get_input('ANGSURAN'));
					$PINJAMAN_ANGSURAN .= "('" . $PINJAMAN_MASTER_ID . "','" . $PERIODE_ID . "','" . $KARYAWAN_ID . "','" . $PROJECT_ID . "','" . $ANGSURAN . "','" . $STATUS_PINJAMAN . "','" . $JENIS_POTONGAN . "'),";
				}
				$PINJAMAN_ANGSURAN = rtrim($PINJAMAN_ANGSURAN, ',');
				if (!empty($PINJAMAN_ANGSURAN)) {
					db_execute(" INSERT INTO pinjaman_angsuran (PINJAMAN_MASTER_ID,PERIODE_ID,KARYAWAN_ID,PROJECT_ID,TOTAL,STATUS_PINJAMAN) VALUES $PINJAMAN_ANGSURAN ");
				}
			}

			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		} else {
			db_execute(" UPDATE pinjaman_master SET " . implode(',', $UPDATE_VAL) . " WHERE PINJAMAN_MASTER_ID='$ID' ");

			$periode1 = get_input('PERIODE_MULAI');
			$periode2 = get_input('PERIODE_SELESAI');
			$STATUS = get_input('STATUS');

			$PERIODE1 = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$periode1' ");
			$PERIODE2 = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$periode2' ");

			$date1 = $PERIODE1->TANGGAL_SELESAI;
			$date2 = $PERIODE2->TANGGAL_SELESAI;

			$PERIODE = db_fetch(" SELECT PERIODE_ID FROM periode WHERE TANGGAL_SELESAI >= '$date1' and TANGGAL_SELESAI <= '$date2'");
			//echo count($PERIODE); die();
			if (count($PERIODE) > 0) {
				foreach ($PERIODE as $key => $value) {
					$PINJAMAN_MASTER_ID = $ID;
					$PERIODE_ID = $value->PERIODE_ID;
					$KARYAWAN_ID = $KARYAWAN_ID;
					$PROJECT_ID = $PROJECT_ID;
					$STATUS_PINJAMAN = $STATUS;
					$JENIS_POTONGAN = get_input('JENIS_POTONGAN');
					$ANGSURAN = input_currency(get_input('ANGSURAN'));
					$PINJAMAN_ANGSURAN .= "('" . $PINJAMAN_MASTER_ID . "','" . $PERIODE_ID . "','" . $KARYAWAN_ID . "','" . $PROJECT_ID . "','" . $ANGSURAN . "','" . $STATUS_PINJAMAN . "','" . $JENIS_POTONGAN . "'),";

					db_execute(" DELETE FROM pinjaman_angsuran WHERE PINJAMAN_MASTER_ID = '$PINJAMAN_MASTER_ID' ");
				}

				$PINJAMAN_ANGSURAN = rtrim($PINJAMAN_ANGSURAN, ',');
				if (!empty($PINJAMAN_ANGSURAN)) {
					db_execute(" INSERT INTO pinjaman_angsuran (PINJAMAN_MASTER_ID,PERIODE_ID,KARYAWAN_ID,PROJECT_ID,TOTAL,STATUS_PINJAMAN,JENIS_POTONGAN) VALUES $PINJAMAN_ANGSURAN ");
				}
			}

			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		}
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Pinjaman
		<a href="pinjaman.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if ($OP == 'edit') {
			echo '<a href="pinjaman-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="pinjaman-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<div class="row">
			<div class="col-md-5">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Karyawan</label>
					<div class="col-sm-8">
						<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
							<?php
							$K = db_first(" SELECT KARYAWAN_ID, NIK, NAMA FROM karyawan WHERE KARYAWAN_ID='" . db_escape(set_value('KARYAWAN_ID', $EDIT->KARYAWAN_ID)) . "' ");
							if (isset($K->KARYAWAN_ID)) {
								echo '<option value="' . $K->KARYAWAN_ID . '" selected="selected">' . $K->NIK . ' - ' . $K->NAMA . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Periode Mulai</label>
					<div class="col-sm-8">
						<?php echo dropdown('PERIODE_MULAI', periode_option(), set_value('PERIODE_MULAI', $EDIT->PERIODE_MULAI), ' id="PERIODE_MULAI" class="form-control" ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Periode Selesai</label>
					<div class="col-sm-8">
						<?php echo dropdown('PERIODE_SELESAI', periode_option(), set_value('PERIODE_SELESAI', $EDIT->PERIODE_SELESAI), ' id="PERIODE_SELESAI" class="form-control" ') ?>
					</div>
				</div>



				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Total Peminjaman</label>
					<div class="col-sm-8">
						<input type="text" name="GRAND_TOTAL" id="GRAND_TOTAL" value="<?php echo set_value('GRAND_TOTAL', $EDIT->GRAND_TOTAL) ?>" class="form-control currency">
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Angsuran Perbulan</label>
					<div class="col-sm-8">
						<input type="text" name="ANGSURAN" id="ANGSURAN" value="<?php echo set_value('ANGSURAN', $EDIT->ANGSURAN) ?>" class="form-control currency" readonly>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">JENIS PINJAMAN/POTONGAN</label>
					<div class="col-sm-9">
						<select name="JENIS_POTONGAN" class="form-control">
							<option value="PINJAMAN KOPERASI DINATERA" <?php if ($EDIT->JENIS_POTONGAN == 'PINJAMAN KOPERASI DINATERA') echo 'selected'; ?>>PINJAMAN KOPERASI DINATERA</option>
							<option value="IURAN KOPERASI DINATERA" <?php if ($EDIT->JENIS_POTONGAN == 'IURAN KOPERASI DINATERA') echo 'selected'; ?>>IURAN KOPERASI DINATERA</option>
							<option value="IURAN KOPERASI AVICENNA" <?php if ($EDIT->JENIS_POTONGAN == 'IURAN KOPERASI AVICENNA') echo 'selected'; ?>>IURAN KOPERASI AVICENNA</option>
							<option value="PINJAMAN KOPERASI AVICENNA" <?php if ($EDIT->JENIS_POTONGAN == 'PINJAMAN KOPERASI AVICENNA') echo 'selected'; ?>>PINJAMAN KOPERASI AVICENNA</option>
							<option value="PINJAMAN BANK BWS" <?php if ($EDIT->JENIS_POTONGAN == 'PINJAMAN BANK BWS') echo 'selected'; ?>>PINJAMAN BANK BWS</option>
							<option value="EKSES KLAIM" <?php if ($EDIT->JENIS_POTONGAN == 'EKSES KLAIM') echo 'selected'; ?>>EKSES KLAIM</option>
							<option value="BIAYA PEND. ANAK" <?php if ($EDIT->JENIS_POTONGAN == 'BIAYA PEND. ANAK') echo 'selected'; ?>>BIAYA PEND. ANAK</option>
							<option value="BIAYA LAPTOP" <?php if ($EDIT->JENIS_POTONGAN == 'BIAYA LAPTOP') echo 'selected'; ?>>BIAYA LAPTOP</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Keterangan</label>
					<div class="col-sm-9">
						<input type="text" name="KETERANGAN" value="<?php echo set_value('KETERANGAN', $EDIT->KETERANGAN) ?>" class="form-control">
					</div>
				</div>
				<?php if (has_access('pinjaman.change_status')) { ?>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Status</label>
						<div class="col-sm-9">
							<?php echo dropdown('STATUS', array('PENDING' => 'PENDING', 'APPROVED' => 'APPROVED'), set_value('STATUS', $EDIT->STATUS), ' class="form-control" ') ?>
						</div>
					</div>
				<?php } ?>

			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		//$('input[name=PINJAMAN]').focus();
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

		$('#GRAND_TOTAL').keyup(function() {
			var GRAND_TOTAL = $('#GRAND_TOTAL').cleanVal();
			var PERIODE1 = $('#PERIODE_MULAI').val();
			var PERIODE2 = $('#PERIODE_SELESAI').val();

			$.ajax({
				type: "GET",
				url: "ajax-pinjaman.php?periode1=" + PERIODE1 + "&periode2=" + PERIODE2,
				success: function(data) {
					var count = data;
					var ANGSURAN = Math.ceil(GRAND_TOTAL / count);
					$('#ANGSURAN').val(ANGSURAN);
					$('#ANGSURAN').mask('000,000,000,000,000', {
						reverse: true
					});
				}
			});
		});

		$('#PERIODE_MULAI').change(function() {
			var GRAND_TOTAL = $('#GRAND_TOTAL').cleanVal();
			var PERIODE1 = $('#PERIODE_MULAI').val();
			var PERIODE2 = $('#PERIODE_SELESAI').val();

			$.ajax({
				type: "GET",
				url: "ajax-pinjaman.php?periode1=" + PERIODE1 + "&periode2=" + PERIODE2,
				success: function(data) {
					var count = data;
					var ANGSURAN = Math.ceil(GRAND_TOTAL / count);
					$('#ANGSURAN').val(ANGSURAN);
					$('#ANGSURAN').mask('000,000,000,000,000', {
						reverse: true
					});
				}
			});
		});

		$('#PERIODE_SELESAI').change(function() {
			var GRAND_TOTAL = $('#GRAND_TOTAL').cleanVal();
			var PERIODE1 = $('#PERIODE_MULAI').val();
			var PERIODE2 = $('#PERIODE_SELESAI').val();

			$.ajax({
				type: "GET",
				url: "ajax-pinjaman.php?periode1=" + PERIODE1 + "&periode2=" + PERIODE2,
				success: function(data) {
					var count = data;
					var ANGSURAN = Math.ceil(GRAND_TOTAL / count);
					$('#ANGSURAN').val(ANGSURAN);
					$('#ANGSURAN').mask('000,000,000,000,000', {
						reverse: true
					});
				}
			});
		});

		/*
		$('.datepicker2').datepick({
			dateFormat: 'yyyy-mm-dd',
			monthsToShow: 2,
			monthsOffset:1
		});
		
		load_date();
		$('#PERIODE_ID').change(function(){
			load_date();
		});
		*/
	});
	/*
	function load_date()
	{
		$.ajax({
			url: 'periode-ajax.php',
			data: { 'PERIODE_ID' : $('#PERIODE_ID').val() },
			dataType: 'json',
			method: 'POST',
			success: function(r){
				$('.datepicker2').datepick('option', {
					minDate: r.TANGGAL_MULAI,
					maxDate: r.TANGGAL_SELESAI
				});
			}
		});
	}
	*/
</script>

<?php
include 'footer.php';
?>