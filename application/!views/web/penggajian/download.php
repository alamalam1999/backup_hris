<?php defined('BASEPATH') or exit('No direct script access allowed');


$Q= "
SELECT *
FROM periode
WHERE PERIODE_ID='$data->PERIODE_ID'
    ";
$periode = $this->crud->set_query($Q)->row();



$PERIODE = isset($periode->PERIODE) ? $periode->PERIODE : '';
$BULAN = isset($periode->BULAN) ? $periode->BULAN : '';
$TAHUN = isset($periode->TAHUN) ? $periode->TAHUN : '';
$TGL_MULAI = isset($periode->TANGGAL_MULAI) ? $periode->TANGGAL_MULAI : '';
$TGL_SELESAI = isset($periode->TANGGAL_SELESAI) ? $periode->TANGGAL_SELESAI : '';



$Q2= "
	SELECT *
	FROM project
	WHERE PROJECT_ID='$data->PROJECT_ID'
    ";
$project = $this->crud->set_query($Q2)->row();

$PROJECT = isset($project->PROJECT) ? $project->PROJECT : '';
$COMPANY_ID = isset($project->COMPANY_ID) ? $project->COMPANY_ID : '';


$Q3= "
	SELECT *
	FROM company
	WHERE COMPANY_ID='$data->COMPANY_ID'
    ";
$company = $this->crud->set_query($Q3)->row();


$COMPANY = isset($company->COMPANY) ? $company->COMPANY : '';

// $rs = db_fetch("
// 	SELECT P.*, K.*, J.JABATAN
// 	FROM penggajian P
// 	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
// 	LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
// 	WHERE P.PERIODE_ID='$PERIODE_ID' AND P.PROJECT_ID='$PROJECT_ID' AND P.KARYAWAN_ID IN (".implode(',',$IDS).")
// 	ORDER BY K.NAMA ASC
// ");

?>
<!DOCTYPE html>
<html>
<head>
	<title>SLIP GAJI</title>
	<!-- <link href="<?php echo base_url() ?>static/favicon.png" rel="SHORTCUT ICON" type="image/png"> -->
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
    tr,td {
      padding: 2px;
    }

		body { font: 13px arial; padding: 10px;}
	</style>
	<!-- <script src="static/js/jquery-3.2.1.min.js" type="text/javascript"></script> -->
</head>
<body>

<?php


//$SCAN = parse_scan($data->KARYAWAN_ID,$TGL_MULAI,$TGL_SELESAI,$SHIFT);

$TOTAL_HK = isset($SCAN['total_working_day']) ? $SCAN['total_working_day'] : 0;
$TOTAL_ABS = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
$TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
$TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
$TOTAL_ABSENSI = $TOTAL_ABS + $TOTAL_SAKIT + $TOTAL_IJIN;

$ADJUSMENT = $data->ADJUSMENT_PLUS - $data->ADJUSMENT_MINUS;

?>

<div >
	<h1 style="font-size: 18px; text-align: center;">
		<?php echo strtoupper($COMPANY) ?><br>
		SLIP GAJI KARYAWAN
	</h1>
  <hr>
	<table style="width: 100%;">
		<tr>
			<td style="width: 20%;">Departemen/Project</td>
			<td style="width: 2%;">:</td>
			<td style="width: 48%;"><?php echo $PROJECT ?></td>
			<td style="width: 20%;">Jumlah Kerja Efektif</td>
			<td style="width: 2%;">:</td>
			<td style="width: 8%;"><?php echo $TOTAL_HK ?> hari</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>:</td>
			<td><?php echo $data->JABATAN ?></td>
			<td>Jumlah Hari Tidak Masuk</td>
			<td>:</td>
			<td><?php echo $TOTAL_ABSENSI ?> hari</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>:</td>
			<td><?php echo $data->NAMA ?></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>NIK</td>
			<td>:</td>
			<td><?php echo $data->NIK ?></td>
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
			<td style="text-align: right; width: 10%;"><?php echo currency($data->GAJI_POKOK_PRORATA) ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">Potongan</td>
			<td style="width: 1%;"></td>
			<td style="width: 10%;"></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">Pendapatan Bersih</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right; width: 16%;"><?php echo currency($data->TOTAL_GAJI_BERSIH) ?></td>
		</tr>
		<tr>
			<td>Tunjangan</td>
			<td></td>
			<td></td>
			<td></td>
			<td>Absensi</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TIDAK_MASUK) ?></td>
			<td></td>
			<td></td>
			<td>:</td>
			<td></td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_JABATAN) ?></td>
			<td></td>
			<td>BPJS JHT</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->BPJS_JHT) ?></td>
			<td></td>
			<td>Jumlah Diterima</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TOTAL_GAJI_BERSIH) ?></td>
		</tr>
		<tr>
			<td>Makan</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_MAKAN) ?></td>
			<td></td>
			<td>BPJS JP</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->BPJS_JP) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Transport</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_TRANSPORT) ?></td>
			<td></td>
			<td>BPJS Kes.</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->BPJS_KES) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Tunjangan Shift</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_SHIFT) ?></td>
			<td></td>
			<td>Angsuran</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->ANGSURAN) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Lembur/Ins. Hari Besar</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->LHK+$data->LHL+$data->IHB) ?></td>
			<td></td>
			<td>Pinjaman</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->PINJAMAN) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Back Up/Piket</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_BACKUP) ?></td>
			<td></td>
			<?php /*
			<td>Adjustment</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->ADJUSMENT_MINUS) ?></td>
			*/ ?>
			<td>Lain-Lain</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency(0) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Medical</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->MEDICAL) ?></td>
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
		</tr>
		<tr>
			<td>Ins. Kehadiran</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_KEHADIRAN) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Sisa Angsuran</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency(0) ?></td>
		</tr>
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
		<tr>
			<td>Keahlian</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_KEAHLIAN) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Komunikasi</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TUNJ_KOMUNIKASI) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Pendapatan Bruto</td>
			<td>:</td>
			<td style="text-align: right;"><?php echo currency($data->TOTAL_GAJI_KOTOR) ?></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
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
			<td colspan="9" style="font-style:italic;"><?php echo ucwords(terbilang($data->TOTAL_GAJI_BERSIH)) ?></td>
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
				Jakarta, &nbsp;&nbsp;&nbsp;&nbsp;<?php echo str_replace('-','',ucfirst(strtolower(getPeriode($data->PERIODE_ID, 'PERIODE')))) ?>
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
				<p style="text-decoration:underline;"><?php echo $data->NAMA ?></p>
			</td>
		</tr>
	</table>
</div>
<?php  ?>
