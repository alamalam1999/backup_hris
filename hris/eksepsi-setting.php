<?php
include 'app-load.php';
is_login('eksepsi-setting.view');

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	is_login('eksepsi-setting.edit');
	$REQUIRE = array('C_SAKIT');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
	} else {

		set_option(array(
			'C_SAKIT' => get_input('C_SAKIT'),
			'C_IJIN' => get_input('C_IJIN'),
			'C_SKD' => get_input('C_SKD'),
			'C_BACKUP' => get_input('C_BACKUP'),
			'C_CT' => get_input('C_CT'),
			'C_CI' => get_input('C_CI'),
			'C_CM' => get_input('C_CM'),
			'C_TO' => get_input('C_TO'),
			'C_TS' => get_input('C_TS'),
			'C_R' => get_input('C_R'),
			'C_BM' => get_input('C_BM'),
			'C_LEMBUR' => get_input('C_LEMBUR'),
			'C_DINAS' => get_input('C_DINAS'),
			'C_UL' => get_input('C_UL'),
			'C_SM' => get_input('C_SM'),

			'F_SAKIT' => get_input('F_SAKIT'),
			'F_IJIN' => get_input('F_IJIN'),
			'F_SKD' => get_input('F_SKD'),
			'F_BACKUP' => get_input('F_BACKUP'),
			'F_CT' => get_input('F_CT'),
			'F_CI' => get_input('F_CI'),
			'F_CM' => get_input('F_CM'),
			'F_TO' => get_input('F_TO'),
			'F_TS' => get_input('F_TS'),
			'F_R' => get_input('F_R'),
			'F_BM' => get_input('F_BM'),
			'F_LEMBUR' => get_input('F_LEMBUR'),
			'F_DINAS' => get_input('F_DINAS'),
			'F_UL' => get_input('F_UL'),
			'F_SM' => get_input('F_SM'),
			'K_JAM_AJAR' => get_input('K_JAM_AJAR'),
			'N_LEMBUR' => get_input('N_LEMBUR'),
			'K_JKK' => get_input('K_JKK'),
			'K_JHT' => get_input('K_JHT'),
			'K_JKM' => get_input('K_JKM'),
			'K_BPJS' => get_input('K_BPJS'),
			'K_JP' => get_input('K_JP'),
			'P_JKK' => get_input('P_JKK'),
			'P_JHT' => get_input('P_JHT'),
			'P_JKM' => get_input('P_JKM'),
			'P_BPJS' => get_input('P_BPJS'),
			'P_JP' => get_input('P_JP'),
		));
		$SUCCESS = 'Data berhasil di simpan.';
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/spectrum/spectrum.js';
$CSS[] = 'static/spectrum/spectrum.css';
include 'header.php';
?>

<section class="container" style="margin-top:70px;">
	<h1 class="border-title">
		General Setting
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="eksepsi-setting.php" method="POST">
		<h3>Warna Eksepsi</h3>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Sakit</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_SAKIT" value="<?php echo set_value('C_SAKIT', get_option('C_SAKIT')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_SAKIT" value="<?php echo set_value('F_SAKIT', get_option('F_SAKIT')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Ijin</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_IJIN" value="<?php echo set_value('C_IJIN', get_option('C_IJIN')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_IJIN" value="<?php echo set_value('F_IJIN', get_option('F_IJIN')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">SKD</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_SKD" value="<?php echo set_value('C_SKD', get_option('C_SKD')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_SKD" value="<?php echo set_value('F_SKD', get_option('F_SKD')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Lembur</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_LEMBUR" value="<?php echo set_value('C_LEMBUR', get_option('C_LEMBUR')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_LEMBUR" value="<?php echo set_value('F_LEMBUR', get_option('F_LEMBUR')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<?php
				/*
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Backup</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_BACKUP" value="<?php echo set_value('C_BACKUP',get_option('C_BACKUP')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_BACKUP" value="<?php echo set_value('F_BACKUP',get_option('F_BACKUP')) ?>" class="form-control colorpicker">
					</div>
				</div>
				*/
				?>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Cuti Tahunan</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_CT" value="<?php echo set_value('C_CT', get_option('C_CT')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_CT" value="<?php echo set_value('F_CT', get_option('F_CT')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Cuti Istimewa</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_CI" value="<?php echo set_value('C_CI', get_option('C_CI')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_CI" value="<?php echo set_value('F_CI', get_option('F_CI')) ?>" class="form-control colorpicker">
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<?php
				/*
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Tukar Off</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_TO" value="<?php echo set_value('C_TO', get_option('C_TO')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_TO" value="<?php echo set_value('F_TO', get_option('F_TO')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Tukar Shift</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_TS" value="<?php echo set_value('C_TS', get_option('C_TS')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_TS" value="<?php echo set_value('F_TS', get_option('F_TS')) ?>" class="form-control colorpicker">
					</div>
				</div>
				*/
				?>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Resign</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_R" value="<?php echo set_value('C_R', get_option('C_R')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_R" value="<?php echo set_value('F_R', get_option('F_R')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Belum Masuk</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_BM" value="<?php echo set_value('C_BM', get_option('C_BM')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_BM" value="<?php echo set_value('F_BM', get_option('F_BM')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Dinas</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_DINAS" value="<?php echo set_value('C_DINAS', get_option('C_DINAS')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_DINAS" value="<?php echo set_value('F_DINAS', get_option('F_DINAS')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Cuti Melahirkan</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_CM" value="<?php echo set_value('C_CM', get_option('C_CM')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_CM" value="<?php echo set_value('F_CM', get_option('F_CM')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Unpaid Leave</label>
					<div class="col-sm-8">
						BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_UL" value="<?php echo set_value('C_UL', get_option('C_UL')) ?>" class="form-control colorpicker">
						&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_UL" value="<?php echo set_value('F_UL', get_option('F_UL')) ?>" class="form-control colorpicker">
					</div>
				</div>
				<?php if (has_access('eksepsi.manual_scan')) { ?>
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Scan Manual</label>
						<div class="col-sm-8">
							BG&nbsp;&nbsp;&nbsp;<input type="text" name="C_SM" value="<?php echo set_value('C_SM', get_option('C_SM')) ?>" class="form-control colorpicker">
							&nbsp;&nbsp;&nbsp;Font&nbsp;&nbsp;&nbsp;<input type="text" name="F_SM" value="<?php echo set_value('F_SM', get_option('F_SM')) ?>" class="form-control colorpicker">
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<h3>Perhitungan Nilai</h3>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Nilai Kelebihan Jam Ajar</label>
					<div class="col-sm-8">
						<input type="text" name="K_JAM_AJAR" value="<?php echo set_value('K_JAM_AJAR', get_option('K_JAM_AJAR')) ?>" class="form-control">
						
					</div>
				</div>
				
				<!-- <div class="form-group">
					<label for="" class="col-sm-4 control-label">Terlambat Masuk</label>
					<div class="col-sm-8">
						<input type="text" name="N_LEMBUR" value="<?php echo set_value('N_LEMBUR', get_option('N_LEMBUR')) ?>" class="form-control">
						
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Nilai Lembur</label>
					<div class="col-sm-8">
						<input type="text" name="N_LEMBUR" value="<?php echo set_value('N_LEMBUR', get_option('N_LEMBUR')) ?>" class="form-control">
						
					</div>
				</div> -->
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">JKK Karyawan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="K_JKK" value="<?php echo set_value('K_JKK', get_option('K_JKK')) ?>" class="form-control">
						
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">JHT Karyawan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="K_JHT" value="<?php echo set_value('K_JHT', get_option('K_JHT')) ?>" class="form-control">
						
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">JKM Karyawan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="K_JKM" value="<?php echo set_value('K_JKM', get_option('K_JKM')) ?>" class="form-control">
						
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">BPJS Kes. Karyawan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="K_BPJS" value="<?php echo set_value('K_BPJS', get_option('K_BPJS')) ?>" class="form-control">
						
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">BPJS JP Karyawan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="K_JP" value="<?php echo set_value('K_JP', get_option('K_JP')) ?>" class="form-control">
						
					</div>
				</div>

			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Nilai Lembur</label>
					<div class="col-sm-8">
						<input type="text" name="N_LEMBUR" value="<?php echo set_value('N_LEMBUR', get_option('N_LEMBUR')) ?>" class="form-control">
						
					</div>
				</div>
				
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">JKK Perusahaan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="P_JKK" value="<?php echo set_value('P_JKK', get_option('P_JKK')) ?>" class="form-control">
						
					</div>
				</div>
				
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">JHT Perusahaan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="P_JHT" value="<?php echo set_value('P_JHT', get_option('P_JHT')) ?>" class="form-control">
						
					</div>
				</div>
				
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">JKM Perusahaan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="P_JKM" value="<?php echo set_value('P_JKM', get_option('P_JKM')) ?>" class="form-control">
						
					</div>
				</div>
				
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">BPJS Kes. Perusahaan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="P_BPJS" value="<?php echo set_value('P_BPJS', get_option('P_BPJS')) ?>" class="form-control">
						
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">BPJS JP Perusahaan(%) - isi nilai saja</label>
					<div class="col-sm-8">
						<input type="text" name="P_JP" value="<?php echo set_value('P_JP', get_option('P_JP')) ?>" class="form-control">
						
					</div>
				</div>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		$('input').keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$('#form').submit();
			}
		});
		$(".colorpicker").spectrum({
			//color: "#ECC",
			showInput: true,
			className: "full-spectrum",
			showInitial: true,
			showPalette: true,
			showSelectionPalette: true,
			maxSelectionSize: 10,
			preferredFormat: "hex",
			//localStorageKey: "spectrum.demo",
			move: function(color) {

			},
			show: function() {

			},
			beforeShow: function() {

			},
			hide: function() {

			},
			change: function() {

			},
			palette: [
				["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
					"rgb(204, 204, 204)", "rgb(217, 217, 217)", "rgb(255, 255, 255)"
				],
				["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
					"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"
				],
				["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
					"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
					"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
					"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
					"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
					"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
					"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
					"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
					"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
					"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"
				]
			]
		});
	});
</script>

<?php
include 'footer.php';
?>