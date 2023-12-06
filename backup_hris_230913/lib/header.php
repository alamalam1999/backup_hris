<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="robots" content="index, follow">
	<meta name="description" content="">
	<meta name="keyword" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>IHRIS</title>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link href="<?php echo base_url() ?>static/favicon.png" rel="SHORTCUT ICON" type="image/png">
	<link href="<?php echo base_url() ?>static/bootstrap/css/bootstrap.cerulean.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url() ?>static/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url() ?>static/autocomplete/jquery.autocomplete.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url() ?>static/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<script src="<?php echo base_url() ?>static/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/autocomplete/jquery.autocomplete.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/highchart/highcharts.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/highchart/modules/series-label.js" type="text/javascript"></script>
	
	<?php if(isset($_SERVER['PHP_SELF']) AND basename($_SERVER['PHP_SELF']) != 'index.php'){ ?>
	<link href="<?php echo base_url() ?>static/easyui/themes/default/easyui.css?v=<?php echo rand(11111,99999) ?>" rel="stylesheet">
	<script src="<?php echo base_url() ?>static/easyui/jquery.easyui.min.js" type="text/javascript"></script>
	<?php } ?>
	
	<?php if(isset($CSS) AND is_array($CSS)){ foreach($CSS as $css){ ?>
	<link href="<?php echo base_url().$css ?>" rel="stylesheet" type="text/css">
	<?php }} ?>
	<?php if(isset($JS) AND is_array($JS)){ foreach($JS as $js){ ?>
	<script src="<?php echo base_url().$js ?>" type="text/javascript"></script>
	<?php }} ?>
	<script src="<?php echo base_url() ?>static/js/plugin.js" type="text/javascript"></script>
	
<script>
$(document).ready(function(){
	$("#clock").clock({
		"timestamp" : parseInt('<?php echo time() + date('Z') ?>'),
		"dateFormat" : "d M y, ",
		"timeFormat" : "H:i:s",
	});
});
</script>
</head>
<body <?php echo isset($BODY_STYLE) ? $BODY_STYLE : ''; ?>>

<?php
if( login_exist() ):
$CU = current_user();

$NOTIF_ABSENSI = 0;
$NOTIF_PAYROLL = 0;
$where = $_EKSEPSI = $_LEMBUR = '';
if( ! empty($CU->PROJECT_ID))
{
	$where = " K.PROJECT_ID='$CU->PROJECT_ID' AND ";
}

if( has_access('eksepsi.view') )
{
	$row = db_first(" SELECT COUNT(1) as cnt FROM eksepsi A LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.KARYAWAN_ID WHERE $where A.STATUS='PENDING' ");
	$NOTIF_ABSENSI = $NOTIF_ABSENSI + $row->cnt;
	$_EKSEPSI = empty($row->cnt) ? '' : '<span class="badge" style="font-size:10px;float:right;margin-top:1px;background-color:#ff0000;color:#fff;padding:4px 6px;">'.$row->cnt.'</span>';
}

if( has_access('lembur.view') )
{
	$row = db_first(" SELECT COUNT(1) as cnt FROM lembur A LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.KARYAWAN_ID WHERE $where A.STATUS='PENDING' ");
	$NOTIF_PAYROLL = $NOTIF_PAYROLL + $row->cnt;
	$_LEMBUR = empty($row->cnt) ? '' : '<span class="badge" style="font-size:10px;float:right;margin-top:1px;background-color:#ff0000;color:#fff;padding:4px 6px;">'.$row->cnt.'</span>';
}

$_NOTIF_ABSENSI = empty($NOTIF_ABSENSI) ? '' : '&nbsp;&nbsp;<span class="badge" style="font-size:10px;background-color:#ff0000;color:#fff;padding:3px 5px;">'.$NOTIF_ABSENSI.'</span>';
$_NOTIF_PAYROLL = empty($NOTIF_PAYROLL) ? '' : '&nbsp;&nbsp;<span class="badge" style="font-size:10px;background-color:#ff0000;color:#fff;padding:3px 5px;">'.$NOTIF_PAYROLL.'</span>';

