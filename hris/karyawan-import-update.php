<?php
include 'app-load.php';
is_login();

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$allow_ext = array('xls');
	$filename = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
	$tmp_name = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
	$ext = strtolower(substr(strrchr($filename, "."), 1));

	if( ! is_uploaded_file($tmp_name)){
		$ERROR[] = 'Tidak ada file yang diupload.';
	}else if( ! in_array($ext,array('xls')) ){
		$ERROR[] = 'Ekstensi tidak diperbolehkan. Ekstensi yang dibolehkan xls.';
	}else{

		$rs = db_fetch(" SELECT JABATAN_ID,PROJECT_ID,UCASE(JABATAN) as JABATAN FROM jabatan ");
		$JABATAN = array();
		$PROJECT = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$JABATAN[$row->JABATAN] = $row->JABATAN_ID;
				$PROJECT[$row->JABATAN] = $row->PROJECT_ID;
			}
		}
		
		$rs = db_fetch(" SELECT PROJECT_ID,COMPANY_ID FROM project ");
		$COMPANY = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$COMPANY[$row->PROJECT_ID] = $row->COMPANY_ID;
			}
		}

		$rs = db_fetch(" SELECT POSISI_ID,UCASE(POSISI) AS POSISI FROM posisi ");
		$POSISI = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$POSISI[$row->POSISI] = $row->POSISI_ID;
			}
		}

		$rs = db_fetch(" SELECT KATEGORI_KEAHLIAN_ID,UCASE(KATEGORI_KEAHLIAN) AS KATEGORI_KEAHLIAN FROM kategori_keahlian ");
		$KK = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$KATEGORI_KEAHLIAN[$row->KATEGORI_KEAHLIAN] = $row->KATEGORI_KEAHLIAN_ID;
			}
		}

		$rs = db_fetch(" SELECT KEAHLIAN_ID,UCASE(KEAHLIAN) AS KEAHLIAN FROM keahlian ");
		$KN = array();
		if(count($rs)>0){
			foreach($rs as $row){
				$KEAHLIAN[$row->KEAHLIAN] = $row->KEAHLIAN_ID;
			}
		}
		
		require 'lib/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($tmp_name);
		$baris = $data->rowcount($sheet_index=0);

		$FIELDS = array(
			'KARYAWAN_ID',
			'NIK',
			'NAMA',
			'JK',
			'JABATAN_ID',
			'TGL_MASUK',
			'PROJECT_ID',
			'COMPANY_ID',
			'GAJI_POKOK',
			'TUNJ_KEAHLIAN',
			'TUNJ_PROYEK',
			'TUNJ_BACKUP',
			'TUNJ_SHIFT',
			'TUNJ_JABATAN',
			'JENIS_THR',
			'BPJS_JKK',
			'BPJS_JKM',
			'BPJS_KES',
			'BPJS_JHT',
			'BPJS_JP',
			'POSISI_ID',
			'JABATAN_BQ',
			'NO_KONTRAK',
			'TGL_MULAI_KONTRAK',
			'TGL_SELESAI_KONTRAK',
			'ALAMAT_KTP',
			'ALAMAT',
			'TP_LAHIR',
			'TGL_LAHIR',
			'NO_IDENTITAS',
			'NO_KK',
			'IBU_KANDUNG',
			'HP',
			'AGAMA',
			'STATUS_PTKP',
			'ST_KAWIN',
			'LULUSAN',
			'JURUSAN',
			'SCAN_IJAZAH',
			'TAHUN_LULUS',
			'PENGALAMAN',
			'KATEGORI_KEAHLIAN_ID',
			'KEAHLIAN_ID',
			'BPJS_KESEHATAN',
			'BPJS_KETENAGAKERJAAN',
			'BPJS_JAMINANPENSIUN',
			'NPWP',
			'ST_KERJA'
		);
		foreach($FIELDS as $F){
			$COL[] = '`'.$F.'`';
		}

		$FIELDS_STATUS = array('KARYAWAN_ID','HISTORI_STATUS','TGL');
		foreach($FIELDS_STATUS as $FS){
			$COL_STATUS[] = '`'.$FS.'`';
		}

		$FIELDS_KARIR = array('KARYAWAN_ID','JABATAN_ID','TGL');
		foreach($FIELDS_KARIR as $FK){
			$COL_KARIR[] = '`'.$FK.'`';
		}

		$FIELDS_POSISI = array('KARYAWAN_ID','POSISI_ID','TGL');
		foreach($FIELDS_POSISI as $FP){
			$COL_POSISI[] = '`'.$FP.'`';
		}

		$FIELDS_GAJI = array('KARYAWAN_ID','HISTORI_GAJI','TGL');
		foreach($FIELDS_GAJI as $FG){
			$COL_GAJI[] = '`'.$FG.'`';
		}

		$FIELDS_PENDIDIKAN = array('KARYAWAN_ID','TINGKAT','JURUSAN','TAHUN_SELESAI');
		foreach($FIELDS_PENDIDIKAN as $FPD){
			$COL_PENDIDIKAN[] = '`'.$FPD.'`';
		}

		$FIELDS_KELUARGA = array('KARYAWAN_ID','ANGGOTA_KELUARGA','NAMA_KELUARGA','GENDER');
		foreach($FIELDS_KELUARGA as $FKG){
			$COL_KELUARGA[] = '`'.$FKG.'`';
		}
		
		$ROW = 5;
		$isegment = $isegment_st = $isegment_kr = $isegment_ps = $isegment_gj = $isegment_pd = $isegment_kg = 1000;
		$segment = $segment_st = $segment_kr = $segment_ps = $segment_gj = $segment_pd = $segment_kg = 0;
		//$segment = 0;
		if($baris>0)
		{
			for ($i=$ROW; $i<=$baris; $i++)
			{
				$KARYAWAN_ID = db_escape($data->val($i, 1));
				$NIK = db_escape($data->val($i, 2));
				$NAMA = db_escape($data->val($i, 3));
				$JK = db_escape($data->val($i, 4));
				$JB = strtoupper(db_escape($data->val($i, 5)));
				$TGL_MASUK = date('Y-m-d',strtotime(db_escape($data->val($i, 6))));
				$GAJI_POKOK = db_escape($data->val($i, 7));
				$TUNJ_KEAHLIAN = db_escape($data->val($i, 8));
				$TUNJ_PROYEK = db_escape($data->val($i, 9));
				$TUNJ_BACKUP = db_escape($data->val($i, 10));
				$TUNJ_SHIFT = db_escape($data->val($i, 11));
				$TUNJ_JABATAN = db_escape($data->val($i, 12));
				$JENIS_THR = db_escape($data->val($i, 13));
				
				$BPJS_JKK = db_escape($data->val($i, 14));
				$BPJS_JKM = db_escape($data->val($i, 15));
				$BPJS_KES = db_escape($data->val($i, 16));
				$BPJS_JHT = db_escape($data->val($i, 17));
				$BPJS_JP = db_escape($data->val($i, 18));

				$PS = strtoupper(db_escape($data->val($i, 19)));
				$JABATAN_BQ = db_escape($data->val($i, 20));
				$NO_KONTRAK = db_escape($data->val($i, 21));
				$TGL_MULAI_KONTAK = db_escape($data->val($i, 22));
				$TGL_BERAKHIR_KONTAK = db_escape($data->val($i, 23));
				$ALAMAT_KTP = db_escape($data->val($i, 24));
				$ALAMAT = db_escape($data->val($i, 25));
				$TP_LAHIR = db_escape($data->val($i, 26));
				$TGL_LAHIR = db_escape($data->val($i, 27));
				$NO_IDENTITAS = db_escape($data->val($i, 28));
				$NO_KK = db_escape($data->val($i, 29));
				$IBU_KANDUNG = db_escape($data->val($i, 30));
				$HP = db_escape($data->val($i, 31));
				$AGAMA = db_escape($data->val($i, 32));
				$STATUS_PTKP = db_escape($data->val($i, 33));
				$ST_KAWIN = db_escape($data->val($i, 34));
				$LULUSAN = db_escape($data->val($i, 35));
				$JURUSAN = db_escape($data->val($i, 36));
				$SCAN_IJAZAH = db_escape($data->val($i, 37));
				$TAHUN_LULUS = db_escape($data->val($i, 38));
				$PENGALAMAN = db_escape($data->val($i, 39));
				$KK = db_escape($data->val($i, 40));
				$KN = db_escape($data->val($i, 41));
				
				$TRAINING = db_escape($data->val($i, 42));
				$SERTIFIKAT = db_escape($data->val($i, 43));
				$MASA_BERLAKU = db_escape($data->val($i, 44));
				$SIO = db_escape($data->val($i, 45));
				$MASA_BERLAKU_SIO = db_escape($data->val($i, 46));
				
				$BPJS_KETENAGAKERJAAN = db_escape($data->val($i, 47));
				$BPJS_JAMINAN_PENSIUN = db_escape($data->val($i, 48));
				$BPJS_KESEHATAN = db_escape($data->val($i, 49));
				$NPWP = db_escape($data->val($i, 50));
				
				$JABATAN_ID = isset($JABATAN[$JB]) ? $JABATAN[$JB] : '';
				$PROJECT_ID = isset($PROJECT[$JB]) ? $PROJECT[$JB] : '';
				$COMPANY_ID = isset($COMPANY[$PROJECT_ID]) ? $COMPANY[$PROJECT_ID] : '';
				$POSISI_ID = isset($POSISI[$PS]) ? $POSISI[$PS] : '';
				$KATEGORI_KEAHLIAN_ID = isset($KATEGORI_KEAHLIAN[$KK]) ? $KATEGORI_KEAHLIAN[$KK] : '';
				$KEAHLIAN_ID = isset($KEAHLIAN[$KN]) ? $KEAHLIAN[$KN] : '';
				$ST_KERJA = 'AKTIF';
				$TGL = $TGL_MASUK;
				$HISTORI_STATUS = $ST_KERJA;
				$HISTORI_GAJI = $GAJI_POKOK;
				$TINGKAT = $LULUSAN;
				$TAHUN_SELESAI = $TAHUN_LULUS;
				$ANGGOTA_KELUARGA='IBU';
				$NAMA_KELUARGA=$IBU_KANDUNG;
				$GENDER='P';
			
				foreach($FIELDS as $F){
					$VAL[$F] = "'".${$F}."'";
				}
				if(!empty($NIK) AND !empty($NAMA))
				{
					$TMP[$segment][] = '('.implode(',',$VAL).')';
					if($i >= $isegment){
						$isegment = $isegment + 1000;
						$segment = $segment + 1;
					}
				}

				if(!empty($NIK) AND !empty($NAMA))
				{

					foreach($FIELDS_STATUS as $FS){
						$VAL_STATUS[$FS] = "'".${$FS}."'";
					}
				
				
					$TMP_STATUS[$segment_st][] = '('.implode(',',$VAL_STATUS).')';
					if($i >= $isegment_st){
						$isegment_st = $isegment_st + 1000;
						$segment_st = $segment_st + 1;
					}
				

					foreach($FIELDS_KARIR as $FK){
						$VAL_KARIR[$FK] = "'".${$FK}."'";
					}

			
					$TMP_KARIR[$segment_kr][] = '('.implode(',',$VAL_KARIR).')';
					if($i >= $isegment_kr){
						$isegment_kr = $isegment_kr + 1000;
						$segment_kr = $segment_kr + 1;
					}
				

					foreach($FIELDS_POSISI as $FP){
						$VAL_POSISI[$FP] = "'".${$FP}."'";
					}
					
					$TMP_POSISI[$segment_ps][] = '('.implode(',',$VAL_POSISI).')';
					if($i >= $isegment_ps){
						$isegment_ps = $isegment_ps + 1000;
						$segment_ps = $segment_ps + 1;
					}

					foreach($FIELDS_GAJI as $FG){
						$VAL_GAJI[$FG] = "'".${$FG}."'";
					}

					$TMP_GAJI[$segment_gj][] = '('.implode(',',$VAL_GAJI).')';
					if($i >= $isegment_gj){
						$isegment_gj = $isegment_gj + 1000;
						$segment_gj = $segment_gj + 1;
					}
					

					foreach($FIELDS_PENDIDIKAN as $FPD){
						$VAL_PENDIDIKAN[$FPD] = "'".${$FPD}."'";
					}
					
					$TMP_PENDIDIKAN[$segment_pd][] = '('.implode(',',$VAL_PENDIDIKAN).')';
					if($i >= $isegment_pd){
						$isegment_pd = $isegment_pd + 1000;
						$segment_pd = $segment_pd + 1;
					}
					

					foreach($FIELDS_KELUARGA as $FKG){
						$VAL_KELUARGA[$FKG] = "'".${$FKG}."'";
					}
					
					$TMP_KELUARGA[$segment_kg][] = '('.implode(',',$VAL_KELUARGA).')';
					if($i >= $isegment_kg){
						$isegment_kg = $isegment_kg + 1000;
						$segment_kg = $segment_kg + 1;
					}
				}
				
			}

			$TOTAL = 0;
			if(count($TMP) > 0){ foreach($TMP as $tmp){
				echo "INSERT IGNORE karyawan (".implode(',',$COL).") VALUES ".implode(',',$tmp).'<br /><br />';
				//db_execute(" INSERT IGNORE karyawan (".implode(',',$COL).") VALUES ".implode(',',$tmp) );
				//$TOTAL = $TOTAL + $DB->Affected_Rows();
			}}

			if(count($TMP_STATUS) > 0){ foreach($TMP_STATUS as $tmp_status){
				//db_execute(" INSERT INTO histori_status (".implode(',',$COL_STATUS).") VALUES ".implode(',',$tmp_status) );
				echo "INSERT INTO histori_status (".implode(',',$COL_STATUS).") VALUES ".implode(',',$tmp_status).'<br /><br />';
			}}

			if(count($TMP_KARIR) > 0){ foreach($TMP_KARIR as $tmp_karir){
				//db_execute(" INSERT INTO histori_karir (".implode(',',$COL_KARIR).") VALUES ".implode(',',$tmp_karir) );
				echo "INSERT INTO histori_karir (".implode(',',$COL_KARIR).") VALUES ".implode(',',$tmp_karir).'<br /><br />';
			}}

			if(count($TMP_POSISI) > 0){ foreach($TMP_POSISI as $tmp_posisi){
				//db_execute(" INSERT INTO histori_posisi (".implode(',',$COL_POSISI).") VALUES ".implode(',',$tmp_posisi) );
				echo "INSERT INTO histori_posisi (".implode(',',$COL_POSISI).") VALUES ".implode(',',$tmp_posisi).'<br /><br />';
			}}

			if(count($TMP_GAJI) > 0){ foreach($TMP_GAJI as $tmp_gaji){
				//db_execute(" INSERT INTO histori_gaji (".implode(',',$COL_GAJI).") VALUES ".implode(',',$tmp_gaji) );
				echo "INSERT INTO histori_gaji (".implode(',',$COL_GAJI).") VALUES ".implode(',',$tmp_gaji).'<br /><br />';
			}}

			if(count($TMP_PENDIDIKAN) > 0){ foreach($TMP_PENDIDIKAN as $tmp_pendidikan){
				//db_execute(" INSERT INTO pedidikan_karyawan (".implode(',',$COL_PENDIDIKAN).") VALUES ".implode(',',$tmp_pendidikan) );
				echo "INSERT INTO pedidikan_karyawan (".implode(',',$COL_PENDIDIKAN).") VALUES ".implode(',',$tmp_pendidikan).'<br /><br />';
			}}

			if(count($TMP_KELUARGA) > 0){ foreach($TMP_KELUARGA as $tmp_keluarga){
				//db_execute(" INSERT INTO keluarga_karyawan (".implode(',',$COL_KELUARGA).") VALUES ".implode(',',$tmp_keluarga) );
				echo "INSERT INTO keluarga_karyawan (".implode(',',$COL_KELUARGA).") VALUES ".implode(',',$tmp_keluarga).'<br /><br />';
			}}

			$SUCCESS = $TOTAL . ' data berhasil di update';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Import Karyawan
		<a href="karyawan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Contoh Template</label>
					<div class="col-sm-8">
						<div class="form-control-static">
							<p><a href="static/tpl/TEMPLATE_KARYAWAN.xls" class="btn btn-success btn-sm">Download Template</a></p>
							<p><b>Catatan Penting : </b></p>
							<p>Pada kolom <b>PIN</b> nilai harus disesuaikan dengan <b>PIN</b> yang ada pada <b>mesin absensi</b></p>
							<p>Kolom <b>LEVEL JABATAN</b> penulisannya harus disamakan dengan menu <a href="jabatan.php" target="_blank">Level Jabatan</a></p>
							<p>Kolom <b>JABATAN</b> penulisannya harus disamakan dengan menu <a href="posisi.php" target="_blank">Jabatan</a></p>
							<p>Kolom <b>KATEGORI KEAHLIAN</b> penulisannya harus disamakan dengan menu <a href="kategori-keahlian.php" target="_blank">K. Keahlian</a></p>
							<p>Kolom <b>KEAHLIAN</b> penulisannya harus disamakan dengan menu <a href="keahlian.php" target="_blank">Keahlian</a></p>
							<p>Kolom <b>PENDIDIKAN TERAKHIR</b> penulisannya hanya bisa diisi dengan SD/SMP/SMA/SMK/D3/S1/S2</p>
							<p>Jenis THR ada 2 yaitu : IDUL FITRI atau KUNINGAN</p>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">File</label>
					<div class="col-sm-8">
						<input type="file" name="file" class="form-control">
					</div>
				</div>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
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