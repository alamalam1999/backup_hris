<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$LOWONGAN_ID = get_input('lowongan');
$LAMARAN_ID = get_input('lamaran');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('lamaran.edit');
	$EDIT = db_first(" SELECT * FROM calon_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
	$LOWONGAN = db_first(" SELECT * FROM lowongan L WHERE LOWONGAN_ID='$LOWONGAN_ID' ");
	$LAMARAN = db_first(" SELECT * FROM lamaran L LEFT JOIN posisi J ON (J.POSISI_ID=L.POSISI_ID) WHERE LAMARAN_ID='$LAMARAN_ID' ");
	if($LAMARAN->TGL_INTERVIEW == '0000-00-00') $LAMARAN->TGL_INTERVIEW = '';
}

is_login('lamaran.edit');
if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('STATUS_LAMARAN','TGL_INTERVIEW');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}
	
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{
		$STATUS_LAMARAN = db_escape(get_input('STATUS_LAMARAN'));
		$TGL_INTERVIEW = db_escape(get_input('TGL_INTERVIEW'));
		$TEKS = db_escape(get_input('TEKS'));
		$SENT_ON = date('Y-m-d');
		$UPDATE_TYPE = get_input('UPDATE_TYPE');

		if($UPDATE_TYPE == 'SAVE_UNDANGAN'){
			db_execute(" UPDATE lamaran SET STATUS_LAMARAN='$STATUS_LAMARAN',TGL_INTERVIEW='$TGL_INTERVIEW',TEKS='$TEKS' WHERE LAMARAN_ID='$LAMARAN_ID' ");
			if($STATUS_LAMARAN == 'PENGAJUAN') header('location: lamaran.php');
			if($STATUS_LAMARAN == 'PANGGILAN INTERVIEW') header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID.'&ref=interview');
			exit;
		}

		if($UPDATE_TYPE == 'SEND_UNDANGAN'){
			send_mail(array($EDIT->EMAIL),'Undangan Interview',$TEKS);
			header('location: interview.php');
			exit;
		}

		
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/summernote/summernote.js';
$CSS[] = 'static/summernote/summernote.css';

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';
include 'header.php';

$GENDER = ($EDIT->JK=='L') ? 'Bapak' : 'Ibu';
$TEMPLATE_REMARK = '
<p>Dear '.$GENDER.' '.$EDIT->NAMA.',</p>
<p>&nbsp;</p>
<p>Sesuai dengan lamaran anda pada posisi '.$LAMARAN->POSISI.' di PT kami, kami mengundang Anda untuk menghadiri interview yang akan dilaksanakan pada tanggal <strong>'.tgl($LAMARAN->TGL_INTERVIEW).' jam 10:00</strong>.</p>
<p>Kami harap anda bisa menghadiri interview tersebut dengan tepat waktu.</p>
<p>&nbsp;</p>
<p>Terima Kasih,</p>
<p>Regards,</p>
<p>HRD Team</p>
';
if(empty($LAMARAN->TEKS)) $LAMARAN->TEKS = $TEMPLATE_REMARK;
$REF = isset($_GET['ref']) ? $_GET['ref'] : '';
?>

