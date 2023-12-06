<?php

include 'app-load.php';

$PERIODE_ID = db_escape(get_input('PERIODE_ID'));
$PROJECT_ID = db_escape(get_input('PROJECT_ID'));
$IDS = get_input('ids');

$periode = db_first("
	SELECT *
	FROM periode
	WHERE PERIODE_ID='$PERIODE_ID'
");

$PERIODE = isset($periode->PERIODE) ? $periode->PERIODE : '';
$BULAN = isset($periode->BULAN) ? $periode->BULAN : '';
$TAHUN = isset($periode->TAHUN) ? $periode->TAHUN : '';
$TGL_MULAI = $periode->TANGGAL_MULAI;
$TGL_SELESAI = $periode->TANGGAL_SELESAI;

$project = db_first("
	SELECT *
	FROM project
	WHERE PROJECT_ID='$PROJECT_ID'
");
$PROJECT = isset($project->PROJECT) ? $project->PROJECT : '';
$COMPANY_ID = isset($project->COMPANY_ID) ? $project->COMPANY_ID : '';
$company = db_first("
	SELECT *
	FROM company
	WHERE COMPANY_ID='$COMPANY_ID'
");
$COMPANY = isset($company->COMPANY) ? $company->COMPANY : '';
 
$rs = db_fetch("
	SELECT P.*, K.*, J.JABATAN,
	P.TUNJ_JABATAN AS P_TUNJ_JABATAN, P.BPJS_KES AS P_BPJS_KES, P.BPJS_JHT AS P_BPJS_JHT, P.BPJS_JKK AS P_BPJS_JKK, P.BPJS_JKM AS P_BPJS_JKM
	FROM penggajian P
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
	WHERE P.PERIODE_ID='$PERIODE_ID' AND P.PROJECT_ID='$PROJECT_ID' AND P.KARYAWAN_ID IN (".implode(',',$IDS).")
	ORDER BY K.NAMA ASC
");

?>
<!DOCTYPE html>
<html>
<head>
	<title>SLIP GAJI</title>
	<link href="<?php echo base_url() ?>static/favicon.png" rel="SHORTCUT ICON" type="image/png">
	<style type="text/css">
		/* http://meyerweb.com/eric/tools/css/reset/ 
		   v2.0 | 20110126
		   License: none (public domain)
		*/
		html, body, div, span, applet, object, iframe,
		h1, h2, h3, h4, h5, h6, p, blockquote, pre,
		a, abbr, acronym, address, big, cite, code,
		del, dfn, em, img, ins, kbd, q, s, samp,
		small, strike, strong, sub, sup, tt, var,
		b, u, i, center,
		dl, dt, dd, ol, ul, li,
		fieldset, form, label, legend,
		table, caption, tbody, tfoot, thead, tr, th, td,
		article, aside, canvas, details, embed, 
		figure, figcaption, footer, header, hgroup, 
		menu, nav, output, ruby, section, summary,
		time, mark, audio, video {
			margin: 0;
			padding: 0;
			border: 0;
			font-size: 100%;
			font: inherit;
			vertical-align: baseline;
		}
		/* HTML5 display-role reset for older browsers */
		article, aside, details, figcaption, figure, 
		footer, header, hgroup, menu, nav, section {
			display: block;
		}
		body {
			line-height: 1;
		}
		ol, ul {
			list-style: none;
		}
		blockquote, q {
			quotes: none;
		}
		blockquote:before, blockquote:after,
		q:before, q:after {
			content: '';
			content: none;
		}
		table {
			border-collapse: collapse;
			border-spacing: 0;
		}

		body { font: 13px arial; }
	</style>
	<script src="static/js/jquery-3.2.1.min.js" type="text/javascript"></script>
</head>
<body>
<?php $ADJUSMENT = 0; ?>
<?php if(count($rs)>0){ foreach($rs as $row){ ?>
<?php

$rs2 = db_fetch("
	SELECT *, K.SHIFT_CODE
	FROM shift_karyawan K
		LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
	WHERE
		KARYAWAN_ID='$row->KARYAWAN_ID' AND
		(DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
");
			
$SHIFT = array();
if(count($rs2)>0){
	foreach($rs2 as $r){
		$SHIFT[$r->KARYAWAN_ID][$r->DATE] = $r;
	}
}
// echo "<pre>";
// print_r($rs); die();
$SCAN = parse_scan($row->KARYAWAN_ID,$TGL_MULAI,$TGL_SELESAI,$SHIFT);

$TOTAL_HK = isset($SCAN['total_working_day']) ? $SCAN['total_working_day'] : 0;
$TOTAL_ABS = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
$TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : 0;
$TOTAL_ABSENSI = $TOTAL_ABS + $TOTAL_SAKIT + $TOTAL_IJIN;

$ADJUSMENT = $row->ADJUSMENT_PLUS - $row->ADJUSMENT_MINUS;

?>

<div style="width:210mm;">
	<h1 style="font-size: 18px; text-align: center;">
		<?php echo strtoupper($COMPANY) ?><br>
		SLIP GAJI KARYAWAN
	</h1>
	<table style="width: 100%;">
		<tr>
			<td style="width: 20%;">Unit</td>
			<td style="width: 2%;">:</td>
			<td style="width: 48%;"><?php echo $PROJECT ?></td>
			<td style="width: 20%;">Hari Kerja Efektif</td>
			<td style="width: 2%;">:</td>
			<td style="width: 8%;"><?php echo $TOTAL_HK ?> hari</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>:</td>
			<td><?php echo $row->JABATAN ?></td>
			<td>Jumlah Hari Tidak Masuk</td>
			<td>:</td>
			<td><?php echo $TOTAL_ABSENSI ?> hari</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>:</td>
			<td><?php echo $row->NAMA ?></td>
			<td>Jumlah Terlambat</td>
			<td>:</td>
			<td><?php echo $TOTAL_LATE ?> hari</td>
		</tr>
		<tr>
			<td>NIK</td>
			<td>:</td>
			<td><?php echo $row->NIK ?></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Bulan</td>
			<td>:</td>
			<td><?php echo $PERIODE ?></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="6" style="border-top: 1px solid #000;"></td>
		</tr>
	</table>
	<br>
	<table style="width: 100%;">
		<tr>
			<td colspan="11" style="border-top: 1px solid #000;"></td>
		</tr>
		<tr>
			<td style="width: 22%;">Gaji Pokok / GP Prorate</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right; width: 10%;">
				<?php 
				if($row->GAJI_POKOK_PRORATA > 0){
					echo currency($row->GAJI_POKOK_PRORATA);
				}else{
					echo currency($row->GAJI_POKOK);
				}
				
				 ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">Potongan BPJS</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->P_BPJS_KES+$row->P_BPJS_JHT+$row->P_BPJS_JKK+$row->P_BPJS_JKM ) ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">Pendapatan Bersih</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right; width: 16%;"><?php echo currency($row->TOTAL_GAJI_BERSIH) ?></td>
		</tr>
		<tr>
			<td style="width: 22%;">Tunjangan Keluarga</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right; width: 10%;">
				<?php 
				
					echo currency($row->TUNJ_KELUARGA);
			
				 ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">BPJS JHT</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->P_BPJS_JHT ) ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;"></td>
			<td style="width: 1%;"></td>
			<td style="text-align: right; width: 16%;"></td>
		</tr>
		<tr>
			<td style="width: 22%; font-weight:bold" >Upah Pokok</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right; width: 10%;">
				<?php 
				
					echo currency($row->GAJI_POKOK_NET);
			
				 ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">BPJS JKK</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->P_BPJS_JKK ) ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;"></td>
			<td style="width: 1%;"></td>
			<td style="text-align: right; width: 16%;"></td>
		</tr>
		<tr>
			<td>Tunjangan Jabatan</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right; width: 10%;"><?php echo currency($row->P_TUNJ_JABATAN) ?></td>
			<td></td>
			<td style="width: 16%;">BPJS JKM</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->P_BPJS_JKM ) ?></td>
			<td></td>
			<td></td>
			<td>:</td>
			<td></td>
		</tr>
		<tr>
			<td>OTTW</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_OTTW) ?></td>
			<td></td>
			<td style="width: 16%;">BPJS KES</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->P_BPJS_KES ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>KASEK/WAKASEK</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_LAINNYA_1) ?></td>
			<td></td>
			<td>Potongan Lainnya</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->PINJAMAN ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>S.KURIKULUM</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_LAINNYA_2) ?></td>
			<td></td>
			<td>Pinj. Kop. Dinatera</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->PINJAMAN_KOPERASI_DINATERA ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>K.MGMP</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_LAINNYA_3) ?></td>
			<td></td>
			<td>Pinj. Kop. Avicenna</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->PINJAMAN_KOPERASI_AVICENNA ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>WALIKELAS</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_LAINNYA_4) ?></td>
			<td></td>
			<td>Iuran. Kop. Dinatera</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->IURAN_KOPERASI_DINATERA ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>KEL. AJAR</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_LAINNYA_5) ?></td>
			<td></td>
			<td>Iuran. Kop. Avicenna</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->IURAN_KOPERASI_AVICENNA ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>Tunjangan tidak tetap</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->LHK+ $row->LHL+ $row->IHB + $row->TUNJ_KEHADIRAN + $row->TUNJ_MAKAN + $row->TUNJ_TRANSPORT) ?></td>
			<td></td>
			<td>Pinj. BWS</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->PINJAMAN_BANK_BWS ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>Ins. Kehadiran</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_KEHADIRAN) ?></td>
			<td></td>
			<td>Ekses Claim</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->EKSES_KLAIM ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td style="text-align: right;"></td>
		</tr>
		<tr>
			<td>Makan</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_MAKAN) ?></td>
			<td></td>
			<td>Biaya Pend. Anak</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->BIAYA_PEND_ANAK ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Transport</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_TRANSPORT) ?></td>
			<td></td>
			<td>Potong Absensi</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TIDAK_MASUK) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<!-- <tr>
			<td>Tunjangan Shift</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_SHIFT) ?></td>
			<td></td>
			<td>Angsuran</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->ANGSURAN) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr> -->
		<tr>
			<td>Lembur</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->LHK+$row->LHL+$row->IHB) ?></td>
			<td></td>
			<td></td>
			<td>:</td>
			<td style="text-align: right;"></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<!-- <tr>
			<td>Back Up/Piket</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_BACKUP) ?></td>
			<td></td>
			<?php /*
			<td>Adjustment</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->ADJUSMENT_MINUS) ?></td>
			*/ ?>
			<td>Lain-Lain</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency(0) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr> -->
		<!-- <tr>
			<td>Medical</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->MEDICAL) ?></td>
			<td></td>
			<?php /*
			<td>Lain-Lain</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency(0) ?></td>
			*/ ?>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Selisih Pendapatan</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency(0) ?></td>
		</tr> -->
		
		<tr>
			<td>Adjustment</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($ADJUSMENT) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<!-- <tr>
			<td>Keahlian</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_KEAHLIAN) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr> -->
		<!-- <tr>
			<td>Komunikasi</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TUNJ_KOMUNIKASI) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr> -->
		<tr>
			<td>Pendapatan Bruto</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($row->TOTAL_GAJI_KOTOR) ?></td>
			<td></td>
			<td>Total Potongan</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right;"><?php echo currency( $row->TOTAL_POTONGAN ) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td colspan="11" style="border-top: 1px solid #000;"></td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>:</td>
			<td colspan="9" style="font-style:italic;"><?php echo ucwords(terbilang($row->TOTAL_GAJI_BERSIH)) ?></td>
		</tr>
		<tr>
			<td colspan="11" style="border-top: 1px solid #000;"></td>
		</tr>
	</table>
	<br>
	<br>
	<table style="width: 100%;">
		<tr>
			<td style="width: 70%;">
				Jakarta, &nbsp;&nbsp;&nbsp;&nbsp;<?php echo str_replace('-','',ucfirst(strtolower($periode->PERIODE))) ?>
				<br>	
				<br>	
				<br>	
				<br>	
				<br>	
				<br>
				_______________________		
			</td>
			<td>
				<br>
				Yang Menerima,
				<br>	
				<br>	
				<br>	
				<br>	
				<br>
				<p style="text-decoration:underline;"><?php echo $row->NAMA ?></p>		
			</td>
		</tr>
	</table>
</div>
<div style="page-break-after: always;"></div>
<?php }} ?>

<script type="text/javascript">
$(document).ready(function(){
	//window.print();
});
</script>
</body>
</html>