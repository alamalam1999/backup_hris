<?php
$EDIT = $data_karyawan;

?>
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header">
        <div class="row ml-4">
          <div class="row col-md-12">
            <h5 class="pt-2 mr-2 text-danger">Data Pribadi</h5>
          </div>
          <div class="row col-md-12">
            <a href="<?php echo site_url('profile'); ?>" class="btn btn-secondary mr-1 mb-1">Profile</a>
            <a href="<?php echo site_url('profile_sertifikat'); ?>" class="btn btn-secondary mr-1 mb-1">Sertifikat</a>
            <a href="<?php echo site_url('profile_pendidikan'); ?>" class="btn btn-secondary mr-1 mb-1">Pendidikan</a>
            <a href="<?php echo site_url('profile_organisasi'); ?>" class="btn btn-secondary mr-1 mb-1">Organisasi</a>
            <a href="<?php echo site_url('profile_keluarga'); ?>" class="btn btn-secondary mr-1 mb-1">Keluarga</a>
            <a href="<?php echo site_url('profile_kerja'); ?>" class="btn btn-secondary mr-1 mb-1">Pengalaman Kerja</a>
          </div>
        </div>
      </div>

      <form action="<?php echo $update_url; ?>" method="post" enctype="multipart/form-data">
        <div class="card-body">
          <!-- <div class="row col-md-12 col-lg-12 col-sm-12">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Nama Lengkap</strong></label>
              <input type="text" name="nama" class="form-control" value="<?php echo $data_karyawan->NAMA ?>" readonly />
            </div>
          </div> -->



          <div class="row  col-md-12 col-lg-12 col-sm-12">
            <div class="col-md-6 col-lg-6 col-sm-12">

              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Nama Lengkap
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="NAMA" value="<?php echo set_value('NAMA', $EDIT->NAMA) ?>" class="form-control" style="text-transform:uppercase" readonly>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Nama Panggilan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="NAMA_PANGGILAN" value="<?php echo set_value('NAMA_PANGGILAN', $EDIT->NAMA_PANGGILAN) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Jenis Kelamin
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('JK', array('Laki-laki' => 'Laki-Laki', 'Perempuan' => 'Perempuan'), set_value('JK', $EDIT->JK), ' class="form-control" ') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Tempat Lahir
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="TP_LAHIR" value="<?php echo set_value('TP_LAHIR', $EDIT->TP_LAHIR) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Tanggal Lahir
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="TGL_LAHIR" value="<?php echo set_value('TGL_LAHIR', $EDIT->TGL_LAHIR) ?>" class="form-control datepicker" autocomplete="off" placeholder="YYYY-MM-DD">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Kewarganegaraan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('KEWARGANEGARAAN', array('WNI' => 'WNI', 'WNA' => 'WNA'), set_value('KEWARGANEGARAAN', $EDIT->KEWARGANEGARAAN), ' class="form-control" ') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Suku
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="SUKU" value="<?php echo set_value('SUKU', $EDIT->SUKU) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Agama
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('AGAMA', array('ISLAM' => 'ISLAM', 'KRISTEN' => 'KRISTEN', 'KATOLIK' => 'KATOLIK', 'HINDU' => 'HINDU', 'BUDHA' => 'BUDHA', 'KONG HU CHU' => 'KONG HU CHU'), set_value('AGAMA', $EDIT->AGAMA), ' class="form-control" ') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Golongan Darah
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('GOL_DARAH', array('A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O'), set_value('GOL_DARAH', $EDIT->GOL_DARAH), ' class="form-control" ') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Tinggi Badan(Cm)
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-4">
                  <input type="number" name="TINGGI" value="<?php echo set_value('TINGGI', $EDIT->TINGGI) ?>" class="form-control">
                </div>

                <label for="" class="col-sm-12 control-label">Berat Badan(Kg)
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-4">
                  <input type="number" name="BERAT" value="<?php echo set_value('BERAT', $EDIT->BERAT) ?>" class="form-control">
                </div>

              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Ukuran Baju
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('UKURAN_BAJU', array('S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL'), set_value('UKURAN_BAJU', $EDIT->UKURAN_BAJU), ' class="form-control" ') ?>
                </div>
                <label for="" class="col-sm-12 control-label">Ukuran Sepatu
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="number" name="UKURAN_SEPATU" value="<?php echo set_value('UKURAN_SEPATU', $EDIT->UKURAN_SEPATU) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">No Telp Rumah
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="TELP" value="<?php echo set_value('TELP', $EDIT->TELP) ?>" class="form-control">
                </div>
                <label for="" class="col-sm-12 control-label">HP
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="HP" value="<?php echo set_value('HP', $EDIT->HP) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Alamat Email
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="email" name="EMAIL" value="<?php echo set_value('EMAIL', $EDIT->EMAIL) ?>" class="form-control" readonly>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">No. e-KTP
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="NO_IDENTITAS" value="<?php echo set_value('NO_IDENTITAS', $EDIT->NO_IDENTITAS) ?>" class="form-control">
                </div>
                <div class="col-sm-12">
                  <input type="file" name="FC_KTP" class="form-control" title="Upload Scan e-KTP">
                </div>
                <?php if (!empty($EDIT->FC_KTP) and url_exists(hris('url') . 'uploads/cv/' . rawurlencode($EDIT->FC_KTP))) { ?>
                  <div class="col-sm-12">
                    <span class="input-group-btn">
                      <a class="btn btn-primary btn-flat" href="<?php echo hris('url') . "uploads/cv/" . $EDIT->FC_KTP ?>" download title="Download">
                        <span class="fa fa-download"></span> Download file
                      </a>
                    </span>
                  </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">No. NPWP
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="NPWP" value="<?php echo set_value('NPWP', $EDIT->NPWP) ?>" class="form-control">
                </div>
                <div class="col-sm-12">
                  <input type="file" name="FC_NPWP" class="form-control" title="Upload Scan NPWP">
                </div>
                <?php if (!empty($EDIT->FC_NPWP) and url_exists(hris('url') . 'uploads/cv/' . rawurlencode($EDIT->FC_NPWP))) { ?>
                  <div class="col-sm-12">
                    <span class="input-group-btn">
                      <a class="btn btn-primary btn-flat" href="<?php echo hris() . "uploads/cv/" . $EDIT->FC_NPWP ?>" download title="Download">
                        <span class="fa fa-download"></span> Download file
                      </a>
                    </span>
                  </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">BPJS Kesehatan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="BPJS_KESEHATAN" value="<?php echo set_value('BPJS_KESEHATAN', $EDIT->BPJS_KESEHATAN) ?>" class="form-control">
                </div>
                <div class="col-sm-12">
                  <input type="file" name="FC_BPJS_KESEHATAN" class="form-control" title="Upload Scan BPJS Kesehatan">
                </div>
                <?php if (!empty($EDIT->FC_BPJS_KESEHATAN) and url_exists(hris() . 'uploads/cv/' . rawurlencode($EDIT->FC_BPJS_KESEHATAN))) { ?>
                  <div class="col-sm-12">
                    <span class="input-group-btn">
                      <a class="btn btn-primary btn-flat" href="<?php echo hris() . "uploads/cv/" . $EDIT->FC_BPJS_KESEHATAN ?>" download title="Download">
                        <span class="fa fa-download"></span> Download file
                      </a>
                    </span>
                  </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">BPJS Ketenagakerjaan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="BPJS_KETENAGAKERJAAN" value="<?php echo set_value('BPJS_KETENAGAKERJAAN', $EDIT->BPJS_KETENAGAKERJAAN) ?>" class="form-control">
                </div>
                <div class="col-sm-12">
                  <input type="file" name="FC_BPJS_KETENAGAKERJAAN" class="form-control" title="Upload Scan BPJS Ketenagakerjaan">
                </div>
                <?php if (!empty($EDIT->FC_BPJS_KETENAGAKERJAAN) and url_exists(hris() . 'uploads/cv/' . rawurlencode($EDIT->FC_BPJS_KETENAGAKERJAAN))) { ?>
                  <div class="col-sm-12">
                    <span class="input-group-btn">
                      <a class="btn btn-primary btn-flat" href="<?php echo hris() . "uploads/cv/" . $EDIT->FC_BPJS_KETENAGAKERJAAN ?>" download title="Download">
                        <span class="fa fa-download"></span> Download file
                      </a>
                    </span>
                  </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Satus Kawin
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('ST_KAWIN', array('LAJANG' => 'LAJANG', 'MENIKAH' => 'MENIKAH', 'JANDA' => 'JANDA', 'DUDA' => 'DUDA'), set_value('ST_KAWIN', $EDIT->ST_KAWIN), ' class="form-control" ') ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Kendaraan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('PUNYA_KENDARAAN', array('PUNYA' => 'PUNYA', 'TIDAK PUNYA' => 'TIDAK'), set_value('PUNYA_KENDARAAN', $EDIT->PUNYA_KENDARAAN), ' class="form-control" id="PUNYA_KENDARAAN"') ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Jenis Kendaraan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('JENIS_KENDARAAN', array('MOTOR' => 'MOTOR', 'MOBIL' => 'MOBIL'), set_value('JENIS_KENDARAAN', $EDIT->JENIS_KENDARAAN), ' class="form-control" id="JENIS_KENDARAAN"') ?>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Milik
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('MILIK_KENDARAAN', array('SENDIRI' => 'SENDIRI', 'ORANG TUA' => 'ORANG TUA', 'KANTOR' => 'KANTOR'), set_value('MILIK_KENDARAAN', $EDIT->MILIK_KENDARAAN), ' class="form-control" id="MILIK_KENDARAAN"') ?>
                </div>
              </div>

            </div>


            <!-- kolom ke 2 -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="" class="col-sm-12 control-label"></label>
                <label for="" class="col-sm-12 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                  Isi sesuai tempat tinggal KTP
                </label>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Alamat
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="ALAMAT_KTP" value="<?php echo set_value('ALAMAT_KTP', $EDIT->ALAMAT_KTP) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Kelurahan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="KELURAHAN_KTP" value="<?php echo set_value('KELURAHAN_KTP', $EDIT->KELURAHAN_KTP) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Kecamatan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="KECAMATAN_KTP" value="<?php echo set_value('KECAMATAN_KTP', $EDIT->KECAMATAN_KTP) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Provinsi
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <select name="PROVINSI_KTP" id="PROVINSI_KTP" class="form-control" style="width: 100%;">
                    <?php
                    //$K = db_first(" SELECT * FROM provinsi WHERE PROVINSI='".db_escape(set_value('PROVINSI_KTP',$EDIT->PROVINSI_KTP))."' ");
                    // if(isset($K->PROVINSI)){
                    //   echo '<option value="'.$K->PROVINSI.'" data-kode="'.$K->PROVINSI_ID.'" selected="selected">'.$K->PROVINSI.'</option>';
                    // }
                    $prov = db_first(" SELECT * FROM provinsi  WHERE PROVINSI='" . db_escape(set_value('PROVINSI_KTP', $EDIT->PROVINSI_KTP)) . "'");
                    foreach ($prov as $key => $K) {
                      $selected = "";
                      if ($K->PROVINSI_ID == $EDIT->PROVINSI_KTP) $selected = "selected";
                      echo '<option value="' . $K->PROVINSI . '" data-kode="' . $K->PROVINSI_ID . '" ' . $selected . '>' . $K->PROVINSI . '</option>';
                    }

                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Kota
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <select name="KOTA_KTP" id="KOTA_KTP" class="form-control" style="width: 100%;">
                    <?php
                    // $K = db_first(" SELECT * FROM kota WHERE KOTA='".db_escape(set_value('KOTA_KTP',$EDIT->KOTA_KTP))."' ");
                    // if(isset($K->KOTA)){
                    //   echo '<option value="'.$K->KOTA.'" selected="selected">'.$K->KOTA.'</option>';
                    // }

                    $kota = db_first(" SELECT * FROM kota WHERE KOTA='" . db_escape(set_value('KOTA_KTP', $EDIT->KOTA_KTP)) . "'");
                    foreach ($kota as $key => $K) {
                      $selected = "";
                      if ($K->KOTA == $EDIT->KOTA_KTP) $selected = "selected";
                      echo '<option value="' . $K->KOTA . '" ' . $selected . '>' . $K->KOTA . '</option>';
                    }

                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Kode Pos
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="KODE_POS_KTP" value="<?php echo set_value('KODE_POS_KTP', $EDIT->KODE_POS_KTP) ?>" class="form-control" style="text-align:center;">
                </div>
                <label for="" class="col-sm-12 control-label">RT
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-4">
                  <input type="text" name="RT_KTP" value="<?php echo set_value('RT_KTP', $EDIT->RT_KTP) ?>" class="form-control" style="text-align:center;">
                </div>
                <label for="" class="col-sm-12 control-label">RW
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-4">
                  <input type="text" name="RW_KTP" value="<?php echo set_value('RW_KTP', $EDIT->RW_KTP) ?>" class="form-control" style="text-align:center;">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label"></label>
                <label for="" class="col-sm-12 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 35px;">

                </label>
              </div>

              <div class="form-group">
                <label for="" class="col-sm-12 control-label"></label>
                <label for="" class="col-sm-12 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                  Isi sesuai dengan tempat tinggal sekarang
                </label>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Alamat
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="ALAMAT" value="<?php echo set_value('ALAMAT', $EDIT->ALAMAT) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Kelurahan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="KELURAHAN" value="<?php echo set_value('KELURAHAN', $EDIT->KELURAHAN) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Kecamatan
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="KECAMATAN" value="<?php echo set_value('KECAMATAN', $EDIT->KECAMATAN) ?>" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Provinsi
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <select name="PROVINSI" id="PROVINSI" class="form-control" style="width: 100%;">
                    <?php
                    // $K = db_first(" SELECT * FROM provinsi WHERE PROVINSI='".db_escape(set_value('PROVINSI',$EDIT->PROVINSI))."' ");
                    // if(isset($K->PROVINSI)){
                    //   echo '<option value="'.$K->PROVINSI.'" data-kode="'.$K->PROVINSI_ID.'" selected="selected">'.$K->PROVINSI.'</option>';
                    // }
                    $prov = db_first(" SELECT * FROM provinsi  WHERE PROVINSI='" . db_escape(set_value('PROVINSI', $EDIT->PROVINSI)) . "'");
                    foreach ($prov as $key => $K) {
                      $selected = "";
                      if ($K->PROVINSI_ID == $EDIT->PROVINSI_KTP) $selected = "selected";
                      echo '<option value="' . $K->PROVINSI . '" data-kode="' . $K->PROVINSI_ID . '" ' . $selected . '>' . $K->PROVINSI . '</option>';
                    }

                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-12 control-label">Kota
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <select name="KOTA" id="KOTA" class="form-control" style="width: 100%;">
                    <?php
                    // $K = db_first(" SELECT * FROM kota WHERE KOTA='".db_escape(set_value('KOTA',$EDIT->KOTA))."' ");
                    // if(isset($K->KOTA)){
                    //   echo '<option value="'.$K->KOTA.'" selected="selected">'.$K->KOTA.'</option>';
                    // }

                    $kota = db_first(" SELECT * FROM kota WHERE KOTA='" . db_escape(set_value('KOTA', $EDIT->KOTA)) . "'");
                    foreach ($kota as $key => $K) {
                      $selected = "";
                      if ($K->KOTA == $EDIT->KOTA_KTP) $selected = "selected";
                      echo '<option value="' . $K->KOTA . '" ' . $selected . '>' . $K->KOTA . '</option>';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Kode Pos
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <input type="text" name="KODE_POS" value="<?php echo set_value('KODE_POS', $EDIT->KODE_POS) ?>" class="form-control" style="text-align:center;">
                </div>
                <label for="" class="col-sm-12 control-label">RT
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-4">
                  <input type="text" name="RT" value="<?php echo set_value('RT', $EDIT->RT) ?>" class="form-control" style="text-align:center;">
                </div>
                <label for="" class="col-sm-12 control-label">RW
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-4">
                  <input type="text" name="RW" value="<?php echo set_value('RW', $EDIT->RW) ?>" class="form-control" style="text-align:center;">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label"></label>
                <label for="" class="col-sm-12 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 35px;">

                </label>
              </div>

              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Tempat Tinggal
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-12">
                  <?php echo dropdown('TEMPAT_TINGGAL', array('MILIK SENDIRI' => 'MILIK SENDIRI', 'MILIK ORANG TUA' => 'MILIK ORANG TUA', 'SEWA / KOS / KONTRAK' => 'SEWA / KOS / KONTRAK', 'LAIN-LAIN' => 'LAIN-LAIN'), set_value('TEMPAT_TINGGAL', $EDIT->TEMPAT_TINGGAL), ' class="form-control" id="MILIK_KENDARAAN"') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">File Foto
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-8">
                  <input type="file" name="FOTO" class="form-control">
                </div>
                <?php if (!empty($EDIT->FOTO)) { ?>
                  <div class="col-sm-12">
                    <span class="input-group-btn">
                      <a class="btn btn-primary btn-flat" href="<?php echo hris() . "uploads/foto/" . $EDIT->FOTO ?>" download title="Download">
                        <span class="fa fa-download"></span> Download file
                      </a>
                    </span>
                  </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">Scan Ijazah</label>
                <div class="col-sm-12">
                  <?php echo dropdown('SCAN_IJAZAH', array('Scan Asli' => 'SCAN ASLI', 'Scan Copy' => 'SCAN COPY'), set_value('SCAN_IJAZAH', $EDIT->SCAN_IJAZAH), ' class="form-control" ') ?>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">File Scan Ijazah
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-8">
                  <input type="file" name="FILE_SCAN_IJAZAH" class="form-control">
                </div>
                <?php if (!empty($EDIT->IJAZAH) and url_exists(hris() . 'uploads/ijazah/' . rawurlencode($EDIT->IJAZAH))) { ?>
                  <div class="col-sm-12">
                    <span class="input-group-btn">
                      <a class="btn btn-primary btn-flat" href="<?php echo hris() . "uploads/ijazah/" . $EDIT->IJAZAH ?>" download title="Download">
                        <span class="fa fa-download"></span> Download file
                      </a>
                    </span>
                  </div>
                <?php } ?>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-12 control-label">File Cv
                  <!--<span style="color:red; padding-left: 5px;">*</span>-->
                </label>
                <div class="col-sm-8">
                  <input type="file" name="CV" class="form-control">
                </div>
                <?php if (!empty($EDIT->CV) and url_exists(hris() . 'uploads/cv/' . rawurlencode($EDIT->CV))) { ?>
                  <div class="col-sm-12">
                    <span class="input-group-btn">
                      <a class="btn btn-primary btn-flat" href="<?php echo hris() . "uploads/cv/" . $EDIT->CV ?>" download title="Download">
                        <span class="fa fa-download"></span> Download file
                      </a>
                    </span>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>





        </div>

        <div class="card-footer">
          <div class="row col-md-12 col-lg-12 col-sm-12">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <input class="btn btn-success" type="submit" name="btn" value="Update Data" />
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>