<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURRENT_ID = get_input('CURRENT_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('interview.edit');
	$EDIT = db_first(" SELECT * FROM calon_karyawan C LEFT JOIN lamaran L ON (L.CALON_KARYAWAN_ID=C.CALON_KARYAWAN_ID) WHERE C.CALON_KARYAWAN_ID='$ID' ");
}
if($OP=='delete'){
	is_login('interview.delete');
	db_execute(" DELETE FROM calon_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
	header('location: karyawan.php');
	exit;
}

is_login('interview.edit');
if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('NAMA','TP_LAHIR','TGL_LAHIR','KEWARGANEGARAAN','JK','NO_IDENTITAS','ALAMAT','PROVINSI','KOTA','EMAIL','AGAMA','ST_KAWIN','HP','LOWONGAN_ID','EXPECTED_SALARY');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$foto_allow_ext = array('png','jpg');
	$foto_name = isset($_FILES['FOTO']['name']) ? $_FILES['FOTO']['name'] : '';
	$foto_tmp = isset($_FILES['FOTO']['tmp_name']) ? $_FILES['FOTO']['tmp_name'] : '';
	$foto_ext = strtolower(substr(strrchr($foto_name, "."), 1));
	$foto_new = rand(11111,99999).'_'.$foto_name;
	$foto_dest = 'uploads/foto/'.$foto_new;
	
	$ijazah_allow_ext = array('png','jpg','pdf');
	$ijazah_name = isset($_FILES['IJAZAH']['name']) ? $_FILES['IJAZAH']['name'] : '';
	$ijazah_tmp = isset($_FILES['IJAZAH']['tmp_name']) ? $_FILES['IJAZAH']['tmp_name'] : '';
	$ijazah_ext = strtolower(substr(strrchr($ijazah_name, "."), 1));
	$ijazah_new = rand(11111,99999).'_'.$ijazah_name;
	$ijazah_dest = 'uploads/ijazah/'.$ijazah_new;

	$cv_allow_ext = array('doc','docx','pdf');
	$cv_name = isset($_FILES['CV']['name']) ? $_FILES['CV']['name'] : '';
	$cv_tmp = isset($_FILES['CV']['tmp_name']) ? $_FILES['CV']['tmp_name'] : '';
	$cv_ext = strtolower(substr(strrchr($cv_name, "."), 1));
	$cv_new = rand(11111,99999).'_'.$cv_name;
	$cv_dest = 'uploads/cv/'.$cv_new;
		
	$ERROR = array();
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom wajib di isi.';
	}
	else if( (get_input('EMAIL') != $EDIT->EMAIL) AND db_exists(" SELECT 1 FROM calon_karyawan WHERE EMAIL='".db_escape(get_input('EMAIL'))."' ") ){
		$ERROR[] = 'Email sudah terdaftar, silakan gunakan email lain';
	}
	else if( ! valid_email(get_input('EMAIL'))){
		$ERROR[] = 'Email tidak benar, contoh : john_doe@host.com';
	}
	else if( get_input('CURRENT_FOTO')=='' AND ! is_uploaded_file($foto_tmp) ){
		$ERROR[] = 'Foto wajib di isi';
		if( ! in_array($foto_ext,$foto_allow_ext) ){
			$ERROR[] = 'Foto harus bertipe jpg atau png';
		}
	}
	else if( get_input('CURRENT_IJAZAH')=='' AND ! is_uploaded_file($ijazah_tmp) ){
		$ERROR[] = 'Scan Ijazah wajib di isi';
		if( ! in_array($ijazah_ext,$ijazah_allow_ext) ){
			$ERROR[] = 'Ijazah harus bertipe jpg atau png';
		}
	}
	else if( get_input('CURRENT_CV')=='' AND ! is_uploaded_file($cv_tmp) ){
		$ERROR[] = 'CV wajib di isi';
		if( ! in_array($cv_ext,$cv_allow_ext) ){
			$ERROR[] = 'CV harus bertipe doc atau pdf';
		}
	}
	else{
		$FIELDS = array(
			'NAMA','TP_LAHIR','TGL_LAHIR','KEWARGANEGARAAN','JK','NO_IDENTITAS','ALAMAT','KELURAHAN','KECAMATAN','PROVINSI','KOTA','KODE_POS','RT','RW','EMAIL','AGAMA','ST_KAWIN','HP','FACEBOOK','TWITTER','INSTAGRAM','EXPECTED_SALARY'
		);

		$NEW_FOTO = 0;
		if(is_uploaded_file($foto_tmp)){
			if(move_uploaded_file($foto_tmp,$foto_dest)){
				$FIELDS[] = 'FOTO';
				$NEW_FOTO = 1;
			}
		}
		$NEW_IJAZAH = 0;
		if(is_uploaded_file($ijazah_tmp)){
			if(move_uploaded_file($ijazah_tmp,$ijazah_dest)){
				$FIELDS[] = 'IJAZAH';
				$NEW_IJAZAH = 1;
			}
		}
		$NEW_CV = 0;
		if(is_uploaded_file($cv_tmp)){
			if(move_uploaded_file($cv_tmp,$cv_dest)){
				$FIELDS[] = 'CV';
				$NEW_CV = 1;
			}
		}
		
		$d = array();
		foreach($FIELDS as $F){
			if($F=='FOTO'){
				if($NEW_FOTO=='1'){
					$INSERT_VAL[$F] = "'".db_escape($foto_new)."'";
					$UPDATE_VAL[$F] = $F."='".db_escape($foto_new)."'";
				}else{
					$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_FOTO'))."'";
					$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_FOTO'))."'";
				}
			}else if($F=='IJAZAH'){
				if($NEW_IJAZAH=='1'){
					$INSERT_VAL[$F] = "'".db_escape($ijazah_new)."'";
					$UPDATE_VAL[$F] = $F."='".db_escape($ijazah_new)."'";
				}else{
					$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_IJAZAH'))."'";
					$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_IJAZAH'))."'";
				}
			}else if($F=='CV'){
				if($NEW_CV=='1'){
					$INSERT_VAL[$F] = "'".db_escape($cv_new)."'";
					$UPDATE_VAL[$F] = $F."='".db_escape($cv_new)."'";
				}else{
					$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_CV'))."'";
					$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_CV'))."'";
				}
			}else{
				$INSERT_VAL[$F] = "'".db_escape(get_input($F))."'";
				$UPDATE_VAL[$F] = $F."='".db_escape(get_input($F))."'";
			}
		}

		$NEW_F = array('EXPECTED_SALARY');
		foreach($NEW_F as $F){
			$INSERT_VAL[$F] = "'".db_escape(input_currency(get_input($F)))."'";
			$UPDATE_VAL[$F] = $F."='".db_escape(input_currency(get_input($F)))."'";
		}

		$LOWONGAN_ID = get_input('LOWONGAN_ID');
		$CREATED_ON = date('Y-m-d H:i:s');

		if($OP=='' OR $OP=='add')
		{
			//print_r($INSERT_VAL);
			//die();
			db_execute(" INSERT INTO calon_karyawan (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_ID();
			db_execute(" INSERT INTO lamaran (LOWONGAN_ID,CALON_KARYAWAN_ID,CREATED_ON,STATUS_LAMARAN) VALUES ('$LOWONGAN_ID','$ID','$CREATED_ON','PENGAJUAN') ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}else{
			db_execute(" UPDATE calon_karyawan SET ".implode(',',$UPDATE_VAL)." WHERE CALON_KARYAWAN_ID='$ID' ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			db_execute(" DELETE FROM lamaran WHERE CALON_KARYAWAN_ID='$ID' ");
			db_execute(" INSERT INTO lamaran (LOWONGAN_ID,CALON_KARYAWAN_ID,CREATED_ON,STATUS_LAMARAN) VALUES ('$LOWONGAN_ID','$ID','$CREATED_ON','PENGAJUAN') ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/datepicker/js/bootstrap-datepicker.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst('Interview') ?> : Data Pelamar
		<a href="<?php if(get_input('proses')=='rekomendasi'){ echo 'rekomendasi.php'; }else{ echo 'interview.php'; } ?>" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<?php /*
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="lamaran-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } */?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="lamaran-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="CURRENT_FOTO" value="<?php echo $EDIT->FOTO ?>">
		<input type="hidden" name="CURRENT_IJAZAH" value="<?php echo $EDIT->IJAZAH ?>">
		<input type="hidden" name="CURRENT_CV" value="<?php echo $EDIT->CV ?>">
		<input type="hidden" name="CURRENT_ID" value="<?php echo $ID ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Nama<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="NAMA" value="<?php echo set_value('NAMA', $EDIT->NAMA) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Tempat Lahir<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="TP_LAHIR" value="<?php echo set_value('TP_LAHIR', $EDIT->TP_LAHIR) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Tgl Lahir<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="TGL_LAHIR" value="<?php echo set_value('TGL_LAHIR', $EDIT->TGL_LAHIR) ?>" class="form-control datepicker" disabled>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">WNI/WNA<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<?php echo dropdown('KEWARGANEGARAAN',array('WNI'=>'WNI','WNA'=>'WNA'),set_value('KEWARGANEGARAAN', $EDIT->KEWARGANEGARAAN),' class="form-control" disabled ') ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">Jenis Kelamin<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<?php echo dropdown('JK',array('L'=>'L','P'=>'P'),set_value('JK', $EDIT->JK),' class="form-control" disabled ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">KTP/Paspor</label>
					<div class="col-sm-9">
						<input type="text" name="NO_IDENTITAS" value="<?php echo set_value('NO_IDENTITAS', $EDIT->NO_IDENTITAS) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Alamat<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="ALAMAT" value="<?php echo set_value('ALAMAT', $EDIT->ALAMAT) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Kelurahan<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="KELURAHAN" value="<?php echo set_value('KELURAHAN', $EDIT->KELURAHAN) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Kecamatan<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="KECAMATAN" value="<?php echo set_value('KECAMATAN', $EDIT->KECAMATAN) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Provinsi<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="PROVINSI" value="<?php echo set_value('PROVINSI', $EDIT->PROVINSI) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Kota<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-9">
						<input type="text" name="KOTA" value="<?php echo set_value('KOTA', $EDIT->KOTA) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Kode Pos</label>
					<div class="col-sm-3">
						<input type="text" name="KODE_POS" value="<?php echo set_value('KODE_POS', $EDIT->KODE_POS) ?>" class="form-control" disabled style="text-align:center;">
					</div>
					<label for="" class="col-sm-1 control-label">RT</label>
					<div class="col-sm-2">
						<input type="text" name="RT" value="<?php echo set_value('RT', $EDIT->RT) ?>" class="form-control" disabled style="text-align:center;">
					</div>
					<label for="" class="col-sm-1 control-label">RW</label>
					<div class="col-sm-2">
						<input type="text" name="RW" value="<?php echo set_value('RW', $EDIT->RW) ?>" class="form-control" disabled style="text-align:center;">
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Email<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-8">
						<input type="text" name="EMAIL" value="<?php echo set_value('EMAIL', $EDIT->EMAIL) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Agama<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-8">
						<?php echo dropdown('AGAMA',array('ISLAM'=>'ISLAM','KRISTEN'=>'KRISTEN','KATOLIK'=>'KATOLIK','HINDU'=>'HINDU','BUDHA'=>'BUDHA','KONG HU CHU'=>'KONG HU CHU'),set_value('AGAMA', $EDIT->AGAMA),' class="form-control" disabled ') ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Satus Kawin<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-8">
						<?php echo dropdown('ST_KAWIN',array('BELUM KAWIN'=>'BELUM KAWIN','KAWIN'=>'KAWIN','JANDA'=>'JANDA','DUDA'=>'DUDA'),set_value('ST_KAWIN', $EDIT->ST_KAWIN),' class="form-control" disabled ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">No HP<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-8">
						<input type="text" name="HP" value="<?php echo set_value('HP', $EDIT->HP) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Facebook</label>
					<div class="col-sm-8">
						<input type="text" name="FACEBOOK" value="<?php echo set_value('FACEBOOK', $EDIT->FACEBOOK) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Twitter</label>
					<div class="col-sm-8">
						<input type="text" name="TWITTER" value="<?php echo set_value('TWITTER', $EDIT->TWITTER) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Instagram</label>
					<div class="col-sm-8">
						<input type="text" name="INSTAGRAM" value="<?php echo set_value('INSTAGRAM', $EDIT->INSTAGRAM) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Foto<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-8">
						<?php if(!empty($EDIT->FOTO) AND url_exists(base_url().'uploads/foto/'.$EDIT->FOTO)){ ?>
						<a href="<?php echo base_url().'uploads/foto/'.$EDIT->FOTO; ?>" data-fancybox data-caption="<?php echo $EDIT->FOTO ?>" download>Unduh Foto</a>
						<?php } ?>
						<input type="file" name="FOTO" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Ijazah<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-8">
						<?php if(!empty($EDIT->IJAZAH) AND url_exists(base_url().'uploads/ijazah/'.$EDIT->IJAZAH)){ ?>
						<a href="<?php echo base_url().'uploads/ijazah/'.$EDIT->IJAZAH; ?>" data-fancybox data-caption="<?php echo $EDIT->IJAZAH ?>" download>Unduh Scan Ijazah</a>
						<?php } ?>
						<input type="file" name="IJAZAH" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Cv<!--<span style="color:red;">*</span>--></label>
					<div class="col-sm-8">
						<?php if(!empty($EDIT->CV) AND url_exists(base_url().'uploads/cv/'.$EDIT->CV)){ ?>
						<a href="<?php echo base_url().'uploads/cv/'.$EDIT->CV; ?>" data-fancybox data-caption="<?php echo $EDIT->CV ?>" download>Unduh CV</a>
						<?php } ?>
						<input type="file" name="CV" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Expected Salary</label>
					<div class="col-sm-8">
						<input type="text" name="EXPECTED_SALARY" value="<?php echo set_value('EXPECTED_SALARY', $EDIT->EXPECTED_SALARY) ?>" class="form-control currency" maxlength="20" disabled>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Untuk Lowongan</label>
					<div class="col-sm-8">
						<?php echo dropdown('LOWONGAN_ID',dropdown_option('lowongan','LOWONGAN_ID','LOWONGAN','ORDER BY LOWONGAN ASC'),set_value('LOWONGAN_ID',$EDIT->LOWONGAN_ID),' class="form-control" disabled ') ?>
					</div>
				</div>
			</div>
		</div> <!-- End Row -->
	</form>		
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
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
	FIND_PROVINSI();
	FIND_KOTA();

	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		orientation: 'bottom'
	});
});
function FIND_PROVINSI(){
	var OB = $('input[name=PROVINSI]');
	OB.autocomplete('autocomplete.php?m=provinsi', {
		minChars: 1,
		cacheLength: 0,
		//width: 310,
		matchContains: "word",
		autoFill: false,
		mustMatch: true,
		formatItem: function (row, i, max) {FIND_KOTA(); eval("row=" + row); return row.PROVINSI; },
		formatResult: function (row, i, max) {FIND_KOTA(); eval("row=" + row); return row.PROVINSI; }
	});
}
function FIND_KOTA(){
	var OB = $('input[name=KOTA]');
	OB.autocomplete('autocomplete.php?m=kota&PROVINSI='+$('input[name=PROVINSI]').val(), {
		minChars: 1,
		cacheLength: 0,
		//width: 310,
		matchContains: "word",
		autoFill: false,
		mustMatch: true,
		formatItem: function (row, i, max) {eval("row=" + row); return row.KOTA; },
		formatResult: function (row, i, max) {eval("row=" + row); return row.KOTA; }
	});
}
</script>

<?php 
include 'footer.php'; 
?>