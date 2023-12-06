<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURRENT_ID = get_input('CURRENT_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('lamaran.edit');
	$EDIT = db_first(" SELECT * FROM calon_karyawan C LEFT JOIN lamaran L ON (L.CALON_KARYAWAN_ID=C.CALON_KARYAWAN_ID) WHERE C.CALON_KARYAWAN_ID='$ID' ");
}

if($OP=='delete'){
	is_login('lamaran.delete');
	db_execute(" DELETE FROM calon_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
	db_execute(" DELETE FROM lamaran WHERE CALON_KARYAWAN_ID='$ID' ");
	header('location: lamaran.php');
	exit;
}
is_login('lamaran.add');
if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{

	$UPDATE_TYPE = get_input('UPDATE_TYPE');

	if($UPDATE_TYPE == 'DATA_PELAMAR'){
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

		$fcktp_allow_ext = array('png','jpg','pdf');
		$fcktp_name = isset($_FILES['FC_KTP']['name']) ? $_FILES['FC_KTP']['name'] : '';
		$fcktp_tmp = isset($_FILES['FC_KTP']['tmp_name']) ? $_FILES['FC_KTP']['tmp_name'] : '';
		$fcktp_ext = strtolower(substr(strrchr($fcktp_name, "."), 1));
		$fcktp_new = rand(11111,99999).'_'.$fcktp_name;
		$fcktp_dest = 'uploads/cv/'.$fcktp_new;

		$fcnpwp_allow_ext = array('png','jpg','pdf');
		$fcnpwp_name = isset($_FILES['FC_NPWP']['name']) ? $_FILES['FC_NPWP']['name'] : '';
		$fcnpwp_tmp = isset($_FILES['FC_NPWP']['tmp_name']) ? $_FILES['FC_NPWP']['tmp_name'] : '';
		$fcnpwp_ext = strtolower(substr(strrchr($fcnpwp_name, "."), 1));
		$fcnpwp_new = rand(11111,99999).'_'.$fcnpwp_name;
		$fcnpwp_dest = 'uploads/cv/'.$fcnpwp_new;

		$fcbpjs_kes_allow_ext = array('png','jpg','pdf');
		$fcbpjs_kes_name = isset($_FILES['FC_BPJS_KESEHATAN']['name']) ? $_FILES['FC_BPJS_KESEHATAN']['name'] : '';
		$fcbpjs_kes_tmp = isset($_FILES['FC_BPJS_KESEHATAN']['tmp_name']) ? $_FILES['FC_BPJS_KESEHATAN']['tmp_name'] : '';
		$fcbpjs_kes_ext = strtolower(substr(strrchr($fcbpjs_kes_name, "."), 1));
		$fcbpjs_kes_new = rand(11111,99999).'_'.$fcbpjs_kes_name;
		$fcbpjs_kes_dest = 'uploads/cv/'.$fcbpjs_kes_new;

		$fcbpjs_ket_allow_ext = array('png','jpg','pdf');
		$fcbpjs_ket_name = isset($_FILES['FC_BPJS_KETENAGAKERJAAN']['name']) ? $_FILES['FC_BPJS_KETENAGAKERJAAN']['name'] : '';
		$fcbpjs_ket_tmp = isset($_FILES['FC_BPJS_KETENAGAKERJAAN']['tmp_name']) ? $_FILES['FC_BPJS_KETENAGAKERJAAN']['tmp_name'] : '';
		$fcbpjs_ket_ext = strtolower(substr(strrchr($fcbpjs_ket_name, "."), 1));
		$fcbpjs_ket_new = rand(11111,99999).'_'.$fcbpjs_ket_name;
		$fcbpjs_ket_dest = 'uploads/cv/'.$fcbpjs_ket_new;

		$fcbpjs_jp_allow_ext = array('png','jpg','pdf');
		$fcbpjs_jp_name = isset($_FILES['FC_BPJS_JAMINANPENSIUN']['name']) ? $_FILES['FC_BPJS_JAMINANPENSIUN']['name'] : '';
		$fcbpjs_jp_tmp = isset($_FILES['FC_BPJS_JAMINANPENSIUN']['tmp_name']) ? $_FILES['FC_BPJS_JAMINANPENSIUN']['tmp_name'] : '';
		$fcbpjs_jp_ext = strtolower(substr(strrchr($fcbpjs_jp_name, "."), 1));
		$fcbpjs_jp_new = rand(11111,99999).'_'.$fcbpjs_jp_name;
		$fcbpjs_jp_dest = 'uploads/cv/'.$fcbpjs_jp_new;
			
		$REQUIRE = array('NAMA');
		$ERROR_REQUIRE = 0;	
		foreach($REQUIRE as $REQ){
			$IREQ = get_input($REQ);
			if($IREQ == "") $ERROR_REQUIRE = 1;
		}
		$ERROR = array();
		if( $ERROR_REQUIRE ){
			$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
		}else if( (get_input('EMAIL') != $EDIT->EMAIL) AND db_exists(" SELECT 1 FROM calon_karyawan WHERE EMAIL='".db_escape(get_input('EMAIL'))."' ") ){
			$ERROR[] = 'Email sudah terdaftar, silakan gunakan email lain';
		}else if( ! valid_email(get_input('EMAIL'))){
			$ERROR[] = 'Email tidak benar, contoh : john_doe@host.com';
		}

		/*else if( get_input('CURRENT_FOTO')!='' OR is_uploaded_file($foto_tmp) ){
			if( ! in_array($foto_ext,$foto_allow_ext) ){
				$ERROR[] = 'Foto harus bertipe jpg atau png';
			}
		}
		else if( get_input('CURRENT_IJAZAH')!='' OR is_uploaded_file($ijazah_tmp) ){
			if( ! in_array($ijazah_ext,$ijazah_allow_ext) ){
				$ERROR[] = 'Ijazah harus bertipe jpg, png, atau pdf';
			}
		}
		else if( get_input('CURRENT_CV')!='' OR is_uploaded_file($cv_tmp) ){
			if( ! in_array($cv_ext,$cv_allow_ext) ){
				$ERROR[] = 'CV harus bertipe doc,docx atau pdf';
			}
		}
		else if( get_input('CURRENT_FCKTP')!='' OR is_uploaded_file($fcktp_tmp) ){
			if( ! in_array($fcktp_ext,$fcktp_allow_ext) ){
				$ERROR[] = 'Scan E-ktp harus bertipe bertipe jpg, png, atau pdf';
			}
		}
		else if( get_input('CURRENT_FCNPWP')!='' OR is_uploaded_file($fcnpwp_tmp) ){
			if( ! in_array($fcnpwp_ext,$fcnpwp_allow_ext) ){
				$ERROR[] = 'Scan NPWP harus bertipe jpg, png, atau pdf';
			}
		}
		else if( get_input('CURRENT_FCBPJS')!='' OR is_uploaded_file($fcbpjs_tmp) ){
			if( ! in_array($fcbpjs_ext,$fcbpjs_allow_ext) ){
				$ERROR[] = 'Scan BPJS harus bertipe jpg, png, atau pdf';
			}
		}*/
		
		else{
			$FIELDS = array(
				'NAMA','NAMA_PANGGILAN','JK','TP_LAHIR','TGL_LAHIR','KEWARGANEGARAAN','SUKU','AGAMA','GOL_DARAH','TINGGI','BERAT','UKURAN_BAJU','UKURAN_SEPATU','TELP','HP','EMAIL','NO_IDENTITAS','NPWP','BPJS_KESEHATAN','BPJS_KETENAGAKERJAAN','ST_KAWIN','PUNYA_KENDARAAN','JENIS_KENDARAAN','MILIK_KENDARAAN','ALAMAT','KELURAHAN','KECAMATAN','PROVINSI','KOTA','KODE_POS','RT','RW','ALAMAT_KTP','KELURAHAN_KTP','KECAMATAN_KTP','PROVINSI_KTP','KOTA_KTP','KODE_POS_KTP','RT_KTP','RW_KTP','TEMPAT_TINGGAL'
			);

			$NEW_FOTO = 0;
			if(is_uploaded_file($foto_tmp)){
				if(move_uploaded_file($foto_tmp,$foto_dest)){
					$FIELDS[] = 'FOTO';
					$NEW_FOTO = 1;
				}
			}
			//echo $NEW_FOTO; die();

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
			$NEW_FCKTP = 0;
			if(is_uploaded_file($fcktp_tmp)){
				if(move_uploaded_file($fcktp_tmp,$fcktp_dest)){
					$FIELDS[] = 'FC_KTP';
					$NEW_FCKTP = 1;
				}
			}
			$NEW_FCNPWP = 0;
			if(is_uploaded_file($fcnpwp_tmp)){
				if(move_uploaded_file($fcnpwp_tmp,$fcnpwp_dest)){
					$FIELDS[] = 'FC_NPWP';
					$NEW_FCNPWP = 1;
				}
			}
			$NEW_FCBPJS_KESEHATAN = 0;
			if(is_uploaded_file($fcbpjs_kes_tmp)){
				if(move_uploaded_file($fcbpjs_kes_tmp,$fcbpjs_kes_dest)){
					$FIELDS[] = 'FC_BPJS_KESEHATAN';
					$NEW_FCBPJS = 1;
				}
			}
			$NEW_FCBPJS_KETENAGAKERJAAN = 0;
			if(is_uploaded_file($fcbpjs_ket_tmp)){
				if(move_uploaded_file($fcbpjs_ket_tmp,$fcbpjs_ket_dest)){
					$FIELDS[] = 'FC_BPJS_KETENAGAKERJAAN';
					$NEW_FCBPJS = 1;
				}
			}
			$NEW_FCBPJS_JAMINANPENSIUN = 0;
			if(is_uploaded_file($fcbpjs_jp_tmp)){
				if(move_uploaded_file($fcbpjs_jp_tmp,$fcbpjs_jp_dest)){
					$FIELDS[] = 'FC_BPJS_JAMINANPENSIUN';
					$NEW_FCBPJS = 1;
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
				}else if($F=='FC_KTP'){
					if($NEW_FCKTP=='1'){
						$INSERT_VAL[$F] = "'".db_escape($fcktp_new)."'";
						$UPDATE_VAL[$F] = $F."='".db_escape($fcktp_new)."'";
					}else{
						$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_FCKTP'))."'";
						$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_FCKTP'))."'";
					}
				}else if($F=='FC_NPWP'){
					if($NEW_FCNPWP=='1'){
						$INSERT_VAL[$F] = "'".db_escape($fcnpwp_new)."'";
						$UPDATE_VAL[$F] = $F."='".db_escape($fcnpwp_new)."'";
					}else{
						$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_FCNPWP'))."'";
						$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_FCNPWP'))."'";
					}
				}else if($F=='FC_BPJS_KESEHATAN'){
					if($NEW_FCBPJS_KESEHATAN=='1'){
						$INSERT_VAL[$F] = "'".db_escape($fcbpjs_kes_new)."'";
						$UPDATE_VAL[$F] = $F."='".db_escape($fcbpjs_kes_new)."'";
					}else{
						$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_FCBPJS_KESEHATAN'))."'";
						$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_FCBPJS_KESEHATAN'))."'";
					}
				}else if($F=='FC_BPJS_KETENAGAKERJAAN'){
					if($NEW_FCBPJS_KETENAGAKERJAAN=='1'){
						$INSERT_VAL[$F] = "'".db_escape($fcbpjs_ket_new)."'";
						$UPDATE_VAL[$F] = $F."='".db_escape($fcbpjs_ket_new)."'";
					}else{
						$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_FCBPJS_KETENAGAKERJAAN'))."'";
						$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_FCBPJS_KETENAGAKERJAAN'))."'";
					}
				}else if($F=='FC_BPJS_JAMINANPENSIUN'){
					if($NEW_FCBPJS_JAMINANPENSIUN=='1'){
						$INSERT_VAL[$F] = "'".db_escape($fcbpjs_ket_new)."'";
						$UPDATE_VAL[$F] = $F."='".db_escape($fcbpjs_ket_new)."'";
					}else{
						$INSERT_VAL[$F] = "'".db_escape(get_input('CURRENT_FCBPJS_JAMINANPENSIUN'))."'";
						$UPDATE_VAL[$F] = $F."='".db_escape(get_input('CURRENT_FCBPJS_JAMINANPENSIUN'))."'";
					}
				}else{
					$INSERT_VAL[$F] = "'".db_escape(get_input($F))."'";
					$UPDATE_VAL[$F] = $F."='".db_escape(get_input($F))."'";
				}
			}
			/*
			$NEW_F = array('EXPECTED_SALARY');
			foreach($NEW_F as $F){
				$INSERT_VAL[$F] = "'".db_escape(input_currency(get_input($F)))."'";
				$UPDATE_VAL[$F] = $F."='".db_escape(input_currency(get_input($F)))."'";
			}
			*/
			//$LOWONGAN_ID = get_input('LOWONGAN_ID');
			//$POSISI_ID = get_input('POSISI_ID');
			//$APPLICANT_NO = get_input('APPLICANT_NO');
			$CREATED_ON = date('Y-m-d H:i:s');
			//$CREATED_BY = date('Y-m-d H:i:s');

			if ($OP=='add'){
				$SQL = db_execute(" INSERT INTO calon_karyawan (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
				$OP = 'edit';
				$ID = $DB->Insert_ID();
				db_execute(" INSERT INTO lamaran (CALON_KARYAWAN_ID,CREATED_ON,STATUS_LAMARAN) VALUES ('$ID','$CREATED_ON','PENGAJUAN') ");
				header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
				exit;
			}else{
				$SQL = db_execute(" UPDATE calon_karyawan SET ".implode(',',$UPDATE_VAL)." WHERE CALON_KARYAWAN_ID='$ID' ");
				header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
				exit;
				/*
				echo "UPDATE calon_karyawan SET ".implode(',',$UPDATE_VAL)." WHERE CALON_KARYAWAN_ID='$ID' <br><br>"; 
				echo " INSERT INTO calon_karyawan (".implode(',',$FIELDS).",CREATED_ON,CREATED_BY) VALUES (".implode(',',$INSERT_VAL).",'$CREATED_ON','$CREATED_BY') ";
				die();
				*/
			}
		}
	}

	if($UPDATE_TYPE == 'DATA_POSISI'){
		$REQUIRE = array('POSISI_ID','LOWONGAN_ID','APPLICANT_NO');
		$ERROR_REQUIRE = 0;
		foreach($REQUIRE as $REQ){
			$IREQ = get_input($REQ);
			if($IREQ == "") $ERROR_REQUIRE = 1;
		}

		if( $ERROR_REQUIRE ){
			$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
		}else{
			$month = date("m", time());
			$data =db_first("SELECT APPLICANT_NO FROM lamaran WHERE MONTH(CREATED_ON)='$month' ORDER BY LAMARAN_ID DESC LIMIT 1");
			$no_urut = explode('-',trim($data->APPLICANT_NO));
			$no = $no_urut[2];
			$no = sprintf('%04d', $no + 1);

			$LOWONGAN_ID = get_input('LOWONGAN_ID');
			$POSISI_ID = get_input('POSISI_ID');
			$APPLICANT_NO = get_input('APPLICANT_NO');
			$NO_URUT = explode('-',trim($APPLICANT_NO));
			$NO = $NO_URUT[2];

			$APPLICANT_NO = str_replace($NO,$no,$APPLICANT_NO);


			$SQL = db_execute(" UPDATE lamaran SET LOWONGAN_ID='$LOWONGAN_ID',POSISI_ID='$POSISI_ID',APPLICANT_NO='$APPLICANT_NO' WHERE CALON_KARYAWAN_ID='$ID' ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
	}

	if($UPDATE_TYPE == 'DATA_PENDIDIKAN'){
		/* Pendidikan formal calon karyawan */
		$TINGKAT = get_input('TINGKAT');
		if(is_array($TINGKAT) AND count($TINGKAT)){
			foreach($TINGKAT as $key=>$val){
				if($val != ''){
					$JURUSAN 			= get_input('JURUSAN');
					$INSTITUSI 			= get_input('INSTITUSI');
					$LOKASI 			= get_input('LOKASI');
					$TAHUN_MULAI 		= get_input('TAHUN_MULAI');
					$TAHUN_SELESAI 		= get_input('TAHUN_SELESAI');
					$GPA 				= get_input('GPA');
					$CALON_KARYAWAN_ID	= $ID;
					$TINGKAT 			= $val;

					$JURUSAN			= isset($JURUSAN[$key]) ? $JURUSAN[$key] : '';
					$INSTITUSI			= isset($INSTITUSI[$key]) ? $INSTITUSI[$key] : '';
					$LOKASI				= isset($LOKASI[$key]) ? $LOKASI[$key] : '';
					$TAHUN_MULAI		= isset($TAHUN_MULAI[$key]) ? $TAHUN_MULAI[$key] : '';
					$TAHUN_SELESAI		= isset($TAHUN_SELESAI[$key]) ? $TAHUN_SELESAI[$key] : '';
					$GPA				= isset($GPA[$key]) ? $GPA[$key] : '';

					$PENDIDIKAN_FORMAL .= "('".$CALON_KARYAWAN_ID."','".$TINGKAT."','".$JURUSAN."','".$INSTITUSI."','".$LOKASI."','".$TAHUN_MULAI."','".$TAHUN_SELESAI."','".$GPA."'),";
				}
			}

			$PENDIDIKAN_FORMAL = rtrim($PENDIDIKAN_FORMAL,',');
			if(!empty($PENDIDIKAN_FORMAL)){
				db_execute(" DELETE FROM pendidikan_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO pendidikan_karyawan (CALON_KARYAWAN_ID,TINGKAT,JURUSAN,INSTITUSI,LOKASI,TAHUN_MULAI,TAHUN_SELESAI,GPA) VALUES $PENDIDIKAN_FORMAL ");
			}
		}

		/* Pendidikan non formal calon karyawan */
		$NAMA_KURSUS = get_input('NAMA_KURSUS');
		if(is_array($NAMA_KURSUS) AND count($NAMA_KURSUS)){
			foreach($NAMA_KURSUS as $key=>$val){
				if($val != ''){
					$TEMPAT 			= get_input('TEMPAT');
					$PERIODE_MULAI 		= get_input('PERIODE_MULAI');
					$PERIODE_SELESAI 	= get_input('PERIODE_SELESAI');
					$KETERANGAN 		= get_input('KETERANGAN');
					$CALON_KARYAWAN_ID	= $ID;
					$NAMA_KURSUS 		= $val;

					$TEMPAT				= isset($TEMPAT[$key]) ? $TEMPAT[$key] : '';
					$PERIODE_MULAI		= isset($PERIODE_MULAI[$key]) ? $PERIODE_MULAI[$key] : '';
					$PERIODE_SELESAI	= isset($PERIODE_SELESAI[$key]) ? $PERIODE_SELESAI[$key] : '';
					$KETERANGAN			= isset($KETERANGAN[$key]) ? $KETERANGAN[$key] : '';

					$PENDIDIKAN_NONFORMAL .= "('".$CALON_KARYAWAN_ID."','".$NAMA_KURSUS."','".$TEMPAT."','".$PERIODE_MULAI."','".$PERIODE_SELESAI."','".$KETERANGAN."'),";
				}
			}

			$PENDIDIKAN_NONFORMAL = rtrim($PENDIDIKAN_NONFORMAL,',');
			if(!empty($PENDIDIKAN_KELUARGA)){
				db_execute(" DELETE FROM kursus_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO kursus_karyawan (CALON_KARYAWAN_ID,NAMA_KURSUS,TEMPAT,PERIODE_MULAI,PERIODE_SELESAI,KETERANGAN) VALUES $PENDIDIKAN_NONFORMAL ");
			}
		}

		/* Penguasaan bahasa asing calon karyawan */
		$BAHASA = get_input('BAHASA');
		if(is_array($BAHASA) AND count($BAHASA)){
			foreach($BAHASA as $key=>$val){
				if($val != ''){
					$LISAN 				= get_input('LISAN');
					$TULISAN 			= get_input('TULISAN');
					$CALON_KARYAWAN_ID	= $ID;
					$BAHASA 			= $val;

					$LISAN				= isset($LISAN[$key]) ? $LISAN[$key] : '';
					$TULISAN			= isset($TULISAN[$key]) ? $TULISAN[$key] : '';

					$BAHASA_ASING .= "('".$CALON_KARYAWAN_ID."','".$BAHASA."','".$LISAN."','".$TULISAN."'),";
				}
			}

			$BAHASA_ASING = rtrim($BAHASA_ASING,',');
			if(!empty($BAHASA_ASING)){
				db_execute(" DELETE FROM bahasa_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO bahasa_karyawan (CALON_KARYAWAN_ID,BAHASA,LISAN,TULISAN) VALUES $BAHASA_ASING ");
			}
		}
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		exit;
	}

	if($UPDATE_TYPE == 'DATA_ORGANISASI'){
		/* Riwayat Organisasi calon karyawan */
		$NAMA_ORGANISASI = get_input('NAMA_ORGANISASI');
		if(is_array($NAMA_ORGANISASI) AND count($NAMA_ORGANISASI)){
			foreach($NAMA_ORGANISASI as $key=>$val){
				if($val != ''){
					$JABATAN_ORGANISASI = get_input('JABATAN_ORGANISASI');
					$LOKASI_ORGANISASI 	= get_input('LOKASI_ORGANISASI');
					$PERIODE_ORGANISASI = get_input('PERIODE_ORGANISASI');
					$CALON_KARYAWAN_ID	= $ID;
					$NAMA_ORGANISASI 	= $val;

					$JABATAN_ORGANISASI	= isset($JABATAN_ORGANISASI[$key]) ? $JABATAN_ORGANISASI[$key] : '';
					$LOKASI_ORGANISASI	= isset($LOKASI_ORGANISASI[$key]) ? $LOKASI_ORGANISASI[$key] : '';
					$PERIODE_ORGANISASI	= isset($PERIODE_ORGANISASI[$key]) ? $PERIODE_ORGANISASI[$key] : '';

					$KRY_ORGANISASI.= "('".$CALON_KARYAWAN_ID."','".$NAMA_ORGANISASI."','".$JABATAN_ORGANISASI."','".$LOKASI_ORGANISASI."','".$PERIODE_ORGANISASI."'),";
				}
			}

			$KRY_ORGANISASI = rtrim($KRY_ORGANISASI,',');
			if(!empty($KRY_ORGANISASI)){ 
				db_execute(" DELETE FROM organisasi_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO organisasi_karyawan (CALON_KARYAWAN_ID,NAMA_ORGANISASI,JABATAN_ORGANISASI,LOKASI_ORGANISASI,PERIODE_ORGANISASI) VALUES $KRY_ORGANISASI ");
			}
		}
	}

	if($UPDATE_TYPE == 'DATA_KELUARGA'){
		/* Keluarga inti calon karyawan */
		$ANGGOTA_KELUARGA_INTI = get_input('ANGGOTA_KELUARGA_INTI');
		if(is_array($ANGGOTA_KELUARGA_INTI) AND count($ANGGOTA_KELUARGA_INTI)){
			foreach($ANGGOTA_KELUARGA_INTI as $key=>$val){
				if($val != ''){
					$NAMA_KELUARGA_INTI 		= get_input('NAMA_KELUARGA_INTI');
					$GENDER_INTI 				= get_input('GENDER_INTI');
					$TP_LAHIR_KELUARGA_INTI 	= get_input('TP_LAHIR_KELUARGA_INTI');
					$TGL_LAHIR_KELUARGA_INTI 	= get_input('TGL_LAHIR_KELUARGA_INTI');
					$PENDIDIKAN_KELUARGA_INTI 	= get_input('PENDIDIKAN_KELUARGA_INTI');
					$PEKERJAAN_KELUARGA_INTI 	= get_input('PEKERJAAN_KELUARGA_INTI');
					$CALON_KARYAWAN_ID			= $ID;
					$ANGGOTA_KELUARGA_INTI 		= $val;

					$NAMA_KELUARGA_INTI			= isset($NAMA_KELUARGA_INTI[$key]) ? $NAMA_KELUARGA_INTI[$key] : '';
					$GENDER_INTI				= isset($GENDER_INTI[$key]) ? $GENDER_INTI[$key] : '';
					$TP_LAHIR_KELUARGA_INTI		= isset($TP_LAHIR_KELUARGA_INTI[$key]) ? $TP_LAHIR_KELUARGA_INTI[$key] : '';
					$TGL_LAHIR_KELUARGA_INTI	= isset($TGL_LAHIR_KELUARGA_INTI[$key]) ? $TGL_LAHIR_KELUARGA_INTI[$key] : '';
					$PENDIDIKAN_KELUARGA_INTI	= isset($PENDIDIKAN_KELUARGA_INTI[$key]) ? $PENDIDIKAN_KELUARGA_INTI[$key] : '';
					$PEKERJAAN_KELUARGA_INTI	= isset($PEKERJAAN_KELUARGA_INTI[$key]) ? $PEKERJAAN_KELUARGA_INTI[$key] : '';

					$INTI_KELUARGA.= "('".$CALON_KARYAWAN_ID."','".$ANGGOTA_KELUARGA_INTI."','".$NAMA_KELUARGA_INTI."','".$GENDER_INTI."','".$TP_LAHIR_KELUARGA_INTI."','".$TGL_LAHIR_KELUARGA_INTI."','".$PENDIDIKAN_KELUARGA_INTI."','".$PEKERJAAN_KELUARGA_INTI."','INTI'),";
				}
			}
			$INTI_KELUARGA = rtrim($INTI_KELUARGA,',');
			if(!empty($INTI_KELUARGA)){
				db_execute(" DELETE FROM keluarga_karyawan WHERE CALON_KARYAWAN_ID='$ID' AND JENIS_KELUARGA='INTI' ");
				db_execute(" INSERT INTO keluarga_karyawan (CALON_KARYAWAN_ID,ANGGOTA_KELUARGA,NAMA_KELUARGA,GENDER,TP_LAHIR_KELUARGA,TGL_LAHIR_KELUARGA,PENDIDIKAN_KELUARGA,PEKERJAAN_KELUARGA,JENIS_KELUARGA) VALUES $INTI_KELUARGA ");
			}
		}

		/* Keluarga besar calon karyawan */
		$ANGGOTA_KELUARGA_BESAR = get_input('ANGGOTA_KELUARGA_BESAR');
		if(is_array($ANGGOTA_KELUARGA_BESAR) AND count($ANGGOTA_KELUARGA_BESAR)){
			foreach($ANGGOTA_KELUARGA_BESAR as $key=>$val){
				if($val != ''){
					$NAMA_KELUARGA_BESAR 		= get_input('NAMA_KELUARGA_BESAR');
					$GENDER_BESAR 				= get_input('GENDER_BESAR');
					$TP_LAHIR_KELUARGA_BESAR 	= get_input('TP_LAHIR_KELUARGA_BESAR');
					$TGL_LAHIR_KELUARGA_BESAR 	= get_input('TGL_LAHIR_KELUARGA_BESAR');
					$PENDIDIKAN_KELUARGA_BESAR 	= get_input('PENDIDIKAN_KELUARGA_BESAR');
					$PEKERJAAN_KELUARGA_BESAR 	= get_input('PEKERJAAN_KELUARGA_BESAR');
					$CALON_KARYAWAN_ID			= $ID;
					$ANGGOTA_KELUARGA_BESAR 	= $val;

					$NAMA_KELUARGA_BESAR		= isset($NAMA_KELUARGA_BESAR[$key]) ? $NAMA_KELUARGA_BESAR[$key] : '';
					$GENDER_BESAR				= isset($GENDER_BESAR[$key]) ? $GENDER_BESAR[$key] : '';
					$TP_LAHIR_KELUARGA_BESAR	= isset($TP_LAHIR_KELUARGA_BESAR[$key]) ? $TP_LAHIR_KELUARGA_BESAR[$key] : '';
					$TGL_LAHIR_KELUARGA_BESAR	= isset($TGL_LAHIR_KELUARGA_BESAR[$key]) ? $TGL_LAHIR_KELUARGA_BESAR[$key] : '';
					$PENDIDIKAN_KELUARGA_BESAR	= isset($PENDIDIKAN_KELUARGA_BESAR[$key]) ? $PENDIDIKAN_KELUARGA_BESAR[$key] : '';
					$PEKERJAAN_KELUARGA_BESAR	= isset($PEKERJAAN_KELUARGA_BESAR[$key]) ? $PEKERJAAN_KELUARGA_BESAR[$key] : '';

					$BESAR_KELUARGA.= "('".$CALON_KARYAWAN_ID."','".$ANGGOTA_KELUARGA_BESAR."','".$NAMA_KELUARGA_BESAR."','".$GENDER_BESAR."','".$TP_LAHIR_KELUARGA_BESAR."','".$TGL_LAHIR_KELUARGA_BESAR."','".$PENDIDIKAN_KELUARGA_BESAR."','".$PEKERJAAN_KELUARGA_BESAR."','BESAR'),";
				}
			}
			$BESAR_KELUARGA = rtrim($BESAR_KELUARGA,',');
			if(!empty($BESAR_KELUARGA)){
				db_execute(" DELETE FROM keluarga_karyawan WHERE CALON_KARYAWAN_ID='$ID' AND JENIS_KELUARGA='BESAR' ");
				db_execute(" INSERT INTO keluarga_karyawan (CALON_KARYAWAN_ID,ANGGOTA_KELUARGA,NAMA_KELUARGA,GENDER,TP_LAHIR_KELUARGA,TGL_LAHIR_KELUARGA,PENDIDIKAN_KELUARGA,PEKERJAAN_KELUARGA,JENIS_KELUARGA) VALUES $BESAR_KELUARGA ");
			}
		}
	}

	if($UPDATE_TYPE == 'DATA_PENGALAMAN'){
		$TUGAS_JABATAN 		=  db_escape(get_input('TUGAS_JABATAN'));
		$MASALAH_PENTING 	=  db_escape(get_input('MASALAH_PENTING'));
		$JABATAN_ATASAN 	=  db_escape(get_input('JABATAN_ATASAN'));
		$JUMLAH_ANAK_BUAH 	=  db_escape(get_input('JUMLAH_ANAK_BUAH'));

		/*$struktur_allow_ext = array('png','jpg','pdf');
		$struktur_name = isset($_FILES['STRUKTUR_IMG']['name']) ? $_FILES['STRUKTUR_IMG']['name'] : '';
		$struktur_tmp = isset($_FILES['STRUKTUR_IMG']['tmp_name']) ? $_FILES['STRUKTUR_IMG']['tmp_name'] : '';
		$struktur_ext = strtolower(substr(strrchr($struktur_name, "."), 1));
		$struktur_new = rand(11111,99999).'_'.$struktur_name;
		$struktur_dest = 'uploads/cv/'.$struktur_new;

		$NEW_STRUKTUR_IMG = 0;
		if(is_uploaded_file($struktur_tmp)){
			if(move_uploaded_file($struktur_tmp,$struktur_dest)){
				$NEW_STRUKTUR_IMG = 1;
			}
		}*/
		
		db_execute(" UPDATE calon_karyawan SET TUGAS_JABATAN='$TUGAS_JABATAN',MASALAH_PENTING='$MASALAH_PENTING',JABATAN_ATASAN='$JABATAN_ATASAN',JUMLAH_ANAK_BUAH='$JUMLAH_ANAK_BUAH' WHERE CALON_KARYAWAN_ID='$ID'");
		

		/* Pengalaman calon karyawan */
		$NAMA_PERUSAHAAN = get_input('NAMA_PERUSAHAAN');
		if(is_array($NAMA_PERUSAHAAN) AND count($NAMA_PERUSAHAAN)){
			foreach($NAMA_PERUSAHAAN as $key=>$val){
				if($val != ''){
					$BIDANG_USAHA			= get_input('BIDANG_USAHA');
					$ALAMAT_PERUSAHAAN		= get_input('ALAMAT_PERUSAHAAN');
					$ATASAN 				= get_input('ALAMAT_PERUSAHAAN');
					$NO_TELP_PERUSAHAAN		= get_input('NO_TELP_PERUSAHAAN');
					$PERIODE_BEKERJA		= get_input('PERIODE_BEKERJA');
					$JABATAN_AWAL			= get_input('JABATAN_AWAL');
					$JABATAN_AKHIR			= get_input('JABATAN_AKHIR');
					$GAPOK_SEBELUMNYA		= input_currency(get_input('GAPOK_SEBELUMNYA'));
					$TUNJANGAN_LAINNYA		= get_input('TUNJANGAN_LAINNYA');
					$ALASAN_RESIGN			= get_input('ALASAN_RESIGN');
					$DESKRIPSI_PEKERJAAN	= get_input('DESKRIPSI_PEKERJAAN');
					$CALON_KARYAWAN_ID		= $ID;
					$NAMA_PERUSAHAAN 		= $val;

					$BIDANG_USAHA			= isset($BIDANG_USAHA[$key]) ? $BIDANG_USAHA[$key] : '';
					$ALAMAT_PERUSAHAAN		= isset($ALAMAT_PERUSAHAAN[$key]) ? $ALAMAT_PERUSAHAAN[$key] : '';
					$ATASAN					= isset($ATASAN[$key]) ? $ATASAN[$key] : '';
					$NO_TELP_PERUSAHAAN		= isset($NO_TELP_PERUSAHAAN[$key]) ? $NO_TELP_PERUSAHAAN[$key] : '';
					$PERIODE_BEKERJA		= isset($PERIODE_BEKERJA[$key]) ? $PERIODE_BEKERJA[$key] : '';
					$JABATAN_AWAL			= isset($JABATAN_AWAL[$key]) ? $JABATAN_AWAL[$key] : '';
					$JABATAN_AKHIR			= isset($JABATAN_AKHIR[$key]) ? $JABATAN_AKHIR[$key] : '';
					$GAPOK_SEBELUMNYA		= isset($GAPOK_SEBELUMNYA[$key]) ? $GAPOK_SEBELUMNYA[$key] : '';
					$TUNJANGAN_LAINNYA		= isset($TUNJANGAN_LAINNYA[$key]) ? $TUNJANGAN_LAINNYA[$key] : '';
					$ALASAN_RESIGN			= isset($ALASAN_RESIGN[$key]) ? $ALASAN_RESIGN[$key] : '';
					$DESKRIPSI_PEKERJAAN	= isset($DESKRIPSI_PEKERJAAN[$key]) ? $DESKRIPSI_PEKERJAAN[$key] : '';

					$PENGALAMAN_DATA .= "('".$CALON_KARYAWAN_ID."','".$NAMA_PERUSAHAAN."','".$BIDANG_USAHA."','".$ALAMAT_PERUSAHAAN."','".$ATASAN."','".$NO_TELP_PERUSAHAAN."','".$PERIODE_BEKERJA."','".$JABATAN_AWAL."','".$JABATAN_AKHIR."','".$GAPOK_SEBELUMNYA."','".$TUNJANGAN_LAINNYA."','".$ALASAN_RESIGN."','".$DESKRIPSI_PEKERJAAN."'),";
				}
			}

			//echo "INSERT INTO pengalaman_karyawan (CALON_KARYAWAN_ID,NAMA_PERUSAHAAN,BIDANG_USAHA,ALAMAT_PERUSAHAAN,ATASAN,NO_TELP_PERUSAHAAN,PERIODE_BEKERJA,JABATAN_AWAL,JABATAN_AKHIR,GAPOK_SEBELUMNYA,TUNJANGAN_LAINNYA,ALASAN_RESIGN,DESKRIPSI_PEKERJAAN) VALUES $PENGALAMAN_DATA ";
			//die();
			
			$PENGALAMAN_DATA = rtrim($PENGALAMAN_DATA,',');
			if(!empty($PENGALAMAN_DATA)){
				db_execute(" DELETE FROM pengalaman_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO pengalaman_karyawan (CALON_KARYAWAN_ID,NAMA_PERUSAHAAN,BIDANG_USAHA,ALAMAT_PERUSAHAAN,ATASAN,NO_TELP_PERUSAHAAN,PERIODE_BEKERJA,JABATAN_AWAL,JABATAN_AKHIR,GAPOK_SEBELUMNYA,TUNJANGAN_LAINNYA,ALASAN_RESIGN,DESKRIPSI_PEKERJAAN) VALUES $PENGALAMAN_DATA ");
			}
			
		}
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		exit;

	}

	if($UPDATE_TYPE == 'DATA_MINAT'){
		$HOBI 					=  db_escape(get_input('HOBI'));
		$EXPECTED_SALARY 		=  db_escape(input_currency(get_input('EXPECTED_SALARY')));
		$MOTIVASI_BEKERJA 		=  db_escape(get_input('MOTIVASI_BEKERJA'));
		$FASILITAS_LAINNYA 		=  db_escape(get_input('FASILITAS_LAINNYA'));
		$SIAP_BEKERJA 			=  db_escape(get_input('SIAP_BEKERJA'));
		$MOTIVASI_DIAIRKON 		=  db_escape(get_input('MOTIVASI_DIAIRKON'));
		$LUAR_DAERAH 			=  db_escape(get_input('LUAR_DAERAH'));
		$ALASAN_DILUAR_DAERAH 	=  db_escape(get_input('ALASAN_DILUAR_DAERAH'));

		
		db_execute(" UPDATE calon_karyawan SET HOBI='$HOBI',EXPECTED_SALARY='$EXPECTED_SALARY',MOTIVASI_BEKERJA='$MOTIVASI_BEKERJA',FASILITAS_LAINNYA='$FASILITAS_LAINNYA',SIAP_BEKERJA='$SIAP_BEKERJA',MOTIVASI_DIAIRKON='$MOTIVASI_DIAIRKON',LUAR_DAERAH='$LUAR_DAERAH',ALASAN_DILUAR_DAERAH='$ALASAN_DILUAR_DAERAH' ");
		
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		exit;

	}

	if($UPDATE_TYPE == 'DATA_LAIN'){
		$INFO_LOKER 		=  db_escape(get_input('INFO_LOKER'));
		$KERABAT_AIRKON 	=  db_escape(get_input('KERABAT_AIRKON'));
		$RIWAYAT_KESEHATAN 	=  db_escape(get_input('RIWAYAT_KESEHATAN'));
		$RIWAYAT_RAWAT 		=  db_escape(get_input('RIWAYAT_RAWAT'));
		$INFO_LOKER_LAINNYA =  db_escape(get_input('INFO_LOKER_LAINNYA'));
		$NAMA_KERABAT 		=  db_escape(get_input('NAMA_KERABAT'));
		$NAMA_PENYAKIT 		=  db_escape(get_input('NAMA_PENYAKIT'));

		db_execute(" UPDATE calon_karyawan SET INFO_LOKER='$INFO_LOKER',KERABAT_AIRKON='$KERABAT_AIRKON',RIWAYAT_KESEHATAN='$RIWAYAT_KESEHATAN',RIWAYAT_RAWAT='$RIWAYAT_RAWAT',INFO_LOKER_LAINNYA='$INFO_LOKER_LAINNYA',NAMA_KERABAT='$NAMA_KERABAT',NAMA_PENYAKIT='$NAMA_PENYAKIT' ");
		

		/* penanggung calon karyawan */
		$NAMA_PENANGGUNG = get_input('NAMA_PENANGGUNG');
		if(is_array($NAMA_PENANGGUNG) AND count($NAMA_PENANGGUNG)){
			foreach($NAMA_PENANGGUNG as $key=>$val){
				if($val != ''){
					$ALAMAT_PENANGGUNG		= get_input('ALAMAT_PENANGGUNG');
					$TELP_PENANGGUNG		= get_input('TELP_PENANGGUNG');
					$HUBUNGAN_PENANGGUNG 	= get_input('HUBUNGAN_PENANGGUNG');
					$CALON_KARYAWAN_ID		= $ID;
					$NAMA_PENANGGUNG 		= $val;

					$ALAMAT_PENANGGUNG		= isset($ALAMAT_PENANGGUNG[$key]) ? $ALAMAT_PENANGGUNG[$key] : '';
					$TELP_PENANGGUNG		= isset($TELP_PENANGGUNG[$key]) ? $TELP_PENANGGUNG[$key] : '';
					$HUBUNGAN_PENANGGUNG	= isset($HUBUNGAN_PENANGGUNG[$key]) ? $HUBUNGAN_PENANGGUNG[$key] : '';

					$PENANGGUNG_DATA .= "('".$CALON_KARYAWAN_ID."','".$NAMA_PENANGGUNG."','".$ALAMAT_PENANGGUNG."','".$TELP_PENANGGUNG."','".$HUBUNGAN_PENANGGUNG."'),";
				}
			}

			$PENANGGUNG_DATA = rtrim($PENANGGUNG_DATA,',');
			if(!empty($PENANGGUNG_DATA)){
				db_execute(" DELETE FROM penanggung_karyawan WHERE CALON_KARYAWAN_ID='$ID' ");
				db_execute(" INSERT INTO penanggung_karyawan (CALON_KARYAWAN_ID,NAMA_PENANGGUNG,ALAMAT_PENANGGUNG,TELP_PENANGGUNG,HUBUNGAN_PENANGGUNG) VALUES $PENANGGUNG_DATA ");
			}
		}
		header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		exit;

	}

}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

if($OP == 'add'){
	$distab='disabled';
	$distoggle='';
}

if($OP == 'edit'){
	$distab='';
	$distoggle='tab';
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
		<?php echo ucfirst($OP) ?> Lamaran
		<a href="lamaran.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<?php /*
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		*/ ?>
		<?php if($OP=='edit'){ echo '<a href="lamaran-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Identitas Diri</a></li>
		<li role="presentation" class="<?php echo $distab ?>"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="<?php echo $distoggle ?>">Posisi Yang Dilamar</a></li>
		<li role="presentation" class="<?php echo $distab ?>"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="<?php echo $distoggle ?>">Riwayat Pendididkan</a></li>
		<li role="presentation" class="<?php echo $distab ?>"><a href="#tab4" aria-controls="tab4" role="tab" data-toggle="<?php echo $distoggle ?>">Pengalaman Organisasi</a></li>
		<li role="presentation" class="<?php echo $distab ?>"><a href="#tab5" aria-controls="tab5" role="tab" data-toggle="<?php echo $distoggle ?>">Lingkungan Keluarga</a></li>
		<li role="presentation" class="<?php echo $distab ?>"><a href="#tab6" aria-controls="tab6" role="tab" data-toggle="<?php echo $distoggle ?>">Riwayat Pekerjaan</a></li>
		<li role="presentation" class="<?php echo $distab ?>"><a href="#tab7" aria-controls="tab7" role="tab" data-toggle="<?php echo $distoggle ?>">Minat dan Konsep Diri</a></li>
		<li role="presentation" class="<?php echo $distab ?>"><a href="#tab8" aria-controls="tab8" role="tab" data-toggle="<?php echo $distoggle ?>">Lain - Lain</a></li>
	</ul>

	<form id="form" class="form-horizontal" action="lamaran-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="CURRENT_FOTO" value="<?php echo $EDIT->FOTO ?>">
		<input type="hidden" name="CURRENT_IJAZAH" value="<?php echo $EDIT->IJAZAH ?>">
		<input type="hidden" name="CURRENT_CV" value="<?php echo $EDIT->CV ?>">
		<input type="hidden" name="CURRENT_FCKTP" value="<?php echo $EDIT->FC_KTP ?>">
		<input type="hidden" name="CURRENT_FCNPWP" value="<?php echo $EDIT->FC_NPWP ?>">
		<input type="hidden" name="CURRENT_FCBPJS_KESEHATAN" value="<?php echo $EDIT->FC_BPJS_KESEHATAN ?>">
		<input type="hidden" name="CURRENT_FCBPJS_KETENAGAKERJAAN" value="<?php echo $EDIT->FC_BPJS_KETENAGAKERJAAN ?>">
		<input type="hidden" name="CURRENT_STRUKTUR_IMG" value="<?php echo $EDIT->STRUKTUR_IMG ?>">
		<input type="hidden" name="CURRENT_ID" value="<?php echo $ID ?>">
		<div class="tab-content" style="margin-top:20px;">
			<?php /* TAB 1 : IDENTITAS DIRI */ ?>
			<div role="tabpanel" class="tab-pane active" id="tab1">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Nama Lengkap<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="NAMA" value="<?php echo set_value('NAMA', $EDIT->NAMA) ?>" class="form-control" style="text-transform:uppercase">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Nama Panggilan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="NAMA_PANGGILAN" value="<?php echo set_value('NAMA_PANGGILAN', $EDIT->NAMA_PANGGILAN) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Jenis Kelamin<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('JK',array('L'=>'L','P'=>'P'),set_value('JK', $EDIT->JK),' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tempat Lahir<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="TP_LAHIR" value="<?php echo set_value('TP_LAHIR', $EDIT->TP_LAHIR) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tanggal Lahir<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="TGL_LAHIR" value="<?php echo set_value('TGL_LAHIR', $EDIT->TGL_LAHIR) ?>" class="form-control datepicker" autocomplete="off" placeholder="YYYY-MM-DD">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kewarganegaraan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('KEWARGANEGARAAN',array('WNI'=>'WNI','WNA'=>'WNA'),set_value('KEWARGANEGARAAN', $EDIT->KEWARGANEGARAAN),' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Suku<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="SUKU" value="<?php echo set_value('SUKU', $EDIT->SUKU) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Agama<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('AGAMA',array('ISLAM'=>'ISLAM','KRISTEN'=>'KRISTEN','KATOLIK'=>'KATOLIK','HINDU'=>'HINDU','BUDHA'=>'BUDHA','KONG HU CHU'=>'KONG HU CHU'),set_value('AGAMA', $EDIT->AGAMA),' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Golongan Darah<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('GOL_DARAH',array('A'=>'A','B'=>'B','AB'=>'AB','O'=>'O'),set_value('GOL_DARAH',$EDIT->GOL_DARAH),' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tinggi Badan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-2">
								<input type="number" name="TINGGI" value="<?php echo set_value('TINGGI', $EDIT->TINGGI) ?>" class="form-control">
							</div>
							<div class="col-sm-1 control-label" style="text-align: left;">Cm</div>
							<label for="" class="col-sm-3 control-label">Berat Badan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-2">
								<input type="number" name="BERAT" value="<?php echo set_value('BERAT', $EDIT->BERAT) ?>" class="form-control">
							</div>
							<div class="col-sm-1 control-label" style="text-align: left;">Kg</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Ukuran Baju<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-3">
								<?php echo dropdown('UKURAN_BAJU',array('S'=>'S','M'=>'M','L'=>'L','XL'=>'XL'),set_value('UKURAN_BAJU', $EDIT->UKURAN_BAJU),' class="form-control" ') ?>
							</div>
							<label for="" class="col-sm-3 control-label">Ukuran Sepatu<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-3">
								<input type="number" name="UKURAN_SEPATU" value="<?php echo set_value('UKURAN_SEPATU', $EDIT->UKURAN_SEPATU) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No Telp Rumah<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-4">
								<input type="text" name="TELP" value="<?php echo set_value('TELP', $EDIT->TELP) ?>" class="form-control">
							</div>
							<label for="" class="col-sm-1 control-label">HP<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-4">
								<input type="text" name="HP" value="<?php echo set_value('HP', $EDIT->HP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Alamat Email<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="EMAIL" value="<?php echo set_value('EMAIL', $EDIT->EMAIL) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No. e-KTP<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-5">
								<input type="text" name="NO_IDENTITAS" value="<?php echo set_value('NO_IDENTITAS', $EDIT->NO_IDENTITAS) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_KTP" class="form-control" title="Upload Scan e-KTP">
							</div>
							<?php if(!empty($EDIT->FC_KTP) AND url_exists(base_url().'uploads/cv/'.rawurlencode($EDIT->FC_KTP))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/cv/".$EDIT->FC_KTP ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No. NPWP<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-5">
								<input type="text" name="NPWP" value="<?php echo set_value('NPWP', $EDIT->NPWP) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_NPWP" class="form-control" title="Upload Scan NPWP">
							</div>
							<?php if(!empty($EDIT->FC_NPWP) AND url_exists(base_url().'uploads/cv/'.rawurlencode($EDIT->FC_NPWP))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/cv/".$EDIT->FC_NPWP ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No BPJS KES<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-5">
								<input type="text" name="BPJS_KESEHATAN" value="<?php echo set_value('BPJS_KESEHATAN', $EDIT->BPJS_KESEHATAN) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_BPJS_KESEHATAN" class="form-control" title="Upload Scan BPJS Kesehatan">
							</div>
							<?php if(!empty($EDIT->FC_BPJS_KESEHATAN) AND url_exists(base_url().'uploads/cv/'.rawurlencode($EDIT->FC_BPJS_KESEHATAN))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/cv/".$EDIT->FC_BPJS_KESEHATAN ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No BPJS TK<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-5">
								<input type="text" name="BPJS_KETENAGAKERJAAN" value="<?php echo set_value('BPJS_KETENAGAKERJAAN', $EDIT->BPJS_KETENAGAKERJAAN) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_BPJS_KETENAGAKERJAAN" class="form-control" title="Upload Scan BPJS Ketenagakerjaan">
							</div>
							<?php if(!empty($EDIT->FC_BPJS_KETENAGAKERJAAN) AND url_exists(base_url().'uploads/cv/'.rawurlencode($EDIT->FC_BPJS_KETENAGAKERJAAN))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/cv/".$EDIT->FC_BPJS_KETENAGAKERJAAN ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">No BPJS JP<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-5">
								<input type="text" name="BPJS_JAMINANPENSIUN" value="<?php echo set_value('BPJS_JAMINANPENSIUN', $EDIT->BPJS_JAMINANPENSIUN) ?>" class="form-control">
							</div>
							<div class="col-sm-3">
								<input type="file" name="FC_BPJS_JAMINANPENSIUN" class="form-control" title="Upload Scan BPJS Jaminan Pensiun">
							</div>
							<?php if(!empty($EDIT->FC_BPJS_JAMINANPENSIUN) AND url_exists(base_url().'uploads/cv/'.rawurlencode($EDIT->FC_BPJS_JAMINANPENSIUN))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/cv/".$EDIT->FC_BPJS_JAMINANPENSIUN ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Satus Kawin<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('ST_KAWIN',array('LAJANG'=>'LAJANG','MENIKAH'=>'MENIKAH','JANDA'=>'JANDA','DUDA'=>'DUDA'),set_value('ST_KAWIN', $EDIT->ST_KAWIN),' class="form-control" ') ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kendaraan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('PUNYA_KENDARAAN',array('PUNYA'=>'PUNYA','TIDAK PUNYA'=>'TIDAK'),set_value('PUNYA_KENDARAAN', $EDIT->PUNYA_KENDARAAN),' class="form-control" id="PUNYA_KENDARAAN"') ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Jenis Kendaraan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('JENIS_KENDARAAN',array('MOTOR'=>'MOTOR','MOBIL'=>'MOBIL'),set_value('JENIS_KENDARAAN', $EDIT->JENIS_KENDARAAN),' class="form-control" id="JENIS_KENDARAAN"') ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Milik<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('MILIK_KENDARAAN',array('SENDIRI'=>'SENDIRI','ORANG TUA'=>'ORANG TUA','KANTOR'=>'KANTOR'),set_value('MILIK_KENDARAAN', $EDIT->MILIK_KENDARAAN),' class="form-control" id="MILIK_KENDARAAN"') ?>
							</div>
						</div>
						<div class="form-group" style="padding-left: 20px;">
							<button name="UPDATE_TYPE" type="submit" value="DATA_PELAMAR" class="btn btn-primary" onclick="$('#form').submit()">
								<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
							</button>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px;">
								Isi sesuai dengan tempat tinggal KTP
							</label>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Alamat<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="ALAMAT_KTP" value="<?php echo set_value('ALAMAT_KTP', $EDIT->ALAMAT_KTP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kelurahan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="KELURAHAN_KTP" value="<?php echo set_value('KELURAHAN_KTP', $EDIT->KELURAHAN_KTP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kecamatan <!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="KECAMATAN_KTP" value="<?php echo set_value('KECAMATAN_KTP', $EDIT->KECAMATAN_KTP) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Provinsi<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<select name="PROVINSI_KTP" id="PROVINSI_KTP" class="form-control" style="width: 100%;">
								<?php
									$K = db_first(" SELECT * FROM provinsi WHERE PROVINSI='".db_escape(set_value('PROVINSI_KTP',$EDIT->PROVINSI_KTP))."' ");
									if(isset($K->PROVINSI)){
										echo '<option value="'.$K->PROVINSI.'" data-kode="'.$K->PROVINSI_ID.'" selected="selected">'.$K->PROVINSI.'</option>';
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kota<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<select name="KOTA_KTP" id="KOTA_KTP" class="form-control" style="width: 100%;">
								<?php
									$K = db_first(" SELECT * FROM kota WHERE KOTA='".db_escape(set_value('KOTA_KTP',$EDIT->KOTA_KTP))."' ");
									if(isset($K->KOTA)){
										echo '<option value="'.$K->KOTA.'" selected="selected">'.$K->KOTA.'</option>';
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kode Pos<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-3">
								<input type="text" name="KODE_POS_KTP" value="<?php echo set_value('KODE_POS_KTP', $EDIT->KODE_POS_KTP) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RT<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-2">
								<input type="text" name="RT_KTP" value="<?php echo set_value('RT_KTP', $EDIT->RT_KTP) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RW<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-2">
								<input type="text" name="RW_KTP" value="<?php echo set_value('RW_KTP', $EDIT->RW_KTP) ?>" class="form-control" style="text-align:center;">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 35px;">
								
							</label>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px;">
								Isi sesuai dengan tempat tinggal sekarang
							</label>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Alamat<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="ALAMAT" value="<?php echo set_value('ALAMAT', $EDIT->ALAMAT) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kelurahan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="KELURAHAN" value="<?php echo set_value('KELURAHAN', $EDIT->KELURAHAN) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kecamatan <!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<input type="text" name="KECAMATAN" value="<?php echo set_value('KECAMATAN', $EDIT->KECAMATAN) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Provinsi<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<select name="PROVINSI" id="PROVINSI" class="form-control" style="width: 100%;">
								<?php
									$K = db_first(" SELECT * FROM provinsi WHERE PROVINSI='".db_escape(set_value('PROVINSI',$EDIT->PROVINSI))."' ");
									if(isset($K->PROVINSI)){
										echo '<option value="'.$K->PROVINSI.'" data-kode="'.$K->PROVINSI_ID.'" selected="selected">'.$K->PROVINSI.'</option>';
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Kota<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<select name="KOTA" id="KOTA" class="form-control" style="width: 100%;">
								<?php
									$K = db_first(" SELECT * FROM kota WHERE KOTA='".db_escape(set_value('KOTA',$EDIT->KOTA))."' ");
									if(isset($K->KOTA)){
										echo '<option value="'.$K->KOTA.'" selected="selected">'.$K->KOTA.'</option>';
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Kode Pos<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-3">
								<input type="text" name="KODE_POS" value="<?php echo set_value('KODE_POS', $EDIT->KODE_POS) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RT<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-2">
								<input type="text" name="RT" value="<?php echo set_value('RT', $EDIT->RT) ?>" class="form-control" style="text-align:center;">
							</div>
							<label for="" class="col-sm-1 control-label">RW<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-2">
								<input type="text" name="RW" value="<?php echo set_value('RW', $EDIT->RW) ?>" class="form-control" style="text-align:center;">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
							<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 35px;">
								
							</label>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Tempat Tinggal <!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-9">
								<?php echo dropdown('TEMPAT_TINGGAL',array('MILIK SENDIRI'=>'MILIK SENDIRI','MILIK ORANG TUA'=>'MILIK ORANG TUA','SEWA / KOS / KONTRAK'=>'SEWA / KOS / KONTRAK','LAIN-LAIN'=>'LAIN-LAIN'),set_value('TEMPAT_TINGGAL', $EDIT->TEMPAT_TINGGAL),' class="form-control" id="MILIK_KENDARAAN"') ?>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">File Foto<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-8">
								<input type="file" name="FOTO" class="form-control">
							</div>
							<?php if(!empty($EDIT->FOTO) AND url_exists(base_url().'uploads/foto/'.rawurlencode($EDIT->FOTO))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/foto/".$EDIT->FOTO ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">File Scan Ijazah<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-8">
								<input type="file" name="IJAZAH" class="form-control">
							</div>
							<?php if(!empty($EDIT->IJAZAH) AND url_exists(base_url().'uploads/ijazah/'.rawurlencode($EDIT->IJAZAH))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/ijazah/".$EDIT->IJAZAH ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">File Cv<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
							<div class="col-sm-8">
								<input type="file" name="CV" class="form-control">
							</div>
							<?php if(!empty($EDIT->CV) AND url_exists(base_url().'uploads/cv/'.rawurlencode($EDIT->CV))){ ?>
							<div class="col-sm-1" style="margin-left: -35px;">
								<span class="input-group-btn">
								<a class="btn btn-primary btn-flat" href="<?php echo base_url()."uploads/cv/".$EDIT->CV ?>" download title="Download">
									<span class="glyphicon glyphicon-download"></span>
								</a>
								</span>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php /* END OF TAB 1 */ ?>
			<?php /* TAB 2 : POSISI YANG DILAMAR */ ?>
			<div role="tabpanel" class="tab-pane" id="tab2">
				<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label class="col-sm-2 control-label">Jabatan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
						<div class="col-sm-8">
							<select name="POSISI_ID" id="POSISI_ID" class="form-control" style="width: 100%;">
							<?php
								$K = db_first(" SELECT * FROM posisi WHERE POSISI_ID='".db_escape(set_value('POSISI_ID',$EDIT->POSISI_ID))."' ");
								if(isset($K->POSISI_ID)){
									echo '<option value="'.$K->POSISI_ID.'" selected="selected">'.$K->POSISI.'</option>';
								}
							?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label">Lowongan<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
						<div class="col-sm-8">
							<select name="LOWONGAN_ID" id="LOWONGAN_ID" class="form-control" style="width: 100%;">
							<?php
								$K = db_first(" SELECT * FROM lowongan WHERE LOWONGAN_ID='".db_escape(set_value('LOWONGAN_ID',$EDIT->LOWONGAN_ID))."' ");
								if(isset($K->LOWONGAN_ID)){
									echo '<option value="'.$K->LOWONGAN_ID.'" data-kode="'.$K->KODE_REFERENSI.'" selected="selected">'.$K->LOWONGAN.' ('.$K->KODE_REFERENSI.')</option>';
								}
							?>
							</select>
							<?php
								if($OP=='edit'){
									$no_urut = explode('-',trim($EDIT->APPLICANT_NO));
									$no = $no_urut[2];
								}else{
									$month = date("m", time());
									$data = db_first("SELECT APPLICANT_NO FROM lamaran WHERE MONTH(CREATED_ON)='$month' ORDER BY LAMARAN_ID DESC LIMIT 1");
									$no_urut = explode('-',trim($data->APPLICANT_NO));
									$no = $no_urut[2];
									$no = sprintf('%04d', $no + 1);
								}
							?>
							<input type="hidden" id="no_urut" value="<?php echo empty($no) ? '0001' : $no ?>">
							<input type="hidden" id="tanggal" value="<?php echo date('ymd'); ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Applicant No.<!--<span style="color:red; padding-left: 5px;">*</span>--></label>
						<div class="col-sm-8">
							<input type="text" name="APPLICANT_NO" id="APPLICANT_NO" value="<?php echo set_value('APPLICANT_NO', $EDIT->APPLICANT_NO) ?>" class="form-control" readonly>
						</div>
					</div>
					<div class="form-group" style="padding-left: 20px;">
						<button name="UPDATE_TYPE" type="submit" value="DATA_POSISI" class="btn btn-primary" onclick="$('#form').submit()">
							<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
						</button>
					</div>
				</div>
				</div>
			</div>
			<?php /* END OF TAB 2 */ ?>
			<?php /* TAB 3 : RIWAYAT PENDIDIKAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab3">
				<div class="row">
					<div class="col-md-12">
						
						<table class="table table-bordered formal">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="5">Pendidikan formal</th>
								<th colspan="4" class="text-right">
									<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Pendidikan" style="width: 150px;" id="add-formal">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
									</span>
								</th>
							</tr>
							<tr>
								<th rowspan="2" class="text-center" style="width: 20px;">No</th>
								<th rowspan="2" class="text-center" style="width: 180px;">Tingkat</th>
								<th rowspan="2" class="text-center">Jurusan</th>
								<th rowspan="2" class="text-center">Nama Sekolah / Institusi</th>
								<th rowspan="2" class="text-center">Lokasi</th>
								<th colspan="2" class="text-center">Periode(Tahun)</th>
								<th rowspan="2" class="text-center" style="width: 98px;">GPA</th>
								<th rowspan="2" class="text-center" style="width: 50px;"></th>
							</tr>
							<tr>
								<th class="text-center" style="width: 100px;">Mulai</th>
								<th class="text-center" style="width: 100px;">Lulus</th>
							</tr>
							<?php $PEND_FORMAL = db_fetch("SELECT * FROM pendidikan_karyawan WHERE CALON_KARYAWAN_ID='$ID'");
							if(count($PEND_FORMAL) > 0){ foreach ($PEND_FORMAL as $key => $row) { ?>
							<tr>
								<td><?php echo $key+1 ?></td>
								<td>
									<select class="form-control" name="TINGKAT[]">
										<option value="SD" <?php if($row->TINGKAT == 'SD') echo 'selected'; ?>>SD</option>
										<option value="SMP" <?php if($row->TINGKAT == 'SMP') echo 'selected'; ?>>SMP</option>
										<option value="SMA" <?php if($row->TINGKAT == 'SMA') echo 'selected'; ?>>SMA</option>
										<option value="D3" <?php if($row->TINGKAT == 'D3') echo 'selected'; ?>>DIPLOMA (D3)</option>
										<option value="S1" <?php if($row->TINGKAT == 'S1') echo 'selected'; ?>>SARJANA (S1)</option>
										<option value="S2" <?php if($row->TINGKAT == 'S2') echo 'selected'; ?>>PASCA SARJANA (S2)</option>
									</select>
								</td>
								<td><input type="text" name="JURUSAN[]" value="<?php echo $row->JURUSAN ?>" class="form-control"></td>
								<td><input type="text" name="INSTITUSI[]" value="<?php echo $row->INSTITUSI ?>" class="form-control"></td>
								<td><input type="text" name="LOKASI[]" value="<?php echo $row->LOKASI ?>" class="form-control"></td>
								<td><input type="text" name="TAHUN_MULAI[]" value="<?php echo $row->TAHUN_MULAI ?>" class="form-control"></td>
								<td><input type="text" name="TAHUN_SELESAI[]" value="<?php echo $row->TAHUN_SELESAI ?>" class="form-control"></td>
								<td><input type="text" name="GPA[]" value="<?php echo $row->GPA ?>" class="form-control"></td>
								<td>
									<span class="input-group-btn">
									<button type="button" class="btn btn-danger btn-flat del-formal" title="Hapus Data">
										<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
									</button>
									</span>
								</td>
							</tr>
							<?php }} ?>
						</table>
						
						<table class="table table-bordered nonf">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="4">Pendidikan Non Formal</th>
								<th colspan="2" class="text-right">
									<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Pendidikan" style="width: 150px;" id="add-nonf">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
									</span>
								</th>
							</tr>
							<tr>
								<th class="text-center" style="width: 20px;">No</th>
								<th class="text-center">Nama Kursus / Training</th>
								<th class="text-center">Tempat</th>
								<th class="text-center" style="width: 335px;">Periode</th>
								<th class="text-center">Keterangan</th>
								<th class="text-center" style="width: 50px;"></th>
							</tr>
							<?php $PEND_NONFORMAL = db_fetch("SELECT * FROM kursus_karyawan WHERE CALON_KARYAWAN_ID='$ID'");
							if(count($PEND_NONFORMAL) > 0){ foreach ($PEND_NONFORMAL as $key => $row) { ?>
							<tr>
								<td><?php echo $key+1 ?></td>
								<td><input type="text" name="NAMA_KURSUS[]" value="<?php echo $row->NAMA_KURSUS ?>" class="form-control"></td>
								<td><input type="text" name="TEMPAT[]" value="<?php echo $row->TEMPAT ?>" class="form-control"></td>
								<td class="text-center">
									<input type="text" name="PERIODE_MULAI[]" value="<?php echo $row->PERIODE_MULAI ?>" class="form-control datepicker" style="display: inline !important; width: 150px;"> - <input type="text" name="PERIODE_SELESAI[]" value="<?php echo $row->PERIODE_SELESAI ?>" class="form-control datepicker" style="display: inline !important; width: 150px;">
								</td>
								<td><input type="text" name="KETERANGAN[]" value="<?php echo $row->KETERANGAN ?>" class="form-control"></td>
								<td class="text-center">
									<span class="input-group-btn">
									<button type="button" class="btn btn-danger btn-flat del-nonf" title="Hapus Data">
										<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
									</button>
									</span>
								</td>
							</tr>
							<?php }} ?>
						</table>

						<table class="table table-bordered bahasa">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="5">Bahasa asing yang dikuasai</th>
								<th colspan="4" class="text-right">
									<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Bahasa" style="width: 150px;" id="add-bahasa">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
									</span>
								</th>
							</tr>
							<tr>
								<th rowspan="2" class="text-center" style="width: 20px;">No</th>
								<th rowspan="2" class="text-center">Bahasa</th>
								<th colspan="3" class="text-center">Lisan</th>
								<th colspan="3" class="text-center">Tulisan</th>
								<th rowspan="2" class="text-center" style="width: 50px;"></th>
							</tr>
							<tr>
								<th class="text-center" style="width: 100px;">Kurang</th>
								<th class="text-center" style="width: 100px;">Cukup</th>
								<th class="text-center" style="width: 100px;">Baik</th>
								<th class="text-center" style="width: 100px;">Kurang</th>
								<th class="text-center" style="width: 100px;">Cukup</th>
								<th class="text-center" style="width: 100px;">Baik</th>
							</tr>
							<?php $BHS_ASING = db_fetch("SELECT * FROM bahasa_karyawan WHERE CALON_KARYAWAN_ID='$ID'");
							if(count($BHS_ASING) > 0){ foreach ($BHS_ASING as $key => $row) { ?>
							<tr>
								<td><?php echo $key+1 ?></td>
								<td><input type="text" name="BAHASA[]" value="<?php echo $row->BAHASA ?>" class="form-control"></td>
								<td class="text-center">
									<input type="radio" class="form-check-input" name="LISAN[]" <?php if($row->LISAN == 'kurang') echo 'checked' ?> value="kurang">
								</td>
								<td class="text-center">
									<input type="radio" class="form-check-input" name="LISAN[]" <?php if($row->LISAN == 'cukup') echo 'checked' ?> value="cukup">
								</td>
								<td class="text-center">
									<input type="radio" class="form-check-input" name="LISAN[]" <?php if($row->LISAN == 'baik') echo 'checked' ?> value="baik">
								</td>
								<td class="text-center">
									<input type="radio" class="form-check-input" name="TULISAN[]" <?php if($row->TULISAN == 'kurang') echo 'checked' ?> value="kurang">
								</td>
								<td class="text-center">
									<input type="radio" class="form-check-input" name="TULISAN[]" <?php if($row->TULISAN == 'cukup') echo 'checked' ?> value="cukup">
								</td>
								<td class="text-center">
									<input type="radio" class="form-check-input" name="TULISAN[]" <?php if($row->TULISAN == 'baik') echo 'checked' ?> value="baik">
								</td>
								<td class="text-center" style="width: 50px;">
									<span class="input-group-btn">
									<button type="button" class="btn btn-danger btn-flat del-bahasa" title="Hapus Data">
										<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
									</button>
									</span>
								</td>
							</tr>
							<?php }} ?>
						</table>

						<div class="form-group" style="padding-left: 20px;">
							<button name="UPDATE_TYPE" type="submit" value="DATA_PENDIDIKAN" class="btn btn-primary" onclick="$('#form').submit()">
								<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
							</button>
						</div>
					</div>
				</div>
			</div>
			<?php /* END OF TAB 3 */ ?>
			<?php /* TAB 4 : PENGALAMAN ORGANISASI */ ?>
			<div role="tabpanel" class="tab-pane" id="tab4">
				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive">
						<table class="table table-bordered organisasi">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="4">Pengalaman Organisasi</th>
								<th colspan="2" class="text-right">
									<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Organisasi" style="width: 150px;" id="add-organisasi">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
									</span>
								</th>
							</tr>
							<tr>
								<th class="text-center" style="width: 20px;">No</th>
								<th class="text-center">Nama Organisasi</th>
								<th class="text-center">Jabatan</th>
								<th class="text-center">Lokasi</th>
								<th class="text-center">Periode (Tahun)</th>
								<th class="text-center" style="width: 50px;"></th>
							</tr>
							<?php $ORG_KARYAWAN = db_fetch("SELECT * FROM organisasi_karyawan WHERE CALON_KARYAWAN_ID='$ID'");
							if(count($ORG_KARYAWAN) > 0){ foreach ($ORG_KARYAWAN as $key => $row) { ?>
							<tr>
								<td><?php echo $key+1 ?></td>
								<td>
									<input type="text" name="NAMA_ORGANISASI[]" value="<?php echo $row->NAMA_ORGANISASI ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="JABATAN_ORGANISASI[]" value="<?php echo $row->JABATAN_ORGANISASI ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="LOKASI_ORGANISASI[]" value="<?php echo $row->LOKASI_ORGANISASI ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="PERIODE_ORGANISASI[]" value="<?php echo $row->PERIODE_ORGANISASI ?>" class="form-control">
								</td>
								<td class="text-center">
									<span class="input-group-btn">
									<button type="button" class="btn btn-danger btn-flat del-organisasi" title="Hapus Data">
										<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
									</button>
									</span>
								</td>
							</tr>
							<?php }} ?>
						</table>
						</div>
						<div class="form-group" style="padding-left: 20px;">
							<button name="UPDATE_TYPE" type="submit" value="DATA_ORGANISASI" class="btn btn-primary" onclick="$('#form').submit()">
								<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
							</button>
						</div>
						
					</div>
				</div>
			</div>
			<?php /* END OF TAB 4 */ ?>
			<?php /* TAB 5 : LINGKUNGAN KELUARGA */ ?>
			<div role="tabpanel" class="tab-pane" id="tab5">
				<div class="row">
					<div class="col-md-12">
						<table class="table table-bordered keluarga-inti">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="7">Susunan Keluarga Inti ( Istri / Suami dan Anak - Anak )</th>
								<th colspan="2" class="text-right">
									<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Keluarga" style="width: 150px;" id="add-keluarga-inti">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
									</span>
								</th>
							</tr>
							<tr>
								<th class="text-center" style="width: 20px;">No</th>
								<th class="text-center">Anggota</th>
								<th class="text-center">Nama</th>
								<th class="text-center">L/P</th>
								<th class="text-center">Tempat Lahir</th>
								<th class="text-center">Tgl Lahir</th>
								<th class="text-center">Pendidikan</th>
								<th class="text-center">Pekerjaan</th>
								<th></th>
							</tr>
							<?php $KLRG_KARYAWAN_INTI = db_fetch("SELECT * FROM keluarga_karyawan WHERE CALON_KARYAWAN_ID='$ID' AND JENIS_KELUARGA='INTI'");
							if(count($KLRG_KARYAWAN_INTI) > 0){ foreach ($KLRG_KARYAWAN_INTI as $key => $row) { ?>
							<tr>
								<td><?php echo $key+1 ?></td>
								<td>
									<select class="form-control" name="ANGGOTA_KELUARGA_INTI[]">
										<option value="SUAMI" <?php if($row->ANGGOTA_KELUARGA=='SUAMI') echo "selected"; ?>>SUAMI</option>
										<option value="ISTRI" <?php if($row->ANGGOTA_KELUARGA=='ISTRI') echo "selected"; ?>>ISTRI</option>
										<option value="ANAK1" <?php if($row->ANGGOTA_KELUARGA=='ANAK1') echo "selected"; ?>>ANAK 1</option>
										<option value="ANAK2" <?php if($row->ANGGOTA_KELUARGA=='ANAK2') echo "selected"; ?>>ANAK 2</option>
										<option value="ANAK3" <?php if($row->ANGGOTA_KELUARGA=='ANAK3') echo "selected"; ?>>ANAK 3</option>
									</select>
								</td>
								<td>
									<input type="text" name="NAMA_KELUARGA_INTI[]" value="<?php echo $row->NAMA_KELUARGA ?>" class="form-control">
								</td>
								<td>
									<select class="form-control" name="GENDER_INTI[]">
										<option value="L" <?php if($row->GENDER=='L') echo "selected"; ?>>L</option>
										<option value="P" <?php if($row->GENDER=='P') echo "selected"; ?>>P</option>
									</select>
								</td>
								<td>
									<input type="text" name="TP_LAHIR_KELUARGA_INTI[]" value="<?php echo $row->TP_LAHIR_KELUARGA ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="TGL_LAHIR_KELUARGA_INTI[]" value="<?php echo $row->TGL_LAHIR_KELUARGA ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="PENDIDIKAN_KELUARGA_INTI[]" value="<?php echo $row->PENDIDIKAN_KELUARGA ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="PEKERJAAN_KELUARGA_INTI[]" value="<?php echo $row->PEKERJAAN_KELUARGA ?>" class="form-control">
								</td>
								<td>
									<span class="input-group-btn">
									<button type="button" class="btn btn-danger btn-flat del-keluarga-inti" title="Hapus Data">
										<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
									</button>
									</span>
								</td>
							</tr>
							<?php }} ?>
						</table>
						<table class="table table-bordered keluarga-besar">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="7">Susunan Keluarga ( Ayah, Ibu, Saudara Kandung, termasuk Anda )</th>
								<th colspan="2" class="text-right">
									<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Keluarga" style="width: 150px;" id="add-keluarga-besar">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
									</span>
								</th>
							</tr>
							<tr>
								<th class="text-center" style="width: 20px;">No</th>
								<th class="text-center">Anggota</th>
								<th class="text-center">Nama</th>
								<th class="text-center">L/P</th>
								<th class="text-center">Tempat Lahir</th>
								<th class="text-center">Tgl Lahir</th>
								<th class="text-center">Pendidikan</th>
								<th class="text-center">Pekerjaan</th>
								<th></th>
							</tr>
							<?php $KLRG_KARYAWAN_BESAR = db_fetch("SELECT * FROM keluarga_karyawan WHERE CALON_KARYAWAN_ID='$ID' AND JENIS_KELUARGA='BESAR'");
							if(count($KLRG_KARYAWAN_BESAR) > 0){ foreach ($KLRG_KARYAWAN_BESAR as $key => $row) { ?>
							<tr>
								<td><?php echo $key+1 ?></td>
								<td>
									<select class="form-control" name="ANGGOTA_KELUARGA_BESAR[]">
										<option value="AYAH" <?php if($row->ANGGOTA_KELUARGA=='AYAH') echo "selected"; ?>>AYAH</option>
										<option value="IBU" <?php if($row->ANGGOTA_KELUARGA=='IBU') echo "selected"; ?>>IBU</option>
										<option value="ANAK1" <?php if($row->ANGGOTA_KELUARGA=='ANAK1') echo "selected"; ?>>ANAK 1</option>
										<option value="ANAK2" <?php if($row->ANGGOTA_KELUARGA=='ANAK2') echo "selected"; ?>>ANAK 2</option>
										<option value="ANAK3" <?php if($row->ANGGOTA_KELUARGA=='ANAK3') echo "selected"; ?>>ANAK 3</option>
										<option value="ANAK4" <?php if($row->ANGGOTA_KELUARGA=='ANAK4') echo "selected"; ?>>ANAK 4</option>
										<option value="ANAK5" <?php if($row->ANGGOTA_KELUARGA=='ANAK5') echo "selected"; ?>>ANAK 5</option>
									</select>
								</td>
								<td><input type="text" name="NAMA_KELUARGA_BESAR[]" value="<?php echo $row->NAMA_KELUARGA ?>" class="form-control"></td>
								<td>
									<select class="form-control" name="GENDER_BESAR[]">
										<option value="L" <?php if($row->ANGGOTA_KELUARGA=='L') echo "selected"; ?>>L</option>
										<option value="P" <?php if($row->ANGGOTA_KELUARGA=='P') echo "selected"; ?>>P</option>
									</select>
								</td>
								<td>
									<input type="text" name="TP_LAHIR_KELUARGA_BESAR[]" value="<?php echo $row->TP_LAHIR_KELUARGA ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="TGL_LAHIR_KELUARGA_BESAR[]" value="<?php echo $row->TGL_LAHIR_KELUARGA ?>" class="form-control">
								</td>
								<td>
									<input type="text" name="PENDIDIKAN_KELUARGA_BESAR[]" value="<?php echo $row->PENDIDIKAN_KELUARGA ?>"class="form-control">
								</td>
								<td>
									<input type="text" name="PEKERJAAN_KELUARGA_BESAR[]" value="<?php echo $row->PEKERJAAN_KELUARGA ?>"class="form-control">
								</td>
								<td>
									<span class="input-group-btn">
									<button type="button" class="btn btn-danger btn-flat del-keluarga-besar" title="Hapus Data">
										<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
									</button>
									</span>
								</td>
							</tr>
							<?php }} ?>
						</table>

						<div class="form-group" style="padding-left: 20px;">
							<button name="UPDATE_TYPE" type="submit" value="DATA_KELUARGA" class="btn btn-primary" onclick="$('#form').submit()">
								<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
							</button>
						</div>
					</div>
				</div>
			</div>
			<?php /* END OF TAB 5 */ ?>
			<?php /* TAB 6 : RIWAYAT PEKERJAAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab6">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Sebutkan tugas, tanggung jawab dan wewenang Pada Jabatan terakhir<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="TUGAS_JABATAN"><?php echo isset($EDIT->TUGAS_JABATAN) ? $EDIT->TUGAS_JABATAN : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Jabatan Atasan pada posisi terakhir<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="JABATAN_ATASAN" value="<?php echo isset($EDIT->JABATAN_ATASAN) ? $EDIT->JABATAN_ATASAN : '' ?>" class="form-control">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Masalah terpenting yang pernah dihadapi dan bagaimana mengatasinya<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="MASALAH_PENTING"><?php echo isset($EDIT->MASALAH_PENTING) ? $EDIT->MASALAH_PENTING : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Jumlah anak buah pada posisi terakhir<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<input type="text" name="JUMLAH_ANAK_BUAH" value="<?php echo isset($EDIT->JUMLAH_ANAK_BUAH) ? $EDIT->JUMLAH_ANAK_BUAH : '' ?>" class="form-control">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-12 pengalaman">
					<table class="table table-bordered">
						<tr style="background-color: #E9ECEF; color: #495057;">
							<th>Pengalaman kerja, dimulai dari pekerjaan calon karyawan yang terakhir (saat ini)</th>
							<th class="text-center" style="width: 200px;">
								<span class="input-group-btn" style="display: inline;">
								<button type="button" class="btn btn-primary btn-flat" title="Tmabah Data Pengalaman" style="width: 150px;" id="add-pengalaman">
									<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
								</button>
								</span>
							</th>
						</tr>
					</table>
					<?php $PENGALAMAN_KARYAWAN = db_fetch("SELECT * FROM pengalaman_karyawan WHERE CALON_KARYAWAN_ID='$ID'");
					if(count($PENGALAMAN_KARYAWAN) > 0){ foreach ($PENGALAMAN_KARYAWAN as $key => $row) { ?>
					<table class="table">
						<tr>
							<td class="text-right" style="vertical-align: middle; width: 180px;">Nama Perusahaan</td>
							<td><input type="text" name="NAMA_PERUSAHAAN[]" value="<?php echo $row->NAMA_PERUSAHAAN ?>" class="form-control"></td>
							<td></td>
							<td class="text-right" style="vertical-align: middle; width: 180px;">Jabatan Awal</td>
							<td><input type="text" name="JABATAN_AWAL[]" value="<?php echo $row->JABATAN_AWAL ?>" class="form-control"></td>
						</tr>
						<tr>
							<td class="text-right" style="vertical-align: middle;">Bergerak di Bidang </td>
							<td><input type="text" name="BIDANG_USAHA[]" value="<?php echo $row->BIDANG_USAHA ?>" class="form-control"></td>
							<td></td>
							<td class="text-right" style="vertical-align: middle;">Jabatan Akhir</td>
							<td><input type="text" name="JABATAN_AKHIR[]" value="<?php echo $row->JABATAN_AKHIR ?>" class="form-control"></td>
						</tr>
						<tr>
							<td class="text-right" style="vertical-align: middle;">Alamat</td>
							<td><input type="text" name="ALAMAT_PERUSAHAAN[]" value="<?php echo $row->ALAMAT_PERUSAHAAN ?>" class="form-control"></td>
							<td></td>
							<td class="text-right" style="vertical-align: middle;">Gaji Pokok</td>
							<td><input type="text" name="GAPOK_SEBELUMNYA[]" value="<?php echo $row->GAPOK_SEBELUMNYA ?>" class="form-control currency"></td>
						</tr>
						<tr>
							<td class="text-right" style="vertical-align: middle;">Nama Atasan Langsung </td>
							<td><input type="text" name="ATASAN[]" value="<?php echo $row->ATASAN ?>" class="form-control"></td>
							<td></td>
							<td class="text-right" style="vertical-align: middle;">Tunjangan Lainnya </td>
							<td><input type="text" name="TUNJANGAN_LAINNYA[]" value="<?php echo $row->TUNJANGAN_LAINNYA ?>" class="form-control"></td>
						</tr>
						<tr>
							<td class="text-right" style="vertical-align: middle;">No. Telepon</td>
							<td><input type="text" name="NO_TELP_PERUSAHAAN[]" value="<?php echo $row->NO_TELP_PERUSAHAAN ?>" class="form-control"></td>
							<td></td>
							<td class="text-right" style="vertical-align: middle;">Alasan Pengunduran Diri </td>
							<td><input type="text" name="ALASAN_RESIGN[]" value="<?php echo $row->ALASAN_RESIGN ?>" class="form-control"></td>
						</tr>
						<tr>
							<td class="text-right" style="vertical-align: middle;">Periode Kerja</td>
							<td><input type="text" name="PERIODE_BEKERJA[]" value="<?php echo $row->PERIODE_BEKERJA ?>" class="form-control"></td>
							<td></td>
							<td class="text-right" style="vertical-align: middle;">Deskripsi Pekerjaan </td>
							<td><input type="text" name="DESKRIPSI_PEKERJAAN[]" value="<?php echo $row->DESKRIPSI_PEKERJAAN ?>" class="form-control"></td>
						</tr>
						<tr>
							<td colspan="5" class="text-right">
								<span class="input-group-btn" style="display: inline;">
								<button type="button" class="btn btn-danger btn-flat del-pengalaman" title="Hapus Data" style="width: 150px;">
									<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Data
								</button>
								</span>
							</td>
						</tr>
					</table>
					<?php }} ?>
				</div>
				<div class="form-group" style="padding-left: 40px;">
					<button name="UPDATE_TYPE" type="submit" value="DATA_PENGALAMAN" class="btn btn-primary" onclick="$('#form').submit()">
						<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
					</button>
				</div>
			</div>
			<?php /* END OF TAB 6 */ ?>
			<?php /* TAB 7 : RIWAYAT PEKERJAAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab7">
				<div class="row">
					<div class="col-md-12">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Hobi Anda<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<input type="text" name="HOBI" value="<?php echo set_value('HOBI', $EDIT->HOBI) ?>" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Apa yang mendorong untuk bekerja<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="MOTIVASI_BEKERJA"><?php echo isset($EDIT->MOTIVASI_BEKERJA) ? $EDIT->MOTIVASI_BEKERJA : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Mengapa ingin bekerja di perusahaan kami<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="MOTIVASI_DIAIRKON"><?php echo isset($EDIT->MOTIVASI_DIAIRKON) ? $EDIT->MOTIVASI_DIAIRKON : '' ?></textarea>
								</div>
							</div>
							<div class="form-group" style="padding-left: 20px;">
							<button name="UPDATE_TYPE" type="submit" value="DATA_MINAT" class="btn btn-primary" onclick="$('#form').submit()">
								<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
							</button>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Gaji yang inginkan<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<input type="text" name="EXPECTED_SALARY" value="<?php echo set_value('EXPECTED_SALARY', $EDIT->EXPECTED_SALARY) ?>" class="form-control currency" maxlength="20">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-12 control-label" style="text-align: left;">
									Sebutkan fasilitas lainnya yang diharapkan<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<textarea class="form-control" rows="5" name="FASILITAS_LAINNYA"><?php echo isset($EDIT->FASILITAS_LAINNYA) ? $EDIT->FASILITAS_LAINNYA : '' ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Kapan siap bekerja<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<input type="text" name="SIAP_BEKERJA" value="<?php echo set_value('SIAP_BEKERJA', $EDIT->SIAP_BEKERJA) ?>" class="form-control datepicker">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Bersedia di luar daerah<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9" style="padding-top: 5px;">
									<label class="form-check-label">
										<input class="form-check-input" type="radio" name="LUAR_DAERAH" value="Ya" <?php if($EDIT->LUAR_DAERAH == 'Ya' ) echo 'checked' ?>> Ya
									</label>
									<label class="form-check-label" style="padding-left:30px; ">
										<input class="form-check-input" type="radio" name="LUAR_DAERAH" value="Tidak" <?php if($EDIT->LUAR_DAERAH == 'Tidak' ) echo 'checked' ?>> Tidak
									</label>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label" style="text-align: left;">
									Jika Tidak, mengapa<!--<span style="color:red; padding-left: 5px;">*</span>-->
								</label>
								<div class="col-sm-9">
									<textarea class="form-control" rows="2" name="ALASAN_DILUAR_DAERAH"><?php echo isset($EDIT->ALASAN_DILUAR_DAERAH) ? $EDIT->ALASAN_DILUAR_DAERAH : '' ?></textarea>
								</div>
							</div>
						</div>
					</div>	
				</div>
			</div>
			<?php /* END OF TAB 7 */ ?>
			<?php /* TAB 8 : RIWAYAT PEKERJAAN */ ?>
			<div role="tabpanel" class="tab-pane" id="tab8">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Sumber informasi lowongan kerja<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Internet" <?php if($EDIT->INFO_LOKER == 'Internet' ) echo 'checked' ?>> Internet
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Sosmed" <?php if($EDIT->INFO_LOKER == 'Sosmed' ) echo 'checked' ?>> Sosmed
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Iklan" <?php if($EDIT->INFO_LOKER == 'Jobfair' ) echo 'checked' ?>> Jobfair
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="INFO_LOKER" value="Lain-Lain" <?php if($EDIT->INFO_LOKER == 'Lain-Lain' ) echo 'checked' ?>> Lain-Lain
								</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="INFO_LOKER_LAINNYA" value="<?php echo set_value('INFO_LOKER_LAINNYA', $EDIT->INFO_LOKER_LAINNYA) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Saudara yang bekerja di Airkon<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="KERABAT_AIRKON" value="Ya" <?php if($EDIT->KERABAT_AIRKON == 'Ya' ) echo 'checked' ?>> Ya
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="KERABAT_AIRKON" value="Tidak" <?php if($EDIT->KERABAT_AIRKON == 'Tidak' ) echo 'checked' ?>> Tidak
								</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="NAMA_KERABAT" value="<?php echo set_value('NAMA_KERABAT', $EDIT->NAMA_KERABAT) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Pernah sakit yang lama sembuh<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="RIWAYAT_KESEHATAN" value="Ya" <?php if($EDIT->RIWAYAT_KESEHATAN == 'Ya' ) echo 'checked' ?>> Ya
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="RIWAYAT_KESEHATAN" value="Tidak" <?php if($EDIT->RIWAYAT_KESEHATAN == 'Tidak' ) echo 'checked' ?>> Tidak
								</label>
							</div>
							<div class="col-sm-6">
								<input type="text" name="NAMA_PENYAKIT" value="<?php echo set_value('NAMA_PENYAKIT', $EDIT->NAMA_PENYAKIT) ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label" style="text-align: left;">
								Dirawat dalam 1 tahun terakhir<!--<span style="color:red; padding-left: 5px;">*</span>-->
							</label>
							<div class="col-sm-4" style="padding-top: 5px;">
								<label class="form-check-label">
									<input class="form-check-input" type="radio" name="RIWAYAT_RAWAT" value="Ya" <?php if($EDIT->RIWAYAT_RAWAT == 'Ya' ) echo 'checked' ?>> Ya
								</label>
								<label class="form-check-label" style="padding-left:30px; ">
									<input class="form-check-input" type="radio" name="RIWAYAT_RAWAT" value="Tidak" <?php if($EDIT->RIWAYAT_RAWAT == 'Tidak' ) echo 'checked' ?>> Tidak
								</label>
							</div>
							
						</div>

						<table class="table table-bordered penanggung">
							<tr style="background-color: #E9ECEF; color: #495057;">
								<th colspan="4">Orang yang dapat dihubungi apabila dalam keadaan darurat, minimal 2(dua)</th>
								<th colspan="2" class="text-right">
									<span class="input-group-btn" style="display: inline;">
									<button type="button" class="btn btn-primary btn-flat" title="Tambah Data" style="width: 150px;" id="add-penanggung">
										<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
									</button>
									</span>
								</th>
							</tr>
							<tr>
								<th class="text-center" style="width: 20px;">No</th>
								<th class="text-center">Nama</th>
								<th class="text-center">Alamat</th>
								<th class="text-center">No Telp / HP</th>
								<th class="text-center">Hubungan</th>
								<th style="width: 50px;"></th>
							</tr>
							<?php $PENANGGUNG_KARYAWAN = db_fetch("SELECT * FROM penanggung_karyawan WHERE CALON_KARYAWAN_ID='$ID'");
							if(count($PENANGGUNG_KARYAWAN) > 0){ foreach ($PENANGGUNG_KARYAWAN as $key => $row) { ?>
							<tr>
								<td><?php echo $no+1 ?></td>
								<td><input type="text" name="NAMA_PENANGGUNG[]" value="<?php echo $row->NAMA_PENANGGUNG ?>" class="form-control"></td>
								<td><input type="text" name="ALAMAT_PENANGGUNG[]" value="<?php echo $row->ALAMAT_PENANGGUNG ?>" class="form-control"></td>
								<td><input type="text" name="TELP_PENANGGUNG[]" value="<?php echo $row->TELP_PENANGGUNG ?>" class="form-control"></td>
								<td><input type="text" name="HUBUNGAN_PENANGGUNG[]" value="<?php echo $row->HUBUNGAN_PENANGGUNG ?>" class="form-control"></td>
								<td>
									<span class="input-group-btn">
									<button type="button" class="btn btn-danger btn-flat del-penanggung" title="Hapus Data">
										<span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span>
									</button>
									</span>
								</td>
							</tr>
							<?php }} ?>
						</table>
						<div class="form-group" style="padding-left: 20px;">
						<button name="UPDATE_TYPE" type="submit" value="DATA_LAIN" class="btn btn-primary" onclick="$('#form').submit()">
							<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
						</button>
						</div>
					</div>	
				</div>
			</div>
			<?php /* END OF TAB 8 */ ?>
	</form>		
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	/*$('input[name=NIK]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});*/

	$('#PROVINSI').select2({
		theme: "bootstrap",
		ajax: {
			url: 'provinsi-json.php',
			dataType: 'json',
		}
	});

	$('#PROVINSI').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-kode',data.kode);
	});

	$('#PROVINSI').on('select2:select', function (e) {
		$('#KOTA').val(null).trigger('change');
	});

	$('#KOTA').select2({
		theme: "bootstrap",
		ajax: {
			url:'kota-json.php',
			dataType: 'json',
			data: function (params) {
				provinsi_id = $('#PROVINSI').find(':selected').attr('data-kode');
				return {
					q: params.term,
					provinsi_id: provinsi_id,
					page_limit: 20
				}
			}
		}
	});

	$('#PROVINSI_KTP').select2({
		theme: "bootstrap",
		ajax: {
			url: 'provinsi-json.php',
			dataType: 'json',
		}
	});

	$('#PROVINSI_KTP').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('data-kode',data.kode);
	});

	$('#PROVINSI_KTP').on('select2:select', function (e) {
		$('#KOTA_KTP').val(null).trigger('change');
	});

	$('#KOTA_KTP').select2({
		theme: "bootstrap",
		ajax: {
			url:'kota-json.php',
			dataType: 'json',
			data: function (params) {
				provinsi_id = $('#PROVINSI_KTP').find(':selected').attr('data-kode');
				return {
					q: params.term,
					provinsi_id: provinsi_id,
					page_limit: 20
				}
			}
		}
	});

	$('#POSISI_ID').select2({
		theme: "bootstrap",
		ajax: {
			url: 'posisi-ac.php',
			dataType: 'json',
		}
	});

	$('#POSISI_ID').on('select2:select', function (e) {
		$('#LOWONGAN_ID').val(null).trigger('change');
	});

	$('#POSISI_ID').change(function(){
		$('#APPLICANT_NO').val(null);
	});

	$('#LOWONGAN_ID').select2({
		theme: "bootstrap",
		ajax: {
			url:'lowongan-ac.php',
			dataType: 'json',
			data: function (params) {
				posisi_id = $("#POSISI_ID").val();
				return {
					q: params.term,
					posisi_id: posisi_id,
					page_limit: 20
				}
			}
		}
	});

	$('#LOWONGAN_ID').change(function(){
		data=$(this).select2('data')[0];
		if (typeof(data) != "undefined"){
			$(this).find(':selected').attr('data-kode',data.kode);
			$("#APPLICANT_NO").val($(this).find(':selected').attr('data-kode')+'-'+$('#tanggal').val()+'-'+$('#no_urut').val());
		}
	});

	delete_formal();
	delete_nonf();
	delete_bahasa();
	delete_organisasi();
	delete_keluarga_inti();
	delete_keluarga_besar();
	delete_pengalaman();
	delete_penanggung();

});

$(function(){
	$("input:file").change(function (){
		$(this).css("border", "2px solid #28A745");
    });

	$('#add-formal').click(function(i){
		$('.formal').append('<tr><td></td><td><select class="form-control" name="TINGKAT[]"><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="D3">DIPLOMA (D3)</option><option value="S1">SARJANA (S1)</option><option value="S2">PASCA SARJANA (S2)</option></select></td><td><input type="text" name="JURUSAN[]" class="form-control"></td><td><input type="text" name="INSTITUSI[]" class="form-control"></td><td><input type="text" name="LOKASI[]" class="form-control"></td><td><input type="text" name="TAHUN_MULAI[]" class="form-control"></td><td><input type="text" name="TAHUN_SELESAI[]" class="form-control"></td><td><input type="text" name="GPA[]" class="form-control"></td><td><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-formal" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
		return false;
	});

	$('#add-nonf').click(function(i){
		$('.nonf').append('<tr><td></td><td><input type="text" name="NAMA_KURSUS[]" class="form-control"></td><td><input type="text" name="TEMPAT[]" class="form-control"></td><td class="text-center"><input type="text" name="PERIODE_MULAI[]" autocomplete="off" class="form-control datepicker" style="display: inline !important; width: 150px;"> - <input type="text" name="PERIODE_SELESAI[]" autocomplete="off" class="form-control datepicker" style="display: inline !important; width: 150px;"></td><td><input type="text" name="KETERANGAN[]" class="form-control"></td><td class="text-center"><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-nonf" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
		$('.datepicker').datepick({dateFormat: 'yyyy-mm-dd'});
		return false;
	});

	$('#add-bahasa').click(function(i){
		$('.bahasa').append('<tr><td></td><td><input type="text" name="BAHASA[]" class="form-control"></td><td class="text-center"><input type="radio" class="form-check-input" name="LISAN[]" value="kurang"></td><td class="text-center"><input type="radio" class="form-check-input" name="LISAN[]" value="cukup"></td><td class="text-center"><input type="radio" class="form-check-input" name="LISAN[]" value="baik"></td><td class="text-center"><input type="radio" class="form-check-input" name="TULISAN[]" value="kurang"></td><td class="text-center"><input type="radio" class="form-check-input" name="TULISAN[]" value="cukup"></td><td class="text-center"><input type="radio" class="form-check-input" name="TULISAN[]" value="baik"></td><td class="text-center"><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-bahasa" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
		return false;
	});

	$('#add-organisasi').click(function(i){
		$('.organisasi').append('<tr><td></td><td><input type="text" name="NAMA_ORGANISASI[]" class="form-control"></td><td><input type="text" name="JABATAN_ORGANISASI[]" class="form-control"></td><td><input type="text" name="LOKASI_ORGANISASI[]" class="form-control"></td><td><input type="text" name="PERIODE_ORGANISASI[]" class="form-control"></td><td class="text-center"><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-organisasi" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
		return false;
	});

	$('#add-keluarga-inti').click(function(i){
		$('.keluarga-inti').append('<tr><td></td><td><select class="form-control" name="ANGGOTA_KELUARGA_INTI[]"><option value="SUAMI">SUAMI</option><option value="ISTRI">ISTRI</option><option value="ANAK1">ANAK 1</option><option value="ANAK2">ANAK 2</option><option value="ANAK3">ANAK 3</option></select></td><td><input type="text" name="NAMA_KELUARGA_INTI[]" class="form-control"></td><td><select class="form-control" name="GENDER_INTI[]"><option value="L">L</option><option value="P">P</option></select></td><td><input type="text" name="TP_LAHIR_KELUARGA_INTI[]" class="form-control"></td><td><input type="text" name="TGL_LAHIR_KELUARGA_INTI[]" class="form-control datepicker"></td><td><input type="text" name="PENDIDIKAN_KELUARGA_INTI[]" class="form-control"></td><td><input type="text" name="PEKERJAAN_KELUARGA_INTI[]" class="form-control"></td><td><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-keluarga-inti" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
		$('.datepicker').datepick({dateFormat: 'yyyy-mm-dd'});
		return false;
	});

	$('#add-keluarga-besar').click(function(i){
		$('.keluarga-besar').append('<tr><td></td><td><select class="form-control" name="ANGGOTA_KELUARGA_BESAR[]"><option value="AYAH">AYAH</option><option value="IBU">IBU</option><option value="ANAK1">ANAK 1</option><option value="ANAK2">ANAK 2</option><option value="ANAK3">ANAK 3</option><option value="ANAK4">ANAK 4</option><option value="ANAK5">ANAK 5</option></select></td><td><input type="text" name="NAMA_KELUARGA_BESAR[]" class="form-control"></td><td><select class="form-control" name="GENDER_BESAR[]"><option value="L">L</option><option value="P">P</option></select></td><td><input type="text" name="TP_LAHIR_KELUARGA_BESAR[]" class="form-control"></td><td><input type="text" name="TGL_LAHIR_KELUARGA_BESAR[]" class="form-control datepicker"></td><td><input type="text" name="PENDIDIKAN_KELUARGA_BESAR[]" class="form-control"></td><td><input type="text" name="PEKERJAAN_KELUARGA_BESAR[]" class="form-control"></td><td><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-keluarga-inti" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
		$('.datepicker').datepick({dateFormat: 'yyyy-mm-dd'});
		return false;
	});

	$('#add-pengalaman').click(function(i){
		$('.pengalaman').append('<table class="table"><tr><td class="text-right" style="vertical-align: middle; width: 180px;">Nama Perusahaan</td><td><input type="text" name="NAMA_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle; width: 180px;">Jabatan Awal</td><td><input type="text" name="JABATAN_AWAL[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Bergerak di Bidang</td><td><input type="text" name="BIDANG_USAHA[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Jabatan Akhir</td><td><input type="text" name="JABATAN_AWKHIR[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Alamat</td><td><input type="text" name="ALAMAT_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Gaji Pokok</td><td><input type="text" name="GAPOK_SEBELUMNYA[]" class="form-control currency"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Nama Atasan Langsung</td><td><input type="text" name="ATASAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Tunjangan Lainnya</td><td><input type="text" name="TUNJANGAN_LAINNYA[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">No. Telepon</td><td><input type="text" name="NO_TELP_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Alasan Pengunduran Diri</td><td><input type="text" name="ALASAN_RESIGN[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Periode Kerja</td><td><input type="text" name="PERIODE_BEKERJA[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Deskripsi Pekerjaan</td><td><input type="text" name="DESKRIPSI_PEKERJAAN[]" class="form-control"></td></tr><tr><td colspan="5" class="text-right"> <span class="input-group-btn" style="display: inline;"> <button type="button" class="btn btn-danger btn-flat del-pengalaman" title="Hapus Data" style="width: 150px;"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Data </button> </span></td></tr></table>');
		$('.datepicker').datepick({dateFormat: 'yyyy-mm-dd'});
		$('.currency').mask('000,000,000,000,000', {reverse: true});
		return false;
	});

	$('#add-penanggung').click(function(i){
		$('.penanggung').append('<tr><td></td><td><input type="text" name="NAMA_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="ALAMAT_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="TELP_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="HUBUNGAN_PENANGGUNG[]" class="form-control"></td><td> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-penanggung" title="Hapus Data"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> </button> </span></td></tr>');
		return false;
	});

});

function delete_formal(){
	$(document).on('click', '.del-formal', function() {
		$(this).closest('tr').remove();
	});
}

function delete_nonf(){
	$(document).on('click', '.del-nonf', function() {
		$(this).closest('tr').remove();
	});
}

function delete_bahasa(){
	$(document).on('click', '.del-bahasa', function() {
		$(this).closest('tr').remove();
	});
}

function delete_organisasi(){
	$(document).on('click', '.del-organisasi', function() {
		$(this).closest('tr').remove();
	});
}

function delete_keluarga_inti(){
	$(document).on('click', '.del-keluarga-inti', function() {
		$(this).closest('tr').remove();
	});
}

function delete_keluarga_besar(){
	$(document).on('click', '.del-keluarga-besar', function() {
		$(this).closest('tr').remove();
	});
}

function delete_pengalaman(){
	$(document).on('click', '.del-pengalaman', function() {
		$(this).closest('table').remove();
	});
}

function delete_penanggung(){
	$(document).on('click', '.del-penanggung', function() {
		$(this).closest('tr').remove();
	});
}
</script>

<?php 
include 'footer.php'; 
?>