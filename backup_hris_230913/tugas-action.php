<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM tugas WHERE TUGAS_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM tugas WHERE TUGAS_ID='$ID' ");
	header('location: tugas.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('TUGAS');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$TUGAS_ID = db_escape(get_input('TUGAS_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'TUGAS','KARYAWAN_ID','TGL_MULAI','TGL_SELESAI'
		);

		$NEW_FILE_TUGAS = 0;
		if(is_uploaded_file($file_tugas_tmp)){
			if(move_uploaded_file($file_tugas_tmp,$file_tugas_dest)){
				$FIELDS[] = 'FILE_TUGAS';
				$NEW_FILE_TUGAS = 1;
			}
		}
		
		$d = array();
		foreach($FIELDS as $F){
			if($F=='FILE_TUGAS'){
				if($NEW_FILE_TUGAS=='1'){
					$INSERT_VAL[$F] = "'".db_escape($file_tugas_new)."'";
					$UPDATE_VAL[$F] = $F."='".db_escape($file_tugas_new)."'";
				}else{
					$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_FILE_TUGAS'))."'";
					$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_FILE_TUGAS'))."'";
				}
			}else{
				$INSERT_VAL[$F] = "'".db_escape(get_input($F))."'";
				$UPDATE_VAL[$F] = $F."='".db_escape(get_input($F))."'";
			}
		}

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			db_execute(" INSERT INTO tugas (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE tugas SET ".implode(',',$UPDATE_VAL)." WHERE TUGAS_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/datepicker/js/bootstrap-datepicker.min.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Tugas
		<a href="tugas.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="tugas-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="tugas-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURRENT_FILE_TUGAS" value="<?php echo $EDIT->FILE_TUGAS ?>">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Nama Tugas</label>
			<div class="col-sm-10">
				<input type="text" name="TUGAS" value="<?php echo set_value('TUGAS',$EDIT->TUGAS) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Karyawan</label>
			<div class="col-sm-6">
				<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Upload File </label>
			<div class="col-sm-10">
				<input type="file" name="FILE_TUGAS" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tanggal Mulai</label>
			<div class="col-sm-10">
				<input type="text" name="TGL_MULAI" value="<?php echo set_value('TGL_MULAI',$EDIT->TGL_MULAI) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tanggal Selesai</label>
			<div class="col-sm-10">
				<input type="text" name="TGL_SELESAI" value="<?php echo set_value('TGL_SELESAI',$EDIT->TGL_SELESAI) ?>" class="form-control">
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=TUGAS]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});

	$('#KARYAWAN_ID').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-ac.php',
			dataType: 'json',
		}
	});
});
</script>

<?php
include 'footer.php';
?>