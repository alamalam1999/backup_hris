
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header">
        <div class="row ml-4">
          <div class="row col-md-12">
            <h5 class="pt-2 mr-2 text-danger"><?= $title ?></h5>
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


        <div class="card-body">

          <?php $this->load->view("web/profile/profile_pendidikan_1") ?>
          <?php $this->load->view("web/profile/profile_pendidikan_2") ?>
          <?php $this->load->view("web/profile/profile_pendidikan_3") ?>


          <!-- <div class="row">
  					<div class="col-md-12">
  						<div class="table-responsive">

  						</div>
  					</div>
  				</div> -->

        </div>

    </div>
  </section>
</div>