?>

	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.php" style="color: #ff6666 !important; font-weight: bold;"><span style="color:#fff;"><i class="fa fa-user-circle-o"></i></span>&nbsp;&nbsp;&nbsp;IHRIS</a>
			</div>

			<div class="collapse navbar-collapse" id="navbar-collapse-1">
			<ul class="nav navbar-nav">
				<?php
				$index_active = '';
				$MOD = remove_ext(basename(strtolower($_SERVER['PHP_SELF'])));
				$M = array();
				if(in_array($MOD,array('','index'))) $index_active = 'active';
				if(in_array($MOD,array('','karyawan'))) $employee_active = 'active';
				
				$sub_karyawan = '';
				if( ! has_access('dashboard.admin') ) {
				$sub_karyawan .= '<li><a href="karyawan-eksepsi.php">Pengajuan Eksepsi</a></li>';
				$sub_karyawan .= '<li><a href="karyawan-lembur.php">Pengajuan Lembur</a></li>';
				$sub_karyawan .= '<li><a href="absensi.php">Absensi</a></li>';
				if(in_array($MOD,get_menu_group('karyawan'))) $M['karyawan'] = '1';
				}
				
				$sub_rekrutmen = '';
				$sub_rekrutmen .= access_button('rekrutmen','lowongan.view','<li><a href="lowongan.php">Lowongan</a></li>');
				$sub_rekrutmen .= access_button('rekrutmen','lamaran.view','<li><a href="lamaran.php">Lamaran</a></li>');
				$sub_rekrutmen .= access_button('rekrutmen','interview.view','<li><a href="interview.php">Interview</a></li>');
				$sub_rekrutmen .= access_button('rekrutmen','rekomendasi.view','<li><a href="rekomendasi.php">Rekomendasi</a></li>');
				if(in_array($MOD,get_menu_group('rekrutmen'))) $M['rekrutmen'] = '1';


				$sub_data_karyawan = '';
				$sub_data_karyawan .= access_button('data_karyawan','karyawan.view','<li><a href="karyawan.php">Karyawan Aktif</a></li>');
				$sub_data_karyawan .= access_button('data_karyawan','karyawan-pasif.view','<li><a href="karyawan-pasif.php">Karyawan Pasif</a></li>');
				$sub_data_karyawan .= access_button('data_karyawan','surat-peringatan.view','<li><a href="surat-peringatan.php">Surat Peringatan</a></li>');
				$sub_data_karyawan .= access_button('data_karyawan','tugas.view','<li><a href="tugas.php">Tugas</a></li>');
				if(in_array($MOD,get_menu_group('data_karyawan'))) $M['data_karyawan'] = '1';
				
				$sub_absensi = '';
				$sub_absensi .= access_button('absensi','mesin.view','<li><a href="mesin.php">Mesin</a></li>');
				$sub_absensi .= access_button('absensi','import-log.view','<li><a href="import-log.php">Import Log</a></li>');
				$sub_absensi .= access_button('absensi','shift.view','<li><a href="shift.php">Shift</a></li>');
				$sub_absensi .= access_button('absensi','shift-mapper.view','<li><a href="shift-mapper.php">Shift Mapper</a></li>');
				$sub_absensi .= access_button('absensi','jadwal.view','<li role="separator" class="divider"></li><li><a href="jadwal.php">Jadwal</a></li>');
				$sub_absensi .= access_button('absensi','absensi.view','<li><a href="absensi.php">Absensi</a></li>');
				$sub_absensi .= access_button('absensi','lembur.view','<li><a href="lembur.php">Lembur '.$_LEMBUR.'</a></li>');
				$sub_absensi .= access_button('absensi','eksepsi.view','<li><a href="eksepsi.php">Eksepsi '.$_EKSEPSI.'</a></li>');
				$sub_absensi .= access_button('absensi','medical.view','<li><a href="medical.php">Medical</a></li>');
				$sub_absensi .= access_button('absensi','potongan.view','<li><a href="potongan.php">Potongan</a></li>');
				$sub_absensi .= access_button('absensi','adjusment.view','<li><a href="adjusment.php">Adjusment</a></li>');
				$sub_absensi .= access_button('absensi','scan-tracking.view','<li role="separator" class="divider"></li><li><a href="scan-tracking.php">Scan Tracking</a></li>');
				if(in_array($MOD,get_menu_group('absensi'))) $M['absensi'] = '1';

				$sub_transaksi = '';
				$sub_transaksi .= access_button('transaksi','periode.view','<li><a href="periode.php">Periode</a></li><li role="separator" class="divider"></li>');
				$sub_transaksi .= access_button('transaksi','penggajian.view','<li><a href="penggajian.php">Penggajian</a></li>');
				$sub_transaksi .= access_button('transaksi','thr.view','<li><a href="thr.php">THR</a></li>');
				$sub_transaksi .= access_button('transaksi','rekap.view','<li><a href="rekap.php">Rekap</a></li>');
				if(in_array($MOD,get_menu_group('transaksi'))) $M['transaksi'] = '1';

				$sub_appraisal = '';
				##$sub_appraisal .= access_button('appraisal','surat-peringatan.view','<li><a href="surat-peringatan.php">Surat Peringatan</a></li>');
				##if(in_array($MOD,get_menu_group('appraisal'))) $M['appraisal'] = '1';

				$sub_equipment = '';
				$sub_equipment .= access_button('equipment','equipment-used.view','<li><a href="equipment-used.php">Equipment Used</a></li>');
				$sub_equipment .= access_button('equipment','equipment-stock.view','<li><a href="equipment-stock.php">Update Stock</a></li>');
				if(in_array($MOD,get_menu_group('equipment'))) $M['equipment'] = '1';
				
				$sub_master = '';
				$sub_master .= access_button('master','company.view','<li><a href="company.php">Company</a></li>');
				$sub_master .= access_button('master','project.view','<li><a href="project.php">Unit</a></li>');
				$sub_master .= access_button('master','jabatan.view','<li><a href="jabatan.php">Level Jabatan</a></li>');
				$sub_master .= access_button('master','posisi.view','<li><a href="posisi.php">Posisi</a></li>');
				$sub_master .= access_button('master','holiday.view','<li><a href="holiday.php">Holiday</a></li>');
				$sub_master .= access_button('master','kategori-keahlian.view','<li role="separator" class="divider"></li><li><a href="kategori-keahlian.php">Kategori Keahlian</a></li>');
				$sub_master .= access_button('master','keahlian.view','<li><a href="keahlian.php">Keahlian</a></li>');
				$sub_master .= access_button('master','kota.view','<li role="separator" class="divider"></li><li><a href="kota.php">Kota / Kabupaten</a></li>');
				$sub_master .= access_button('master','provinsi.view','<li><a href="provinsi.php">Provinsi</a></li>');
				$sub_master .= access_button('master','eksepsi-setting.view','<li role="separator" class="divider"></li><li><a href="eksepsi-setting.php">Setting Eksepsi</a></li>');
				if(in_array($MOD,get_menu_group('master'))) $M['master'] = '1';
				
				$sub_system = '';
				$sub_system .= access_button('system','user.view','<li><a href="user.php">User</a></li>');
				$sub_system .= access_button('system','user-level.view','<li><a href="user-level.php">User Level</a></li>');
				$sub_system .= access_button('system','user-module.view','<li><a href="user-module.php">User Module</a></li>');
				$sub_system .= access_button('system','smtp-setting.view','<li role="separator" class="divider"></li><li><a href="smtp-setting.php">SMTP Setting</a></li>');
				$sub_system .= access_button('system','recaptcha-setting.view','<li><a href="recaptcha-setting.php">ReCaptcha Setting</a></li>');
				$sub_system .= access_button('system','gmap-setting.view','<li><a href="gmap-setting.php">Gmap Setting</a></li>');
				if(in_array($MOD,get_menu_group('system'))) $M['system'] = '1';
				if(in_array($MOD,array('','index'))) $M['index'] = '1';
				if(in_array($MOD,array('','karyawan'))) $M['karyawan'] = '1';
				?>
				<?php /*<li class="<?php echo $index_active ?>"><a href="index.php">Home</a></li>*/ ?>
				<?php if($sub_karyawan){ ?>
				<li class="dropdown <?php echo isset($M['karyawan']) ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Karyawan <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_karyawan ?>
					</ul>
				</li>
				<?php } ?>
				<?php if($sub_rekrutmen){ ?>
				<li class="dropdown <?php echo isset($M['rekrutmen']) ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Rekrutmen <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_rekrutmen ?>
					</ul>
				</li>
				<?php } ?>
				
				<?php /*if( has_access('karyawan.view') ) { ?>
				<li class="<?php echo $employee_active ?>"><a href="karyawan.php">Karyawan</a></li>
				<?php } */?>

				<?php if($sub_data_karyawan){ ?>
				<li class="dropdown <?php echo isset($M['data_karyawan']) ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Karyawan <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_data_karyawan ?>
					</ul>
				</li>
				<?php } ?>
				<?php if($sub_absensi){ ?>
				<li class="dropdown <?php echo isset($M['absensi']) ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Absensi<?php echo $_NOTIF_ABSENSI ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_absensi ?>
					</ul>
				</li>
				<?php } ?>
				<?php if($sub_transaksi){ ?>
				<li class="dropdown <?php echo isset($M['transaksi']) ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Payroll<?php echo $_NOTIF_PAYROLL ?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_transaksi ?>
					</ul>
				</li>
				<?php } ?>
				<?php /* if($sub_appraisal){ ?>
				<li class="dropdown <?php echo isset($M['appraisal']) ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Appraisal <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_appraisal ?>
					</ul>
				</li>
				<?php } */ ?>
				<?php if($sub_equipment){ ?>
				<li class="dropdown <?php echo isset($M['equipment']) ? 'active' : '' ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Equipment <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_equipment ?>
					</ul>
				</li>
				<?php } ?>
				<?php if($sub_master){ ?>
				<li class="dropdown <?php echo isset($M['master']) ? 'active' : '' ?> ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Master <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_master ?>
					</ul>
				</li>
				<?php } ?>
				<?php if($sub_system){ ?>
				<li class="dropdown <?php echo isset($M['system']) ? 'active' : '' ?> ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">System <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<?php echo $sub_system ?>
						<?php /*<li role="separator" class="divider"></li>
						<li><a href="logout.php">Keluar</a></li>*/ ?>
					</ul>
				</li>
				<?php } ?>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			<?php /*if( has_access('lembur.view') ) { ?>
			<li><a href="lembur.php"><i class="fa fa-calendar-plus-o" style="font-size:16px;"></i><?php echo $_LEMBUR ?></a></li>
			<?php } ?>
			<?php if( has_access('eksepsi.view') ) { ?>
			<li><a href="eksepsi.php"><i class="fa fa-calendar-times-o" style="font-size:16px;"></i><?php echo $_EKSEPSI ?></a></li>
			<?php }*/ ?>
			<li><a href="javascript:void(0)"><span id="clock"></span> WIB</a></li>
			<li class="dropdown">
				<?php if( login_exist() ){ ?>
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="hidden-md"><?php echo $CU->NAMA ?>&nbsp;&nbsp;</span><img src="<?php echo gravatar($CU->EMAIL) ?>" alt="" style="width:18px;">&nbsp;&nbsp;<span class="caret"></span></a>
				<ul class="dropdown-menu">
				<li><a href="profile.php">Profile</a></li>
				<li><a href="change-password.php">Change Password</a></li>
				<li role="separator" class="divider"></li>
				<li><a href="logout.php">Log Out</a></li>
				</ul>
				<?php } ?>
			</li>
			</ul>
			</div>
		</div>
	</nav>


<?php endif; ?>