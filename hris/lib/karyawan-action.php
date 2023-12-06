<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('karyawan.edit');
	$EDIT = db_first(" SELECT * FROM karyawan WHERE KARYAWAN_ID='$ID' ");
	$JABATAN = db_first("
		SELECT J.JABATAN,P.PROJECT,C.COMPANY
		FROM jabatan J
		LEFT JOIN project P ON P.PROJECT_ID=J.PROJECT_ID
		LEFT JOIN company C ON C.COMPANY_ID=P.COMPANY_ID
		WHERE J.JABATAN_ID='$EDIT->JABATAN_ID'
	");

	// HISTORI STATUS
	$HISTORI_STATUS = db_fetch(" SELECT * FROM histori_status WHERE KARYAWAN_ID='$ID' ");

	// HISTORI JABATAN
	$HISTORI_JABATAN = db_fetch("
		SELECT HJ.TGL,J.JABATAN,P.PROJECT,C.COMPANY
		FROM histori_karir HJ
		LEFT JOIN jabatan J ON J.JABATAN_ID=HJ.JABATAN_ID
		LEFT JOIN project P ON P.PROJECT_ID=J.PROJECT_ID
		LEFT JOIN company C ON C.COMPANY_ID=P.COMPANY_ID
		WHERE HJ.KARYAWAN_ID='$ID'
	");

	// HISTORI GAJI
	$HISTORI_GAJI = db_fetch(" SELECT * FROM histori_gaji WHERE KARYAWAN_ID='$ID' ");
}
if($OP=='delete'){
	is_login('karyawan.delete');
	$IDS = get_input('ids');
	if( is_array($IDS) )
	{
		db_execute(" DELETE FROM karyawan WHERE KARYAWAN_ID IN (".implode(',',$IDS).")");
	}
	header('location: karyawan.php');
	exit;
}

is_login('karyawan.add');
if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('NIK','NAMA');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$allow_ext = array('xls');
	$foto_name = isset($_FILES['FOTO']['name']) ? $_FILES['FOTO']['name'] : '';
	$foto_tmp = isset($_FILES['FOTO']['tmp_name']) ? $_FILES['FOTO']['tmp_name'] : '';
	$foto_ext = strtolower(substr(strrchr($foto_name, "."), 1));
	$foto_new = rand(11111,99999).'_'.$foto_name.'.'.$foto_ext;
	$foto_dest = 'uploads/foto/'.$foto_new;
		
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'NIK','NAMA','TP_LAHIR','TGL_LAHIR','NO_IDENTITAS','NO_KK','KEWARGANEGARAAN','TINGGI_BADAN','BERAT_BADAN','JK','ALAMAT','KELURAHAN','KECAMATAN','KOTA','PROVINSI',
			'KODE_POS','RT','RW','SIM','HP','WA','LULUSAN','JURUSAN','SCAN_IJAZAH','TAHUN_LULUS','PENGALAMAN','AGAMA','GOL_DARAH','TELPON','EMAIL','ST_KAWIN','ST_PEGAWAI',
			/*'TGL_MASUK',*/'TGL_KELUAR','NO_KONTRAK','PENEMPATAN','JENIS','JOBDESC',/*'JABATAN_ID',*/'NPWP','BPJS','FACEBOOK','TWITTER','INSTAGRAM',
			'NAMA_PASANGAN','NO_PASANGAN','PEKERJAAN_PASANGAN','JML_ANAK','IBU_KANDUNG',/*'PPH21','ST_KERJA',*/'KATEGORI_KEAHLIAN_ID','KEAHLIAN_ID','JENIS_THR',
			'PROJECT_ID','COMPANY_ID','TIPE_GAJI'
		);

		$NEW_FOTO = 0;
		if(is_uploaded_file($foto_tmp)){
			if(move_uploaded_file($foto_tmp,$foto_dest)){
				$FIELDS[] = 'FOTO';
				$NEW_FOTO = 1;
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
			}else if($F=='SIM'){
				$sim = implode(',',get_input('SIM'));
				$INSERT_VAL[$F] = "'".db_escape($sim)."'";
				$UPDATE_VAL[$F] = $F."='".db_escape($sim)."'";
			}else{
				$INSERT_VAL[$F] = "'".db_escape(get_input($F))."'";
				$UPDATE_VAL[$F] = $F."='".db_escape(get_input($F))."'";
			}
		}
		
		$JAB = db_first("
			SELECT P.PROJECT_ID,P.COMPANY_ID
			FROM jabatan J
			LEFT JOIN project P ON P.PROJECT_ID=J.PROJECT_ID
			WHERE J.JABATAN_ID='".db_escape(get_input('TMP_JABATAN_ID'))."'
		");
		$PROJECT_ID = isset($JAB->PROJECT_ID) ? $JAB->PROJECT_ID : '';
		$COMPANY_ID = isset($JAB->COMPANY_ID) ? $JAB->COMPANY_ID : '';
		
		$INSERT_VAL['PROJECT_ID'] = "'".$PROJECT_ID."'";
		$UPDATE_VAL['PROJECT_ID'] = "PROJECT_ID='".$PROJECT_ID."'";

		$INSERT_VAL['COMPANY_ID'] = "'".$COMPANY_ID."'";
		$UPDATE_VAL['COMPANY_ID'] = "COMPANY_ID='".$COMPANY_ID."'";
		
		$NEW_F = array(
			'TUNJ_JABATAN','TUNJ_KEAHLIAN','TUNJ_PROYEK','TUNJ_BACKUP','TUNJ_SHIFT','BPJS_JHT','BPJS_JP','BPJS_KES','PPH21'
		);
		foreach($NEW_F as $F){
			$INSERT_VAL[$F] = "'".db_escape(input_currency(get_input($F)))."'";
			$UPDATE_VAL[$F] = $F."='".db_escape(input_currency(get_input($F)))."'";
		}

		// HISTORI STATUS KERJA 
		$HISTORI_KERJA = array('ST_KERJA');
		$ST_KERJA = get_input('ST_KERJA');
		$TGL_ST_KERJA = get_input('TGL_ST_KERJA');
		$KET_ST_KERJA = get_input('KET_ST_KERJA');
		foreach($HISTORI_KERJA as $F){
			if($ST_KERJA != ''){
				$UPDATE_VAL[$F] = $F."='".db_escape(get_input($F))."'";
			}
		}

		// HISTORI KARIR
		$HISTORI_KARIR = array('JABATAN_ID');
		$JABATAN_ID = get_input('JABATAN_ID');
		$TGL_JABATAN = get_input('TGL_JABATAN');
		foreach($HISTORI_KARIR as $F){
			if($JABATAN_ID != ''){
				$UPDATE_VAL[$F] = $F."='".db_escape(get_input($F))."'";
			}
		}

		// HISTORI GAJI
		$HISTORI_GAJI = array('GAJI_POKOK');
		$GAJI_POKOK = input_currency(get_input('GAJI_POKOK'));
		$TGL_GAJI_POKOK = get_input('TGL_GAJI_POKOK');
		$KET_GAJI_POKOK = get_input('KET_GAJI_POKOK');
		foreach($HISTORI_GAJI as $F){
			if($GAJI_POKOK != ''){
				$UPDATE_VAL[$F] = $F."='".db_escape(input_currency(get_input($F)))."'";
			}
		}
	
		if($OP=='' OR $OP=='add')
		{
			is_login('karyawan.add');
			db_execute(" INSERT INTO karyawan (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_ID();
			$DOKUMEN = get_input('DOKUMEN_KARYAWAN');
			if(is_array($DOKUMEN) AND count($DOKUMEN)){
				foreach($DOKUMEN as $key=>$val){
					if($val != ''){
						$EXPIRED_DATE 		= get_input('EXPIRED_DATE');
						$CURR_FILES 		= get_input('CURR_FILES');
						$file_name			= $_FILES['FILES']['name'];
						$file_size			= $_FILES['FILES']['size'];
						$file_tmp			= $_FILES['FILES']['tmp_name'];
						$file_type			= $_FILES['FILES']['type'];
						$KARYAWAN_ID 		= $ID;
						$DOKUMEN_KARYAWAN 	= $val;

						$EXPIRED_DATE		= isset($EXPIRED_DATE[$key]) ? $EXPIRED_DATE[$key] : '';
						$CURR_FILES			= isset($CURR_FILES[$key]) ? $CURR_FILES[$key] : '';
						$FILENAME 			= rand(11111,99999).'_'.$file_name[$key];
						$FILE_TMP 			= $file_tmp[$key];

						$NEW_FILES = 0;
						if(is_uploaded_file($FILE_TMP)){
							if(move_uploaded_file($FILE_TMP,"uploads/karyawan/".$FILENAME)){
								$NEW_FILES = 1;
							}
						}

						if($NEW_FILES=='1'){
							$FILES = $FILENAME;
							
						}else{
							$FILES = $CURR_FILES;
						}

						db_execute(" INSERT INTO dokumen_karyawan (DOKUMEN_KARYAWAN,FILES,EXPIRED_DATE,KARYAWAN_ID) VALUES ('$DOKUMEN_KARYAWAN','$FILES','$EXPIRED_DATE','$ID') ");
					}
				}
			}
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{	
			db_execute(" UPDATE karyawan SET ".implode(',',$UPDATE_VAL)." WHERE KARYAWAN_ID='$ID' ");

			// UPDATE HISTORI STATUS KERJA KARYAWAN
			if($ST_KERJA != ''){
				db_execute(" INSERT INTO histori_status (KARYAWAN_ID,HISTORI_STATUS,KETERANGAN,TGL) VALUES ('$ID','$ST_KERJA','$KET_ST_KERJA','$TGL_ST_KERJA') ");
			}

			// UPDATE HISTORI KARIR(JABATAN) KARYAWAN
			if($JABATAN_ID != ''){
				db_execute(" INSERT INTO histori_karir (KARYAWAN_ID,JABATAN_ID,TGL) VALUES ('$ID','$JABATAN_ID','$TGL_JABATAN') ");
			}

			// UPDATE GAJI
			if($GAJI_POKOK != ''){
				db_execute(" INSERT INTO histori_gaji (KARYAWAN_ID,HISTORI_GAJI,KETERANGAN,TGL) VALUES ('$ID','$GAJI_POKOK','$KET_GAJI_POKOK','$TGL_GAJI_POKOK') ");
			}

			// UPDATE DOKUMEN KARYAWAN
			db_execute(" DELETE FROM dokumen_karyawan WHERE KARYAWAN_ID='$ID' ");
			$DOKUMEN = get_input('DOKUMEN_KARYAWAN');
			if(is_array($DOKUMEN) AND count($DOKUMEN)){
				foreach($DOKUMEN as $key=>$val){
					if($val != ''){
						$ADA_DOKUMEN 		= get_input('ADA_DOKUMEN');
						$EXPIRED_DATE 		= get_input('EXPIRED_DATE');
						$CURR_FILES 		= get_input('CURR_FILES');
						$file_name			= $_FILES['FILES']['name'];
						$file_size			= $_FILES['FILES']['size'];
						$file_tmp			= $_FILES['FILES']['tmp_name'];
						$file_type			= $_FILES['FILES']['type'];
						$KARYAWAN_ID 		= $ID;
						$DOKUMEN_KARYAWAN 	= $val;

						$EXPIRED_DATE		= isset($EXPIRED_DATE[$key]) ? $EXPIRED_DATE[$key] : '';
						$CURR_FILES			= isset($CURR_FILES[$key]) ? $CURR_FILES[$key] : '';
						$FILENAME 			= rand(11111,99999).'_'.$file_name[$key];
						$FILE_TMP 			= $file_tmp[$key];

						$NEW_FILES = 0;
						if(is_uploaded_file($FILE_TMP)){
							if(move_uploaded_file($FILE_TMP,"uploads/karyawan/".$FILENAME)){
								$NEW_FILES = 1;
							}
						}

						if($NEW_FILES=='1'){
							$FILES = $FILENAME;
							
						}else{
							$FILES = $CURR_FILES;
						}

						db_execute(" INSERT INTO dokumen_karyawan (DOKUMEN_KARYAWAN,FILES,ADA_DOKUMEN,EXPIRED_DATE,KARYAWAN_ID) VALUES ('$DOKUMEN_KARYAWAN','$FILES','$ADA_DOKUMEN','$EXPIRED_DATE','$KARYAWAN_ID') ");
					}
				}
			}
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
	}
}

if($EDIT->TAHUN_LULUS == '0000') $EDIT->TAHUN_LULUS = ''; 
if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 style="margin-top:0px;" class="border-title">
		<?php echo ucfirst($OP) ?> Karyawan
		<a href="karyawan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="karyawan-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	
	<?php include 'msg.php' ?>

	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Biodata</a></li>
		<li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Alamat</a></li>
		<li role="presentation"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">Setup</a></li>
		<li role="presentation"><a href="#tab4" aria-controls="tab4" role="tab" data-toggle="tab">Tunjangan</a></li>
		<li role="presentation"><a href="#tab5" aria-controls="tab5" role="tab" data-toggle="tab">Keluarga</a></li>
		<li role="presentation"><a href="#tab6" aria-controls="tab6" role="tab" data-toggle="tab">Socmed</a></li>
		<li role="presentation"><a href="#tab7" aria-controls="tab7" role="tab" data-toggle="tab">Foto</a></li>
		<li role="presentation"><a href="#tab8" aria-controls="tab8" role="tab" data-toggle="tab">Sertifikat Kompetensi</a></li>
		<li role="presentation"><a href="#tab9" aria-controls="tab9" role="tab" data-toggle="tab">Karir</a></li>
		<li role="presentation"><a href="#tab10" aria-controls="tab10" role="tab" data-toggle="tab">Riwayat</a></li>
	</ul>
	
	<form id="form" class="form-horizontal" action="karyawan-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="CURRENT_FOTO" value="<?php echo $EDIT->FOTO ?>">
	<input type="hidden" name="CURRENT_IJAZAH" value="<?php echo $EDIT->IJAZAH ?>">
	<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
	<input type="hidden" name="TMP_JABATAN_ID" value="<?php echo $EDIT->JABATAN_ID ?>">
	
	<div class="tab-content" style="margin-top:20px;">
		<div role="tabpanel" class="tab-pane active" id="tab1">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">NIK</label>
						<div class="col-sm-9">
							<input type="text" name="NIK" value="<?php echo set_value('NIK',$EDIT->NIK) ?>" class="form-control" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Nama</label>
						<div class="col-sm-9">
							<input type="text" name="NAMA" value="<?php echo set_value('NAMA',$EDIT->NAMA) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Tmpt Lahir</label>
						<div class="col-sm-9">
							<input type="text" name="TP_LAHIR" value="<?php echo set_value('TP_LAHIR',$EDIT->TP_LAHIR) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Tgl Lahir</label>
						<div class="col-sm-9">
							<input type="text" name="TGL_LAHIR" value="<?php echo set_value('TGL_LAHIR',$EDIT->TGL_LAHIR) ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No Identitas</label>
						<div class="col-sm-9">
							<input type="text" name="NO_IDENTITAS" value="<?php echo set_value('NO_IDENTITAS',$EDIT->NO_IDENTITAS) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No KK</label>
						<div class="col-sm-9">
							<input type="text" name="NO_KK" value="<?php echo set_value('NO_KK',$EDIT->NO_KK) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Jns Kelamin</label>
						<div class="col-sm-9">
							<?php echo dropdown('JK',array('LAKI-LAKI'=>'LAKI-LAKI','PEREMPUAN'=>'PEREMPUAN'),set_value('JK',$EDIT->JK),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Agama</label>
						<div class="col-sm-9">
							<?php echo dropdown('AGAMA',array('ISLAM'=>'ISLAM','KRISTEN'=>'KRISTEN','KATOLIK'=>'KATOLIK','HINDU'=>'HINDU','BUDHA'=>'BUDHA','KONG HU CHU'=>'KONG HU CHU'),set_value('AGAMA',$EDIT->AGAMA),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Pend Terakhir</label>
						<div class="col-sm-9">
							<input type="text" name="LULUSAN" value="<?php echo set_value('LULUSAN',$EDIT->LULUSAN) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Jurusan</label>
						<div class="col-sm-9">
							<input type="text" name="JURUSAN" value="<?php echo set_value('JURUSAN',$EDIT->JURUSAN) ?>" class="form-control">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="col-sm-3 control-label">Satus Kawin</label>
						<div class="col-sm-9">
							<?php echo dropdown('ST_KAWIN',array('BELUM KAWIN'=>'BELUM KAWIN','KAWIN'=>'KAWIN','JANDA'=>'JANDA','DUDA'=>'DUDA'),set_value('ST_KAWIN',$EDIT->ST_KAWIN),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Tinggi Badan</label>
						<div class="col-sm-3">
							<input type="text" name="TINGGI_BADAN" value="<?php echo set_value('TINGGI_BADAN',$EDIT->TINGGI_BADAN) ?>" class="form-control" style="text-align:center;">
						</div>
						<label for="" class="col-sm-3 control-label">Berat Badan</label>
						<div class="col-sm-3">
							<input type="text" name="BERAT_BADAN" value="<?php echo set_value('BERAT_BADAN',$EDIT->BERAT_BADAN) ?>" class="form-control" style="text-align:center;">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Gol Darah</label>
						<div class="col-sm-9">
							<?php echo dropdown('GOL_DARAH',array('A'=>'A','B'=>'B','AB'=>'AB','O'=>'O'),set_value('GOL_DARAH',$EDIT->GOL_DARAH),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Telpon</label>
						<div class="col-sm-9">
							<input type="text" name="TELPON" value="<?php echo set_value('TELPON',$EDIT->TELPON) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No HP</label>
						<div class="col-sm-9">
							<input type="text" name="HP" value="<?php echo set_value('HP',$EDIT->HP) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No WA</label>
						<div class="col-sm-9">
							<input type="text" name="WA" value="<?php echo set_value('WA',$EDIT->WA) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Email</label>
						<div class="col-sm-9">
							<input type="text" name="EMAIL" value="<?php echo set_value('EMAIL',$EDIT->EMAIL) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Scan Ijazah</label>
						<div class="col-sm-9">
							<?php echo dropdown('SCAN_IJAZAH',array('Scan Asli'=>'Scan Asli','Scan Copy'=>'Scan Copy'),set_value('SCAN_IJAZAH',$EDIT->SCAN_IJAZAH),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Tahun Lulus</label>
						<div class="col-sm-9">
							<input type="text" name="TAHUN_LULUS" value="<?php echo set_value('TAHUN_LULUS',$EDIT->TAHUN_LULUS) ?>" class="form-control dateyear">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Pengalaman Kerja</label>
						<div class="col-sm-3">
							<input type="number" name="PENGALAMAN" value="<?php echo set_value('PENGALAMAN',$EDIT->PENGALAMAN) ?>" class="form-control">
						</div>
						<label for="" class="col-sm-3 control-label" style="text-align: left !important;">Tahun</label>
					</div>
				</div>
			</div> <!-- end row -->
		</div>
		
		<!-- Start tab 2 -->
		<div role="tabpanel" class="tab-pane" id="tab2">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Alamat</label>
						<div class="col-sm-9">
							<input type="text" name="ALAMAT" value="<?php echo set_value('ALAMAT',$EDIT->ALAMAT) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Kelurahan</label>
						<div class="col-sm-9">
							<input type="text" name="KELURAHAN" value="<?php echo set_value('KELURAHAN',$EDIT->KELURAHAN) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Kecamatan</label>
						<div class="col-sm-9">
							<input type="text" name="KECAMATAN" value="<?php echo set_value('KECAMATAN',$EDIT->KECAMATAN) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Provinsi</label>
						<div class="col-sm-9">
							<input type="text" name="PROVINSI" value="<?php echo set_value('PROVINSI',$EDIT->PROVINSI) ?>" class="form-control">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="col-sm-3 control-label">Warga Negara</label>
						<div class="col-sm-9">
							<?php echo dropdown('KEWARGANEGARAAN',array('WNI'=>'WNI','WNA'=>'WNA'),set_value('KEWARGANEGARAAN',$EDIT->KEWARGANEGARAAN),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Kota</label>
						<div class="col-sm-9">
							<input type="text" name="KOTA" value="<?php echo set_value('KOTA',$EDIT->KOTA) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Kode Pos</label>
						<div class="col-sm-3">
							<input type="text" name="KODE_POS" value="<?php echo set_value('KODE_POS',$EDIT->KODE_POS) ?>" class="form-control" style="text-align:center;">
						</div>
						<label for="" class="col-sm-1 control-label">RT</label>
						<div class="col-sm-2">
							<input type="text" name="RT" value="<?php echo set_value('RT',$EDIT->RT) ?>" class="form-control" style="text-align:center;">
						</div>
						<label for="" class="col-sm-1 control-label">RW</label>
						<div class="col-sm-2">
							<input type="text" name="RW" value="<?php echo set_value('RW',$EDIT->RW) ?>" class="form-control" style="text-align:center;">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End tab 2 -->

		<!-- Start tab 3 -->
		<div role="tabpanel" class="tab-pane" id="tab3">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No Kontrak</label>
						<div class="col-sm-9">
							<input type="text" name="NO_KONTRAK" value="<?php echo set_value('NO_KONTRAK',$EDIT->NO_KONTRAK) ?>" class="form-control">
						</div>
					</div>
					<?php /*
					<div class="form-group">
						<label class="col-sm-3 control-label">Company</label>
						<div class="col-sm-9">
							<?php echo dropdown('COMPANY_ID',dropdown_option('company','COMPANY_ID','COMPANY','ORDER BY COMPANY ASC'),set_value('COMPANY_ID',$EDIT->COMPANY_ID),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Groups</label>
						<div class="col-sm-9">
							<?php echo dropdown('GROUPS_ID',dropdown_option('groups','GROUPS_ID','GROUPS','ORDER BY GROUPS ASC'),set_value('GROUPS_ID',$EDIT->GROUPS_ID),' class="form-control" ') ?>
						</div>
					</div>
					*/ ?>
					<div class="form-group">
						<label class="col-sm-3 control-label">Jenis</label>
						<div class="col-sm-9">
							<?php echo dropdown('JENIS',array('TETAP'=>'TETAP','KONTRAK'=>'KONTRAK'),set_value('JENIS',$EDIT->JENIS),' class="form-control" id="jenis"') ?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Penempatan</label>
						<div class="col-sm-9">
							<input type="text" name="PENEMPATAN" value="<?php echo set_value('PENEMPATAN',$EDIT->PENEMPATAN) ?>" class="form-control">
						</div>
					</div>
					<?php /*
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Tgl Masuk</label>
						<div class="col-sm-9">
							<input type="text" name="TGL_MASUK" value="<?php echo set_value('TGL_MASUK',$EDIT->TGL_MASUK) ?>" class="form-control datepicker">
						</div>
					</div>
					*/ ?>
					<div class="form-group" id="tgl_keluar">
						<label for="" class="col-sm-3 control-label">Tgl Keluar</label>
						<div class="col-sm-9">
							<input type="text" name="TGL_KELUAR" value="<?php echo set_value('TGL_KELUAR',cdate($EDIT->TGL_KELUAR)) ?>" class="form-control datepicker">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Tipe</label>
						<div class="col-sm-9">
							<?php echo dropdown('TIPE',array('MANAJEMEN'=>'MANAJEMEN','NON MANAJEMEN'=>'NON MANAJEMEN'),set_value('TIPE',$EDIT->TIPE),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Status PTKP</label>
						<div class="col-sm-9">
							<?php echo dropdown('TIPE',array('TK/0'=>'TK/0','TK/1'=>'TK/1','TK/2'=>'TK/2','TK/3'=>'TK/3','K/0'=>'K/0','K/1'=>'K/1','K/2'=>'K/2','K/3'=>'K/3','K/4'=>'K/4','HB/0'=>'HB/0','HB/1'=>'HB/1','HB/2'=>'HB/2','HB/3'=>'HB/3'),set_value('TIPE',$EDIT->TIPE),' class="form-control" ') ?>
						</div>
					</div>
					<?php /*
					<div class="form-group">
						<label class="col-sm-3 control-label">Jabatan</label>
						<div class="col-sm-9">
							<?php echo dropdown('JABATAN_ID',dropdown_option('jabatan','JABATAN_ID','JABATAN','ORDER BY JABATAN ASC'),set_value('JABATAN_ID',$EDIT->JABATAN_ID),' class="form-control" ') ?>
						</div>
					</div>
					*/ ?>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No NPWP</label>
						<div class="col-sm-9">
							<input type="text" name="NPWP" value="<?php echo set_value('NPWP',$EDIT->NPWP) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No BPJS</label>
						<div class="col-sm-9">
							<input type="text" name="BPJS" value="<?php echo set_value('BPJS',$EDIT->BPJS) ?>" class="form-control">
						</div>
					</div>

				</div>
				<div class="col-md-6">
					<?php /*
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Gaji Pokok</label>
						<div class="col-sm-9">
							<input type="text" name="GAJI_POKOK" value="<?php echo set_value('GAJI_POKOK',$EDIT->GAJI_POKOK) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					*/ ?>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">BPJS JHT</label>
						<div class="col-sm-9">
							<input type="text" name="BPJS_JHT" value="<?php echo set_value('BPJS_JHT',$EDIT->BPJS_JHT) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">BPJS JP</label>
						<div class="col-sm-9">
							<input type="text" name="BPJS_JP" value="<?php echo set_value('BPJS_JP',$EDIT->BPJS_JP) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">BPJS KES</label>
						<div class="col-sm-9">
							<input type="text" name="BPJS_KES" value="<?php echo set_value('BPJS_KES',$EDIT->BPJS_KES) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<?php /*<div class="form-group">
						<label for="" class="col-sm-3 control-label">PPH 21</label>
						<div class="col-sm-9">
							<input type="text" name="PPH21" value="<?php echo set_value('PPH21',$EDIT->PPH21) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>*/ ?>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Nama Bank</label>
						<div class="col-sm-9">
							<input type="text" name="NAMA_BANK" value="<?php echo set_value('NAMA_BANK',$EDIT->NAMA_BANK) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Nama Akun</label>
						<div class="col-sm-9">
							<input type="text" name="NAMA_AKUN" value="<?php echo set_value('NAMA_AKUN',$EDIT->NAMA_AKUN) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No Rekening</label>
						<div class="col-sm-9">
							<input type="text" name="NO_REKENING" value="<?php echo set_value('NO_REKENING',$EDIT->NO_REKENING) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Deskripsi Pekerjaan</label>
						<div class="col-sm-9">
							<textarea class="form-control" rows="4" name="JOBDESC"><?php echo isset($EDIT->JOBDESC) ? $EDIT->JOBDESC : '' ?></textarea>
						</div>
					</div>
				</div>
			</div> <!-- end row -->
		</div>
		<!-- End tab 3 -->

		<!-- Start tab 4 -->
		<div role="tabpanel" class="tab-pane" id="tab4">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Tunj Jabatan</label>
						<div class="col-sm-8">
							<input type="text" name="TUNJ_JABATAN" value="<?php echo set_value('TUNJ_JABATAN',$EDIT->TUNJ_JABATAN) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Tunj Keahlian</label>
						<div class="col-sm-8">
							<input type="text" name="TUNJ_KEAHLIAN" value="<?php echo set_value('TUNJ_KEAHLIAN',$EDIT->TUNJ_KEAHLIAN) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Tunj Proyek</label>
						<div class="col-sm-8">
							<input type="text" name="TUNJ_PROYEK" value="<?php echo set_value('TUNJ_PROYEK',$EDIT->TUNJ_PROYEK) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Backup</label>
						<div class="col-sm-8">
							<input type="text" name="TUNJ_BACKUP" value="<?php echo set_value('TUNJ_BACKUP',$EDIT->TUNJ_BACKUP) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Tunj Shift</label>
						<div class="col-sm-8">
							<input type="text" name="TUNJ_SHIFT" value="<?php echo set_value('TUNJ_SHIFT',$EDIT->TUNJ_SHIFT) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-4 control-label">Insentif Kehadiran</label>
						<div class="col-sm-8">
							<input type="text" name="TUNJ_KEHADIRAN" value="<?php echo set_value('TUNJ_KEHADIRAN',$EDIT->TUNJ_KEHADIRAN) ?>" class="form-control currency" maxlength="20">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Jenis THR</label>
						<div class="col-sm-8">
							<?php echo dropdown('JENIS_THR',array('IDUL FITRI'=>'IDUL FITRI','KUNINGAN'=>'KUNINGAN'),set_value('JENIS_THR',$EDIT->JENIS_THR),' class="form-control" ') ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Tipe Gaji</label>
						<div class="col-sm-8">
							<?php echo dropdown('TIPE_GAJI',array('MIDDLE'=>'MIDDLE','END'=>'END'),set_value('TIPE_GAJI',$EDIT->TIPE_GAJI),' class="form-control" ') ?>
						</div>
					</div>
				</div>
			</div> <!-- end row -->
		</div>
		<!-- End tab 4 -->
		
		<!-- Start tab 5 -->
		<div role="tabpanel" class="tab-pane" id="tab5">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Suami/Istri</label>
						<div class="col-sm-9">
							<input type="text" name="NAMA_PASANGAN" value="<?php echo set_value('NAMA_PASANGAN',$EDIT->NAMA_PASANGAN) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">No. Identitas</label>
						<div class="col-sm-9">
							<input type="text" name="NO_PASANGAN" value="<?php echo set_value('NO_PASANGAN',$EDIT->NO_PASANGAN) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Pekerjaan</label>
						<div class="col-sm-9">
							<input type="text" name="PEKERJAAN_PASANGAN" value="<?php echo set_value('PEKERJAAN_PASANGAN',$EDIT->PEKERJAAN_PASANGAN) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Jumlah Anak</label>
						<div class="col-sm-9">
							<input type="text" name="JML_ANAK" value="<?php echo set_value('JML_ANAK',$EDIT->JML_ANAK) ?>" class="form-control">
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Ibu Kandung</label>
						<div class="col-sm-9">
							<input type="text" name="IBU_KANDUNG" value="<?php echo set_value('IBU_KANDUNG',$EDIT->IBU_KANDUNG) ?>" class="form-control">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End tab 5 -->
		
		<!-- Start tab 6 -->
		<div role="tabpanel" class="tab-pane" id="tab6">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Facebook</label>
						<div class="col-sm-9">
							<input type="text" name="FACEBOOK" value="<?php echo set_value('FACEBOOK',$EDIT->FACEBOOK) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Twitter</label>
						<div class="col-sm-9">
							<input type="text" name="TWITTER" value="<?php echo set_value('TWITTER',$EDIT->TWITTER) ?>" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Instagram</label>
						<div class="col-sm-9">
							<input type="text" name="INSTAGRAM" value="<?php echo set_value('INSTAGRAM',$EDIT->INSTAGRAM) ?>" class="form-control">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End tab 6 -->
		
		<!-- Start tab 7 -->
		<div role="tabpanel" class="tab-pane" id="tab7">
			<div class="row">
				<div class="col-md-6">
					<div class="col-md-4">
					<div class="form-group">
							<?php if(!empty($EDIT->FOTO) AND url_exists(base_url().'uploads/foto/'.$EDIT->FOTO)){ ?>
							<img src="<?php echo base_url().'uploads/foto/'.$EDIT->FOTO; ?>" alt="" class="img-thumbnail" style="width:300px;margin:0 auto 10px;">
							<?php } ?>
							<input type="file" name="FOTO" class="form-control">
					</div>
				</div>
				</div>
			</div>
		</div>
		<!-- End tab 7 -->

		<!-- Start tab 8 -->
		<div role="tabpanel" class="tab-pane" id="tab8">
			<div class="row" style="margin-bottom: 40px;">
				<div class="col-md-6">
					<div class="form-group">
						<label class="col-sm-3 control-label">Kategori Keahlian</label>
						<div class="col-sm-9">
							<?php echo dropdown('KATEGORI_KEAHLIAN_ID',dropdown_option('kategori_keahlian','KATEGORI_KEAHLIAN_ID','KATEGORI_KEAHLIAN','ORDER BY KATEGORI_KEAHLIAN ASC'),set_value('KATEGORI_KEAHLIAN_ID',$EDIT->KATEGORI_KEAHLIAN_ID),' class="form-control" id="kat_kh"') ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label">Keahlian</label>
						<div class="col-sm-9">
							<?php $KAT_KH = 1; ?>
							<?php echo dropdown('KEAHLIAN_ID',dropdown_option('keahlian','KEAHLIAN_ID','KEAHLIAN','WHERE KATEGORI_KEAHLIAN_ID='.$KAT_KH.' ORDER BY KEAHLIAN ASC'),set_value('KEAHLIAN_ID',$EDIT->KEAHLIAN_ID),' class="form-control" ') ?>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-2 control-label">
					<a href="#" class="btn btn-info" id="btn-add-file"><span class="glyphicon glyphicon-plus"></span>&nbsp;Tambah Dokumen</a>
				</label>
				<div class="col-sm-10">
					<label for="" class="col-sm-4 control-label" style="text-align: center;">
						Jenis Training/Kompetensi
					</label>
					<label for="" class="col-sm-2 control-label" style="text-align: left;">
						Ada Sertifiat
					</label>
					<label for="" class="col-sm-2 control-label" style="text-align: left;">
						Masa Berlaku
					</label>
					<label for="" class="col-sm-4 control-label" style="text-align: left;">
						Upload File
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-2 control-label">
				</label>
				<div class="col-sm-10">
					<div id="upload-file">
					<?php $DOKUMEN = db_fetch("SELECT * FROM dokumen_karyawan WHERE KARYAWAN_ID='$ID'");
					if(count($DOKUMEN) > 0){ foreach ($DOKUMEN as $key => $row) { ?>
					<div class="input-group" style="padding-top: 15px;">
						<input type="hidden" name="CURR_FILES[]" value="<?php echo $row->FILES ?>">
						<div class="col-sm-4">
							<input type="text" name="DOKUMEN_KARYAWAN[]" value="<?php echo $row->DOKUMEN_KARYAWAN ?>" class="form-control">
						</div>
						<div class="col-sm-2">
							<?php echo dropdown('ADA_DOKUMEN',array('ADA'=>'ADA','TIDAK'=>'TIDAK'),set_value('ADA_DOKUMEN',$EDIT->ADA_DOKUMEN),' class="form-control" ') ?>
						</div>
						<div class="col-sm-2">
							<input type="text" name="EXPIRED_DATE[]" value="<?php echo $row->EXPIRED_DATE ?>" class="form-control dateyear">
						</div>
						<div class="col-sm-4">
							<input type="file" name="FILES[]" value="" class="form-control">
						</div>

						<span class="input-group-btn">
							<a type="button" class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/karyawan/".$row->FILES ?>" title='Download File: <?php echo $row->DOKUMEN_KARYAWAN ?>' download>
								<span class="glyphicon glyphicon-download"></span>
							</a>
						</span>
						
						<span class="input-group-btn">
							<button type="button" class="btn btn-danger btn-flat del-file" title="Delete File">
								<span class="glyphicon glyphicon-trash btn-danger"></span>
							</button>
						</span>
					</div>	
					<?php }} ?>
					</div>
					
				</div>
			</div>
		</div>
		<!-- End tab 8 -->

		<!-- Start tab 9 -->
		<div role="tabpanel" class="tab-pane" id="tab9">
			<div class="row">
				<div class="col-md-12">
					<?php /* STATUS KERJA */ ?>
					<div class="form-group">
						<label for="" class="col-sm-1 control-label">Status Kerja</label>
						<div class="col-sm-2">
							<?php echo dropdown('ST_KERJA_INFO',array('AKTIF'=>'AKTIF','RESIGN'=>'RESIGN','PENSIUN'=>'PENSIUN'),set_value('ST_KERJA_INFO',$EDIT->ST_KERJA),' class="form-control" disabled') ?>
						</div>
						<?php if($EDIT->ST_KERJA == 'AKTIF'){ ?>
							<div class="col-sm-2">
							<?php echo dropdown('ST_KERJA',array(''=>'--STATUS BARU--','RESIGN'=>'RESIGN','PENSIUN'=>'PENSIUN'),set_value('ST_KERJA',''),' class="form-control"') ?>
							</div>
						<?php }elseif($EDIT->ST_KERJA == 'RESIGN') { ?>
							<div class="col-sm-2">
							<?php echo dropdown('ST_KERJA',array(''=>'--STATUS BARU--','AKTIF'=>'AKTIF','PENSIUN'=>'PENSIUN'),set_value('ST_KERJA',''),' class="form-control"') ?>
							</div>
						<?php } ?>
						<div class="col-sm-3">
							<input type="text" name="TGL_ST_KERJA" value="" class="form-control datepicker" placeholder="Tanggal">
						</div>
						<div class="col-sm-4">
							<input type="text" name="KET_ST_KERJA" value="" class="form-control" placeholder="Keterangan">
						</div>
					</div>

					<?php /* JENJANG KARIR */ ?>
					<div class="form-group">
						<label for="" class="col-sm-1 control-label">Jenjang Karir</label>
						<div class="col-sm-2">
							<?php echo dropdown('JABATAN_ID_INFO',dropdown_option('jabatan','JABATAN_ID','JABATAN','ORDER BY JABATAN ASC'),set_value('JABATAN_ID_INFO',$EDIT->JABATAN_ID),' class="form-control" disabled') ?>
						</div>
						<div class="col-sm-2">
							<input type="text" name="PROJECT_INFO" value="<?php echo set_value('PROJECT_INFO',$JABATAN->PROJECT) ?>" class="form-control" disabled>
						</div>
						<div class="col-sm-3">
							<input type="text" name="COMPANY_INFO" value="<?php echo set_value('COMPANY_INFO',$JABATAN->COMPANY) ?>" class="form-control" disabled>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-1 control-label"></label>
						<div class="col-sm-2">
							<?php echo dropdown('JABATAN_ID',dropdown_option_default('jabatan','JABATAN_ID','JABATAN','ORDER BY JABATAN ASC','-- JABATAN BARU --'),'',' class="form-control"') ?>
						</div>
						<div class="col-sm-2">
							<input type="text" name="PROJECT_INFO" value="" class="form-control" placeholder="Unit" disabled>
						</div>
						<div class="col-sm-3">
							<input type="text" name="COMPANY_INFO" value="" class="form-control" placeholder="Perusahaan" disabled>
						</div>
						<div class="col-sm-2">
							<input type="text" name="TGL_JABATAN" value="" class="form-control datepicker" placeholder="Tanggal">
						</div>
					</div>

					<?php /* GAJI  */ ?>
					<div class="form-group">
						<label for="" class="col-sm-1 control-label">Jenjang Gaji</label>
						<div class="col-sm-2">
							<input type="text" name="GAJI_POKOK_INFO" value="<?php echo set_value('GAJI_POKOK_INFO',$EDIT->GAJI_POKOK) ?>" class="form-control currency" maxlength="20" disabled>
						</div>
						<div class="col-sm-2">
							<input type="text" name="GAJI_POKOK" value="" class="form-control currency" maxlength="20" placeholder="Gaji Baru">
						</div>
						<div class="col-sm-3">
							<input type="text" name="TGL_GAJI_POKOK" value="" class="form-control datepicker" placeholder="Tanggal">
						</div>
						<div class="col-sm-4">
							<input type="text" name="KET_GAJI_POKOK" value="" class="form-control" placeholder="Keterangan">
						</div>
					</div>

				</div>
			</div>
		</div>
		<!-- End tab 9 -->

		<!-- Start tab 10 -->
		<div role="tabpanel" class="tab-pane" id="tab10">
			<div class="row">
				<div class="col-md-3">
					<ul class="list-group">
						<a href="#" class="list-group-item <?php //echo isset($M['notif']) ? 'active' : '' ?>">
							Status Kerja
						</a>
						<a href="#" class="list-group-item">
							Jenjang Karir
						</a>
						<a href="#" class="list-group-item">
							Gaji
						</a>
						<?php /*
						<a href="#" class="list-group-item">
							Surat Peringatan
						</a>
						*/ ?>
					</ul>
				</div>
				<div class="col-md-9">
					<div class="panel panel-default">
						<div class="panel-heading">Status Kerja</div>
						<div class="panel-body entry">
							<div class="table-responsive">
							<table class="table table-hover table-striped" style="border-bottom:2px solid #cccccc;">
							<thead>
							<tr>
								<th style="width:10%;text-align:center;">NO</th>
								<th>NAMA</th>
								<th style="width:10%;text-align:center;">TANGGAL</th>
								<th style="width:10%;text-align:center;">STATUS</th>
								<th>KETERANGAN</th>
							</tr>
							</thead>
							<tbody>
							<?php $NO = 0; if(count($HISTORI_STATUS)>0){ foreach($HISTORI_STATUS as $row){ $NO=$NO+1; ?>
							<tr>
								<td class="text-center"><?php echo $NO ?></td>
								<td><?php echo $EDIT->NAMA ?></td>
								<td><?php echo tgl($row->TGL) ?></td>
								<td class="text-center"><?php echo $row->HISTORI_STATUS ?></td>
								<td><?php echo $row->KETERANGAN ?></td>
							</tr>
							<?php }}else{ ?>
							<tr>
								<td colspan="5" style="text-align:center;color:#0000ff;">Data Kosong</td>
							</tr>
							<?php } ?>
							</tbody>
							</table>
							</div>
						</div>

						<div class="panel-heading">Jenjang Karir</div>
						<div class="panel-body entry">
							<div class="table-responsive">
							<table class="table table-hover table-striped" style="border-bottom:2px solid #cccccc;">
							<thead>
							<tr>
								<th style="width:10%;text-align:center;">NO</th>
								<th>NAMA</th>
								<th>JABATAN</th>
								<th>PROJECT</th>
								<th>PERUSAHAAN</th>
								<th style="width:10%;text-align:center;">TANGGAL</th>
							</tr>
							</thead>
							<tbody>
							<?php $NO = 0; if(count($HISTORI_JABATAN)>0){ foreach($HISTORI_JABATAN as $row){ $NO=$NO+1; ?>
							<tr>
								<td class="text-center"><?php echo $NO ?></td>
								<td><?php echo $EDIT->NAMA ?></td>
								<td><?php echo $row->JABATAN ?></td>
								<td><?php echo $row->PROJECT ?></td>
								<td><?php echo $row->COMPANY ?></td>
								<td class="text-center"><?php echo tgl($row->TGL) ?></td>
							</tr>
							<?php }}else{ ?>
							<tr>
								<td colspan="6" style="text-align:center;color:#0000ff;">Data Kosong</td>
							</tr>
							<?php } ?>
							</tbody>
							</table>
							</div>
						</div>

						<div class="panel-heading">Gaji</div>
						<div class="panel-body entry">
							<div class="table-responsive">
							<table class="table table-hover table-striped" style="border-bottom:2px solid #cccccc;">
							<thead>
							<tr>
								<th style="width:10%;text-align:center;">NO</th>
								<th>NAMA</th>
								<th>GAJI</th>
								<th style="width:10%;text-align:center;">TANGGAL</th>
								<th>KETERANGAN</th>
							</tr>
							</thead>
							<tbody>
							<?php $NO = 0; if(count($HISTORI_GAJI)>0){ foreach($HISTORI_GAJI as $row){ $NO=$NO+1; ?>
							<tr>
								<td class="text-center"><?php echo $NO ?></td>
								<td><?php echo $EDIT->NAMA ?></td>
								<td><?php echo currency($row->HISTORI_GAJI) ?></td>
								<td><?php echo tgl($row->TGL) ?></td>
								<td><?php echo $row->KETERANGAN ?></td>
							</tr>
							<?php }}else{ ?>
							<tr>
								<td colspan="5" style="text-align:center;color:#0000ff;">Data Kosong</td>
							</tr>
							<?php } ?>
							</tbody>
							</table>
							</div>
						</div>
						<?php /*
						<div class="panel-heading">Surat Peringatan</div>
						<div class="panel-body entry">
							<div class="table-responsive">
							<table class="table table-hover table-striped" style="border-bottom:2px solid #cccccc;">
							<thead>
							<tr>
								<th style="width:10%;text-align:center;">NO</th>
								<th style="">NAMA</th>
								<th style="">JENIS SP</th>
								<th style="width:10%;text-align:center;">TANGGAL</th>
								<th style="">KETERANGAN</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td colspan="5" style="text-align:center;color:#0000ff;">Data Kosong</td>
							</tr>
							</tbody>
							</table>
							</div>
						</div>
						*/?>
					</div>
				</div>
			</div>
		</div>
		
	</div><!-- End Tab -->
	
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
	FIND_PROVINSI();
	FIND_KOTA();
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		$('.currency').mask('000,000,000,000,000', {reverse: true});
		mask();
	})

	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		orientation: 'bottom'
	});

	$(".dateyear").datepicker({
		format: "yyyy",
		viewMode: "years", 
		minViewMode: "years",
		autoclose: true
	});

	$('#kat_kh').on('change', function() {
  		<?php $KAT_KH = '$("#kat_kh").val()'; ?>
	});

	delete_file();
});

$(function(){
	if($('#jenis').val() == 'KONTRAK'){
		$('#tgl_keluar').show(); 
	}else{
		$('#tgl_keluar').hide(); 
	}
	//$('#tgl_keluar').hide(); 
	$('#jenis').change(function(){
		if($('#jenis').val() == 'KONTRAK'){
			$('#tgl_keluar').show(); 
		}else{
			$('#tgl_keluar').hide(); 
		}
	});

	$('#btn-add-file').click(function(i){
		$('#upload-file').append('<div class="input-group" style="padding-top:15px;"><div class="col-sm-4"><input type="text" name="DOKUMEN_KARYAWAN[]" value="" class="form-control" placeholder="Jenis Training/Kompetensi"></div><div class="col-sm-2"><select name="ADA_DOKUMEN[]" class="form-control"><option value="ADA">ADA</option><option value="TIDAK">TIDAK</option></select></div><div class="col-sm-2"><input type="text" name="EXPIRED_DATE[]" value="" class="form-control dateyear" placeholder="Masa Berlaku"></div><div class="col-sm-4"><input type="file" name="FILES[]" value="" class="form-control"></div><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-file" title="Delete File"><span class="glyphicon glyphicon-trash btn-danger"></span></button></span></div>');

		$(".dateyear").datepicker({
			format: "yyyy",
			viewMode: "years", 
			minViewMode: "years",
			autoclose: true
		});
		return false;
	});

});

function delete_file(){
	$('#upload-file').on('click', '.del-file', function(){
		$(this).closest('div').remove();
	});
}

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