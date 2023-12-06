<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="title-header">Data Profile</h4>
      </div>

      <form action="<?php echo $update_url; ?>" method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="row col-md-12 col-lg-12 col-sm-12">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Nama Sarpelkes</strong></label>
              <input type="text" name="nama" class="form-control" value="<?php echo $data_customer->nama ?>" readonly />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Status Customer</strong></label>
              <input type="text" name="status" class="form-control" value="<?php echo $data_customer->status ?>" readonly />
            </div>
          </div>
          <div class="row col-md-12 col-lg-12 col-sm-12">
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
          <div class="row col-md-12 col-lg-12 col-sm-12">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Email</strong></label>
              <input type="text" name="email" class="form-control" value="<?php echo $data_customer->email ?>" required />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Alamat</strong></label>
              <input type="text" name="alamat" class="form-control" value="<?php echo $data_customer->alamat ?>" required />
            </div>
          </div>
          <div class="row col-md-12 col-lg-12 col-sm-12">
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Telepon</strong></label>
              <input type="text" name="telpon" class="form-control" value="<?php echo $data_customer->telpon ?>" required />
            </div>
            <div class="col-md-6 col-lg-6 col-sm-12 form-group mb-2">
              <label><strong>Fax</strong></label>
              <input type="text" name="fax" class="form-control" value="<?php echo $data_customer->fax ?>" required />
            </div>
          </div>
          <div class="row col-md-12 col-lg-12 col-sm-12">
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