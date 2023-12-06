<?php
include 'app-load.php';

is_login('shift.import');

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
			'SHIFT_CODE', 'START_TIME', 'START_BEGIN', 'START_END', 'FINISH_TIME', 'FINISH_BEGIN', 'FINISH_END', 'SHIFT_COLOR', 'OVERNIGHT'
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
				$SHIFT_CODE = db_escape($data->val($i, 3));
				$START_TIME = db_escape($data->val($i, 4));
				$START_BEGIN = db_escape($data->val($i, 5));
				$START_END = db_escape($data->val($i, 6));
				$FINISH_TIME = db_escape($data->val($i, 7));
				$FINISH_BEGIN = db_escape($data->val($i, 8));
				$FINISH_END = db_escape($data->val($i, 9));
				$SHIFT_COLOR = db_escape($data->val($i, 10));
				$OVERNIGHT = db_escape($data->val($i, 11));

				$d = array();
				foreach($FIELDS as $F){
					$VAL[$F] = "'".${$F}."'";
				}

				if(!empty($SHIFT_CODE))
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
				db_execute(" INSERT IGNORE shift (".implode(',',$COL).") VALUES ".implode(',',$tmp) );
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
		<?php echo ucfirst($OP) ?> Import Shift
		<a href="shift.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="shift-import.php" method="POST" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Contoh Template</label>
					<div class="col-sm-8">
						<div class="form-control-static">
							<p><a href="static/tpl/TEMPLATE_SHIFT.xls" class="btn btn-sm btn-success">Download Template</a></p>
							<p><b>Catatan Penting : </b></p>
							<p>Format SHIFT_CODE : <b>A-HH-NO_URUT</b>, contoh : <b>A0801</b></p>
							<p>Format START atau FINISH TIME : <b>hh:mm</b>, contoh : <b>08:30</b></p>
							<p>Format COLOR : Hexadecimal, contoh <b>#ff0000</b> untuk merah</p>
							<p>Format OVERNIGHT : <b>YES</b> atau <b>NO</b></p>
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