<?php
include 'app-load.php';

is_login('jadwal.import');

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$allow_ext = array('xls');
	$filename = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
	$tmp_name = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
	$ext = strtolower(substr(strrchr($filename, "."), 1));

	if (!is_uploaded_file($tmp_name)) {
		$ERROR[] = 'Tidak ada file yang diupload.';
	} else if (!in_array($ext, array('xls'))) {
		$ERROR[] = 'Ekstensi tidak diperbolehkan. Ekstensi yang dibolehkan xls.';
	} else {

		$_PROJECT_ID = get_input('PROJECT_ID');
		$MAPPER = array();
		if (!empty($_PROJECT_ID)) {
			$M = db_fetch(" SELECT * FROM shift_mapper WHERE PROJECT_ID='$_PROJECT_ID' ");
			if (count($M) > 0) {
				foreach ($M as $row) {
					$MAPPER[$row->VAR] = $row->VAL;
				}
			}
		}

		$PERIODE_ID = get_input('PERIODE_ID');
		$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
		$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
		$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;
		$RANGE = date_range($TGL_MULAI, $TGL_SELESAI);

		$rs = db_fetch(" SELECT * FROM shift ");
		$SHIFT = array();
		if (count($rs) > 0) {
			foreach ($rs as $row) {
				$SHIFT[$row->SHIFT_CODE] = $row;
			}
		}

		require 'lib/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($tmp_name);
		$baris = $data->rowcount($sheet_index = 0);

		$FIELDS = array(
			'PERIODE_ID', 'KARYAWAN_ID', 'PROJECT_ID', 'SHIFT_CODE', 'DATE'
		);

		foreach ($FIELDS as $F) {
			$COL[] = '`' . $F . '`';
		}

		$ROW = 6;
		$isegment = 1000;
		$segment = 0;
		if ($baris > 0) {
			for ($i = $ROW; $i <= $baris; $i++) {
				$NIK = db_escape($data->val($i, 3));

				$K = db_first(" SELECT KARYAWAN_ID,JABATAN_ID FROM karyawan WHERE NIK='$NIK' ");
				$KARYAWAN_ID = isset($K->KARYAWAN_ID) ? $K->KARYAWAN_ID : '';
				$JABATAN_ID = isset($K->JABATAN_ID) ? $K->JABATAN_ID : '';
				$J = db_first(" SELECT PROJECT_ID FROM jabatan WHERE JABATAN_ID='$JABATAN_ID' ");
				$PROJECT_ID = isset($J->PROJECT_ID) ? $J->PROJECT_ID : '';

				if (!empty($KARYAWAN_ID)) {
					$TMP_PROJECT_ID = $PROJECT_ID;

					$R = 0;
					for ($j = 5; $j <= 5 + 30; $j++) {
						$DATE = isset($RANGE[$R]) ? $RANGE[$R] : '';
						$SHIFT_CODE = db_escape($data->val($i, $j));

						if (count($MAPPER) > 0) {
							$SHIFT_CODE = isset($MAPPER[$SHIFT_CODE]) ? $MAPPER[$SHIFT_CODE] : strtoupper($SHIFT_CODE);
						}

						/*if( ! in_array($SHIFT_CODE,array('X','OFF')))
						{
							$SHIFT_CODE = '';
						}*/

						$d = array();
						foreach ($FIELDS as $F) {
							$VAL[$F] = "'" . ${$F} . "'";
						}

						if (!empty($SHIFT_CODE)) {
							$TMP[$segment][] = '(' . implode(',', $VAL) . ')';
							if ($i >= $isegment) {
								$isegment = $isegment + 1000;
								$segment = $segment + 1;
							}
						}
						$R++;
					}
				}
			}

			db_execute(" DELETE FROM shift_karyawan WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$TMP_PROJECT_ID' ");
			$TOTAL = 0;
			if (count($TMP) > 0) {
				foreach ($TMP as $tmp) {
					db_execute(" INSERT IGNORE shift_karyawan (" . implode(',', $COL) . ") VALUES " . implode(',', $tmp));
					$TOTAL = $TOTAL + $DB->Affected_Rows();
				}
			}
			$SUCCESS = $TOTAL . ' data berhasil di simpan.';
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
		<?php echo ucfirst($OP) ?> Import Jadwal
		<a href="jadwal.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="jadwal-import.php" method="POST" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Contoh Template</label>
					<div class="col-sm-8">
						<div class="form-control-static">
							<a href="static/tpl/TEMPLATE_JADWAL.xls" class="btn btn-sm btn-success">Download Template</a>
							<?php
							/*
							<p><b>Catatan Penting : </b></p>
							<p>Sebelum import jadwal, jika kode jadwal yang digunakan masih format R1, R2 dst kode jadwal harus di mapping perproyek di menu <a href="shift-mapper.php" target="_blank">Jabatan</a></p>
							*/
							?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Periode</label>
					<div class="col-sm-8">
						<?php echo dropdown('PERIODE_ID', periode_option(), set_value('PERIODE_ID', $EDIT->PERIODE_ID), ' class="form-control" ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">File</label>
					<div class="col-sm-8">
						<input type="file" name="file" class="form-control">
					</div>
				</div>
				<?php
				/*
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Mapper</label>
					<div class="col-sm-8">
						<?php
						$CU = current_user();
						$PROJECT_ID = isset($CU->PROJECT_ID) ? $CU->PROJECT_ID : '';
						$where = '';
						if (!empty($PROJECT_ID)) {
							$where = " WHERE PROJECT_ID='$PROJECT_ID' ";
						}
						echo dropdown('PROJECT_ID', dropdown_option_default('project', 'PROJECT_ID', 'PROJECT', $where . ' ORDER BY PROJECT ASC', '-- No Mapper --'), set_value('PROJECT_ID', $EDIT->PROJECT_ID), ' class="form-control" id="company"')
						?>
						<p>Mapper dipilih jika format kode jadwal masih reguler seperti R1, R2, R3 dst.</p>
					</div>
				</div>
				*/
				?>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		$('input[name=PERIODE_ID]').focus();
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