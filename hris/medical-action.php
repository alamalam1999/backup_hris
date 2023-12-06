<?php

include 'app-load.php';

is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('medical.edit');
	$EDIT = db_first(" SELECT * FROM medical WHERE MEDICAL_ID='$ID' ");
}
if($OP=='delete'){
	is_login('medical.delete');
	db_execute(" DELETE FROM medical WHERE MEDICAL_ID='$ID' ");
	header('location: medical.php');
	exit;
}

if($OP=='approve'){
	is_login('medical.change_status');
	$IDS = get_input('ids');
	if( is_array($IDS) )
	{
		$CU = current_user();
		$TIME = date('Y-m-d H:i:s');
		db_execute(" UPDATE medical SET STATUS='APPROVED',UPDATED_BY='$CU->NAMA',UPDATED_ON='$TIME',APPROVED_BY='$CU->NAMA',APPROVED_ON='$TIME' WHERE MEDICAL_ID IN (".implode(',',$IDS).")");
	}
	header('location: medical.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('PERIODE_ID','KARYAWAN_ID');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{
		$FIELDS = array(
			'PERIODE_ID','KARYAWAN_ID','KETERANGAN','TANGGAL','STATUS'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}
		
		$KARYAWAN_ID = get_input('KARYAWAN_ID');
		$rs = db_first(" SELECT J.PROJECT_ID FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID WHERE KARYAWAN_ID='$KARYAWAN_ID'  ");
		$PROJECT_ID = isset($rs->PROJECT_ID) ? $rs->PROJECT_ID : '';
		$FIELDS[] = 'PROJECT_ID';
		$INSERT_VAL[] = "'".$PROJECT_ID."'";
		$UPDATE_VAL[] = "PROJECT_ID='".$PROJECT_ID."'";
		
		$TOTAL = get_input('TOTAL');
		$FIELDS[] = 'TOTAL';
		$INSERT_VAL[] = "'".input_currency($TOTAL)."'";
		$UPDATE_VAL[] = "TOTAL='".input_currency($TOTAL)."'";

		if($OP=='' OR $OP=='add')
		{
			is_login('medical.add');
			db_execute(" INSERT INTO medical (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE medical SET ".implode(',',$UPDATE_VAL)." WHERE MEDICAL_ID='$ID' ");
			
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Medical
		<a href="medical.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="medical-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="medical-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
	<div class="row">
		<div class="col-md-5">
			<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Periode</label>
				<div class="col-sm-8">
					<?php echo dropdown('PERIODE_ID',periode_option(),set_value('PERIODE_ID',$EDIT->PERIODE_ID),' id="PERIODE_ID" class="form-control" ') ?>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Karyawan</label>
				<div class="col-sm-8">
					<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
					<?php
						$K = db_first(" SELECT KARYAWAN_ID,NIK,NAMA FROM karyawan WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$EDIT->KARYAWAN_ID))."' ");
						if(isset($K->KARYAWAN_ID)){
							echo '<option value="'.$K->KARYAWAN_ID.'" selected="selected">'.$K->NIK.' - '.$K->NAMA.'</option>';
						}
					?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Tanggal</label>
				<div class="col-sm-8">
					<input type="text" name="TANGGAL" value="<?php echo set_value('TANGGAL',$EDIT->TANGGAL) ?>" class="form-control datepicker2" autocomplete="off">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Total</label>
				<div class="col-sm-8">
					<input type="text" name="TOTAL" value="<?php echo set_value('TOTAL',$EDIT->TOTAL) ?>" class="form-control currency">
				</div>
			</div>
		</div>
		<div class="col-md-5">
			<div class="form-group">
				<label for="" class="col-sm-3 control-label">Keterangan</label>
				<div class="col-sm-9">
					<input type="text" name="KETERANGAN" value="<?php echo set_value('KETERANGAN',$EDIT->KETERANGAN) ?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-3 control-label">Status</label>
				<div class="col-sm-9">
					<?php echo dropdown('STATUS',array('PENDING'=>'PENDING','APPROVED'=>'APPROVED','NOT APPROVED'=>'NOT APPROVED'),set_value('STATUS',$EDIT->STATUS),' class="form-control" ') ?>
				</div>
			</div>
		</div>
	</div>
	</form>
	<div style="border-top:1px dashed #cccccc;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=MEDICAL]').focus();
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
	
	$('.datepicker2').datepick({
		dateFormat: 'yyyy-mm-dd',
		monthsToShow: 2,
		monthsOffset:1
	});
	
	load_date();
	$('#PERIODE_ID').change(function(){
		load_date();
	});
});
function load_date()
{
	$.ajax({
		url: 'periode-ajax.php',
		data: { 'PERIODE_ID' : $('#PERIODE_ID').val() },
		dataType: 'json',
		method: 'POST',
		success: function(r){
			$('.datepicker2').datepick('option', {
				minDate: r.TANGGAL_MULAI,
				maxDate: r.TANGGAL_SELESAI
			});
		}
	});
}
</script>

<?php
include 'footer.php';
?>