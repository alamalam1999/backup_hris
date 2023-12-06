<?php
include 'app-load.php';
is_login();


if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$allow_ext = array('xls');
	$filename = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
	$tmp_name = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
	$ext = strtolower(substr(strrchr($filename, "."), 1));

	if( ! is_uploaded_file($tmp_name)){
		$ERROR[] = 'Tidak ada file yang diupload.';
	}else if( ! in_array($ext,array('xls')) ){
		$ERROR[] = 'Ekstensi tidak diperbolehkan. Ekstensi yang dibolehkan xls.';
	}else{
		$_PROJECT_ID = get_input('PROJECT_ID');
		
		$PERIODE_ID = get_input('PERIODE_ID');
		
		require 'lib/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($tmp_name);
		$baris = $data->rowcount($sheet_index=0);

		//print_r($baris); die();

		
		$TIME = date('Y-m-d H:i:s');
		$ROW = 5;
		$isegment = 1000;
		$segment = 0;
		if($baris>0)
		{
			$TOTAL = 0;

			for ($i=$ROW; $i<=$baris; $i++)
			{
				
				$NIK = db_escape($data->val($i, 2));
				$TOTAL_KELEBIHAN = db_escape($data->val($i, 4));
				$TANGGAL = date('Y-m-d');
				$STATUS = 'APPROVED';
				$KETERANGAN = 'IMPORT DARI EXCEL';
				$CU = current_user();
				$CREATED_BY = $CU->NAMA;
				$CREATED_ON = $TIME;
				// echo "<pre>";
				// print_r($NIK); die();
				$K = db_first(" SELECT K.KARYAWAN_ID, K.PROJECT_ID AS PROJECT_ID_K, J.KELEBIHAN_JAM_AJAR FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID WHERE K.NIK='$NIK' ");
				$KARYAWAN_ID = isset($K->KARYAWAN_ID) ? $K->KARYAWAN_ID : '';
				$PROJECT_ID = isset($K->PROJECT_ID_K) ? $K->PROJECT_ID_K : '';
				$R_KELEBIHAN = $K->KELEBIHAN_JAM_AJAR;

				$NILAI = $TOTAL_KELEBIHAN*$R_KELEBIHAN;

				if($_PROJECT_ID == $PROJECT_ID){
					db_execute(" DELETE FROM kelebihan_jamajar WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$_PROJECT_ID' AND KARYAWAN_ID = '$KARYAWAN_ID' ");
					$TOTAL++;
				
					db_execute(" INSERT INTO kelebihan_jamajar (KARYAWAN_ID,PERIODE_ID,PROJECT_ID,TANGGAL,TOTAL,NILAI,STATUS,KETERANGAN,CREATED_BY,CREATED_ON,RATE) 

								VALUES ('$KARYAWAN_ID','$PERIODE_ID','$_PROJECT_ID','$TANGGAL','$TOTAL_KELEBIHAN','$NILAI','$STATUS','$KETERANGAN','$CREATED_BY','$CREATED_ON','$R_KELEBIHAN') ");
				}
						
			}
				
				
		}

			
			

			

			

			

			$SUCCESS = $TOTAL . ' data berhasil di update';
			
		}
	}


if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Import Kelebihan Jam Ajar
		<a href="kelebihan-jamajar.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<button class="btn btn-sucess" onclick="$('#form2').submit()"><span class="glyphicon glyphicon-download"></span>&nbsp;&nbsp;Download</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form2" class="form-horizontal" action="export-kelebihan-jamajar.php" method="POST" enctype="multipart/form-data">

		<div class="row bg-info text-center p-3" style="margin-bottom:20px; padding: 5px;">
			<strong>Download Template</strong> 
		</div>
		<div class="row">
			<div class="col-md-7">
				
				<div class="form-group">
					<?php
						$PERIODE = db_fetch(" SELECT * FROM periode ");
						//print_r($PERIODE); die();
					 ?>
					<label for="" class="col-sm-4 control-label">Periode</label>
					<div class="col-sm-8">
						<select name="DOWN_PERIODE_ID" class="form-control">
							<?php
								foreach ($PERIODE as $key => $value) { ?>
									<option value="<?= $value->PERIODE_ID ?>"><?= $value->PERIODE ?></option>
								<?php }
							 ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<?php
						$UNIT = db_fetch(" SELECT * FROM project ");
						//print_r($PERIODE); die();
					 ?>
					<label for="" class="col-sm-4 control-label">Unit</label>
					<div class="col-sm-8">
						<select name="DOWN_PROJECT_ID" class="form-control">
							<?php
								foreach ($UNIT as $key => $value) { ?>
									<option value="<?= $value->PROJECT_ID ?>"><?= $value->PROJECT ?></option>
								<?php }
							 ?>
						</select>
					</div>
				</div>

				
			</div>
		</div>
	</form>

	<form id="form" class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
	
		<div class="row bg-info text-center p-3" style="margin-bottom:20px; padding: 5px;">
			<strong>Import Kelebihan Jam Ajar</strong> 
		</div>
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Catatan Penting</label>
					<div class="col-sm-8">
						<div class="form-control-static">

							<!-- <p><a href="static/tpl/TEMPLATE_JAMAJAR.xls" class="btn btn-success btn-sm">Download Template</a></p> -->
							<!-- <p><b>Catatan Penting : </b></p> -->
							<p>Pada kolom <b>PIN dan NIK</b> nilai harus disesuaikan dengan <b>PIN</b> yang ada pada <b>data karyawan</b></p>
							<p>Kolom <b>TOTAL</b> di isi jumlah kelebihan jam ajar</p>
							<p>Nilai / konversi uang akan terhitung otomatis oleh sistem</p>
							
							
						</div>
					</div>
				</div>
				<div class="form-group">
					<?php
						$PERIODE = db_fetch(" SELECT * FROM periode ");
						//print_r($PERIODE); die();
					 ?>
					<label for="" class="col-sm-4 control-label">Periode</label>
					<div class="col-sm-8">
						<select name="PERIODE_ID" class="form-control">
							<?php
								foreach ($PERIODE as $key => $value) { ?>
									<option value="<?= $value->PERIODE_ID ?>"><?= $value->PERIODE ?></option>
								<?php }
							 ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<?php
						$UNIT = db_fetch(" SELECT * FROM project ");
						//print_r($PERIODE); die();
					 ?>
					<label for="" class="col-sm-4 control-label">Unit</label>
					<div class="col-sm-8">
						<select name="PROJECT_ID" class="form-control">
							<?php
								foreach ($UNIT as $key => $value) { ?>
									<option value="<?= $value->PROJECT_ID ?>"><?= $value->PROJECT ?></option>
								<?php }
							 ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">File</label>
					<div class="col-sm-8">
						<input type="file" name="file" class="form-control">
					</div>
				</div>
				
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input').keypress(function (e) {
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