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
                      <h4><?php echo isset($title) ? $title : ''; ?> <img src="<?php echo base_url() ?>assets/images/logosimpel.png" class="img-fluid logo-simpel"></h4>
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

                    <a href="<?php echo site_url('auth/register_customer/1'); ?>" class="btn btn-lg btn-block bg-primary text-white py-4 text-left mb-4 font-weight-bold shadow" style="border-radius: 14px; font-size: 18px;">
                      <i class="fas fa-user-tie pr-4" style="font-size: 18px;"></i> CUSTOMER BPFK
                    </a>

                    <a href="<?php echo site_url('auth/register_customer/2'); ?>" class="btn btn-lg btn-block bg-primary text-white py-4 text-left mb-4 font-weight-bold shadow" style="border-radius: 14px; font-size: 18px;">
                      <i class="fas fa-flask pr-4" style="font-size: 18px;"></i> UJI PROFISIENSI
                    </a>

                    <a href="<?php echo site_url('auth/register_pelatihan'); ?>" class="btn btn-lg btn-block bg-primary text-white py-4 text-left mb-4 font-weight-bold shadow" style="border-radius: 14px; font-size: 18px;">
                      <i class="fas fa-book-open pr-4" style="font-size: 18px;"></i>PESERTA PELATIHAN
                    </a>

                    <div class="my-4 text-muted text-center">
                      Sudah punya akun? <a href="<?php echo site_url('auth'); ?>" class="font-weight-bold">Login disini</a>
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