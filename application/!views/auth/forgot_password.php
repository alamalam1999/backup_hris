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
                        <label for="account_type">Tipe Akun</label>
                        <select name="account_type" id="account_type" class="form-control" tabindex="1" required autofocus>
                          <option value="">-- Pilih --</option>
                          <option value="customer">Customer</option>
                          <option value="ujiprofisiensi">Peserta Uji Profisiensi</option>
                          <option value="pelatihan">Peserta Pelatihan</option>
                        </select>
                        <?php echo form_error('account_type', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your account type</div>
                      </div>

                      <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" name="email" tabindex="2" required>
                        <?php echo form_error('email', '<small class="text-danger"> ', '</small>'); ?>
                        <div class="invalid-feedback">Please fill in your valid email</div>
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