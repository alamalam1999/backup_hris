<?php

include 'app-load.php';

is_login('pph.view');

$MODULE = 'PPH';

if( isset($_GET['generate']) )
{
	is_login('pph.generate');
	
	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');
	
	$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
	$GAJI = db_first(" SELECT COUNT(1) as CTN FROM penggajian WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	
	if( ! isset($PERIODE->PERIODE_ID) )
	{
		header('location: pph.php');
		exit;
	}
	if( $PERIODE->STATUS_PERIODE == 'CLOSED' )
	{
		header('location: pph.php?m=closed');
		exit;
	}
	if( $GAJI->CTN == 0 )
	{
		header('location: pph.php?m=notgenerate');
		exit;
	}
	
	$TAHUN = $PERIODE->TAHUN;
	$BULAN = $PERIODE->BULAN;
	$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;
	$TGL_MULAI2 = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI2 = $PERIODE->TANGGAL_SELESAI;
	$THR_IDUL_FITRI = $PERIODE->THR_IDUL_FITRI;
	$TGL_IDUL_FITRI = $PERIODE->TGL_IDUL_FITRI;
	$THR_KUNINGAN = $PERIODE->THR_KUNINGAN;
	$TGL_KUNINGAN = $PERIODE->TGL_KUNINGAN;

	db_execute(" DELETE FROM pph WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	
	$karyawan = db_fetch("
		SELECT *
		FROM karyawan K
		LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
		WHERE J.PROJECT_ID='$PROJECT_ID' AND ST_KERJA='AKTIF'
	");

	if( count($karyawan) > 0 )
	{
		db_execute (" INSERT INTO pph(PERIODE_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,TIDAK_MASUK,GAJI_POKOK_NET,TUNJ_BACKUP,TUNJ_KEHADIRAN,TUNJ_JABATAN,TUNJ_KEAHLIAN,TUNJ_KOMUNIKASI,TUNJ_MAKAN,TUNJ_TRANSPORT,TUNJ_PROYEK,TUNJ_SHIFT,LHK,LHL,IHB,MEDICAL,ADJUSMENT,THR,TOTAL_TUNJANGAN,TOTAL_GAJI_KOTOR,ANGSURAN,PINJAMAN,POTONGAN,BPJS_JHT,BPJS_JP,BPJS_KES,TOTAL_POTONGAN,TOTAL_THR,TOTAL_GAJI_BERSIH) SELECT PERIODE_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,TIDAK_MASUK,GAJI_POKOK_NET,TUNJ_BACKUP,TUNJ_KEHADIRAN,TUNJ_JABATAN,TUNJ_KEAHLIAN,TUNJ_KOMUNIKASI,TUNJ_MAKAN,TUNJ_TRANSPORT,TUNJ_PROYEK,TUNJ_SHIFT,LHK,LHL,IHB,MEDICAL,ADJUSMENT,THR,TOTAL_TUNJANGAN,TOTAL_GAJI_KOTOR,ANGSURAN,PINJAMAN,POTONGAN,BPJS_JHT,BPJS_JP,BPJS_KES,TOTAL_POTONGAN,TOTAL_THR,TOTAL_GAJI_BERSIH FROM penggajian WHERE penggajian.PERIODE_ID='$PERIODE_ID' AND penggajian.PROJECT_ID='$PROJECT_ID' ");

		$data = db_fetch("
			SELECT P.TOTAL_TUNJANGAN AS TOTAL_TUNJANGAN,P.KARYAWAN_ID AS PIN,K.GAJI_POKOK AS GAJI_POKOK,K.BPJS_JKK AS BPJS_JKK,K.BPJS_JKM AS BPJS_JKM,K.BPJS_KES AS BPJS_KES,K.BPJS_JP AS BPJS_JP,K.BPJS_JHT AS BPJS_JHT,PP.NILAI AS NILAI 
			FROM pph P 
			LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
			LEFT JOIN ptkp PP ON (PP.NAMA=K.STATUS_PTKP)
			WHERE P.PERIODE_ID='$PERIODE_ID' AND P.PROJECT_ID='$PROJECT_ID'
		");

		foreach($data as $k)
		{
			$BPJS_PLUS = 0;
			$BPJS_MINUS = 0;
			$PLUS_BRUTO = 0;
			$BIAYA_JABATAN = 0;
			$MINUS_BRUTO = 0;
			$NETTO_SEBULAN = 0;
			$NETTO_SETAHUN = 0;
			$KENA_PAJAK_SETAHUN = 0;
			$PPH_SETAHUN = 0;
			$PPH = 0;

			$GAPOK = $k->GAJI_POKOK;
			$TOTAL_TUNJANGAN = $k->TOTAL_TUNJANGAN;
			$BPJS_JKK = $k->BPJS_JKK;
			$BPJS_JKM = $k->BPJS_JKM;
			$BPJS_KES = $k->BPJS_KES;
			$BPJS_PLUS = $BPJS_JKK + $BPJS_JKM + $BPJS_KES;

			$BPJS_JP = $k->BPJS_JP;
			$BPJS_JHT = $k->BPJS_JHT;
			$BPJS_MINUS = $BPJS_JP + $BPJS_JHT;

			$PLUS_BRUTO = $GAPOK + $TOTAL_TUNJANGAN + $BPJS_PLUS;
			$BIAYA_JABATAN = 0.05 * $GAPOK;
			$MINUS_BRUTO = $BIAYA_JABATAN + $BPJS_MINUS;
			$MASA_KERJA = 12;
			$TARIF_PPH = 0.05;

			$NETTO_SEBULAN = $PLUS_BRUTO - $MINUS_BRUTO;
			$NETTO_SETAHUN = $NETTO_SEBULAN * $MASA_KERJA;

			$NILAI_PTKP = empty($k->NILAI) ? 54000000 : $k->NILAI;
			if(($NETTO_SETAHUN - $NILAI_PTKP) > 0){
				$KENA_PAJAK_SETAHUN = $NETTO_SETAHUN - $NILAI_PTKP;
				if (substr($KENA_PAJAK_SETAHUN,-3)<1000){
					$KENA_PAJAK_SETAHUN=$KENA_PAJAK_SETAHUN - substr($KENA_PAJAK_SETAHUN,-3);
				}
				$PPH_SETAHUN = $KENA_PAJAK_SETAHUN * $TARIF_PPH;
				$PPH = $PPH_SETAHUN / $MASA_KERJA;
			}

			//echo $PLUS_BRUTO; die();

			db_execute(" UPDATE pph SET BPJS_JKK='$BPJS_JKK',BPJS_JKM='$BPJS_JKM',PLUS_BRUTO='$PLUS_BRUTO',BIAYA_JABATAN='$BIAYA_JABATAN',MINUS_BRUTO='$MINUS_BRUTO',NETTO_SEBULAN='$NETTO_SEBULAN',NETTO_SETAHUN='$NETTO_SETAHUN',KENA_PAJAK_SETAHUN='$KENA_PAJAK_SETAHUN',PPH_SETAHUN='$PPH_SETAHUN',PPH='$PPH' WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' AND KARYAWAN_ID='$k->PIN' ");
		}
	}
	
	header('location: pph.php?m=1');
	exit;
}

if( isset($_GET['spt']) )
{
	is_login('pph.generate');
	
	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');
	
	$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
	$GAJI = db_first(" SELECT COUNT(1) as CTN FROM penggajian WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	
	if( ! isset($PERIODE->PERIODE_ID) )
	{
		header('location: pph.php');
		exit;
	}
	if( $PERIODE->STATUS_PERIODE == 'CLOSED' )
	{
		header('location: pph.php?m=closed');
		exit;
	}
	if( $GAJI->CTN == 0 )
	{
		header('location: pph.php?m=notgenerate');
		exit;
	}
	
	$TAHUN = $PERIODE->TAHUN;
	$BULAN = $PERIODE->BULAN;
	$TGL_MULAI = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;
	$TGL_MULAI2 = $PERIODE->TANGGAL_MULAI;
	$TGL_SELESAI2 = $PERIODE->TANGGAL_SELESAI;
	$THR_IDUL_FITRI = $PERIODE->THR_IDUL_FITRI;
	$TGL_IDUL_FITRI = $PERIODE->TGL_IDUL_FITRI;
	$THR_KUNINGAN = $PERIODE->THR_KUNINGAN;
	$TGL_KUNINGAN = $PERIODE->TGL_KUNINGAN;

	db_execute(" DELETE FROM pph WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	
	$karyawan = db_fetch("
		SELECT *
		FROM karyawan K
		LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
		WHERE J.PROJECT_ID='$PROJECT_ID' AND ST_KERJA='AKTIF'
	");

	if( count($karyawan) > 0 )
	{
		db_execute (" INSERT INTO pph(PERIODE_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,TIDAK_MASUK,GAJI_POKOK_NET,TUNJ_BACKUP,TUNJ_KEHADIRAN,TUNJ_JABATAN,TUNJ_KEAHLIAN,TUNJ_KOMUNIKASI,TUNJ_MAKAN,TUNJ_TRANSPORT,TUNJ_PROYEK,TUNJ_SHIFT,LHK,LHL,IHB,MEDICAL,ADJUSMENT,THR,TOTAL_TUNJANGAN,TOTAL_GAJI_KOTOR,ANGSURAN,PINJAMAN,POTONGAN,BPJS_JHT,BPJS_JP,BPJS_KES,TOTAL_POTONGAN,TOTAL_THR,TOTAL_GAJI_BERSIH) SELECT PERIODE_ID,PROJECT_ID,KARYAWAN_ID,GAJI_POKOK,GAJI_POKOK_PRORATA,TIDAK_MASUK,GAJI_POKOK_NET,TUNJ_BACKUP,TUNJ_KEHADIRAN,TUNJ_JABATAN,TUNJ_KEAHLIAN,TUNJ_KOMUNIKASI,TUNJ_MAKAN,TUNJ_TRANSPORT,TUNJ_PROYEK,TUNJ_SHIFT,LHK,LHL,IHB,MEDICAL,ADJUSMENT,THR,TOTAL_TUNJANGAN,TOTAL_GAJI_KOTOR,ANGSURAN,PINJAMAN,POTONGAN,BPJS_JHT,BPJS_JP,BPJS_KES,TOTAL_POTONGAN,TOTAL_THR,TOTAL_GAJI_BERSIH FROM penggajian WHERE penggajian.PERIODE_ID='$PERIODE_ID' AND penggajian.PROJECT_ID='$PROJECT_ID' ");

		$data = db_fetch("
			SELECT P.TOTAL_TUNJANGAN AS TOTAL_TUNJANGAN,P.KARYAWAN_ID AS PIN,K.GAJI_POKOK AS GAJI_POKOK,K.BPJS_JKK AS BPJS_JKK,K.BPJS_JKM AS BPJS_JKM,K.BPJS_KES AS BPJS_KES,K.BPJS_JP AS BPJS_JP,K.BPJS_JHT AS BPJS_JHT,PP.NILAI AS NILAI 
			FROM pph P 
			LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
			LEFT JOIN ptkp PP ON (PP.NAMA=K.STATUS_PTKP)
			WHERE P.PERIODE_ID='$PERIODE_ID' AND P.PROJECT_ID='$PROJECT_ID'
		");

		foreach($data as $k)
		{
			$BPJS_PLUS = 0;
			$BPJS_MINUS = 0;
			$PLUS_BRUTO = 0;
			$BIAYA_JABATAN = 0;
			$MINUS_BRUTO = 0;
			$NETTO_SEBULAN = 0;
			$NETTO_SETAHUN = 0;
			$KENA_PAJAK_SETAHUN = 0;
			$PPH_SETAHUN = 0;
			$PPH = 0;

			$GAPOK = $k->GAJI_POKOK;
			$TOTAL_TUNJANGAN = $k->TOTAL_TUNJANGAN;
			$BPJS_JKK = $k->BPJS_JKK;
			$BPJS_JKM = $k->BPJS_JKM;
			$BPJS_KES = $k->BPJS_KES;
			$BPJS_PLUS = $BPJS_JKK + $BPJS_JKM + $BPJS_KES;

			$BPJS_JP = $k->BPJS_JP;
			$BPJS_JHT = $k->BPJS_JHT;
			$BPJS_MINUS = $BPJS_JP + $BPJS_JHT;

			$PLUS_BRUTO = $GAPOK + $TOTAL_TUNJANGAN + $BPJS_PLUS;
			$BIAYA_JABATAN = 0.05 * $PLUS_BRUTO;
			$MINUS_BRUTO = $BIAYA_JABATAN + $BPJS_MINUS;
			$MASA_KERJA = 12;
			$TARIF_PPH = 0.05;

			$NETTO_SEBULAN = $PLUS_BRUTO - $MINUS_BRUTO;
			$NETTO_SETAHUN = $NETTO_SEBULAN * $MASA_KERJA;

			$NILAI_PTKP = $k->NILAI;
			if(($NETTO_SETAHUN - $NILAI_PTKP) > 0){
				$KENA_PAJAK_SETAHUN = $NETTO_SETAHUN - $NILAI_PTKP;
				if (substr($KENA_PAJAK_SETAHUN,-3)<1000){
					$KENA_PAJAK_SETAHUN=$KENA_PAJAK_SETAHUN - substr($KENA_PAJAK_SETAHUN,-3);
				}
				$PPH_SETAHUN = $KENA_PAJAK_SETAHUN * $TARIF_PPH;
				$PPH = $PPH_SETAHUN / $MASA_KERJA;
			}

			//echo $PLUS_BRUTO; die();

			db_execute(" UPDATE pph SET BPJS_JKK='$BPJS_JKK',BPJS_JKM='$BPJS_JKM',PLUS_BRUTO='$PLUS_BRUTO',BIAYA_JABATAN='$BIAYA_JABATAN',MINUS_BRUTO='$MINUS_BRUTO',NETTO_SEBULAN='$NETTO_SEBULAN',NETTO_SETAHUN='$NETTO_SETAHUN',KENA_PAJAK_SETAHUN='$KENA_PAJAK_SETAHUN',PPH_SETAHUN='$PPH_SETAHUN',PPH='$PPH' WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' AND KARYAWAN_ID='$k->PIN' ");
		}
	}
	
	header('location: pph.php?m=1');
	exit;
}

if( isset($_GET['export']) )
{
	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');
	header('location: export-gaji.php?PERIODE_ID='.$PERIODE_ID.'&PROJECT_ID='.$PROJECT_ID);
}

$JS[] = 'static/tipsy/jquery.tipsy.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<?php
if(get_input('m') == '1'){
	$SUCCESS = 'Pph berhasil dibuat';
}
if(get_input('m') == 'closed'){
	$ERROR[] = 'Tidak dapat membuat laporan pph<br>Periode penggajian sudah di tutup';
}
if(get_input('m') == 'notgenerate'){
	$ERROR[] = 'Tidak dapat membuat laporan pph<br>Generate penggajian belum dilakukan';
}
include 'msg.php';
?>

<section class="container-fluid">

	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
				<li><a href="javascript:void(0)" id="btn-generate"><i class="fa fa-cog"></i>&nbsp;&nbsp;Generate</a></li>
				<li><a href="javascript:void(0)" id="btn-spt_induk"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Create SPT Induk</a></li>
				<li><a href="javascript:void(0)" id="btn-spt_2"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Create SPT II</a></li>
				<li><a href="javascript:void(0)" id="btn-spt_a1"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Create SPT A1</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),get_search($MODULE,'PERIODE_ID'),' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID',project_option(0),get_search($MODULE,'PROJECT_ID'),' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search($MODULE,'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">PPH</h1>
		</div>
	</div>
	
	<section class="content">
		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>
	</section>
</section>

<script>
$(document).ready(function(){
	$('#t').datagrid({
		queryParams: { 'PERIODE_ID': $('#PERIODE_ID').val(), 'PROJECT_ID': $('#PROJECT_ID').val(), 'NAMA': $('#NAMA').val() },
		url:'pph-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'NAMA',
		sortOrder: 'asc',
		singleSelect:false,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		frozenColumns:[[
			{field:'ck',checkbox:true},
			{field:'NAMA',title:'Nama',width:200,sortable:true,align:'left'},
			{field:'TGL_MASUK',title:'Join Date',width:90,sortable:true,align:'center'},
			{field:'STATUS_PTKP',title:'PTKP',width:90,sortable:true,align:'center'},
		]],
		columns:[[
			{field:'GAJI_POKOK',title:'Gaji Pokok',width:80,sortable:false,align:'right'},
			{field:'TOTAL_TUNJANGAN',title:'Total Tunjangan',width:100,sortable:false,align:'right'},
			{field:'BPJS_JKK',title:'BPJS JKK',width:80,sortable:false,align:'right'},
			{field:'BPJS_JKM',title:'BPJS JKM',width:80,sortable:false,align:'right'},
			{field:'BPJS_KES',title:'BPJS KES',width:80,sortable:false,align:'right'},
			{field:'PLUS_BRUTO',title:'T. Penghasilan Bruto',width:150,sortable:false,align:'right'},
			{field:'BIAYA_JABATAN',title:'Biaya Jabatan',width:100,sortable:false,align:'right'},
			{field:'BPJS_JHT',title:'BPJS JHT',width:80,sortable:false,align:'right'},
			{field:'BPJS_JP',title:'BPJS JP',width:80,sortable:false,align:'right'},
			{field:'MINUS_BRUTO',title:'T. Pengurangan Bruto',width:150,sortable:false,align:'right'},
			{field:'NETTO_SEBULAN',title:'T. Penghasilan Netto 1 Bulan',width:200,sortable:false,align:'right'},
			{field:'NETTO_SETAHUN',title:'T. Penghasilan Netto 1 Tahun',width:200,sortable:false,align:'right'},
			{field:'NILAI_PTKP',title:'Besaran PTKP',width:150,sortable:false,align:'right'},
			{field:'KENA_PAJAK_SETAHUN',title:'Kena Pajak 1 Tahun',width:150,sortable:false,align:'right'},
			{field:'PPH_SETAHUN',title:'PPH 1 Tahun',width:150,sortable:false,align:'right'},
			{field:'PPH',title:'PPH',width:150,sortable:false,align:'right'},
		]],
		onLoadSuccess: function(data){
			$('.tip').tipsy({
				opacity : 1,
			});
		}
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-print').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else window.location = 'lembur-action.php?op=edit&id='+sel.LEMBUR_ID;
		return false;
	});
	
	$('#btn-generate').click(function(){
		window.location = 'pph.php?generate=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-spt').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else {
			var rows = $('#t').datagrid('getSelections');
			var QS = '';
			$.each(rows, function(index, value){
				QS += '&ids[]=' + value.KARYAWAN_ID;
			});
			window.location('pph.php?spt=1&PERIODE_ID='+ $('#PERIODE_ID').val() + '&PROJECT_ID='+$('#PROJECT_ID').val() + QS);
		}
		return false;
	});

	$('#btn-search').click(function(){
		doSearch();
		return false;
	});

	$('#PERIODE_ID, #PROJECT_ID').change(function(){
		doSearch();
		return false;
	});

	$('.input-search, #NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	
	$('#btn-reset').click(function(){
		$('#PERIODE_ID').val("");
		$('#PROJECT_ID').val("");
		$('#NAMA').val("");
		doSearch();
		return false;
	});
	datagrid();
});
function datagrid()
{
	var wind = parseInt($(window).height());
	var top = parseInt($('.navbar').outerHeight());
	$('#t-responsive').height(wind - top - 70);
	$('#t').datagrid('resize');
}
function doSearch(){
	$('#t').datagrid('load',{
		PERIODE_ID: $('#PERIODE_ID').val(),
		PROJECT_ID: $('#PROJECT_ID').val(),
		NAMA: $('#NAMA').val(),
	});
}
</script>

<?php
include 'footer.php';
?>