<!-- reCAPTCHA JavaScript API -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="content-login p-1">
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-lg-7">
            <div class="card shadow">
              <div class="card-body pb-5">
                <!-- 
                <div class="pb-4">
                  <img src="<?php /* echo base_url() */ ?>assets/images/logo-kemenkes.png" class="logo-kemeskes">
                  <img src="<?php /* echo base_url() */ ?>assets/images/logo.png" class="logo-bpfk">
                </div> 
                -->

                <div class="row pb-2">
                  <div class="col-lg-12">
                    <img src="<?php echo base_url() ?>assets/images/logo-bpfk.png" class="img-fluid">
                  </div>
                </div>
                <hr>

                <div class="pb-2">
                  <h4>Sign Up to <img src="<?php echo base_url() ?>assets/images/logosimpel.png" class="img-fluid logo-simpel"></h4>
                  <p class="font-weight-bold text-muted">Sistem Informasi Pelayanan Terpadu BPFK Jakarta</p>
                </div>

                <?php
                /*
                if ($this->session->flashdata('error')) { ?>
                  <div class="alert alert-danger" role="alert">
                    <?php echo $this->session->flashdata('error'); ?>
                  </div>
                <?php } else if ($this->session->flashdata('success')) { ?>
                  <div class="alert alert-success" role="alert">
                    <?php echo $this->session->flashdata('success'); ?>
                  </div>
                <?php } 
                */
                ?>

                <?php if ($this->uri->segment(3) == '1' || $this->uri->segment(3) == '2') { ?>
                  <form method="POST" action="" class="needs-validation" novalidate="" id="form-register-customer">
                    <input type="hidden" name="category" value="<?php echo $this->uri->segment(3); ?>">

                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label for="customer_type">Jenis Registrasi</label>
                          <select name="status" id="customer_type" class="form-control" tabindex="1" required>
                            <option value="" <?php echo set_select('status', 'Principle', TRUE); ?>>-- Pilih --</option>
                            <option value="RS SWASTA" <?php echo set_select('status', 'RS SWASTA'); ?>>Customer Swasta</option>
                            <option value="RS PEMERINTAH" <?php echo set_select('status', 'RS PEMERINTAH'); ?>>Customer Pemerintah</option>
                          </select>
                          <?php echo form_error('status', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">Please fill in your Registration Type</div>
                        </div>

                        <div class="form-group">
                          <label for="name">Nama Customer/Sarpelkes</label>
                          <input id="name" type="text" class="form-control" name="nama" tabindex="2" value="<?php echo set_value('nama'); ?>" required>
                          <?php echo form_error('nama', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">Please fill in your name</div>
                        </div>

                        <div class="form-group">
                          <label for="pic">Nama Penanggung Jawab</label>
                          <input id="pic" type="pic" class="form-control" name="pic" tabindex="3" value="<?php echo set_value('pic'); ?>" required>
                          <?php echo form_error('pic', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">Please fill in your PIC</div>
                        </div>

                        <div class="form-group">
                          <label for="city">Kota</label>
                          <select id="city" class="form-control select2" name="id_propinsi" tabindex="4" style="width: 100%;" required>
                            <?php if (set_value('id_propinsi')) { ?>
                              <option value="<?php echo set_value('id_propinsi'); ?>" selected>
                                <?php echo $this->auth_model->city_name(set_value('id_propinsi')); ?>
                              </option>
                            <?php } ?>
                          </select>
                          <?php echo form_error('id_propinsi', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">Please fill in your City</div>
                        </div>

                        <div class="form-group">
                          <label for="address">Alamat</label>
                          <textarea name="alamat" id="address" class="form-control" tabindex="5" style="height: 62px;" required><?php echo set_value('alamat'); ?></textarea>
                          <?php echo form_error('alamat', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">Please fill in your Address</div>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="form-group">
                          <label for="phone">No. Telepon</label>
                          <input id="phone" type="text" class="form-control" name="telpon" tabindex="6" value="<?php echo set_value('telpon'); ?>" required>
                          <?php echo form_error('telpon', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">Please fill in your Phone Number</div>
                        </div>

                        <div class="form-group">
                          <label for="email">Email</label>
                          <input id="email" type="email" class="form-control" name="email" tabindex="7" required value="<?php echo set_value('email'); ?>">
                          <?php echo form_error('email', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">Please fill in your valid email</div>
                        </div>

                        <div class="form-group">
                          <label for="password" class="control-label">Password</label>
                          <input id="password" type="password" class="form-control" name="password" tabindex="8" required>
                          <?php echo form_error('password', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">please fill in your password</div>
                        </div>

                        <div class="form-group">
                          <label for="password_verify" class="control-label">Ulangi Password</label>
                          <input id="password_verify" type="password" class="form-control" name="password_verify" tabindex="9" required>
                          <?php echo form_error('password_verify', '<small class="text-danger"> ', '</small>'); ?>
                          <div class="invalid-feedback">please fill in your password Verify</div>
                        </div>

                        <div class="form-group pt-4">
                          <div style="margin: 5px auto 0;"><?php echo $recaptcha; ?></div>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <?php if ($this->uri->segment(3) == '1') { ?>
                        <button type="submit" class="btn btn-info btn-lg btn-block font-weight-bold" style="font-size: 14px;">
                          Daftar Sebagai Customer
                        </button>
                      <?php } ?>

                      <?php if ($this->uri->segment(3) == '2') { ?>
                        <button type="submit" class="btn btn-info btn-lg btn-block py-3 font-weight-bold" style="font-size: 15px;">
                          Daftar Uji Profisiensi
                        </button>
                      <?php } ?>
                    </div>
                  </form>
                <?php } ?>

                <div class="mb-4 text-muted text-center">
                  Sudah punya akun? <a href="<?php echo site_url('auth'); ?>" class="font-weight-bold">Login disini</a>
                  <br>
                  Ingin mendaftar akun lainnya? <a href="<?php echo site_url('auth/register_type'); ?>" class="font-weight-bold">Klik disini</a>
                </div>

                <div class="row sm-gutters">
                  <div class="col-6">
                    <a href="#" class="btn btn-block bg-secondary text-white">
                      <i class="fa fa-book"></i> Panduan
                    </a>
                  </div>
                  <div class="col-6">
                    <a href="#" class="btn btn-block bg-secondary text-white">
                      <i class="fa fa-comments"></i> FAQ
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <img src="<?php echo base_url() ?>assets/images/bg-auth.png" alt="Sistem Informasi Pelayan Terpadu BPFK" class="img-fluid" style="width: 32rem; height: 40rem; object-fit: contain;">
          </div>
        </div>
      </div>
    </section>
  </div>
</div>