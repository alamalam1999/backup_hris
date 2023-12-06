<!-- reCAPTCHA JavaScript API -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="content-login p-1">
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-lg-6">
            <div class="row justify-content-center">
              <div class="col-lg-9">
                <div class="card shadow">
                  <div class="card-body">
                    <!-- <div class="pb-4">
                        <img src="<?php echo base_url() ?>assets/images/logo-kemenkes.png" class="logo-kemeskes">
                        <img src="<?php echo base_url() ?>assets/images/logo.png" class="logo-bpfk">
                      </div> -->

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

                    <form method="POST" action="" class="needs-validation" novalidate="" id="form-register-customer">
                      <div class="form-group">
                        <label for="sarpelkes">Sarpelkes</label>
                        <select id="sarpelkes" class="form-control select2" name="id_customer" tabindex="1" style="width: 100%;" required></select>
                        <?php echo form_error('id_customer', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your Sarpelkes</div>
                      </div>

                      <div class="form-group">
                        <label for="name">Nama</label>
                        <input id="name" type="text" class="form-control" name="nama" tabindex="2" value="<?php echo set_value('nama'); ?>" required>
                        <?php echo form_error('nama', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your name</div>
                      </div>

                      <div class="form-group">
                        <label for="phone">No. Telepon</label>
                        <input id="phone" type="text" class="form-control" name="telpon" tabindex="4" value="<?php echo set_value('telpon'); ?>" required>
                        <?php echo form_error('telpon', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your Phone Number</div>
                      </div>

                      <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" name="email" tabindex="5" required value="<?php echo set_value('email'); ?>">
                        <?php echo form_error('email', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your valid email</div>
                      </div>

                      <div class="form-group">
                        <label for="password" class="control-label">Password</label>
                        <input id="password" type="password" class="form-control" name="password" tabindex="6" required>
                        <?php echo form_error('password', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">please fill in your password</div>
                      </div>

                      <div class="form-group">
                        <label for="password_verify" class="control-label">Ulangi Password</label>
                        <input id="password_verify" type="password" class="form-control" name="password_verify" tabindex="7" required>
                        <?php echo form_error('password_verify', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">please fill in your password Verify</div>
                      </div>

                      <?php echo $recaptcha; ?>

                      <div class="form-group pt-3">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="font-size: 14px;">
                          Daftar Sebagai Peserta Pelatihan
                        </button>
                      </div>
                    </form>

                    <div class="my-4 text-muted text-center">
                      Sudah punya akun? <a href="<?php echo site_url('auth'); ?>" class="font-weight-bold">Login disini</a>
                      <br>
                      Ingin mendaftar bukan sebagai Customer? <a href="<?php echo site_url('auth/register_type'); ?>" class="font-weight-bold">Klik disini</a>
                    </div>

                    <div class="row sm-gutters">
                      <div class="col-6">
                        <a href="#" class="btn btn-block btn-info text-white">
                          <i class="fa fa-book"></i> Panduan
                        </a>
                      </div>
                      <div class="col-6">
                        <a href="#" class="btn btn-block btn-info text-white">
                          <i class="fa fa-comments"></i> FAQ
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <img src="<?php echo base_url() ?>assets/images/bg-auth.png" alt="Sistem Informasi Pelayan Terpadu BPFK" class="img-fluid">
          </div>
        </div>
      </div>
    </section>
  </div>
</div>