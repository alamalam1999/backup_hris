<?php
$url1 = $this->uri->segment(1);
$url2 = $this->uri->segment(2);
$KARYAWAN_ID = getUserActive("KARYAWAN_ID");
$data_karyawan =  $this->db->query(" SELECT * FROM karyawan WHERE KARYAWAN_ID = $KARYAWAN_ID ")->row();
$data_pendidikan_karyawan = $this->db->query(" SELECT * FROM pendidikan_karyawan WHERE KARYAWAN_ID = $KARYAWAN_ID AND APPROVED != 'APPROVED' AND KETERANGAN_APPROVED != '' ")->result();
$notif_pendidikan = 0;
if(COUNT($data_pendidikan_karyawan) > 0) $notif_pendidikan = COUNT($data_pendidikan_karyawan);
$_notif_pendidikan = '';
if($notif_pendidikan > 0) $_notif_pendidikan = ' <p style="font-weight:bold; color:#ff0000; margin-left: 5px; margin-top: 18px;">(' . $notif_pendidikan . ')</p>';

$data_kursus_karyawan = $this->db->query(" SELECT * FROM kursus_karyawan WHERE KARYAWAN_ID = $KARYAWAN_ID AND APPROVED != 'APPROVED' AND KETERANGAN_APPROVED != '' ")->result();
$notif_kursus = 0;
if(COUNT($data_kursus_karyawan) > 0) $notif_kursus = COUNT($data_kursus_karyawan);
$_notif_kursus = '';
if($notif_kursus > 0) $_notif_kursus = ' <p style="font-weight:bold; color:#ff0000; margin-left: 5px; margin-top: 18px;">(' . $notif_kursus . ')</p>';

$data_keluarga_karyawan = $this->db->query(" SELECT * FROM keluarga_karyawan WHERE KARYAWAN_ID = $KARYAWAN_ID AND APPROVED != 'APPROVED' AND KETERANGAN_APPROVED != '' ")->result();
$notif_keluarga = 0;
if(COUNT($data_keluarga_karyawan) > 0) $notif_keluarga = COUNT($data_keluarga_karyawan);
$_notif_keluarga = '';
if($notif_keluarga > 0) $_notif_keluarga = ' <p style="font-weight:bold; color:#ff0000; margin-left: 5px; margin-top: 18px;">(' . $notif_keluarga . ')</p>';

$data_dok_karyawan = $this->db->query(" SELECT * FROM dok_karyawan WHERE KARYAWAN_ID = $KARYAWAN_ID AND APPROVED != 'APPROVED' AND KETERANGAN_APPROVED != '' ")->result();
$notif_dok = 0;
if(COUNT($data_dok_karyawan) > 0) $notif_dok = COUNT($data_dok_karyawan);
$_notif_dok = '';
if($notif_dok > 0) $_notif_dok = ' <p style="font-weight:bold; color:#ff0000; margin-left: 5px; margin-top: 18px;">(' . $notif_dok . ')</p>';


?>

