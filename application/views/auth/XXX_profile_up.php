<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="title-header">Data Profile</h4>
      </div>

      <form action="<?php echo $update_url; ?>" method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="row">
            <div class="form-group mb-1 col-md-6 col-lg-6 col-sm-12">
              <input type="hidden" name="id_customer" class="form-control" value="<?php echo $data_customer->id_customer ?>" readonly />
              <label><strong>Nama Kepala IPFK</strong></label>
              <input type="text" name="nama_kepala" class="form-control" value="<?php echo $data_customer->nama_kepala ?>" />
            </div>
            <div class="form-group mb-1 col-md-6 col-lg-6 col-sm-12">
              <label><strong>Nomor Akreditasi</strong></label>
              <input type="text" name="no_akreditasi" class="form-control" value="<?php echo $data_customer->no_akreditasi ?>" />
            </div>
          </div>
          <div class="row">
            <div class="form-group mb-1 col-md-6 col-lg-6 col-sm-12">
              <label><strong>No. SK izin Operasional</strong></label>
              <input type="text" name="no_izin_operasional" class="form-control" value="<?php echo $data_customer->no_izin_operasional ?>" />
            </div>
            <div class="form-group mb-1 col-md-6 col-lg-6 col-sm-12">
              <label><strong>FIle SK Izin Operasional</strong></label>
              <div class="input-group">
                <input type="file" name="file_izin_operasional" class="form-control">
                <?php if ($data_customer->file_izin_operasional != '' && file_exists(FCPATH . 'upload/customer/' . $data_customer->file_izin_operasional)) { ?>
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <a href="<?php echo base_url() ?>upload/customer/<?php echo $data_customer->file_izin_operasional; ?>" title="Download" download>
                        <i class="fas fa-cloud-download-alt"></i>
                      </a>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="form-group mb-1 col-md-6 col-lg-6 col-sm-12">
              <label><strong>Email PIC</strong></label>
              <input type="text" name="email_pic" class="form-control" value="<?php echo $data_customer->email_pic ?>" />
            </div>
            <div class="form-group mb-1 col-md-6 col-lg-6 col-sm-12">
              <label><strong>Nomor Handphone PIC</strong></label>
              <input type="text" name="no_hp_pic" class="form-control" value="<?php echo $data_customer->no_hp_pic ?>" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>PIC</strong></label>
              <input type="text" name="pic" class="form-control" value="<?php echo $data_customer->pic ?>" required />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Kota</strong></label>
              <select id="city" class="form-control select2" name="id_propinsi" required>
                <?php if ($data_customer->id_propinsi) { ?>
                  <option value="<?php echo $data_customer->id_propinsi; ?>" selected>
                    <?php echo $this->auth_model->city_name($data_customer->id_propinsi); ?>
                  </option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Nama Sarpelkes</strong></label>
              <input type="text" name="nama" class="form-control" value="<?php echo $data_customer->nama ?>" readonly />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Status Customer</strong></label>
              <input type="text" name="status" class="form-control" value="<?php echo $data_customer->status ?>" readonly />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Email</strong></label>
              <input type="text" name="email" class="form-control" value="<?php echo $data_customer->email ?>" required />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Alamat</strong></label>
              <input type="text" name="alamat" class="form-control" value="<?php echo $data_customer->alamat ?>" required />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Telepon</strong></label>
              <input type="text" name="telpon" class="form-control" value="<?php echo $data_customer->telpon ?>" required />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Fax</strong></label>
              <input type="text" name="fax" class="form-control" value="<?php echo $data_customer->fax ?>" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Profile Picture</strong></label>
              <input type="file" name="file_foto" class="form-control" />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <figure class="avatar mr-2 avatar-xl">
                <?php if ($this->session->userdata('user_logged')->user_avatar != '' && file_exists(FCPATH . 'upload/customer/' . $this->session->userdata('user_logged')->user_avatar)) { ?>
                  <img src="<?php echo base_url() ?>upload/customer/<?php echo $this->session->userdata('user_logged')->user_avatar; ?>" style="object-fit: cover;">
                <?php } else { ?>
                  <img src="<?php echo base_url() ?>assets/img/avatar/avatar-1.png">
                <?php } ?>
              </figure>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <input class="btn btn-success" type="submit" name="btn" value="Update Data Profile" />
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
</div>