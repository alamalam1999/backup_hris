<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$PROSES = get_input('proses');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM lowongan WHERE LOWONGAN_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM lowongan WHERE LOWONGAN_ID='$ID' ");
	header('location: lowongan.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('KODE_REFERENSI','LOWONGAN','POSISI_ID','DESKRIPSI','TGL_BERAKHIR');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$LOWONGAN_ID = db_escape(get_input('LOWONGAN_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else if( (get_input('KODE_REFERENSI') != $EDIT->KODE_REFERENSI) AND db_exists(" SELECT 1 FROM lowongan WHERE KODE_REFERENSI='".db_escape(get_input('KODE_REFERENSI'))."' ") ){
		$ERROR[] = 'Kode Referensi sudah terdaftar, silakan gunakan Kode Referensi lain';
	}else{

		$FIELDS = array(
			'KODE_REFERENSI','LOWONGAN','POSISI_ID','DESKRIPSI','TGL_BERAKHIR'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			db_execute(" INSERT INTO lowongan (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE lowongan SET ".implode(',',$UPDATE_VAL)." WHERE LOWONGAN_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

$JS[] = 'static/summernote/summernote.js';
$CSS[] = 'static/summernote/summernote.css';

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Lowongan
		<?php if($PROSES == 'lamaran'){ ?>
			<a href="lamaran.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<?php }else{ ?>
			<a href="lowongan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<?php } ?>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="lowongan-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	
	<?php include 'msg.php' ?>
	
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="lowongan-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST" autocomplete="off">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label class="col-sm-2 control-label">Kode Referensi</label>
			<div class="col-sm-10">
				<input type="text" name="KODE_REFERENSI" value="<?php echo set_value('KODE_REFERENSI',$EDIT->KODE_REFERENSI) ?>" class="form-control" maxlength="6">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Lowongan</label>
			<div class="col-sm-10">
				<input type="text" name="LOWONGAN" value="<?php echo set_value('LOWONGAN',$EDIT->LOWONGAN) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Posisi</label>
			<div class="col-sm-10">
				<select name="POSISI_ID" id="POSISI_ID" class="form-control">
					<?php
					$P = db_first(" SELECT * FROM posisi WHERE POSISI_ID='".db_escape(set_value('POSISI_ID',$EDIT->POSISI_ID))."' ");
					if(isset($P->POSISI_ID)){
						echo '<option value="'.$P->POSISI_ID.'" selected="selected">'.$P->POSISI.'</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Deskripsi</label>
			<div class="col-sm-10">
				<textarea name="DESKRIPSI" class="form-control summernote"><?php echo set_value('DESKRIPSI',$EDIT->DESKRIPSI) ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">Tgl Berakhir</label>
			<div class="col-sm-10">
				<input type="text" name="TGL_BERAKHIR" value="<?php echo set_value('TGL_BERAKHIR',$EDIT->TGL_BERAKHIR) ?>" class="form-control datepicker" autocomplete="off">
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=LOWONGAN]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});
	$('.datepicker').datepick({
		dateFormat: 'yyyy-mm-dd',
	});
	$('.summernote').summernote({
		height: 300
	});

	$('#POSISI_ID').select2({
		theme: "bootstrap",
		ajax: {
			url: 'posisi-ac.php',
			dataType: 'json',
		}
	});
});
</script>

<?php
include 'footer.php';
?>