<div class="main-sidebar">
  <aside id="sidebar-wrapper">

    <!-- sidebar customer -->
    <div class="sidebar-brand mt-2 mb-2">
      <div>
      <img src="<?= base_url('assets/images/logo-avicenna.png') ?>" style="width: 50px; height: auto; ">
      <p style="margin-top: -5px; font-style: italic; font-size: 10px;">Employee Self Service</p>
      </div>
     
      
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
      <a href="<?php echo site_url() ?>">HADIR</a>
    </div>
    <ul class="sidebar-menu mt-2">
      <li class="menu-header bg-secondary text-white">Menu Utama</li>
      <?php if($data_karyawan->COMPLETED == 1){ ?>

      <li <?php if ($url1 == 'beranda') echo 'class="active"'; ?>>
        <a class="nav-link" href="<?php echo site_url('beranda'); ?>">
          <i class="fas fa-home"></i> <span>Beranda</span>
        </a>
      </li>
    <?php } ?>
      <!-- <li <?php if ($url1 == 'profile') echo 'class="active"'; ?>>
        <a class="nav-link" href="<?php echo site_url('profile'); ?>">
          <i class="fas fa-user"></i> <span>Profile</span>
        </a>
      </li> -->

      <li class="dropdown <?php echo in_array($url1, array('profile', 'profile_sertifikat', 'profile_pendidikan')) ? 'active' : ''; ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-folder-open"></i> <span>Data Pribadi</span></a>
        <ul class="dropdown-menu">
          <li <?php if ($url1 == 'profile') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('profile'); ?>">Profile diri</a></li>
          <li <?php if ($url1 == 'profile_sertifikat') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('profile_sertifikat'); ?>">Dokumen<?= $_notif_dok; ?></a></li>
          <li <?php if ($url1 == 'profile_pendidikan') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('profile_pendidikan'); ?>">Pendidikan <?= $_notif_pendidikan; ?><?= $_notif_kursus; ?></a></li>
          <li <?php if ($url1 == 'profile_organisasi') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('profile_organisasi'); ?>">Organisasi</a></li>
          <li <?php if ($url1 == 'profile_keluarga') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('profile_keluarga'); ?>">Keluarga <?= $_notif_keluarga; ?></a></li>
          <li <?php if ($url1 == 'profile_kerja') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('profile_kerja'); ?>">Pengalaman Kerja</a></li>

        </ul>
      </li>
      <?php if($data_karyawan->COMPLETED == 1){ ?>
      <li class="dropdown <?php echo in_array($url1, array('jadwal', 'absen_in_out', 'absensi', 'eksepsi')) ? 'active' : ''; ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-folder-open"></i> <span>Data Absensi</span></a>
        <ul class="dropdown-menu">
          <li <?php if ($url1 == 'jadwal') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('jadwal'); ?>">Jadwal</a></li>
          <li <?php if ($url1 == 'absen_in_out') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('absen_in_out'); ?>">Absen</a></li>
          <li <?php if ($url1 == 'absensi') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('absensi'); ?>">Data Absensi</a></li>
          <li <?php if ($url1 == 'eksepsi') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('eksepsi'); ?>">Pengajuan Eksepsi</a></li>
          

        </ul>
      </li>

      <li class="dropdown <?php echo in_array($url1, array('tugas', 'kpi', 'surat_peringatan')) ? 'active' : ''; ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-folder-open"></i> <span>Data Kinerja</span></a>
        <ul class="dropdown-menu">
          <li <?php if ($url1 == 'tugas') echo ' class="active"'; ?>><a class="nav-link" href="#">Tugas</a></li>
          <li <?php if ($url1 == 'kpi') echo ' class="active"'; ?>><a class="nav-link" href="#">KPI</a></li>
          <li <?php if ($url1 == 'surat_peringatan') echo ' class="active"'; ?>><a class="nav-link" href="#">Surat Peringatan</a></li>
         
          

        </ul>
      </li>

      <li class="dropdown <?php echo in_array($url1, array('data_medical', 'reimburse_medical')) ? 'active' : ''; ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-folder-open"></i> <span>Data Medical</span></a>
        <ul class="dropdown-menu">
          <li <?php if ($url1 == 'tugas') echo ' class="active"'; ?>><a class="nav-link" href="#">Data Medical</a></li>
          <li <?php if ($url1 == 'kpi') echo ' class="active"'; ?>><a class="nav-link" href="#">Reimburse Medical</a></li>

        </ul>
      </li>

      
     

      <li <?php if ($url1 == 'penggajian') echo 'class="active"'; ?>>
        <a class="nav-link" href="<?php echo site_url('penggajian'); ?>">
          <i class="fas fa-download"></i> <span>Penggajian</span>
        </a>
      </li>
      
      <?php } ?>

      
      <li class="menu-header bg-secondary text-white">Setting</li>
        <li class="dropdown <?php echo in_array($url1, array('profile/update_password')) ? 'active' : ''; ?>">
          <a href="#" class="nav-link has-dropdown"><i class="far fa-user"></i> <span>Account</span></a>
          <ul class="dropdown-menu">
            <li <?php if ($url1 == 'profile/update_password') echo 'class="active"'; ?>><a href="<?php echo site_url('profile/update_password'); ?>">Change Password</a></li>
            <li><a href="<?php echo site_url('auth/logout') ?>">Logout</a></li>
          </ul>
      </li>
        
        <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
              <?php /*
              <a href="#" class="btn bg-warning text-white btn-lg btn-block btn-icon-split">
                <i class="fas fa-book-open"></i> Buku Panduan
              </a>
              */ ?>
            </div>
     

      


      <?php
      /*
      <li <?php if ($url1 == 'penggajian') echo 'class="active"'; ?>>
        <a class="nav-link" href="<?php echo site_url('penggajian'); ?>">
          <i class="fas fa-star"></i> <span>Penggajian</span>
        </a>
      </li>


      <li <?php if ($url1 == 'kinerja') echo 'class="active"'; ?>>
        <a class="nav-link" href="<?php echo site_url('kinerja'); ?>">
          <i class="far fa-star"></i> <span>Kinerja</span>
        </a>
      </li>


      <li class="dropdown <?php echo in_array($url1, array('permohonan', 'penawaran', 'job_order')) ? 'active' : ''; ?>">
        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="far fa-folder-open"></i> <span>Order</span></a>
        <ul class="dropdown-menu">
          <li <?php if ($url1 == 'permohonan') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('permohonan'); ?>">Permohonan</a></li>
          <li <?php if ($url1 == 'penawaran') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('penawaran'); ?>">Penawaran</a></li>
          <li <?php if ($url1 == 'job_order') echo ' class="active"'; ?>><a class="nav-link" href="<?php echo site_url('job_order'); ?>">Job Order</a></li>
        </ul>
      </li>
      */
      ?>

    </ul>
  </aside>
</div>
