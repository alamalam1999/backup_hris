<?php include 'app-load.php'; ?>
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
<body style="width:210mm;">
	<?php
	$ID = get_input('id');
	$EDIT = db_first(" SELECT P.*,PR.PROJECT,K.NIK,K.NAMA,J.JABATAN,PE.PERIODE FROM penggajian P 
		LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) 
		LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID) 
		LEFT JOIN project PR ON (PR.PROJECT_ID=J.PROJECT_ID) 
		LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 
		WHERE PENGGAJIAN_ID='$ID' ");

	$POTONGAN = db_fetch(" SELECT * FROM potongan_detail WHERE POTONGAN_ID='$EDIT->POTONGAN_ID' ");
	?>
	<h1 style="font-size: 18px; text-align: center;">
		PT. AIRKON PRATAMA <br>
		SLIP GAJI KARYAWAN
	</h1>
	<table style="width: 100%;">
		<tr>
			<td style="width: 20%;">Unit</td>
			<td style="width: 2%;">:</td>
			<td style="width: 48%;"><?php echo $EDIT->PROJECT ?></td>
			<td style="width: 20%;">Jumlah Kerja Efektif</td>
			<td style="width: 5%;">:</td>
			<td style="width: 5%;"><?php echo '' ?> Hari</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>:</td>
			<td><?php echo $EDIT->JABATAN ?></td>
			<td>Jumlah Hari Tidak Masuk</td>
			<td>:</td>
			<td><?php echo '' ?> Hari</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>:</td>
			<td><?php echo $EDIT->NAMA ?></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Nik</td>
			<td>:</td>
			<td><?php echo $EDIT->NIK ?></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Bulan</td>
			<td>:</td>
			<td><?php echo $EDIT->PERIODE ?></td>
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
			<td style="text-align: right; width: 10%;"><?php echo currency($EDIT->GAJI_POKOK) ?></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">Potongan</td>
			<td style="width: 1%;"></td>
			<td style="width: 10%;"></td>
			<td style="width: 3%;"></td>
			<td style="width: 16%;">Pendapatan Bersih</td>
			<td style="width: 1%;">:</td>
			<td style="text-align: right; width: 16%;">7,5000,00</td>
		</tr>
		<tr>
			<td>Tunjangan</td>
			<td></td>
			<td></td>
			<td></td>
			<td>Absensi</td>
			<td>:</td>
			<td style="text-align: right;">500,000</td>
			<td></td>
			<td></td>
			<td>:</td>
			<td></td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td>BPJS JHT</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td>Jumlah Diterima Karyawan</td>
			<td>:</td>
			<td></td>
		</tr>
		<tr>
			<td>Makan</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td>BPJS JP</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Transport</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td>BPJS Kes.</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Tunjangan Shift</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td>Angsuran Seragam</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Intensif Hari Besar Nasional</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td>Pinjaman</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Back Up/Piket</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td>Lain-Lain</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td>Medical</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Selisih Pendapatan</td>
			<td>:</td>
			<td></td>
		</tr>
		<tr>
			<td>Penghargaan</td>
			<td>:</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>Sisa Angsuran Seragam</td>
			<td>:</td>
			<td></td>
		</tr>
		<tr>
			<td>Adjustment</td>
			<td>:</td>
			<td></td>
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
			<td></td>
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
			<td></td>
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
			<td></td>
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
			<td colspan="9"></td>
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
				Jakarta, <?php echo $EDIT->PERIODE ?>
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
				_______________________		
			</td>
		</tr>
	</table>

<script type="text/javascript">
$(document).ready(function(){
	//window.print();
});
</script>
</body>
</html>