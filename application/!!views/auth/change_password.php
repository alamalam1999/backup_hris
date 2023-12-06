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
                      <h4>Sign In to <img src="<?php echo base_url() ?>assets/images/logosimpel.png" class="img-fluid logo-simpel"></h4>
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

                    <form method="POST" action="" class="needs-validation" novalidate="">
                      <div class="form-group">
                        <label for="password1">Password Baru</label>
                        <input id="password1" type="password" class="form-control" name="password1" tabindex="2" required>
                        <?php echo form_error('password1', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your password</div>
                      </div>

                      <div class="form-group">
                        <label for="password2">Ulangi Password Baru</label>
                        <input id="password2" type="password" class="form-control" name="password2" tabindex="2" required>
                        <?php echo form_error('password2', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your verify password</div>
                      </div>

                      <?php echo $recaptcha; ?>

                      <div class="form-group pt-3">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                          FORGOT PASSWORD
                        </button>
                      </div>
                    </form>

                    <div class="mb-3 text-muted text-center">
                      Tidak punya akun? <a href="<?php echo site_url('auth/register_type'); ?>" class="font-weight-bold">Daftar disini</a>
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