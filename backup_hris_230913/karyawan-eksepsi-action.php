<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM eksepsi WHERE EKSEPSI_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" UPDATE eksepsi SET STATUS='CANCEL' WHERE EKSEPSI_ID='$ID' ");
	header('location: karyawan-eksepsi.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('TGL_MULAI','TGL_SELESAI','KETERANGAN');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$EKSEPSI_ID = db_escape(get_input('EKSEPSI_ID'));
	$PERIODE_ID = get_input('PERIODE_ID');
	$KARYAWAN_ID = get_input('KARYAWAN_ID');
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{
		$CU = current_user();
		
		$FIELDS = array(
			'PERIODE_ID','JENIS','KETERANGAN','TGL_MULAI','TGL_SELESAI',
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[$F] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[$F] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			$FIELDS[] = 'KARYAWAN_ID';
			$FIELDS[] = 'STATUS';
			$INSERT_VAL['KARYAWAN_ID'] = "'".db_escape($CU->KARYAWAN_ID)."'";
			$INSERT_VAL['STATUS'] = "'".db_escape('PENDING')."'";
		
			db_execute(" INSERT INTO eksepsi (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE eksepsi SET ".implode(',',$UPDATE_VAL)." WHERE EKSEPSI_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/datepicker/js/bootstrap-datepicker.min.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Eksepsi
		<a href="karyawan-eksepsi.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="karyawan-eksepsi-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Periode</label>
			<div class="col-sm-10">
				<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY TANGGAL_MULAI DESC'),set_value('PERIODE_ID',$EDIT->PERIODE_ID),' class="form-control" ') ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Jenis</label>
			<div class="col-sm-10">
				<?php echo dropdown('JENIS',array('CUTI'=>'CUTI','SAKIT'=>'SAKIT','DINAS'=>'DINAS'),set_value('KARYAWAN_ID',$EDIT->KARYAWAN_ID),' class="form-control" ') ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tgl Mulai</label>
			<div class="col-sm-10">
				<input type="text" name="TGL_MULAI" value="<?php echo set_value('TGL_MULAI',$EDIT->TGL_MULAI) ?>" class="form-control datepicker">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tgl Selesai</label>
			<div class="col-sm-10">
				<input type="text" name="TGL_SELESAI" value="<?php echo set_value('TGL_SELESAI',$EDIT->TGL_SELESAI) ?>" class="form-control datepicker">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Keterangan</label>
			<div class="col-sm-10">
				<input type="text" name="KETERANGAN" value="<?php echo set_value('KETERANGAN',$EDIT->KETERANGAN) ?>" class="form-control">
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=EKSEPSI]').focus();
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