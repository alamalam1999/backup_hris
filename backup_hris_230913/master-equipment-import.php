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
		
		/*$rs = db_fetch(" SELECT COMPANY_ID FROM company ");
		$COMPANY = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$COMPANY[$row->COMPANY_ID] = $row->COMPANY_ID;
			}
		}*/
		
		require 'lib/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($tmp_name);
		$baris = $data->rowcount($sheet_index=0);

		$FIELDS = array(
			'COMPANY_ID','NAMA','KETERANGAN','STOK_AWAL'
		);
				
		foreach($FIELDS as $F){
			$COL[] = '`'.$F.'`';
		}
		
		$ROW = 2;
		$isegment = 1000;
		$segment = 0;
		if($baris>0)
		{
			for ($i=$ROW; $i<=$baris; $i++)
			{
				$COMPANY_ID = db_escape($data->val($i, 1));
				$NAMA = db_escape($data->val($i, 2));
				$KETERANGAN = db_escape($data->val($i, 3));
				$STOK_AWAL = db_escape($data->val($i, 4));
					
				foreach($FIELDS as $F){
					$VAL[$F] = "'".${$F}."'";
				}

				if(!empty($COMPANY_ID) AND !empty($NAMA) AND !empty($STOK_AWAL))
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
				db_execute(" INSERT IGNORE equipment (".implode(',',$COL).") VALUES ".implode(',',$tmp) );
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
		<?php echo ucfirst($OP) ?> Import Equipment
		<a href="master-equipment.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Contoh Template</label>
					<div class="col-sm-8">
						<div class="form-control-static">
							<p><a href="static/tpl/TEMPLATE_EQUIPMENT.xls" class="btn btn-success btn-sm">Download Template</a></p>
							<p><b>Catatan Penting : </b></p>
							<p>Pada kolom <b>COMPANY ID</b> nilai harus disesuaikan dengan <b>COMPANY ID</b> yang ada pada Master <b><a href="company.php" target="_blank">Company</a></b></p>
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