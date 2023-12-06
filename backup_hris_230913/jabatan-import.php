<?php
include 'app-load.php';

is_login('jabatan.import');

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

		require 'lib/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($tmp_name);
		$baris = $data->rowcount($sheet_index=0);

		$FIELDS = array(
			'PROJECT_ID', 'JABATAN', 'R_GAJI_POKOK', 'R_LEMBUR', 'R_CUTI', 'R_BPJS_JHT', 'R_BPJS_JP', 'R_BPJS_KES',
			'R_MEDICAL_CASH', 'R_TUNJ_BACKUP', 'R_TUNJ_TRANSPORT', 'R_TUNJ_MAKAN', 'R_TUNJ_KEAHLIAN', 'R_TUNJ_JABATAN', 'R_TUNJ_KOMUNIKASI',
			'R_TUNJ_KEHADIRAN', 'R_TUNJ_PROYEK', 'R_TUNJ_SHIFT', 'R_THR_PRORATA', 'R_THR', 'R_POT_ABSEN_GP', 'R_POT_ABSEN_CUTI',
			'R_POT_ABSEN_TUNJ_TRANSPORT', 'R_POT_ABSEN_TUNJ_MAKAN', 'R_POT_ABSEN_TUNJ_KEHADIRAN', 'R_POT_NSKD_GP', 'R_POT_NSKD_CUTI',
			'R_POT_NSKD_TUNJ_TRANSPORT', 'R_POT_NSKD_TUNJ_MAKAN', 'R_POT_NSKD_TUNJ_KEHADIRAN', 'R_POT_SKD_TUNJ_KEHADIRAN', 'R_POT_LATE_TUNJ_MAKAN',
			'R_POT_LATE_TUNJ_KEHADIRAN', 'R_POT_EARLY_TUNJ_KEHADIRAN', 'TUNJ_TRANSPORT', 'TUNJ_MAKAN', 'TUNJ_KOMUNIKASI'
		);
				
		foreach($FIELDS as $F){
			$COL[] = '`'.$F.'`';
		}
		
		$ROW = 6;
		$isegment = 1000;
		$segment = 0;
		if($baris>0)
		{
			for ($i=$ROW; $i<=$baris; $i++)
			{
				$JABATAN = db_escape($data->val($i, 3));
				$PROJECT_ID = db_escape($data->val($i, 4));
				$R_GAJI_POKOK = db_escape($data->val($i, 5));
				$R_LEMBUR = db_escape($data->val($i, 6));
				$R_CUTI = db_escape($data->val($i, 7));
				$R_BPJS_JHT = db_escape($data->val($i, 8));
				$R_BPJS_JP = db_escape($data->val($i, 9));
				$R_BPJS_KES = db_escape($data->val($i, 10));
				$R_MEDICAL_CASH = db_escape($data->val($i, 11));
				$R_TUNJ_BACKUP = db_escape($data->val($i, 12));
				$R_TUNJ_TRANSPORT = db_escape($data->val($i, 13));
				$R_TUNJ_MAKAN = db_escape($data->val($i, 14));
				$R_TUNJ_KEAHLIAN = db_escape($data->val($i, 15));
				$R_TUNJ_JABATAN = db_escape($data->val($i, 16));
				$R_TUNJ_KOMUNIKASI = db_escape($data->val($i, 17));
				$R_TUNJ_KEHADIRAN = db_escape($data->val($i, 18));
				$R_TUNJ_PROYEK = db_escape($data->val($i, 19));
				$R_TUNJ_SHIFT = db_escape($data->val($i, 20));
				$R_THR_PRORATA = db_escape($data->val($i, 21));
				$R_THR = db_escape($data->val($i, 22));
				$R_POT_ABSEN_GP = db_escape($data->val($i, 23));
				$R_POT_ABSEN_CUTI = db_escape($data->val($i, 24));
				$R_POT_ABSEN_TUNJ_TRANSPORT = db_escape($data->val($i, 25));
				$R_POT_ABSEN_TUNJ_MAKAN = db_escape($data->val($i, 26));
				$R_POT_ABSEN_TUNJ_KEHADIRAN = db_escape($data->val($i, 27));
				$R_POT_NSKD_GP = db_escape($data->val($i, 28));
				$R_POT_NSKD_CUTI = db_escape($data->val($i, 29));
				$R_POT_NSKD_TUNJ_TRANSPORT = db_escape($data->val($i, 30));
				$R_POT_NSKD_TUNJ_MAKAN = db_escape($data->val($i, 31));
				$R_POT_NSKD_TUNJ_KEHADIRAN = db_escape($data->val($i, 32));
				$R_POT_SKD_TUNJ_KEHADIRAN = db_escape($data->val($i, 33));
				$R_POT_LATE_TUNJ_MAKAN = db_escape($data->val($i, 34));
				$R_POT_LATE_TUNJ_KEHADIRAN = db_escape($data->val($i, 35));
				$R_POT_EARLY_TUNJ_KEHADIRAN = db_escape($data->val($i, 36));
				$TUNJ_TRANSPORT = db_escape($data->val($i, 37));
				$TUNJ_MAKAN = db_escape($data->val($i, 38));
				$TUNJ_KOMUNIKASI = db_escape($data->val($i, 39));

				$d = array();
				foreach($FIELDS as $F){
					$VAL[$F] = "'".${$F}."'";
				}

				if(!empty($JABATAN))
				{
					$TMP[$segment][] = '('.implode(',',$VAL).')';
					if($i >= $isegment){
						$isegment = $isegment + 1000;
						$segment = $segment + 1;
					}
				}
			}
			//print_r($TMP);
			
			$TOTAL = 0;
			if(count($TMP) > 0){ foreach($TMP as $tmp){
				db_execute(" INSERT IGNORE jabatan (".implode(',',$COL).") VALUES ".implode(',',$tmp) );
				//echo " INSERT IGNORE jabatan (".implode(',',$COL).") VALUES ".implode(',',$tmp);
				$TOTAL = $TOTAL + $DB->Affected_Rows();
			}}
			$SUCCESS = $TOTAL . ' data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:20px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Import Level Jabatan
		<a href="jabatan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="jabatan-import.php" method="POST" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Contoh Template</label>
					<div class="col-sm-8">
						<div class="form-control-static">
							<p><a href="static/tpl/TEMPLATE_JABATAN.xls" class="btn btn-sm btn-success">Download Template</a></p>
							<p><b>Catatan Penting : </b></p>
							<p>Format <b>PROJECT_ID</b> : ID bisa dilihat pada menu <b><a href="project.php" target="_blank">project</a></b></p>
							<p>Format <b>RULES</b> : <b>1 = Enable, 0 = Disable</b></p>
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
	$('input[name=PERIODE_ID]').focus();
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