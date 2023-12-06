<?php

include 'app-load.php';

is_login('laporan-pph-karyawan-bulanan.view');



if( isset($_GET['generate']) )
{	
	$PERIODE_ID = get_input('PERIODE_ID');
	$PROJECT_ID = get_input('PROJECT_ID');
	//echo $PROJECT_ID;
	//echo $PERIODE_ID;
	//die();
	// is_login('pph.generate');
	if($PROJECT_ID == ''){
	header('location: laporan-pph-karyawan-bulanan.php?m=2');
	exit;
	}
	
	
	$PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");
	$COMPANY = db_first(" SELECT * FROM project P
						LEFT JOIN company C ON C.COMPANY_ID = P.COMPANY_ID
						 WHERE P.PROJECT_ID='$PROJECT_ID' ");
	// print_r($COMPANY); die();
	//$GAJI = db_first(" SELECT COUNT(1) as CTN FROM penggajian WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	//header('location: laporan-pph-karyawan-bulanan.php');
	//exit;
	if( empty($PERIODE->PERIODE_ID) )
	{
		header('location: laporan-pph-karyawan-bulanan.php');
		exit;
	}
	if( $PERIODE->STATUS_PERIODE == 'CLOSED' )
	{
		header('location: laporan-pph-karyawan-bulanan.php?m=closed');
		exit;
	}
	// if( $GAJI->CTN == 0 )
	// {
	// 	header('location: laporan-pph-karyawan-bulanan.php?m=notgenerate');
	// 	exit;
	// }
	$COMPANY_ID = $COMPANY->COMPANY_ID;
	$COMPANY_NAME = $COMPANY->COMPANY;
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

	db_execute(" DELETE FROM pph21 WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");
	
	$karyawan = db_fetch("
		SELECT K.*, J.*, PT.NILAI AS NILAI_PTKP
		FROM karyawan K
		LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
		LEFT JOIN ptkp PT ON PT.NAMA=K.STATUS_PTKP
		WHERE K.ST_KERJA = 'AKTIF' AND K.PROJECT_ID = $PROJECT_ID
	");
	
	$FIELDS = array(
					'PERIODE_ID', 'PROJECT_ID', 'KARYAWAN_ID', 'NAMA_KARYAWAN', 'STATUS_PTKP', 'NILAI_PTKP', 'GAJI_POKOK', 'GAJI_PRORATA', 'TUNJANGAN_KELUARGA', 'THR', 'JKK_KARYAWAN', 'JKK_PERUSAHAAN', 'JHT_KARYAWAN', 'JHT_PERUSAHAAN', 'JKM_KARYAWAN', 'JKM_PERUSAHAAN', 'JP_KARYAWAN', 'JP_PERUSAHAAN', 'BPJS_KESEHATAN_KARYAWAN', 'BPJS_KESEHATAN_PERUSAHAAN', 'TOTAL_PENGHASILAN','TOTAL_POTONGAN','PPH21'
				);
	// print_r($karyawan); die();
	
	foreach ($karyawan as $key => $value) {
		$timestamp = strtotime($value->TGL_MASUK);
		$BULAN_MASUK = date("m", $timestamp);
		$HARI_MASUK = $value->TGL_MASUK;

		$TANGGAL_SELESAI_PAYROLL 	= $PERIODE->TANGGAL_SELESAI2;

		$TANGGAL_MULAI_PAYROLL 		= $PERIODE->TANGGAL_MULAI2;
		
		$diff =  strtotime($TANGGAL_SELESAI_PAYROLL) - strtotime($TANGGAL_MULAI_PAYROLL);
		$diff_karyawan =  strtotime($TANGGAL_SELESAI_PAYROLL) - strtotime($HARI_MASUK);

		$HARI_PAYROLL_SEBULAN =  round($diff / 86400);
		$HARI_MASUK_SEBULAN =  round($diff_karyawan / 86400);

		$TOTAL_GJ_PRORATA = 0;
		$GJ_PRORATA = 0;
		$GJ_POKOK = 0;

		$TUNJ_OTTW = $value->TUNJ_OTTW;
		$TUNJ_LAINNYA_1 = $value->TUNJ_LAINNYA_1;
		$TUNJ_LAINNYA_2 = $value->TUNJ_LAINNYA_2;
		$TUNJ_LAINNYA_3 = $value->TUNJ_LAINNYA_3;
		$TUNJ_LAINNYA_4 = $value->TUNJ_LAINNYA_4;
		$TUNJ_LAINNYA_5 = $value->TUNJ_LAINNYA_5;

		$TOTAL_TUNJANGAN_JABATAN = $TUNJ_OTTW+$TUNJ_LAINNYA_1+$TUNJ_LAINNYA_2+$TUNJ_LAINNYA_3+$TUNJ_LAINNYA_4+$TUNJ_LAINNYA_5;
		$JUMLAH_THR_KARYAWAN = 0;
		if($value->JENIS_THR == 'IDUL FITRI')$THR_KARYAWAN = 'IDUL FITRI';
		if($value->JENIS_THR == 'KUNINGAN')$THR_KARYAWAN = 'KUNINGAN';
		$DAPAT_THR = 0;

		if($THR_IDUL_FITRI == 1 && $THR_KARYAWAN == 'IDUL FITRI'){
			//echo "dapet thr";
			$tanggalthr = $TGL_IDUL_FITRI;
			$DAPAT_THR = 1;

			// echo $THR_PRORATA.'<br>';
			// echo $dif_bulan_thr.'<br>';
			// echo $JUMLAH_THR_KARYAWAN.'<br>';

		}

		if($THR_KUNINGAN == 1 && $THR_KARYAWAN == 'KUNINGAN'){
			echo "dapet thr kuningan";
			$tanggalthr = $TGL_KUNINGAN;
			$DAPAT_THR = 1;
		}

		if($DAPAT_THR == 1){
			$d1 = strtotime($value->TGL_MASUK);
			$d2 = strtotime($tanggalthr);
			$totalSecondsDiff = abs($d1-$d2);
			$dif_bulan_thr  = ceil($totalSecondsDiff/60/60/24/30); //16.43
			
			//echo $dif_bulan_thr; die();

			if($dif_bulan_thr >= 12){
				$THR_PRORATA = 'FULL';
				$JUMLAH_THR_KARYAWAN = $value->GAJI_POKOK + $value->TUNJ_KELUARGA + $TOTAL_TUNJANGAN_JABATAN;
			}
			if($dif_bulan_thr < 12){
				if($dif_bulan_thr > 1){
					$THR_PRORATA = 'PRORATE';
					$JUMLAH_THR_CONVERT_PERBULAN = ($value->GAJI_POKOK + $value->TUNJ_KELUARGA + $TOTAL_TUNJANGAN_JABATAN)/12;
					$JUMLAH_THR_KARYAWAN = $JUMLAH_THR_CONVERT_PERBULAN * $dif_bulan_thr;
				}
			}
		}

		// echo "<pre>";
		// print_r($value->JENIS_THR);
		
		//  die();
		if($BULAN_MASUK <= $PERIODE->BULAN){

			if($PERIODE->BULAN == $BULAN_MASUK){
				if($HARI_MASUK_SEBULAN < $HARI_PAYROLL_SEBULAN){
					//PRORATA
					$GAJI_SEHARI = round($value->GAJI_POKOK/$HARI_PAYROLL_SEBULAN);
					$PRORATA = $HARI_MASUK_SEBULAN * $GAJI_SEHARI; 
					$TOTAL_GJ_PRORATA = $PRORATA + $value->TUNJ_KELUARGA;
					$GJ_PRORATA = $PRORATA;
					
					
				}else{
					$TOTAL_GJ_POKOK = $value->GAJI_POKOK + $value->TUNJ_KELUARGA + $TOTAL_TUNJANGAN_JABATAN;
					$GJ_POKOK = $value->GAJI_POKOK;
					

				}
			}else{
				$TOTAL_GJ_POKOK = $value->GAJI_POKOK + $value->TUNJ_KELUARGA + $TOTAL_TUNJANGAN_JABATAN;
				$GJ_POKOK = $value->GAJI_POKOK;
				
			}
			

			$INSERT_VAL['PERIODE_ID'] = $PERIODE_ID;
			
			$INSERT_VAL['PROJECT_ID'] = $PROJECT_ID;


			$INSERT_VAL['KARYAWAN_ID'] = $value->KARYAWAN_ID;
			$INSERT_VAL['NAMA_KARYAWAN'] = $value->NAMA;
			$INSERT_VAL['STATUS_PTKP'] = $value->STATUS_PTKP;


			$VAL_JHT_KARYAWAN = ceil(($TOTAL_GJ_POKOK*2)/100);
			$VAL_JKM_KARYAWAN = 0;
			$VAL_BPJS_K_KARYAWAN = ceil(($TOTAL_GJ_POKOK*1)/100);
			$VAL_JP_KARYAWAN = 0;
			$TOTAL_POTONGAN_PPH_KARYAWAN = $VAL_JHT_KARYAWAN + $VAL_JKM_KARYAWAN + $VAL_BPJS_K_KARYAWAN + $VAL_JP_KARYAWAN;
			$GAJI_SETAHUN = (($TOTAL_GJ_POKOK-$TOTAL_POTONGAN_PPH_KARYAWAN) + $JUMLAH_THR_KARYAWAN)*12;
			$NILAI_PTKP = db_first(" SELECT NILAI as NILAI FROM ptkp WHERE NAMA = '$value->STATUS_PTKP' ")->NILAI;
			
			$PKP = $GAJI_SETAHUN-$NILAI_PTKP;

			$PROGRESIF = 60000000;
			if($PKP < 0) $TIPE_PPH = 0; 
			if($PKP<= 60000000 && $PKP > 0)$TIPE_PPH = 1;
			if($PKP > 60000000 && $PKP <= 250000000){
				$TIPE_PPH = 2;
				$SELISIH_PKP = $PKP - 60000000;
				$LEVEL_2 = ($SELISIH_PKP * 15)/100;
			}
			if($PKP > 250000000 && $PKP <= 500000000){
				$TIPE_PPH = 3;
				$SELISIH_PKP = $PKP - 60000000;
				$LEVEL_2 = ($SELISIH_PKP * 25)/100;
			}
			if($PKP > 500000000){
				$TIPE_PPH = 4;
				$SELISIH_PKP = $PKP - 60000000;
				$LEVEL_2 = ($SELISIH_PKP * 30)/100;
			}
					
			if($TIPE_PPH == 0)$VAL_PPH21 = 0;
			if($TIPE_PPH == 1){
				$PPH_TAHUNAN = (($PKP*5)/100);
				$VAL_PPH21 = ceil($PPH_TAHUNAN/12);
			}
			if($TIPE_PPH >= 2){
				$PPH_TAHUNAN_LV_1 = (($PKP*5)/100);
				$PPH_TAHUNAN_LV_2 = $LEVEL_2;
				$PPH_TAHUNAN = $PPH_TAHUNAN_LV_1+$PPH_TAHUNAN_LV_2;
				$VAL_PPH21 = ceil($PPH_TAHUNAN/12);
			}

			if($DAPAT_THR == 1){
				
			}

			$INSERT_VAL['NILAI_PTKP'] = $NILAI_PTKP;
			$INSERT_VAL['TUNJ_JABATAN'] = $TOTAL_TUNJANGAN_JABATAN;
			$INSERT_VAL['TUNJ_OTTW'] = $TUNJ_OTTW;
			$INSERT_VAL['TUNJ_LAINNYA_1'] = $TUNJ_LAINNYA_1;
			$INSERT_VAL['TUNJ_LAINNYA_2'] = $TUNJ_LAINNYA_2;
			$INSERT_VAL['TUNJ_LAINNYA_3'] = $TUNJ_LAINNYA_3;
			$INSERT_VAL['TUNJ_LAINNYA_4'] = $TUNJ_LAINNYA_4;
			$INSERT_VAL['TUNJ_LAINNYA_5'] = $TUNJ_LAINNYA_5;
			$INSERT_VAL['GAJI_POKOK'] =  $GJ_POKOK;
			$INSERT_VAL['GAJI_PRORATA'] = $GJ_PRORATA;
			$INSERT_VAL['TUNJANGAN_KELUARGA'] =  $value->TUNJ_KELUARGA;
			$INSERT_VAL['THR'] = $JUMLAH_THR_KARYAWAN;
			$INSERT_VAL['JKK_KARYAWAN'] = 0;
			$INSERT_VAL['JKK_PERUSAHAAN'] = ceil(($TOTAL_GJ_POKOK*0.24)/100);
			$INSERT_VAL['JHT_KARYAWAN'] = $VAL_JHT_KARYAWAN;	
			$INSERT_VAL['JHT_PERUSAHAAN'] = ceil(($TOTAL_GJ_POKOK*3.7)/100);
			$INSERT_VAL['JKM_KARYAWAN'] = $VAL_JKM_KARYAWAN;
			$INSERT_VAL['JKM_PERUSAHAAN'] = ceil(($TOTAL_GJ_POKOK*0.3)/100);
			$INSERT_VAL['JP_KARYAWAN'] = $VAL_JP_KARYAWAN;
			$INSERT_VAL['JP_PERUSAHAAN'] = 0;
			$INSERT_VAL['BPJS_KESEHATAN_KARYAWAN'] = $VAL_BPJS_K_KARYAWAN;
			$INSERT_VAL['BPJS_KESEHATAN_PERUSAHAAN'] = ceil(($TOTAL_GJ_POKOK*4)/100);
			$INSERT_VAL['TOTAL_PENGHASILAN'] = ($TOTAL_GJ_POKOK-$TOTAL_POTONGAN_PPH_KARYAWAN) + $JUMLAH_THR_KARYAWAN;
			$INSERT_VAL['TOTAL_POTONGAN'] = $TOTAL_POTONGAN_PPH_KARYAWAN;
			$INSERT_VAL['PPH21'] = $VAL_PPH21;

			$INSERT_DATA = implode(',', $INSERT_VAL);

			
			// echo "<pre>";
			// print_r($INSERT_DATA); die();

			db_execute(" INSERT INTO pph21 (PERIODE_ID,PROJECT_ID,TAHUN,COMPANY_ID,COMPANY_NAME,KARYAWAN_ID,NAMA_KARYAWAN,STATUS_PTKP,NILAI_PTKP, TUNJ_JABATAN, TUNJ_OTTW, TUNJ_LAINNYA_1, TUNJ_LAINNYA_2, TUNJ_LAINNYA_3,TUNJ_LAINNYA_4, TUNJ_LAINNYA_5, GAJI_POKOK,GAJI_PRORATA,TUNJANGAN_KELUARGA,THR,JKK_KARYAWAN,JKK_PERUSAHAAN,JHT_KARYAWAN,JHT_PERUSAHAAN,JKM_KARYAWAN,JKM_PERUSAHAAN,JP_KARYAWAN,JP_PERUSAHAAN,BPJS_KESEHATAN_KARYAWAN,BPJS_KESEHATAN_PERUSAHAAN,TOTAL_PENGHASILAN,TOTAL_POTONGAN,PPH21) VALUES ('$PERIODE_ID','$PROJECT_ID','$TAHUN','$COMPANY_ID','$COMPANY_NAME','" .$INSERT_VAL['KARYAWAN_ID']. "','" .$INSERT_VAL['NAMA_KARYAWAN']. "','" .$INSERT_VAL['STATUS_PTKP']. "','" .$INSERT_VAL['NILAI_PTKP']. "','" .$INSERT_VAL['TUNJ_JABATAN']. "','" .$INSERT_VAL['TUNJ_OTTW']. "','" .$INSERT_VAL['TUNJ_LAINNYA_1']. "','" .$INSERT_VAL['TUNJ_LAINNYA_2']. "','" .$INSERT_VAL['TUNJ_LAINNYA_3']. "','" .$INSERT_VAL['TUNJ_LAINNYA_4']. "','" .$INSERT_VAL['TUNJ_LAINNYA_5']. "','" .$INSERT_VAL['GAJI_POKOK']. "','" .$INSERT_VAL['GAJI_PRORATA']. "','" .$INSERT_VAL['TUNJANGAN_KELUARGA']. "','" .$INSERT_VAL['THR']. "','" .$INSERT_VAL['JKK_KARYAWAN']. "','" .$INSERT_VAL['JKK_PERUSAHAAN']. "','" .$INSERT_VAL['JHT_KARYAWAN']. "','" .$INSERT_VAL['JHT_PERUSAHAAN']. "','" .$INSERT_VAL['JKM_KARYAWAN']. "','" .$INSERT_VAL['JKM_PERUSAHAAN']. "','" .$INSERT_VAL['JP_KARYAWAN']. "','" .$INSERT_VAL['JP_PERUSAHAAN']. "','" .$INSERT_VAL['BPJS_KESEHATAN_KARYAWAN']. "','" .$INSERT_VAL['BPJS_KESEHATAN_PERUSAHAAN']. "','" .$INSERT_VAL['TOTAL_PENGHASILAN']. "','" .$INSERT_VAL['TOTAL_POTONGAN']. "','" .$INSERT_VAL['PPH21']. "' ) ");
		}//EndIfBulanGaji

	}//EndForeach
	
// echo "selesai"; die();	
	header('location: laporan-pph-karyawan-bulanan.php?m=1');
	exit;
};

$JS[] = 'static/tipsy/jquery.tipsy.js';
$JS[] = 'static/js/datagrid-export.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';

?>
<?php
if(get_input('m') == '1'){
	$SUCCESS = 'PPH berhasil dibuat';
}
if(get_input('m') == '2'){
	$ERROR[] = 'Generate hanya bisa per unit';
}
if(get_input('m') == 'closed'){
	$ERROR[] = 'Tidak dapat membuat laporan pph<br>Periode sudah di tutup';
}

if(get_input('m') == 'notgenerate'){
	$ERROR[] = 'Tidak dapat membuat laporan pph data belum digenerate';
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
				<li><a href="javascript:;" onclick="$('#t').datagrid('toExcel','LAPORAN PENGGAJIAN - '+$('#PERIODE_ID option:selected').html()+' - '+$('#PROJECT_ID option:selected').html() +'.xls')" style=""><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),get_search('LAPORAN-PENGGAJIAN','PERIODE_ID'),' id="PERIODE_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-2">
			<?php echo dropdown('PROJECT_ID',project_option_filter_by_company(1),get_search('LAPORAN-PENGGAJIAN','PROJECT_ID'),' id="PROJECT_ID" class="form-control input-sm" ') ?>
		</div>
		<?php /*
		<div class="col-sm-2">
			<input type="text" id="NAMA" value="<?php echo get_search('PENGGAJIAN','NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
		</div>
		*/ ?>
		<div class="col-sm-6">
			<h1 style="margin:0;text-align:right;">Summary PPH 21</h1>
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
		queryParams: { 'PERIODE_ID': $('#PERIODE_ID').val(), 'PROJECT_ID': $('#PROJECT_ID').val() },
		url:'laporan-pph-karyawan-bulanan-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'NAMA',
		sortOrder: 'asc',
		singleSelect:false,
		pagination: false,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		frozenColumns:[[
			//{field:'ck',checkbox:true},
			//{field:'PERIODE',title:'Periode',width:100,sortable:true,align:'center'},
			{field:'NAMA',title:'Nama',width:190,sortable:true,align:'left'},
			{field:'PERIODE',title:'Periode',width:90,sortable:true,align:'left'},
			{field:'TGL_MASUK',title:'Join Date',width:90,sortable:true,align:'center'},
			{field:'POSISI',title:'Jabatan',width:120,sortable:true,align:'center'},
			{field:'TOTAL_PENGHASILAN',title:'Penghasilan',width:90,sortable:true,align:'center'},
			{field:'PPH21',title:'PPH21',width:90,sortable:true,align:'center'},
		]],
		columns:[[
			{title:'TUNJANGAN JABATAN',align:'center',colspan:7},
			{title:'UPAH TETAP',align:'center',colspan:4},
			{title:'BPJS',align:'center',colspan:5},
			
			
		],[
			{field:'TUNJ_JABATAN',title:'TOTAL',width:80,sortable:false,align:'right'},
			{field:'TUNJ_OTTW',title:'OTTW',width:80,sortable:false,align:'right'},
			{field:'TUNJ_LAINNYA_1',title:'K/WK.SEK',width:80,sortable:false,align:'right'},
			{field:'TUNJ_LAINNYA_2',title:'S.Kurikulum',width:80,sortable:false,align:'right'},
			{field:'TUNJ_LAINNYA_3',title:'K.MGMP',width:80,sortable:false,align:'right'},
			{field:'TUNJ_LAINNYA_4',title:'Walikelas',width:80,sortable:false,align:'right'},
			{field:'TUNJ_LAINNYA_5',title:'Kel. Ajar',width:80,sortable:false,align:'right'},
			
			{field:'GAJI_POKOK',title:'GAPOK',width:80,sortable:false,align:'right'},
			{field:'GAJI_PRORATA',title:'GP Prorata',width:80,sortable:false,align:'right'},
			{field:'TUNJANGAN_KELUARGA',title:'Tunj Keluarga',width:80,sortable:false,align:'right'},
			{field:'THR',title:'THR',width:80,sortable:false,align:'right'},
			{field:'TOTAL_POTONGAN',title:'TOTAL',width:80,sortable:false,align:'right'},
			
			{field:'JKK_KARYAWAN',title:'JKK',width:80,sortable:false,align:'right'},
			{field:'JHT_KARYAWAN',title:'JHT',width:80,sortable:false,align:'right'},
			{field:'JKM_KARYAWAN',title:'JKM',width:80,sortable:false,align:'right'},
			{field:'BPJS_KESEHATAN_KARYAWAN',title:'KESEHATAN',width:80,sortable:false,align:'right'},
			
			
			
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
		window.location = 'laporan-pph-karyawan-bulanan.php?generate=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-export').click(function(){
		window.location = 'penggajian.php?export=1&PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-dbank').click(function(){
		window.location = 'penggajian-dbank.php?PERIODE_ID='+$('#PERIODE_ID').val()+'&PROJECT_ID='+$('#PROJECT_ID').val();
		return false;
	});
	$('#btn-slip').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ) alert('No item selected');
		else {
			var rows = $('#t').datagrid('getSelections');
			var QS = '';
			$.each(rows, function(index, value){
				QS += '&ids[]=' + value.KARYAWAN_ID;
			});
			window.open('penggajian-slip.php?PERIODE_ID='+ $('#PERIODE_ID').val() + '&PROJECT_ID='+$('#PROJECT_ID').val() + QS,'_blank');
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