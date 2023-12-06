<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$LOWONGAN_ID = get_input('lowongan');
$LAMARAN_ID = get_input('lamaran');
$PROSES = get_input('proses');
$DISABLED = '';
if($PROSES=='rekomendasi') $DISABLED = 'disabled';
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM calon_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
	$LOWONGAN = db_first(" SELECT * FROM lowongan L LEFT JOIN posisi J ON (J.POSISI_ID=L.POSISI_ID) WHERE LOWONGAN_ID='$LOWONGAN_ID' ");
	$LAMARAN = db_first(" SELECT * FROM lamaran WHERE LAMARAN_ID='$LAMARAN_ID' ");
	if($LAMARAN->TGL_DATANG== '0000-00-00') $LAMARAN->TGL_DATANG = '';
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$UPDATE_TYPE = get_input('UPDATE_TYPE');
	$TGL_DATANG = get_input('TGL_DATANG');
	$JAM_DATANG = get_input('JAM_DATANG');

	if($UPDATE_TYPE == 'DATA_PELAMAR'){
		db_execute(" UPDATE lamaran SET TGL_DATANG='$TGL_DATANG',JAM_DATANG='$JAM_DATANG' WHERE LAMARAN_ID='$LAMARAN_ID' ");
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		exit;
	}

	if($UPDATE_TYPE == 'INTERVIEWER1'){
		$INTERVIEWER 	= 1;
		$KARYAWAN_ID 	= get_input('KARYAWAN_ID1');
		$TEMPLATE_ID 	= get_input('TEMPLATE_ID1');
		$TOTAL_NILAI 	= get_input('TOTAL_NILAI1');
		$NOTE 			= get_input('NOTE'.$INTERVIEWER);
		$KESIMPULAN 	= get_input('KEPUTUSAN'.$INTERVIEWER);
		$TEMPLATE = db_fetch("SELECT * FROM template_pertanyaan WHERE TEMPLATE_ID='$TEMPLATE_ID'"); 

		foreach ($TEMPLATE as $key => $value) {
			$PERTANYAAN_ID = $value->PERTANYAAN_ID;
			$NILAI = get_input($value->PERTANYAAN_ID.'-'.$INTERVIEWER);
			$VALUES .= "(".$LAMARAN_ID.",".$INTERVIEWER.",".$KARYAWAN_ID.",".$TEMPLATE_ID.",".$PERTANYAAN_ID.",".$NILAI.",".$TOTAL_NILAI.",'".$NOTE."','".$KESIMPULAN."'),";
		}

		$VALUES = rtrim($VALUES,',');
		//echo $VALUES; die();

		db_execute(" DELETE FROM interview WHERE INTERVIEWER=1 AND LAMARAN_ID='$LAMARAN_ID'");
		db_execute(" INSERT INTO interview (LAMARAN_ID,INTERVIEWER,KARYAWAN_ID,TEMPLATE_ID,PERTANYAAN_ID,NILAI,TOTAL_NILAI,NOTE,KEPUTUSAN) VALUES $VALUES ");
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		exit;
	}

	if($UPDATE_TYPE == 'INTERVIEWER2'){
		$INTERVIEWER 	= 2;
		$KARYAWAN_ID 	= get_input('KARYAWAN_ID2');
		$TEMPLATE_ID 	= get_input('TEMPLATE_ID2');
		$TOTAL_NILAI 	= get_input('TOTAL_NILAI2');
		$NOTE 			= get_input('NOTE'.$INTERVIEWER);
		$KESIMPULAN 	= get_input('KEPUTUSAN'.$INTERVIEWER);
		$TEMPLATE = db_fetch("SELECT * FROM template_pertanyaan WHERE TEMPLATE_ID='$TEMPLATE_ID'"); 

		foreach ($TEMPLATE as $key => $value) {
			$PERTANYAAN_ID = $value->PERTANYAAN_ID;
			$NILAI = get_input($value->PERTANYAAN_ID.'-'.$INTERVIEWER);
			$VALUES .= "(".$LAMARAN_ID.",".$INTERVIEWER.",".$KARYAWAN_ID.",".$TEMPLATE_ID.",".$PERTANYAAN_ID.",".$NILAI.",".$TOTAL_NILAI.",'".$NOTE."','".$KESIMPULAN."'),";
		}

		$VALUES = rtrim($VALUES,',');
		db_execute(" DELETE FROM interview WHERE INTERVIEWER=2 AND LAMARAN_ID='$LAMARAN_ID'");
		db_execute(" INSERT INTO interview (LAMARAN_ID,INTERVIEWER,KARYAWAN_ID,TEMPLATE_ID,PERTANYAAN_ID,NILAI,TOTAL_NILAI,NOTE,KEPUTUSAN) VALUES $VALUES ");
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		exit;
	}

	if($UPDATE_TYPE == 'INTERVIEWER3'){
		$INTERVIEWER 	= 3;
		$KARYAWAN_ID 	= get_input('KARYAWAN_ID3');
		$TEMPLATE_ID 	= get_input('TEMPLATE_ID3');
		$TOTAL_NILAI 	= get_input('TOTAL_NILAI3');
		$NOTE 			= get_input('NOTE'.$INTERVIEWER);
		$KESIMPULAN 	= get_input('KEPUTUSAN'.$INTERVIEWER);
		$TEMPLATE = db_fetch("SELECT * FROM template_pertanyaan WHERE TEMPLATE_ID='$TEMPLATE_ID'"); 

		foreach ($TEMPLATE as $key => $value) {
			$PERTANYAAN_ID = $value->PERTANYAAN_ID;
			$NILAI = get_input($value->PERTANYAAN_ID.'-'.$INTERVIEWER);
			$VALUES .= "(".$LAMARAN_ID.",".$INTERVIEWER.",".$KARYAWAN_ID.",".$TEMPLATE_ID.",".$PERTANYAAN_ID.",".$NILAI.",".$TOTAL_NILAI.",'".$NOTE."','".$KESIMPULAN."'),";
		}

		$VALUES = rtrim($VALUES,',');
		db_execute(" DELETE FROM interview WHERE INTERVIEWER=3 AND LAMARAN_ID='$LAMARAN_ID'");
		db_execute(" INSERT INTO interview (LAMARAN_ID,INTERVIEWER,KARYAWAN_ID,TEMPLATE_ID,PERTANYAAN_ID,NILAI,TOTAL_NILAI,NOTE,KEPUTUSAN) VALUES $VALUES ");
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		exit;
	}

	if($UPDATE_TYPE == 'INTERVIEWER4'){
		$INTERVIEWER 	= 4;
		$KARYAWAN_ID 	= get_input('KARYAWAN_ID4');
		$TEMPLATE_ID 	= get_input('TEMPLATE_ID4');
		$TOTAL_NILAI 	= get_input('TOTAL_NILAI4');
		$NOTE 			= get_input('NOTE'.$INTERVIEWER);
		$KESIMPULAN 	= get_input('KEPUTUSAN'.$INTERVIEWER);
		$TEMPLATE = db_fetch("SELECT * FROM template_pertanyaan WHERE TEMPLATE_ID='$TEMPLATE_ID'"); 

		foreach ($TEMPLATE as $key => $value) {
			$PERTANYAAN_ID = $value->PERTANYAAN_ID;
			$NILAI = get_input($value->PERTANYAAN_ID.'-'.$INTERVIEWER);
			$VALUES .= "(".$LAMARAN_ID.",".$INTERVIEWER.",".$KARYAWAN_ID.",".$TEMPLATE_ID.",".$PERTANYAAN_ID.",".$NILAI.",".$TOTAL_NILAI.",'".$NOTE."','".$KESIMPULAN."'),";
		}

		$VALUES = rtrim($VALUES,',');
		db_execute(" DELETE FROM interview WHERE INTERVIEWER=4 AND LAMARAN_ID='$LAMARAN_ID'");
		db_execute(" INSERT INTO interview (LAMARAN_ID,INTERVIEWER,KARYAWAN_ID,TEMPLATE_ID,PERTANYAAN_ID,NILAI,TOTAL_NILAI,NOTE,KEPUTUSAN) VALUES $VALUES ");

		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		exit;
	}

	if($UPDATE_TYPE == 'INTERVIEWER5'){
		$INTERVIEWER 	= 5;
		$KARYAWAN_ID 	= get_input('KARYAWAN_ID5');
		$TEMPLATE_ID 	= get_input('TEMPLATE_ID5');
		$TOTAL_NILAI 	= get_input('TOTAL_NILAI5');
		$NOTE 			= get_input('NOTE'.$INTERVIEWER);
		$KESIMPULAN 	= get_input('KEPUTUSAN'.$INTERVIEWER);
		$TEMPLATE = db_fetch("SELECT * FROM template_pertanyaan WHERE TEMPLATE_ID='$TEMPLATE_ID'"); 

		foreach ($TEMPLATE as $key => $value) {
			$PERTANYAAN_ID = $value->PERTANYAAN_ID;
			$NILAI = get_input($value->PERTANYAAN_ID.'-'.$INTERVIEWER);
			$VALUES .= "(".$LAMARAN_ID.",".$INTERVIEWER.",".$KARYAWAN_ID.",".$TEMPLATE_ID.",".$PERTANYAAN_ID.",".$NILAI.",".$TOTAL_NILAI.",'".$NOTE."','".$KESIMPULAN."'),";
		}

		$VALUES = rtrim($VALUES,',');
		db_execute(" DELETE FROM interview WHERE INTERVIEWER=5 AND LAMARAN_ID='$LAMARAN_ID'");
		db_execute(" INSERT INTO interview (LAMARAN_ID,INTERVIEWER,KARYAWAN_ID,TEMPLATE_ID,PERTANYAAN_ID,NILAI,TOTAL_NILAI,NOTE,KEPUTUSAN) VALUES $VALUES ");

		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		exit;
	}

	if($UPDATE_TYPE == 'INTERVIEWER6'){
		$INTERVIEWER 	= 6;
		$KARYAWAN_ID 	= get_input('KARYAWAN_ID6');
		$TEMPLATE_ID 	= get_input('TEMPLATE_ID6');
		$TOTAL_NILAI 	= get_input('TOTAL_NILAI6');
		$NOTE 			= get_input('NOTE'.$INTERVIEWER);
		$KESIMPULAN 	= get_input('KEPUTUSAN'.$INTERVIEWER);
		$TEMPLATE = db_fetch("SELECT * FROM template_pertanyaan WHERE TEMPLATE_ID='$TEMPLATE_ID'"); 

		foreach ($TEMPLATE as $key => $value) {
			$PERTANYAAN_ID = $value->PERTANYAAN_ID;
			$NILAI = get_input($value->PERTANYAAN_ID.'-'.$INTERVIEWER);
			$VALUES .= "(".$LAMARAN_ID.",".$INTERVIEWER.",".$KARYAWAN_ID.",".$TEMPLATE_ID.",".$PERTANYAAN_ID.",".$NILAI.",".$TOTAL_NILAI.",'".$NOTE."','".$KESIMPULAN."'),";
		}

		$VALUES = rtrim($VALUES,',');
		db_execute(" DELETE FROM interview WHERE INTERVIEWER=6 AND LAMARAN_ID='$LAMARAN_ID'");
		db_execute(" INSERT INTO interview (LAMARAN_ID,INTERVIEWER,KARYAWAN_ID,TEMPLATE_ID,PERTANYAAN_ID,NILAI,TOTAL_NILAI,NOTE,KEPUTUSAN) VALUES $VALUES ");

		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		exit;
	}

	if($UPDATE_TYPE == 'FINAL'){
		$TOTAL_NILAI_FINAL 	= get_input('TOTAL_NILAI_FINAL');
		$NOTE_FINAL 		= get_input('NOTE_FINAL');
		$KEPUTUSAN_FINAL 	= get_input('KEPUTUSAN_FINAL');
		$POSISI_ID 			= get_input('POSISI_ID');

		if($KEPUTUSAN_FINAL == 'DIREKOMENDASIKAN POSISI LAIN'){
			db_execute(" DELETE FROM interview WHERE LAMARAN_ID='$LAMARAN_ID'");
			db_execute(" UPDATE lamaran SET TOTAL_NILAI='',NOTE='',KEPUTUSAN='', STATUS_LAMARAN='POSISI LAIN', POSISI_ID='$POSISI_ID',LOWONGAN_ID=0 WHERE LAMARAN_ID='$LAMARAN_ID' ");
			header('location: lamaran.php');
		}else{
			db_execute(" UPDATE lamaran SET TOTAL_NILAI='$TOTAL_NILAI_FINAL',NOTE='$NOTE_FINAL',KEPUTUSAN='$KEPUTUSAN_FINAL', STATUS_LAMARAN='SELESAI INTERVIEW' WHERE LAMARAN_ID='$LAMARAN_ID' ");

			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID);
		}

		
		exit;
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
		<?php //echo ucfirst($OP) ?> Update Interview 
		<?php if($PROSES=='rekomendasi'){ ?>
			<a href="rekomendasi.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<?php }else{ ?>
			<a href="interview.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<?php } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Data Pelamar</a></li>
		<li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Interviewer 1</a></li>
		<li role="presentation"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">Interviewer 2</a></li>
		<li role="presentation"><a href="#tab4" aria-controls="tab4" role="tab" data-toggle="tab">Interviewer 3</a></li>
		<li role="presentation"><a href="#tab5" aria-controls="tab5" role="tab" data-toggle="tab">Interviewer 4</a></li>
		<li role="presentation"><a href="#tab6" aria-controls="tab6" role="tab" data-toggle="tab">Interviewer 5</a></li>
		<li role="presentation"><a href="#tab7" aria-controls="tab7" role="tab" data-toggle="tab">Interviewer 6</a></li>
		<li role="presentation"><a href="#tab8" aria-controls="tab8" role="tab" data-toggle="tab">Interview FInal</a></li>
	</ul>
	<form id="form" class="form-horizontal" action="interview-status.php?op=<?php echo $OP ?><?php echo '&id='.$ID.'&lamaran='.$LAMARAN_ID.'&lowongan='.$LOWONGAN_ID ?>" method="POST">
		<input type="hidden" name="LAMARAN_ID" id="LAMARAN_ID" value="<?php echo $LAMARAN_ID ?>">
		<div class="tab-content" style="margin-top:20px;">
		<div role="tabpanel" class="tab-pane active" id="tab1">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group" style="padding-left: 15px;">
						<?php if(!empty($EDIT->FOTO) AND url_exists(base_url().'uploads/foto/'.$EDIT->FOTO)){ ?>
						<img src="<?php echo base_url().'uploads/foto/'.$EDIT->FOTO; ?>" alt="" class="img-thumbnail" style="width:300px;margin:0 auto 10px;">
						<?php } ?>
					</div>
					<div class="form-group">
					<label for="" class="col-sm-2 control-label">Tgl Datang</label>
					<div class="col-sm-8">
						<input type="text" name="TGL_DATANG" value="<?php echo set_value('TGL_DATANG',$LAMARAN->TGL_DATANG) ?>" class="form-control datepicker" <?php echo $DISABLED ?> autocomplete="off">
					</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Jam Datang</label>
						<div class="col-sm-8">
							<input type="text" name="JAM_DATANG" value="<?php echo set_value('JAM_DATANG',$LAMARAN->JAM_DATANG) ?>" class="form-control time" <?php echo $DISABLED ?>>
						</div>
					</div>
					<?php if($PROSES != 'rekomendasi'){ ?>
					<div class="form-group" style="padding-left: 20px;">
						<button name="UPDATE_TYPE" type="submit" value="DATA_PELAMAR" class="btn btn-primary" onclick="$('#form').submit()">
							<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
						</button>
					</div>
					<?php } ?>
				</div>
				<div class="col-md-6">
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
						<label for="" class="col-sm-3">Facebook</label>
						<div class="col-sm-9"><?php echo $EDIT->FACEBOOK ?></div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3">Expected Salary</label>
						<div class="col-sm-9"><?php echo number($EDIT->EXPECTED_SALARY) ?></div>
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
						<?php if(!empty($EDIT->CV) AND url_exists(base_url().'uploads/cv/'.$EDIT->CV)){ ?>
						<a href="<?php echo base_url().'uploads/cv/'.$EDIT->CV; ?>" data-fancybox data-caption="<?php echo $EDIT->CV ?>" download>Unduh</a>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="tab2">
			<div class="row">
			<div class="col-md-6">
			<div class="panel-bt panel-default-bt">
				<div class="panel-heading">Data Interviewer</div>
				<div class="panel-body-bt entry">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Karyawan</label>
								<div class="col-sm-10">
									<select name="KARYAWAN_ID1" id="KARYAWAN_ID1" class="form-control" <?php echo $DISABLED ?> style="width: 100%;">
										<?php
											$DETAIL  = db_first(" SELECT KARYAWAN_ID FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER = '1'");
											$K = db_first(" SELECT J.JABATAN,P.POSISI,K.KARYAWAN_ID,K.NIK,K.NAMA FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID LEFT JOIN posisi P ON P.POSISI_ID=K.POSISI_ID WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$DETAIL->KARYAWAN_ID))."' ");
											if(isset($K->KARYAWAN_ID)){
												echo '<option value="'.$K->KARYAWAN_ID.'" data-nik="'.$K->NIK.'" data-posisi="'.$K->POSISI.'" data-jabatan="'.$K->JABATAN.'" selected="selected">'.$K->NAMA.'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" name="NIK1" id="nik1" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Posisi</label>
								<div class="col-sm-10">
									<input type="text" name="POSISI1" id="posisi1" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Level</label>
								<div class="col-sm-10">
									<input type="text" name="JABATAN1" id="jabatan1" value="" class="form-control" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Data Pelamar</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Nama</label>
									<div class="col-sm-10">
										<input type="text" name="NAMA" value="<?php echo $EDIT->NAMA ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Lowongan</label>
									<div class="col-sm-10">
										<input type="text" name="TP_LAHIR" value="<?php echo $LOWONGAN->LOWONGAN ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Posisi</label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $LOWONGAN->POSISI ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Expected </label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $EDIT->EXPECTED_SALARY ?>" class="form-control currency" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Template Pertanyaan</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Template</label>
									<div class="col-sm-10">
										<select name="TEMPLATE_ID1" id="TEMPLATE_ID1" class="form-control" style="width: 100%;" <?php echo $DISABLED ?>>
										<?php
										$INT1 = db_first(" SELECT * FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER='1'");
										$K = db_first("SELECT * FROM template WHERE TEMPLATE_ID='".db_escape(set_value('TEMPLATE_ID',$INT1->TEMPLATE_ID))."' 
										");
										if(isset($K->TEMPLATE_ID)){
											echo '<option value="'.$K->TEMPLATE_ID.'" selected="selected">'.$K->TEMPLATE.'</option>';
										}
										?>
										</select>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<table class="table">
					<tr>
						<td colspan="7" class="text-center">KETENTUAN PENILAIAN</td>
					</tr>
					<tr>
						<td>1</td>
						<td>=</td>
						<td>Tidak Menunjang</td>
						<td style="width: 30%;"> </td>
						<td>3</td>
						<td>=</td>
						<td>Cukup Menunjang</td>
					</tr>
					<tr>
						<td>2</td>
						<td>=</td>
						<td>Kurang Menunjang</td>
						<td> </td>
						<td>4</td>
						<td>=</td>
						<td>Sangat Menunjang</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12" id="tabel_nilai1">	
			</div>

			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="tab3">
			<div class="row">
			<div class="col-md-6">
			<div class="panel-bt panel-default-bt">
				<div class="panel-heading">Data Interviewer</div>
				<div class="panel-body-bt entry">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Karyawan</label>
								<div class="col-sm-10">
									<select name="KARYAWAN_ID2" id="KARYAWAN_ID2" class="form-control" <?php echo $DISABLED ?> style="width: 100%;">
										<?php
											$DETAIL  = db_first(" SELECT KARYAWAN_ID FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER = '2'");
											$K = db_first(" SELECT J.JABATAN,P.POSISI,K.KARYAWAN_ID,K.NIK,K.NAMA FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID LEFT JOIN posisi P ON P.POSISI_ID=K.POSISI_ID WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$DETAIL->KARYAWAN_ID))."' ");
											if(isset($K->KARYAWAN_ID)){
												echo '<option value="'.$K->KARYAWAN_ID.'" data-nik="'.$K->NIK.'" data-posisi="'.$K->POSISI.'" data-jabatan="'.$K->JABATAN.'" selected="selected">'.$K->NAMA.'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" name="NIK2" id="nik2" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Posisi</label>
								<div class="col-sm-10">
									<input type="text" name="POSISI2" id="posisi2" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Level</label>
								<div class="col-sm-10">
									<input type="text" name="JABATAN2" id="jabatan2" value="" class="form-control" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Data Pelamar</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Nama</label>
									<div class="col-sm-10">
										<input type="text" name="NAMA" value="<?php echo $EDIT->NAMA ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Lowongan</label>
									<div class="col-sm-10">
										<input type="text" name="TP_LAHIR" value="<?php echo $LOWONGAN->LOWONGAN ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Posisi</label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $LOWONGAN->POSISI ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Expected </label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $EDIT->EXPECTED_SALARY ?>" class="form-control currency" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Template Pertanyaan</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Template</label>
									<div class="col-sm-10">
										<select name="TEMPLATE_ID2" id="TEMPLATE_ID2" class="form-control" style="width: 100%;" <?php echo $DISABLED ?>>
										<?php
										$INT2 = db_first(" SELECT * FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER='2'");
										$K = db_first("SELECT * FROM template WHERE TEMPLATE_ID='".db_escape(set_value('TEMPLATE_ID',$INT2->TEMPLATE_ID))."' 
										");
										if(isset($K->TEMPLATE_ID)){
											echo '<option value="'.$K->TEMPLATE_ID.'" selected="selected">'.$K->TEMPLATE.'</option>';
										}
										?>
										</select>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<table class="table">
					<tr>
						<td colspan="7" class="text-center">KETENTUAN PENILAIAN</td>
					</tr>
					<tr>
						<td>1</td>
						<td>=</td>
						<td>Tidak Menunjang</td>
						<td style="width: 30%;"> </td>
						<td>3</td>
						<td>=</td>
						<td>Cukup Menunjang</td>
					</tr>
					<tr>
						<td>2</td>
						<td>=</td>
						<td>Kurang Menunjang</td>
						<td> </td>
						<td>4</td>
						<td>=</td>
						<td>Sangat Menunjang</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12" id="tabel_nilai2">	
			</div>

			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="tab4">
			<div class="row">
			<div class="col-md-6">
			<div class="panel-bt panel-default-bt">
				<div class="panel-heading">Data Interviewer</div>
				<div class="panel-body-bt entry">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Karyawan</label>
								<div class="col-sm-10">
									<select name="KARYAWAN_ID3" id="KARYAWAN_ID3" class="form-control" <?php echo $DISABLED ?> style="width: 100%;">
										<?php
											$DETAIL  = db_first(" SELECT KARYAWAN_ID FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER = '3'");
											$K = db_first(" SELECT J.JABATAN,P.POSISI,K.KARYAWAN_ID,K.NIK,K.NAMA FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID LEFT JOIN posisi P ON P.POSISI_ID=K.POSISI_ID WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$DETAIL->KARYAWAN_ID))."' ");
											if(isset($K->KARYAWAN_ID)){
												echo '<option value="'.$K->KARYAWAN_ID.'" data-nik="'.$K->NIK.'" data-posisi="'.$K->POSISI.'" data-jabatan="'.$K->JABATAN.'" selected="selected">'.$K->NAMA.'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" name="NIK3" id="nik3" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Posisi</label>
								<div class="col-sm-10">
									<input type="text" name="POSISI3" id="posisi3" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Level</label>
								<div class="col-sm-10">
									<input type="text" name="JABATAN3" id="jabatan3" value="" class="form-control" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Data Pelamar</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Nama</label>
									<div class="col-sm-10">
										<input type="text" name="NAMA" value="<?php echo $EDIT->NAMA ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Lowongan</label>
									<div class="col-sm-10">
										<input type="text" name="TP_LAHIR" value="<?php echo $LOWONGAN->LOWONGAN ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Posisi</label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $LOWONGAN->POSISI ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Expected </label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $EDIT->EXPECTED_SALARY ?>" class="form-control currency" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Template Pertanyaan</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Template</label>
									<div class="col-sm-10">
										<select name="TEMPLATE_ID3" id="TEMPLATE_ID3" class="form-control" style="width: 100%;" <?php echo $DISABLED ?>>
										<?php
										$INT3 = db_first(" SELECT * FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER='3'");
										$K = db_first("SELECT * FROM template WHERE TEMPLATE_ID='".db_escape(set_value('TEMPLATE_ID',$INT3->TEMPLATE_ID))."' 
										");
										if(isset($K->TEMPLATE_ID)){
											echo '<option value="'.$K->TEMPLATE_ID.'" selected="selected">'.$K->TEMPLATE.'</option>';
										}
										?>
										</select>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<table class="table">
					<tr>
						<td colspan="7" class="text-center">KETENTUAN PENILAIAN</td>
					</tr>
					<tr>
						<td>1</td>
						<td>=</td>
						<td>Tidak Menunjang</td>
						<td style="width: 30%;"> </td>
						<td>3</td>
						<td>=</td>
						<td>Cukup Menunjang</td>
					</tr>
					<tr>
						<td>2</td>
						<td>=</td>
						<td>Kurang Menunjang</td>
						<td> </td>
						<td>4</td>
						<td>=</td>
						<td>Sangat Menunjang</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12" id="tabel_nilai3">	
			</div>

			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="tab5">
			<div class="row">
			<div class="col-md-6">
			<div class="panel-bt panel-default-bt">
				<div class="panel-heading">Data Interviewer</div>
				<div class="panel-body-bt entry">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Karyawan</label>
								<div class="col-sm-10">
									<select name="KARYAWAN_ID4" id="KARYAWAN_ID4" class="form-control" <?php echo $DISABLED ?> style="width: 100%;">
										<?php
											$DETAIL  = db_first(" SELECT KARYAWAN_ID FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER = '4'");
											$K = db_first(" SELECT J.JABATAN,P.POSISI,K.KARYAWAN_ID,K.NIK,K.NAMA FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID LEFT JOIN posisi P ON P.POSISI_ID=K.POSISI_ID WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$DETAIL->KARYAWAN_ID))."' ");
											if(isset($K->KARYAWAN_ID)){
												echo '<option value="'.$K->KARYAWAN_ID.'" data-nik="'.$K->NIK.'" data-posisi="'.$K->POSISI.'" data-jabatan="'.$K->JABATAN.'" selected="selected">'.$K->NAMA.'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" name="NIK4" id="nik4" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Posisi</label>
								<div class="col-sm-10">
									<input type="text" name="POSISI4" id="posisi4" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Level</label>
								<div class="col-sm-10">
									<input type="text" name="JABATAN4" id="jabatan4" value="" class="form-control" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Data Pelamar</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Nama</label>
									<div class="col-sm-10">
										<input type="text" name="NAMA" value="<?php echo $EDIT->NAMA ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Lowongan</label>
									<div class="col-sm-10">
										<input type="text" name="TP_LAHIR" value="<?php echo $LOWONGAN->LOWONGAN ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Posisi</label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $LOWONGAN->POSISI ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Expected </label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $EDIT->EXPECTED_SALARY ?>" class="form-control currency" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Template Pertanyaan</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Template</label>
									<div class="col-sm-10">
										<select name="TEMPLATE_ID4" id="TEMPLATE_ID4" class="form-control" style="width: 100%;" <?php echo $DISABLED ?>>
										<?php
										$INT4 = db_first(" SELECT * FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER='4'");
										$K = db_first("SELECT * FROM template WHERE TEMPLATE_ID='".db_escape(set_value('TEMPLATE_ID',$INT4->TEMPLATE_ID))."' 
										");
										if(isset($K->TEMPLATE_ID)){
											echo '<option value="'.$K->TEMPLATE_ID.'" selected="selected">'.$K->TEMPLATE.'</option>';
										}
										?>
										</select>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<table class="table">
					<tr>
						<td colspan="7" class="text-center">KETENTUAN PENILAIAN</td>
					</tr>
					<tr>
						<td>1</td>
						<td>=</td>
						<td>Tidak Menunjang</td>
						<td style="width: 30%;"> </td>
						<td>3</td>
						<td>=</td>
						<td>Cukup Menunjang</td>
					</tr>
					<tr>
						<td>2</td>
						<td>=</td>
						<td>Kurang Menunjang</td>
						<td> </td>
						<td>4</td>
						<td>=</td>
						<td>Sangat Menunjang</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12" id="tabel_nilai4">	
			</div>

			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="tab6">
			<div class="row">
			<div class="col-md-6">
			<div class="panel-bt panel-default-bt">
				<div class="panel-heading">Data Interviewer</div>
				<div class="panel-body-bt entry">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Karyawan</label>
								<div class="col-sm-10">
									<select name="KARYAWAN_ID5" id="KARYAWAN_ID5" class="form-control" <?php echo $DISABLED ?> style="width: 100%;">
										<?php
											$DETAIL  = db_first(" SELECT KARYAWAN_ID FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER = '5'");
											$K = db_first(" SELECT J.JABATAN,P.POSISI,K.KARYAWAN_ID,K.NIK,K.NAMA FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID LEFT JOIN posisi P ON P.POSISI_ID=K.POSISI_ID WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$DETAIL->KARYAWAN_ID))."' ");
											if(isset($K->KARYAWAN_ID)){
												echo '<option value="'.$K->KARYAWAN_ID.'" data-nik="'.$K->NIK.'" data-posisi="'.$K->POSISI.'" data-jabatan="'.$K->JABATAN.'" selected="selected">'.$K->NAMA.'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" name="NIK5" id="nik5" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Posisi</label>
								<div class="col-sm-10">
									<input type="text" name="POSISI5" id="posisi5" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Level</label>
								<div class="col-sm-10">
									<input type="text" name="JABATAN5" id="jabatan5" value="" class="form-control" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Data Pelamar</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Nama</label>
									<div class="col-sm-10">
										<input type="text" name="NAMA" value="<?php echo $EDIT->NAMA ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Lowongan</label>
									<div class="col-sm-10">
										<input type="text" name="TP_LAHIR" value="<?php echo $LOWONGAN->LOWONGAN ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Posisi</label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $LOWONGAN->POSISI ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Expected </label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $EDIT->EXPECTED_SALARY ?>" class="form-control currency" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Template Pertanyaan</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Template</label>
									<div class="col-sm-10">
										<select name="TEMPLATE_ID5" id="TEMPLATE_ID5" class="form-control" style="width: 100%;" <?php echo $DISABLED ?>>
										<?php
										$INT4 = db_first(" SELECT * FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER='5'");
										$K = db_first("SELECT * FROM template WHERE TEMPLATE_ID='".db_escape(set_value('TEMPLATE_ID',$INT5->TEMPLATE_ID))."' 
										");
										if(isset($K->TEMPLATE_ID)){
											echo '<option value="'.$K->TEMPLATE_ID.'" selected="selected">'.$K->TEMPLATE.'</option>';
										}
										?>
										</select>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<table class="table">
					<tr>
						<td colspan="7" class="text-center">KETENTUAN PENILAIAN</td>
					</tr>
					<tr>
						<td>1</td>
						<td>=</td>
						<td>Tidak Menunjang</td>
						<td style="width: 30%;"> </td>
						<td>3</td>
						<td>=</td>
						<td>Cukup Menunjang</td>
					</tr>
					<tr>
						<td>2</td>
						<td>=</td>
						<td>Kurang Menunjang</td>
						<td> </td>
						<td>4</td>
						<td>=</td>
						<td>Sangat Menunjang</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12" id="tabel_nilai5">	
			</div>

			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="tab7">
			<div class="row">
			<div class="col-md-6">
			<div class="panel-bt panel-default-bt">
				<div class="panel-heading">Data Interviewer</div>
				<div class="panel-body-bt entry">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Karyawan</label>
								<div class="col-sm-10">
									<select name="KARYAWAN_ID6" id="KARYAWAN_ID6" class="form-control" <?php echo $DISABLED ?> style="width: 100%;">
										<?php
											$DETAIL  = db_first(" SELECT KARYAWAN_ID FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER = '5'");
											$K = db_first(" SELECT J.JABATAN,P.POSISI,K.KARYAWAN_ID,K.NIK,K.NAMA FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID LEFT JOIN posisi P ON P.POSISI_ID=K.POSISI_ID WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$DETAIL->KARYAWAN_ID))."' ");
											if(isset($K->KARYAWAN_ID)){
												echo '<option value="'.$K->KARYAWAN_ID.'" data-nik="'.$K->NIK.'" data-posisi="'.$K->POSISI.'" data-jabatan="'.$K->JABATAN.'" selected="selected">'.$K->NAMA.'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">NIK</label>
								<div class="col-sm-10">
									<input type="text" name="NIK6" id="nik6" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Posisi</label>
								<div class="col-sm-10">
									<input type="text" name="POSISI6" id="posisi6" value="" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-2 control-label">Level</label>
								<div class="col-sm-10">
									<input type="text" name="JABATAN7" id="jabatan7" value="" class="form-control" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>
			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Data Pelamar</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Nama</label>
									<div class="col-sm-10">
										<input type="text" name="NAMA" value="<?php echo $EDIT->NAMA ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Lowongan</label>
									<div class="col-sm-10">
										<input type="text" name="TP_LAHIR" value="<?php echo $LOWONGAN->LOWONGAN ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Posisi</label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $LOWONGAN->POSISI ?>" class="form-control" disabled>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Expected </label>
									<div class="col-sm-10">
										<input type="text" name="TGL_LAHIR" value="<?php echo $EDIT->EXPECTED_SALARY ?>" class="form-control currency" disabled>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<div class="panel-bt panel-default-bt">
					<div class="panel-heading">Template Pertanyaan</div>
					<div class="panel-body-bt entry">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Template</label>
									<div class="col-sm-10">
										<select name="TEMPLATE_ID6" id="TEMPLATE_ID6" class="form-control" style="width: 100%;" <?php echo $DISABLED ?>>
										<?php
										$INT4 = db_first(" SELECT * FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER='5'");
										$K = db_first("SELECT * FROM template WHERE TEMPLATE_ID='".db_escape(set_value('TEMPLATE_ID',$INT5->TEMPLATE_ID))."' 
										");
										if(isset($K->TEMPLATE_ID)){
											echo '<option value="'.$K->TEMPLATE_ID.'" selected="selected">'.$K->TEMPLATE.'</option>';
										}
										?>
										</select>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-6">
				<table class="table">
					<tr>
						<td colspan="7" class="text-center">KETENTUAN PENILAIAN</td>
					</tr>
					<tr>
						<td>1</td>
						<td>=</td>
						<td>Tidak Menunjang</td>
						<td style="width: 30%;"> </td>
						<td>3</td>
						<td>=</td>
						<td>Cukup Menunjang</td>
					</tr>
					<tr>
						<td>2</td>
						<td>=</td>
						<td>Kurang Menunjang</td>
						<td> </td>
						<td>4</td>
						<td>=</td>
						<td>Sangat Menunjang</td>
					</tr>
				</table>
			</div>

			<div class="col-md-12" id="tabel_nilai6">	
			</div>

			</div>
		</div>

		<div role="tabpanel" class="tab-pane" id="tab8">
			<div class="row">
			<div class="col-md-12">
			<div class="panel-bt panel-default-bt">
				<div class="panel-heading">Penilian Interviewer</div>
				<div class="panel-body-bt entry">
					<table class="table table-striped">
						<thead>
							<tr>
								<th style="width: 20px;">No</th>
								<th style="width: 250px;">Interviewer</th>
								<th style="width: 100px; text-align: center;">Nilai</th>
								<th>Catatan</th>
								<th style="width: 350px;">Keputusan</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$rs = db_fetch("
								SELECT I.*,K.NAMA FROM interview I 
								LEFT JOIN karyawan K ON (K.KARYAWAN_ID=I.KARYAWAN_ID) 
								WHERE LAMARAN_ID ='$LAMARAN_ID'
								GROUP BY INTERVIEWER
								"); 
							
							if(count($rs)>0){ foreach($rs as $val=>$row){
							?>
							<tr>
								<td><?php echo $val+1 ?></td>
								<td><?php echo $row->NAMA ?></td>
								<td style="text-align: center;"><?php echo $row->TOTAL_NILAI ?></td>
								<td><?php echo $row->NOTE ?></td>
								<td><?php echo $row->KEPUTUSAN ?></td>
							</tr>
							<?php }} ?>
							<?php 
							$AVG = db_first(" SELECT SUM(DISTINCT TOTAL_NILAI)/COUNT(DISTINCT INTERVIEWER) AS VAL FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' ");
							?>
							<tr>
								<td colspan="2"> </td>
								<td>
									<br />
									<input type="text" name="TOTAL_NILAI_FINAL" value="<?php echo number_format($AVG->VAL,2) ?>" class="form-control" style="text-align: center;" readonly>
								</td>
								<td>
									<br />
									<textarea style="resize: none;" class="form-control" rows="4" name="NOTE_FINAL" <?php echo $DISABLED ?>><?php echo isset($LAMARAN->NOTE) ? $LAMARAN->NOTE : '' ?></textarea>
									<br />
								</td>
								<td>
									<br />
									<?php echo dropdown('KEPUTUSAN_FINAL',array('DISARANKAN'=>'Disarankan','DIPERTIMBANGKAN'=>'Dipertimbangkan','DIREKOMENDASIKAN POSISI LAIN'=>'Direkomendasikan Posisi Lain','TIDAK DISARANKAN'=>'Tidak Disarankan'),set_value('KEPUTUSAN_FINAL', $LAMARAN->KEPUTUSAN),' class="form-control" id="KEPUTUSAN"'.$DISABLED) ?>
									<br />
									<?php echo dropdown('POSISI_ID',dropdown_option_default('posisi','POSISI_ID','POSISI','ORDER BY POSISI ASC','--PILIH JABATAN--'),set_value('POSISI_ID',''),' class="form-control posisi" '.$DISABLED) ?>
								</td>
							</tr>
							<?php if($PROSES != 'rekomendasi'){ ?>
							<tr>
								<td colspan="4"> </td>
								<td style="text-align: right;">
									<button name="UPDATE_TYPE" type="submit" value="FINAL" class="btn btn-primary" onclick="$('#form').submit()">
										<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
									</button>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			</div>
			</div>
		</div>
	</form>

	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('#KARYAWAN_ID1').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-int.php',
			dataType: 'json',
		}
	});

	$('#TEMPLATE_ID1').select2({
		theme: "bootstrap",
		ajax: {
			url: 'template-ac.php',
			dataType: 'json',
		}
	});

	$("#nik1").val($('#KARYAWAN_ID1').find(':selected').attr('data-nik'));
	$("#posisi1").val($('#KARYAWAN_ID1').find(':selected').attr('data-posisi'));
	$("#jabatan1").val($('#KARYAWAN_ID1').find(':selected').attr('data-jabatan'));

	$('#KARYAWAN_ID1').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-nik',data.nik);
		$(this).find(':selected').attr('data-posisi',data.posisi);
		$(this).find(':selected').attr('data-jabatan',data.jabatan);
		$("#nik1").val($(this).find(':selected').attr('data-nik'));
		$("#posisi1").val($(this).find(':selected').attr('data-posisi'));
		$("#jabatan1").val($(this).find(':selected').attr('data-jabatan'));
	});

	$('#KARYAWAN_ID2').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-int.php',
			dataType: 'json',
		}
	});

	$('#TEMPLATE_ID2').select2({
		theme: "bootstrap",
		ajax: {
			url: 'template-ac.php',
			dataType: 'json',
		}
	});

	$("#nik2").val($('#KARYAWAN_ID2').find(':selected').attr('data-nik'));
	$("#posisi2").val($('#KARYAWAN_ID2').find(':selected').attr('data-posisi'));
	$("#jabatan2").val($('#KARYAWAN_ID2').find(':selected').attr('data-jabatan'));

	$('#KARYAWAN_ID2').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-nik',data.nik);
		$(this).find(':selected').attr('data-posisi',data.posisi);
		$(this).find(':selected').attr('data-jabatan',data.jabatan);
		$("#nik2").val($(this).find(':selected').attr('data-nik'));
		$("#posisi2").val($(this).find(':selected').attr('data-posisi'));
		$("#jabatan2").val($(this).find(':selected').attr('data-jabatan'));
	});

	$('#KARYAWAN_ID3').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-int.php',
			dataType: 'json',
		}
	});

	$('#TEMPLATE_ID3').select2({
		theme: "bootstrap",
		ajax: {
			url: 'template-ac.php',
			dataType: 'json',
		}
	});

	$("#nik3").val($('#KARYAWAN_ID3').find(':selected').attr('data-nik'));
	$("#posisi3").val($('#KARYAWAN_ID3').find(':selected').attr('data-posisi'));
	$("#jabatan3").val($('#KARYAWAN_ID3').find(':selected').attr('data-jabatan'));

	$('#KARYAWAN_ID3').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-nik',data.nik);
		$(this).find(':selected').attr('data-posisi',data.posisi);
		$(this).find(':selected').attr('data-jabatan',data.jabatan);
		$("#nik3").val($(this).find(':selected').attr('data-nik'));
		$("#posisi3").val($(this).find(':selected').attr('data-posisi'));
		$("#jabatan3").val($(this).find(':selected').attr('data-jabatan'));
	});


	$('#KARYAWAN_ID4').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-int.php',
			dataType: 'json',
		}
	});

	$('#TEMPLATE_ID4').select2({
		theme: "bootstrap",
		ajax: {
			url: 'template-ac.php',
			dataType: 'json',
		}
	});

	$("#nik4").val($('#KARYAWAN_ID4').find(':selected').attr('data-nik'));
	$("#posisi4").val($('#KARYAWAN_ID4').find(':selected').attr('data-posisi'));
	$("#jabatan4").val($('#KARYAWAN_ID4').find(':selected').attr('data-jabatan'));

	$('#KARYAWAN_ID4').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-nik',data.nik);
		$(this).find(':selected').attr('data-posisi',data.posisi);
		$(this).find(':selected').attr('data-jabatan',data.jabatan);
		$("#nik4").val($(this).find(':selected').attr('data-nik'));
		$("#posisi4").val($(this).find(':selected').attr('data-posisi'));
		$("#jabatan4").val($(this).find(':selected').attr('data-jabatan'));
	});

	$('#KARYAWAN_ID5').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-int.php',
			dataType: 'json',
		}
	});

	$('#TEMPLATE_ID5').select2({
		theme: "bootstrap",
		ajax: {
			url: 'template-ac.php',
			dataType: 'json',
		}
	});

	$("#nik5").val($('#KARYAWAN_ID5').find(':selected').attr('data-nik'));
	$("#posisi5").val($('#KARYAWAN_ID5').find(':selected').attr('data-posisi'));
	$("#jabatan5").val($('#KARYAWAN_ID5').find(':selected').attr('data-jabatan'));

	$('#KARYAWAN_ID5').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-nik',data.nik);
		$(this).find(':selected').attr('data-posisi',data.posisi);
		$(this).find(':selected').attr('data-jabatan',data.jabatan);
		$("#nik5").val($(this).find(':selected').attr('data-nik'));
		$("#posisi5").val($(this).find(':selected').attr('data-posisi'));
		$("#jabatan5").val($(this).find(':selected').attr('data-jabatan'));
	});

	$('#KARYAWAN_ID6').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-int.php',
			dataType: 'json',
		}
	});

	$('#TEMPLATE_ID6').select2({
		theme: "bootstrap",
		ajax: {
			url: 'template-ac.php',
			dataType: 'json',
		}
	});

	$("#nik6").val($('#KARYAWAN_ID6').find(':selected').attr('data-nik'));
	$("#posisi6").val($('#KARYAWAN_ID6').find(':selected').attr('data-posisi'));
	$("#jabatan6").val($('#KARYAWAN_ID6').find(':selected').attr('data-jabatan'));

	$('#KARYAWAN_ID6').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-nik',data.nik);
		$(this).find(':selected').attr('data-posisi',data.posisi);
		$(this).find(':selected').attr('data-jabatan',data.jabatan);
		$("#nik6").val($(this).find(':selected').attr('data-nik'));
		$("#posisi6").val($(this).find(':selected').attr('data-posisi'));
		$("#jabatan6").val($(this).find(':selected').attr('data-jabatan'));
	});

	if($('#KEPUTUSAN').val() == 'DIREKOMENDASIKAN POSISI LAIN'){
		$('.posisi').show();
	}else{
		$('.posisi').hide();
	}

	$('#KEPUTUSAN').change(function(){
		if($(this).val() == 'DIREKOMENDASIKAN POSISI LAIN'){
			$('.posisi').show();
		}else{
			$('.posisi').hide();
		}
	});

});

