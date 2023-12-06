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

		$rs = db_fetch(" SELECT KARYAWAN_ID,PROJECT_ID FROM karyawan ");
		$PROJECT = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$PROJECT[$row->KARYAWAN_ID] = $row->PROJECT_ID;
			}
		}

		require 'lib/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($tmp_name);
		$baris = $data->rowcount($sheet_index=0);

		$FIELDS = array(
			'KARYAWAN_ID',
			'JENIS',
			'PERIODE_ID',
			'TANGGAL',
			'TOTAL',
			'KETERANGAN',
			'PROJECT_ID',			
			'STATUS',		
			'CREATED_ON',
			'CREATED_BY'
		);
		foreach($FIELDS as $F){
			$COL[] = '`'.$F.'`';
		}
		
		$ROW = 5;
		$isegment = 1000;
		$segment = 0;
		//$segment = 0;
		$TIME = date('Y-m-d H:i:s');
		$CU = current_user();
		if($baris>0)
		{
			for ($i=$ROW; $i<=$baris; $i++)
			{
				$KARYAWAN_ID = db_escape($data->val($i, 1));
				$JENIS = db_escape($data->val($i, 4));
				$PERIODE_ID = db_escape($data->val($i, 5));
				$TANGGAL = date('Y-m-d',strtotime(db_escape($data->val($i, 6))));
				$TOTAL = db_escape($data->val($i, 7));
				$KETERANGAN = db_escape($data->val($i, 8));
				$PROJECT_ID = isset($PROJECT[$KARYAWAN_ID]) ? $PROJECT[$KARYAWAN_ID] : '';
				$STATUS = 'PENDING';
				$CREATED_ON = $TIME;
				$CREATED_BY = $CU->NAMA;
			
				foreach($FIELDS as $F){
					$VAL[$F] = "'".${$F}."'";
				}
				if(!empty($KARYAWAN_ID) AND !empty($TOTAL))
				{
					$TMP[$segment][] = '('.implode(',',$VAL).')';
					if($i >= $isegment){
						$isegment = $isegment + 1000;
						$segment = $segment + 1;
					}
				}
				
			}

			$TOTAL = 0;
			if(count($TMP) > 0){ foreach($TMP as $tmp){
				db_execute(" INSERT IGNORE angsuran (".implode(',',$COL).") VALUES ".implode(',',$tmp) );
				//echo "INSERT IGNORE angsuran (".implode(',',$COL).") VALUES ".implode(',',$tmp).'<br /><br />';
				$TOTAL = $TOTAL + $DB->Affected_Rows();
			}}

			$SUCCESS = $TOTAL . ' data berhasil di update';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Import Angsuran
		<a href="angsuran.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Contoh Template</label>
					<div class="col-sm-9">
						<div class="form-control-static">
							<p><a href="static/tpl/TEMPLATE_ANGSURAN.xls" class="btn btn-success btn-sm">Download Template</a></p>
							<p><b>Catatan Penting : </b></p>
							<p>Pada kolom <b>PIN</b> nilai harus disesuaikan dengan <b>PIN</b> yang ada pada <b>mesin absensi</b></p>
							<p>Kolom <b>PERIODE ID</b> penulisannya harus disamakan dengan menu <a href="periode.php" target="_blank">Periode</a></p>
							<p>Kolom <b>JENIS ANGSURAN</b> penulisannya hanya bisa diisi dengan <b>PIHAK KETIGA</b> atau <b>PERUSAHAAN</b></p>
						</div>
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