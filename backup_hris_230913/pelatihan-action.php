<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('pelatihan.edit');
	$EDIT = db_first(" SELECT * FROM pelatihan WHERE PELATIHAN_ID='$ID' ");
}
if($OP=='delete'){
	is_login('pelatihan.delete');
	db_execute(" DELETE FROM pelatihan WHERE PELATIHAN_ID='$ID' ");
	header('location: pelatihan.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('NAMA_PELATIHAN','TGL_MULAI','TGL_SELESAI','TEMPAT','PESERTA','PENYELENGGARA','KETERANGAN');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'NAMA_PELATIHAN','TGL_MULAI','TGL_SELESAI','TEMPAT','PESERTA','PENYELENGGARA','KETERANGAN','STATUS',
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('pelatihan.add');
			db_execute(" INSERT INTO pelatihan (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			$PELATIHAN_PESERTA = get_input('KARYAWAN_ID');
			if(is_array($PELATIHAN_PESERTA) AND count($PELATIHAN_PESERTA)){
				foreach($PELATIHAN_PESERTA as $key=>$val){
					if($val != ''){
						$KARYAWAN_ID 	= $val;
						db_execute(" INSERT INTO pelatihan_peserta (PELATIHAN_ID,KARYAWAN_ID) VALUES ('$ID','$KARYAWAN_ID') ");
					}
				}
			}

			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE pelatihan SET ".implode(',',$UPDATE_VAL)." WHERE PELATIHAN_ID='$ID' ");
			db_execute(" DELETE FROM pelatihan_peserta WHERE PELATIHAN_ID='$ID' ");
			$PELATIHAN_PESERTA = get_input('KARYAWAN_ID');
			if(is_array($PELATIHAN_PESERTA) AND count($PELATIHAN_PESERTA)){
				foreach($PELATIHAN_PESERTA as $key=>$val){
					if($val != ''){
						$KARYAWAN_ID 	= $val;
						db_execute(" INSERT INTO pelatihan_peserta (PELATIHAN_ID,KARYAWAN_ID) VALUES ('$ID','$KARYAWAN_ID') ");
					}
				}
			}
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$STATUS = array(
	'PENGAJUAN' => 'PENGAJUAN',
	'TIDAK DISETUJUI' => 'TIDAK DISETUJUI',
	'DISETUJUI' => 'DISETUJUI',
	'TERLAKSANA' => 'TERLAKSANA',
);


$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/datepicker/js/bootstrap-datepicker.min.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Pelatihan
		<a href="pelatihan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="pelatihan-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="pelatihan-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Nama Pelatihan</label>
			<div class="col-sm-10">
				<input type="text" name="NAMA_PELATIHAN" value="<?php echo set_value('NAMA_PELATIHAN',$EDIT->NAMA_PELATIHAN) ?>" class="form-control">
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
			<label for="" class="col-sm-2 control-label">Tempat</label>
			<div class="col-sm-10">
				<input type="text" name="TEMPAT" value="<?php echo set_value('TEMPAT',$EDIT->TEMPAT) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">kuota</label>
			<div class="col-sm-10">
				<input type="text" name="PESERTA" value="<?php echo set_value('PESERTA',$EDIT->PESERTA) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Penyelenggara</label>
			<div class="col-sm-10">
				<input type="text" name="PENYELENGGARA" value="<?php echo set_value('PENYELENGGARA',$EDIT->PENYELENGGARA) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Keterangan</label>
			<div class="col-sm-10">
				<input type="text" name="KETERANGAN" value="<?php echo set_value('KETERANGAN',$EDIT->KETERANGAN) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Status</label>
			<div class="col-sm-10">
				<?php echo dropdown('STATUS',$STATUS,set_value('STATUS',$EDIT->STATUS),' class="form-control" ') ?>
			</div>
		</div>

		<div class="form-group">
			<label for="" class="col-sm-2 control-label">
				<a href="#" class="btn btn-info" id="btn-add-peserta"><span class="glyphicon glyphicon-plus"></span>&nbsp;Tambah Peserta</a>
			</label>
			<div class="col-sm-8" style="border-bottom: 1px solid #eee; padding: 0 0 20px 0; margin-left: 15px;">
				<label for="" class="col-sm-6 control-label" style="text-align: left;">
					Nama Peserta (Karyawan)
				</label>
			</div>
		</div>

		<div class="form-group">
			<label for="" class="col-sm-2 control-label">
			</label>
			<div class="col-sm-8">
				<div id="add-peserta">
					<?php $PELATIHAN_PESERTA = db_fetch("SELECT * FROM pelatihan_peserta WHERE PELATIHAN_ID='$ID'");
					if(count($PELATIHAN_PESERTA) > 0){ foreach ($PELATIHAN_PESERTA as $key => $row) { ?>
					<div class="input-group" style="padding-top: 15px;">
						<div class="col-sm-8">
							<select class="form-control KARYAWAN_ID" name="KARYAWAN_ID[]">
								<?php 
								$KARYAWAN = db_fetch("SELECT KARYAWAN_ID,NAMA FROM karyawan WHERE ST_KERJA = 'AKTIF' AND KARYAWAN_ID = '$row->KARYAWAN_ID'");
								if(count($KARYAWAN)>0){ foreach($KARYAWAN as $EQ){ ?>
									<option value="<?php echo $EQ->KARYAWAN_ID ?>" <?php echo 'selected'; ?>>
										<?php echo $EQ->NAMA ?>
									</option>
								<?php }} ?>
							</select>
						</div>
						<span class="input-group-btn">
							<button type="button" class="btn btn-danger btn-flat del-peserta" title="Delete Peserta">
								<span class="glyphicon glyphicon-trash btn-danger"></span>
							</button>
						</span>
					</div>
					<?php }} ?>
				</div>
			</div>
		</div>

	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	delete_peserta();
	$('input[name=INDICATOR]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});
});

$(function() {
	peserta_dtl();
	$('#btn-add-peserta').click(function(i){
		$('#add-peserta').append('<div class="input-group" style="padding-top: 15px;"><div class="col-sm-8"><select class="form-control KARYAWAN_ID" name="KARYAWAN_ID[]"></select></div><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-peserta" title="Delete Peserta"><span class="glyphicon glyphicon-trash btn-danger"></span></button></span></div>');
		peserta_dtl();
	});


});

function delete_peserta(){
	$('#add-peserta').on('click', '.del-peserta', function(){
		$(this).closest('div').remove();
	});
}

function peserta_dtl() {
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
}

</script>

<?php
include 'footer.php';
?>