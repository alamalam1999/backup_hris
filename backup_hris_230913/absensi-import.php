<?php

include 'app-load.php';

is_login('absensi.import');

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
		
		$PERIODE_ID = get_input('PERIODE_ID');
		$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
		$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
		$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;
		$RANGE = date_range($TGL_MULAI,$TGL_SELESAI);
		
		require 'lib/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($tmp_name);
		$baris = $data->rowcount($sheet_index=0);

		$FIELDS = array(
			'PIN','DATE'
		);
				
		foreach($FIELDS as $F){
			$COL[] = '`'.$F.'`';
		}
		
		$ROW = 9;
		$isegment = 1000;
		$segment = 0;
		$CURRENT_PIN = '';
		$SCAN_OUT_TRIGGER = 0;
		$T = 0;
		$TMP = array();
		if($baris>0)
		{
			for ($i=$ROW; $i<=$baris; $i++)
			{
				$NIK = db_escape($data->val($i, 2));
				
				$K = db_first(" SELECT KARYAWAN_ID FROM karyawan WHERE NIK='$NIK' ");
				$PIN = isset($K->KARYAWAN_ID) ? $K->KARYAWAN_ID : '';
				
				if($SCAN_OUT_TRIGGER==1){
					$PIN = $CURRENT_PIN;
				}
				
				if(!empty($PIN))
				{
					if($SCAN_OUT_TRIGGER==0){
						$CURRENT_PIN = $PIN;
					}
					$SCAN_OUT_TRIGGER++;

					$D = 0;
					for($j=5; $j<=5+30; $j++)
					{
						$TIME = db_escape($data->val($i, $j));

						/*if(valid_date($TIME)){
							echo $TIME.' == ';
							echo $DATE.' == '.date('Y-m-d H:i:s',strtotime($TIME));
							echo '<br>';
						}*/
						
						if(preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $TIME) OR valid_date($TIME))
						{
							$DATE = isset($RANGE[$D]) ? $RANGE[$D].' '.$TIME : '';
							
							if(valid_date($TIME)){
								$DATE = date('Y-m-d H:i:s',strtotime($TIME));
							}

							foreach($FIELDS as $F){
								$VAL[$F] = "'".${$F}."'";
							}

							$TMP[$segment][] = '('.implode(',',$VAL).')';
							if($T >= $isegment){
								$isegment = $isegment + 1000;
								$segment = $segment + 1;
							}
							$T++;
						}
						$D++;
					}
				}
				if($SCAN_OUT_TRIGGER==2){
					$SCAN_OUT_TRIGGER = 0;
				}
			}

			$TOTAL = 0;
			if(count($TMP) > 0){ foreach($TMP as $tmp){
				db_execute(" INSERT IGNORE log_mesin (".implode(',',$COL).") VALUES ".implode(',',$tmp) );
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

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Import Absensi
		<a href="absensi.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="absensi-import.php" method="POST" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Contoh Template</label>
					<div class="col-sm-8">
						<div class="form-control-static">
							<p><a href="static/tpl/TEMPLATE_ABSENSI.xls" class="btn btn-sm btn-success">Download Template</a></p>
							<p><b>Catatan Penting : </b></p>
							<p style="color:#ff0000;">Untuk overnight penulisan jam harus dengan tanggal dengan format <b>MM/DD/YY hh:mm</b>, contoh : <b>12/30/2018 12:30</b></p>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Periode</label>
					<div class="col-sm-10">
						<?php echo dropdown('PERIODE_ID',periode_option(),set_value('PERIODE_ID',$EDIT->PERIODE_ID),' class="form-control" ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">File</label>
					<div class="col-sm-10">
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