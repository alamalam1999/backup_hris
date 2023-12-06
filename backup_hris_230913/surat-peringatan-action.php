<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM sp WHERE SP_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM sp WHERE SP_ID='$ID' ");
	header('location: surat-peringatan.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('KARYAWAN_ID','TANGGAL','SANKSI');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$SP_ID = db_escape(get_input('SP_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'KARYAWAN_ID','PELANGGARAN','KETERANGAN','SANKSI','TANGGAL'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			db_execute(" INSERT INTO sp (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE sp SET ".implode(',',$UPDATE_VAL)." WHERE SP_ID='$ID' ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/datepicker/js/bootstrap-datepicker.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Surat Peringatan
		<a href="surat-peringatan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="surat-peringatan-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="surat-peringatan-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Karyawan</label>
			<div class="col-sm-10">
				<select class="form-control KARYAWAN_ID" name="KARYAWAN_ID">
					<?php 
					$KARYAWAN = db_fetch("SELECT KARYAWAN_ID,NAMA FROM karyawan WHERE ST_KERJA = 'AKTIF' AND KARYAWAN_ID = '$EDIT->KARYAWAN_ID' ");
					if(count($KARYAWAN)>0){ foreach($KARYAWAN as $EQ){ ?>
						<option value="<?php echo $EQ->KARYAWAN_ID ?>" <?php echo 'selected'; ?>>
							<?php echo $EQ->NAMA ?>
						</option>
					<?php }} ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Pelanggaran</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="4" name="PELANGGARAN"><?php echo isset($EDIT->PELANGGARAN) ? $EDIT->PELANGGARAN : '' ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Keterangan</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="4" name="KETERANGAN"><?php echo isset($EDIT->KETERANGAN) ? $EDIT->KETERANGAN : '' ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tanggal</label>
			<div class="col-sm-10">
				<input type="text" name="TANGGAL" value="<?php echo set_value('TANGGAL',$EDIT->TANGGAL) ?>" class="form-control datepicker">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Sanksi</label>
			<div class="col-sm-10">
				<div class="form-check">
					<label class="form-check-label">
						<input type="radio" class="form-check-input" name="SANKSI" <?php if (isset($EDIT->SANKSI) && $EDIT->SANKSI=="SP1") echo "checked";?> value="SP1"> SP1
					</label>
				</div>
				<div class="form-check">
					<label class="form-check-label">
						<input type="radio" class="form-check-input" name="SANKSI" <?php if (isset($EDIT->SANKSI) && $EDIT->SANKSI=="SP2") echo "checked";?> value="SP2"> SP2
					</label>
				</div>
				<div class="form-check">
					<label class="form-check-label">
						<input type="radio" class="form-check-input" name="SANKSI" <?php if (isset($EDIT->SANKSI) && $EDIT->SANKSI=="SP3") echo "checked";?> value="SP3"> SP3
					</label>
				</div>
				<div class="form-check">
					<label class="form-check-label">
						<input type="radio" class="form-check-input" name="SANKSI" <?php if (isset($EDIT->SANKSI) && $EDIT->SANKSI=="PHK") echo "checked";?> value="PHK"> PHK
					</label>
				</div>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){

	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		orientation: 'top'
	});

	$('.KARYAWAN_ID').select2({
		theme: "bootstrap",
		ajax: {
			url:'karyawan-ac.php',
			dataType: 'json',
			data: function (params) {
				return {
					q: params.term,
					page_limit: 20
				}
			}
		}
	});
});
</script>

<?php
include 'footer.php';
?>