$(function(){
	var LAMARAN_ID = $('#LAMARAN_ID').val();
	var TEMPLATE_ID1 = $('#TEMPLATE_ID1').val();
	$("#tabel_nilai1").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID1+"&INTERVIEWER=1&LAMARAN_ID="+LAMARAN_ID);
	$('#TEMPLATE_ID1').change(function(){
		var TEMPLATE_ID1 = $(this).val();
		$("#tabel_nilai1").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID1+"&INTERVIEWER=1&LAMARAN_ID="+LAMARAN_ID);
	});

	var TEMPLATE_ID2 = $('#TEMPLATE_ID2').val();
	$("#tabel_nilai2").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID2+"&INTERVIEWER=2&LAMARAN_ID="+LAMARAN_ID);
	$('#TEMPLATE_ID2').change(function(){
		var TEMPLATE_ID2 = $(this).val();
		$("#tabel_nilai2").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID2+"&INTERVIEWER=2&LAMARAN_ID="+LAMARAN_ID);
	});

	var TEMPLATE_ID3 = $('#TEMPLATE_ID3').val();
	$("#tabel_nilai3").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID3+"&INTERVIEWER=3&LAMARAN_ID="+LAMARAN_ID);
	$('#TEMPLATE_ID3').change(function(){
		var TEMPLATE_ID3 = $(this).val();
		$("#tabel_nilai3").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID3+"&INTERVIEWER=3&LAMARAN_ID="+LAMARAN_ID);
	});

	var TEMPLATE_ID4 = $('#TEMPLATE_ID4').val();
	$("#tabel_nilai4").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID4+"&INTERVIEWER=4&LAMARAN_ID="+LAMARAN_ID);
	$('#TEMPLATE_ID4').change(function(){
		var TEMPLATE_ID4 = $(this).val();
		$("#tabel_nilai4").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID4+"&INTERVIEWER=4&LAMARAN_ID="+LAMARAN_ID);
	});

	var TEMPLATE_ID5 = $('#TEMPLATE_ID5').val();
	$("#tabel_nilai5").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID5+"&INTERVIEWER=5&LAMARAN_ID="+LAMARAN_ID);
	$('#TEMPLATE_ID5').change(function(){
		var TEMPLATE_ID5 = $(this).val();
		$("#tabel_nilai5").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID5+"&INTERVIEWER=5&LAMARAN_ID="+LAMARAN_ID);
	});

	var TEMPLATE_ID6 = $('#TEMPLATE_ID6').val();
	$("#tabel_nilai6").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID6+"&INTERVIEWER=6&LAMARAN_ID="+LAMARAN_ID);
	$('#TEMPLATE_ID6').change(function(){
		var TEMPLATE_ID6 = $(this).val();
		$("#tabel_nilai6").load("interview-status-nilai.php","TEMPLATE_ID="+TEMPLATE_ID6+"&INTERVIEWER=6&LAMARAN_ID="+LAMARAN_ID);
	});
});
</script>

<?php
include 'footer.php';
?>