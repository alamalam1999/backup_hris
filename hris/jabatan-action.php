<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');

if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {
	is_login('jabatan.edit');
	$EDIT = db_first(" SELECT * FROM jabatan WHERE JABATAN_ID='$ID' ");
}
if ($OP == 'delete') {
	is_login('jabatan.delete');
	db_execute(" DELETE FROM jabatan WHERE JABATAN_ID='$ID' ");
	header('location: jabatan.php');
	exit;
}

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('PROJECT_ID', 'JABATAN');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$JABATAN_ID = db_escape(get_input('JABATAN_ID'));
	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
	} else {

		$FIELDS = array(
			'PROJECT_ID', 'JABATAN', 'R_GAJI_POKOK', 'R_LEMBUR', 'R_CUTI', 'R_BPJS_JHT', 'R_BPJS_JP', 'R_BPJS_KES',
			'R_MEDICAL_CASH', 'R_TUNJ_TRANSPORT', 'R_TUNJ_MAKAN', 'R_TUNJ_KEAHLIAN', 'R_TUNJ_KOMUNIKASI', 'R_TUNJ_JABATAN', 'R_TUNJ_KEHADIRAN',
			'R_TUNJ_PROYEK', 'R_TUNJ_BACKUP', 'R_TUNJ_SHIFT', 'R_THR_PRORATA', 'R_THR',
			'R_POT_ABSEN_GP', 'R_POT_ABSEN_CUTI', 'R_POT_ABSEN_TUNJ_TRANSPORT', 'R_POT_ABSEN_TUNJ_MAKAN', 'R_POT_ABSEN_TUNJ_KEHADIRAN',
			'R_POT_NSKD_GP', 'R_POT_NSKD_CUTI', 'R_POT_NSKD_TUNJ_TRANSPORT', 'R_POT_NSKD_TUNJ_MAKAN', 'R_POT_NSKD_TUNJ_KEHADIRAN',
			'R_POT_SKD_TUNJ_KEHADIRAN',
			'R_POT_LATE_TUNJ_TRANSPORT', 'R_POT_LATE_TUNJ_MAKAN', 'R_POT_LATE_TUNJ_KEHADIRAN',
			'R_POT_EARLY_TUNJ_TRANSPORT', 'R_POT_EARLY_TUNJ_MAKAN', 'R_POT_EARLY_TUNJ_KEHADIRAN',
			'TUNJ_MAKAN', 'TUNJ_TRANSPORT', 'TUNJ_KOMUNIKASI', 'TUNJ_JABATAN', 'KELEBIHAN_JAM_AJAR', 'R_9JAM'
		);

		$d = array();
		foreach ($FIELDS as $F) {
			if (in_array($F, array('TUNJ_JABATAN', 'TUNJ_MAKAN', 'TUNJ_TRANSPORT', 'TUNJ_KOMUNIKASI', 'TUNJ_BACKUP', 'KELEBIHAN_JAM_AJAR', 'R_9JAM'))) {
				$INSERT_VAL[] = "'" . db_escape(input_currency(get_input($F))) . "'";
				$UPDATE_VAL[] = $F . "='" . db_escape(input_currency(get_input($F))) . "'";
			} else {
				$INSERT_VAL[] = "'" . db_escape(get_input($F)) . "'";
				$UPDATE_VAL[] = $F . "='" . db_escape(get_input($F)) . "'";
			}
		}

		if ($OP == '' or $OP == 'add') {
			is_login('jabatan.add');
			db_execute(" INSERT INTO jabatan (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		} else {
			db_execute(" UPDATE jabatan SET " . implode(',', $UPDATE_VAL) . " WHERE JABATAN_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
			$EDIT = db_first(" SELECT * FROM jabatan WHERE JABATAN_ID='$ID' ");
		}
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Jabatan
		<a href="jabatan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if ($OP == 'edit') {
			echo '<a href="jabatan-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="jabatan-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<div class="form-group">
			<label for="inputPassword" class="col-sm-2 control-label">Jabatan</label>
			<div class="col-sm-10">
				<input type="text" name="JABATAN" value="<?php echo set_value('JABATAN', $EDIT->JABATAN) ?>" class="form-control" style="height:30px;padding:5px 10px;">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Unit/Divisi</label>
			<div class="col-sm-10">
				<?php
				echo dropdown('PROJECT_ID', project_option_filter(0), set_value('PROJECT_ID', $EDIT->PROJECT_ID), ' class="form-control" id="company" style="height:30px;padding:5px 10px;" ')
				?>
			</div>
		</div>

		<hr>

		<h3>Nilai Tunjangan Tidak Tetap</h3>
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					<label for="" class="col-sm-6 control-label">Tunj. Transport</label>
					<div class="col-sm-6">
						<input type="text" name="TUNJ_TRANSPORT" value="<?php echo set_value('TUNJ_TRANSPORT', $EDIT->TUNJ_TRANSPORT) ?>" class="form-control currency" maxlength="20" style="height:30px;padding:5px 10px;">
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="" class="col-sm-6 control-label">Tunj. Makan</label>
					<div class="col-sm-6">
						<input type="text" name="TUNJ_MAKAN" value="<?php echo set_value('TUNJ_MAKAN', $EDIT->TUNJ_MAKAN) ?>" class="form-control currency" maxlength="20" style="height:30px;padding:5px 10px;">
					</div>
				</div>
			</div>
			<!-- <div class="col-sm-3">
				<div class="form-group">
					<label for="" class="col-sm-6 control-label">Tunj. Komunikasi</label>
					<div class="col-sm-6">
						<input type="text" name="TUNJ_KOMUNIKASI" value="<?php echo set_value('TUNJ_KOMUNIKASI', $EDIT->TUNJ_KOMUNIKASI) ?>" class="form-control currency" maxlength="20" style="height:30px;padding:5px 10px;">
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="" class="col-sm-6 control-label">Transportasi Dinas</label>
					<div class="col-sm-6">
						<input type="text" name="TRANSPORTASI_DINAS" value="<?php echo set_value('TRANSPORTASI_DINAS', $EDIT->TRANSPORTASI_DINAS) ?>" class="form-control currency" maxlength="20" style="height:30px;padding:5px 10px;">
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="" class="col-sm-6 control-label">Tunj. Jabatan</label>
					<div class="col-sm-6">
						<input type="text" name="TUNJ_JABATAN" value="<?php /* echo set_value('TUNJ_JABATAN', $EDIT->TUNJ_JABATAN) */ ?>" class="form-control currency" maxlength="20" style="height:30px;padding:5px 10px;">
					</div>
				</div> 
			</div>
			-->
		</div>

		<h3>Konfigurasi lainnya</h3>
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					<label for="" class="col-sm-6 control-label">Nilai Kelebihan Jam Ajar</label>
					<div class="col-sm-6">
						<input type="text" name="KELEBIHAN_JAM_AJAR" value="<?php echo set_value('KELEBIHAN_JAM_AJAR', $EDIT->KELEBIHAN_JAM_AJAR) ?>" class="form-control currency" maxlength="20" style="height:30px;padding:5px 10px;">
					</div>
				</div>
			</div>
		</div>

		<h3>Rules</h3>
		<div class="row">
			<div class="col-md-3">
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_GAJI_POKOK == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_GAJI_POKOK" value="1" <?php echo $ch ?>> Gaji Pokok
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_LEMBUR == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_LEMBUR" value="1" <?php echo $ch ?>> Hitung Lembur
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_CUTI == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_CUTI" value="1" <?php echo $ch ?>> Hitung Cuti (Berjalan)
					</label>
				</div>
				<!-- <div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_BPJS_JHT == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_BPJS_JHT" value="1" <?php echo $ch ?>> BPJS JHT
				</label>
				</div>
				<div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_BPJS_JP == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_BPJS_JP" value="1" <?php echo $ch ?>> BPJS JP
				</label>
				</div>
				<div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_BPJS_KES == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_BPJS_KES" value="1" <?php echo $ch ?>> BPJS KES
				</label>
				</div> -->
			</div>
			<div class="col-md-3">
				<!-- <div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_TUNJ_BACKUP == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_TUNJ_BACKUP" value="1" <?php echo $ch ?>> Backup
				</label>
				</div> -->
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_TUNJ_TRANSPORT == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_TUNJ_TRANSPORT" value="1" <?php echo $ch ?>> Tunjangan Transportasi
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_TUNJ_MAKAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_TUNJ_MAKAN" value="1" <?php echo $ch ?>> Tunjangan Makan
					</label>
				</div>
				<!-- <div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_TUNJ_KEAHLIAN == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_TUNJ_KEAHLIAN" value="1" <?php echo $ch ?>> Tunjangan Keahlian
				</label>
				</div> -->
				<!-- <div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_TUNJ_KOMUNIKASI == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_TUNJ_KOMUNIKASI" value="1" <?php echo $ch ?>> Tunjangan Komunikasi
					</label>
				</div> -->
				<!-- <div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_TUNJ_JABATAN == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_TUNJ_JABATAN" value="1" <?php echo $ch ?>> Tunjangan Jabatan
				</label>
				</div> -->
			</div>
			<div class="col-md-3">
				<!-- <div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_MEDICAL_CASH == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_MEDICAL_CASH" value="1" <?php echo $ch ?>> Medical Cash (Reimbursment)
				</label>
				</div> -->
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_TUNJ_KEHADIRAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_TUNJ_KEHADIRAN" value="1" <?php echo $ch ?>> Insentif Kehadiran/Penghargaan
					</label>
				</div>
				<!-- <div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_TUNJ_PROYEK == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_TUNJ_PROYEK" value="1" <?php echo $ch ?>> Tunjangan Proyek/Kinerja
				</label>
				</div>
				<div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_TUNJ_SHIFT == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_TUNJ_SHIFT" value="1" <?php echo $ch ?>> Tunjangan Shift
				</label>
				</div>
				
				<div class="checkbox">
				<label>
					<?php $ch = $EDIT->R_THR_PRORATA == '1' ? 'checked' : '' ?>
					<input type="checkbox" name="R_THR_PRORATA" value="1" <?php echo $ch ?>> THR Pro Rata
				</label>
				</div> -->
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_THR == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_THR" value="1" <?php echo $ch ?>> THR Full
					</label>
				</div>
				<!-- <div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_9JAM == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_9JAM" value="1" <?php echo $ch ?>> 9 JAM
					</label>
				</div> -->
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h3>Potongan jika absen</h3>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_ABSEN_GP == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_ABSEN_GP" value="1" <?php echo $ch ?>> Pemotongan GAJI POKOK dan Tunj Tetap (1/25)
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_ABSEN_CUTI == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_ABSEN_CUTI" value="1" <?php echo $ch ?>> Pemotongan Cuti Tahunan
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_ABSEN_TUNJ_TRANSPORT == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_ABSEN_TUNJ_TRANSPORT" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Transportasi
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_ABSEN_TUNJ_MAKAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_ABSEN_TUNJ_MAKAN" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Makan
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_ABSEN_TUNJ_KEHADIRAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_ABSEN_TUNJ_KEHADIRAN" value="1" <?php echo $ch ?>> Tidak dapat Insentif Kehadiran
					</label>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h3>Potongan jika terlambat</h3>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_LATE_TUNJ_TRANSPORT == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_LATE_TUNJ_TRANSPORT" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Transportasi
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_LATE_TUNJ_MAKAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_LATE_TUNJ_MAKAN" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Makan
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_LATE_TUNJ_KEHADIRAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_LATE_TUNJ_KEHADIRAN" value="1" <?php echo $ch ?>> Tidak dapat Insentif Kehadiran
					</label>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h3>Potongan jika Sakit tanpa SKD atau Ijin</h3>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_NSKD_GP == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_NSKD_GP" value="1" <?php echo $ch ?>> Pemotongan GAJI POKOK dan Tunj Tetap (1/25)
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_NSKD_CUTI == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_NSKD_CUTI" value="1" <?php echo $ch ?>> Pemotongan Cuti Tahunan
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_NSKD_TUNJ_TRANSPORT == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_NSKD_TUNJ_TRANSPORT" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Transportasi
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_NSKD_TUNJ_MAKAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_NSKD_TUNJ_MAKAN" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Makan
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_NSKD_TUNJ_KEHADIRAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_NSKD_TUNJ_KEHADIRAN" value="1" <?php echo $ch ?>> Tidak dapat Insentif Kehadiran
					</label>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h3>Potongan jika sakit dengan SKD</h3>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_SKD_TUNJ_KEHADIRAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_SKD_TUNJ_KEHADIRAN" value="1" <?php echo $ch ?>> Tidak dapat Insentif Kehadiran
					</label>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<h3>Potongan jika pulang awal</h3>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_EARLY_TUNJ_TRANSPORT == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_EARLY_TUNJ_TRANSPORT" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Transportasi
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_EARLY_TUNJ_MAKAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_EARLY_TUNJ_MAKAN" value="1" <?php echo $ch ?>> Tidak dapat Tunjangan Makan
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?php $ch = $EDIT->R_POT_EARLY_TUNJ_KEHADIRAN == '1' ? 'checked' : '' ?>
						<input type="checkbox" name="R_POT_EARLY_TUNJ_KEHADIRAN" value="1" <?php echo $ch ?>> Tidak dapat Insentif Kehadiran
					</label>
				</div>
			</div>
		</div>

	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		$('input[name=JABATAN]').focus();
		$('input').keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$('#form').submit();
			}
		});
	});
</script>

<?php
include 'footer.php';
?>