<section class="container-fluid" style="margin-top:70px;">
	<form id="form" class="form-horizontal" action="lamaran-status.php?op=<?php echo $OP ?><?php echo '&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID ?>" method="POST" enctype="multipart/form-data">
	<h1 style="margin-top:0px;" class="border-title">
		Update Status Lamaran
		<a href="<?php if($REF=='interview') { echo 'interview.php'; }else{ echo 'lamaran.php'; } ?>" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>

		<button name="UPDATE_TYPE" value="SAVE_UNDANGAN" type="submit" class="btn btn-primary" onclick="$('#form').submit()">
			<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
		</button>

		<?php if($LAMARAN->TGL_INTERVIEW != '' && $LAMARAN->STATUS_LAMARAN != 'PENGAJUAN' && $LAMARAN->STATUS_LAMARAN != 'POSISI LAIN'){ ?>
		<button name="UPDATE_TYPE" value="SEND_UNDANGAN" type="submit" class="btn btn-success" onclick="$('#form').submit()">
			<span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Send
		</button>
		<?php } ?>
	</h1>
	
	<?php include 'msg.php' ?>

	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	
	<div class="row">
	<div class="col-md-7">
		<div class="form-group">
			<label for="" class="col-sm-3 control-label">Lowongan</label>
			<div class="col-sm-9"><p class="form-control-static"><?php echo $LOWONGAN->LOWONGAN ?></p></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3 control-label">Posisi</label>
			<div class="col-sm-9"><p class="form-control-static"><?php echo $LAMARAN->POSISI ?></p></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3 control-label">Tgl Lamaran</label>
			<div class="col-sm-9"><p class="form-control-static"><?php echo tgl($LAMARAN->CREATED_ON,1) ?></p></div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Tgl Interview<span style="color:red; padding-left: 5px;">*</span></label>
			<div class="col-sm-9">
				<input type="text" name="TGL_INTERVIEW" value="<?php echo set_value('TGL_INTERVIEW', $LAMARAN->TGL_INTERVIEW) ?>" class="form-control datepicker" autocomplete="off">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Status<span style="color:red; padding-left: 5px;">*</span></label>
			<div class="col-sm-9">
				<?php echo dropdown('STATUS_LAMARAN',array('PENGAJUAN'=>'PENGAJUAN','PANGGILAN INTERVIEW'=>'PANGGILAN INTERVIEW'),set_value('STATUS_LAMARAN', $LAMARAN->STATUS_LAMARAN),' class="form-control" ') ?>
			</div>
		</div>
		<?php if($LAMARAN->TGL_INTERVIEW != '' && $LAMARAN->STATUS_LAMARAN != 'PENGAJUAN' && $LAMARAN->STATUS_LAMARAN != 'POSISI LAIN'){ ?>
		<div class="form-group">
			<label class="col-sm-3 control-label">Remark</label>
			<div class="col-sm-9">
				<textarea name="TEKS" class="form-control summernote"><?php echo set_value('TEKS',$LAMARAN->TEKS) ?></textarea>
				<p>Remark akan dikirim juga sebagai email kepada yang bersangkutan.</p>
			</div>
		</div>
		<?php } ?>
	</div>
	<div class="col-md-5">
		<div class="form-group" style="padding-left: 15px;">
			<?php if(!empty($EDIT->FOTO) AND url_exists(base_url().'uploads/foto/'.$EDIT->FOTO)){ ?>
			<img src="<?php echo base_url().'uploads/foto/'.$EDIT->FOTO; ?>" alt="" class="img-thumbnail" style="width:300px;margin:0 auto 10px;">
			<?php } ?>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Nama</label>
			<div class="col-sm-9"><?php echo $EDIT->NAMA ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Tmpt Lahir</label>
			<div class="col-sm-9"><?php echo $EDIT->TP_LAHIR ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Tgl Lahir</label>
			<div class="col-sm-9"><?php echo $EDIT->TGL_LAHIR ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Alamat</label>
			<div class="col-sm-9"><?php echo $EDIT->ALAMAT ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Kelurahan</label>
			<div class="col-sm-9"><?php echo $EDIT->KELURAHAN ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Kecamatan</label>
			<div class="col-sm-9"><?php echo $EDIT->KECAMATAN ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Kota</label>
			<div class="col-sm-9"><?php echo $EDIT->PROVINSI ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Provinsi</label>
			<div class="col-sm-9"><?php echo $EDIT->KOTA ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Kode Pos</label>
			<div class="col-sm-3"><?php echo $EDIT->KODE_POS ?></div>
			<label for="" class="col-sm-1">RT</label>
			<div class="col-sm-2"><?php echo $EDIT->RT ?></div>
			<label for="" class="col-sm-1">RW</label>
			<div class="col-sm-2"><?php echo $EDIT->RW ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Warga Negara</label>
			<div class="col-sm-9"><?php echo $EDIT->KEWARGANEGARAAN ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Jns Kelamin</label>
			<div class="col-sm-9"><?php echo $EDIT->JK ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">No HP</label>
			<div class="col-sm-9"><?php echo $EDIT->HP ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Email</label>
			<div class="col-sm-9"><?php echo $EDIT->EMAIL ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Expected Salary</label>
			<div class="col-sm-9"><?php echo number($EDIT->EXPECTED_SALARY) ?></div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">Foto</label>
			<div class="col-sm-9">
			<?php if(!empty($EDIT->CV) AND url_exists(base_url().'uploads/cv/'.$EDIT->CV)){ ?>
			<a href="<?php echo base_url().'uploads/cv/'.$EDIT->CV; ?>" data-fancybox data-caption="<?php echo $EDIT->CV ?>" download>Unduh</a>
			<?php } ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">File Ijazah</label>
			<div class="col-sm-9">
			<?php if(!empty($EDIT->IJAZAH) AND url_exists(base_url().'uploads/ijazah/'.$EDIT->IJAZAH)){ ?>
			<a href="<?php echo base_url().'uploads/ijazah/'.$EDIT->IJAZAH; ?>" data-fancybox data-caption="<?php echo $EDIT->IJAZAH ?>" download>Unduh</a>
			<?php } ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-3">File Cv</label>
			<div class="col-sm-9">
			<?php if(!empty($EDIT->FOTO) AND url_exists(base_url().'uploads/foto/'.$EDIT->FOTO)){ ?>
			<a href="<?php echo base_url().'uploads/cv/'.$EDIT->FOTO; ?>" data-fancybox data-caption="<?php echo $EDIT->FOTO ?>" download>Unduh</a>
			<?php } ?>
			</div>
		</div>
	</div>
	</div> <!-- end row -->
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	</form>
</section>

<script>
$(document).ready(function(){
	$('input[name=NIK]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});
	$('.summernote').summernote({
		height: 180
	});
	/*
	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		orientation: 'bottom'
	});
	*/
});
</script>

<?php 
include 'footer.php'; 
?>