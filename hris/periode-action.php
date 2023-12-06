<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('periode.edit');
	$EDIT = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$ID' ");
}
if($OP=='delete'){
	is_login('periode.delete');
	db_execute(" DELETE FROM periode WHERE PERIODE_ID='$ID' ");
	header('location: periode.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('PERIODE','TAHUN','TANGGAL_MULAI','TANGGAL_SELESAI','TANGGAL_MULAI2','TANGGAL_SELESAI2');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$PERIODE_ID = db_escape(get_input('PERIODE_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'PERIODE','BULAN','TAHUN','TANGGAL_MULAI','TANGGAL_SELESAI','TANGGAL_MULAI2','TANGGAL_SELESAI2',
			'THR_IDUL_FITRI','TGL_IDUL_FITRI','THR_KUNINGAN','TGL_KUNINGAN','STATUS_PERIODE',
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('periode.add');
			db_execute(" INSERT INTO periode (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE periode SET ".implode(',',$UPDATE_VAL)." WHERE PERIODE_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$BULAN = array();
$BLN = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');
foreach( $BLN as $key => $bln ){
	$i = $key + 1;
	$BULAN[$i] = isset($BLN[$key]) ? $BLN[$key] : '';
}

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:25px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Periode
		<a href="periode.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="periode-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="periode-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Nama Periode</label>
				<div class="col-sm-8">
					<input type="text" name="PERIODE" value="<?php echo set_value('PERIODE',$EDIT->PERIODE) ?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Tahun</label>
				<div class="col-sm-8">
					<div class="input-group">
						<?php echo dropdown('BULAN',$BULAN,set_value('BULAN',$EDIT->BULAN),' class="form-control" ') ?>
						<div class="input-group-addon">-</div>
						<input type="text" name="TAHUN" value="<?php echo set_value('TAHUN',$EDIT->TAHUN) ?>" class="form-control">
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Periode Absensi</label>
				<div class="col-sm-8">
					<div class="input-group">
						<input type="text" name="TANGGAL_MULAI" value="<?php echo set_value('TANGGAL_MULAI',$EDIT->TANGGAL_MULAI) ?>" class="form-control datepicker" autocomplete="off">
						<div class="input-group-addon">to</div>
						<input type="text" name="TANGGAL_SELESAI" value="<?php echo set_value('TANGGAL_SELESAI',$EDIT->TANGGAL_SELESAI) ?>" class="form-control datepicker" autocomplete="off">
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Periode Payroll</label>
				<div class="col-sm-8">
					<div class="input-group">
						<input type="text" name="TANGGAL_MULAI2" value="<?php echo set_value('TANGGAL_MULAI2',$EDIT->TANGGAL_MULAI2) ?>" class="form-control datepicker" autocomplete="off">
						<div class="input-group-addon">to</div>
						<input type="text" name="TANGGAL_SELESAI2" value="<?php echo set_value('TANGGAL_SELESAI2',$EDIT->TANGGAL_SELESAI2) ?>" class="form-control datepicker" autocomplete="off">
					</div>
					<p style="margin-top:10px;font-size:11px;color:#dd0000;">Periode payroll akan digunakan untuk proyek dengan cutoff berbeda antara absensi dan payroll</p>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Status</label>
				<div class="col-sm-8">
					<?php echo dropdown('STATUS_PERIODE',array('OPEN'=>'OPEN','CLOSED'=>'CLOSED'),set_value('STATUS_PERIODE',$EDIT->STATUS_PERIODE),' class="form-control" ') ?>
					<p style="margin-top:10px;"><b style="color:#00cf00;">Open</b> : admin dapat membuat/generate penggajian<br>
					<b style="color:#cf0000;">Closed</b> : admin dapat tidak dapat membuat/generate penggajian</p>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">THR Idul Fitri</label>
				<div class="col-sm-8">
					<?php $ch = $EDIT->THR_IDUL_FITRI=='1' ? 'checked' : '' ?>
					<input type="checkbox" name="THR_IDUL_FITRI" value="1" <?php echo $ch ?> style="margin:10px 5px 0 0;">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Tgl Idul Fitri</label>
				<div class="col-sm-8">
					<input type="text" name="TGL_IDUL_FITRI" value="<?php echo set_value('TGL_IDUL_FITRI',$EDIT->TGL_IDUL_FITRI) ?>" class="form-control datepicker" autocomplete="off">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">THR Kuningan</label>
				<div class="col-sm-8">
					<?php $ch = $EDIT->THR_KUNINGAN=='1' ? 'checked' : '' ?>
					<input type="checkbox" name="THR_KUNINGAN" value="1" <?php echo $ch ?> style="margin:10px 5px 0 0;">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Tgl Kuningan</label>
				<div class="col-sm-8">
					<input type="text" name="TGL_KUNINGAN" value="<?php echo set_value('TGL_KUNINGAN',$EDIT->TGL_KUNINGAN) ?>" class="form-control datepicker" autocomplete="off">
				</div>
			</div>
		</div>
	</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=PERIODE]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});

	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		orientation: 'bottom'
	});

});
</script>

<?php
include 'footer.php';
?>