
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

          <div class="row">
  					<div class="col-md-12">
  						<div class="table-responsive">
                <table class="table table-bordered organisasi">
                  <tr style="background-color: #E9ECEF; color: #495057;">
                    <th colspan="4">Pengalaman Organisasi</th>
                    <th colspan="2" class="text-right">
                      <span class="input-group-btn" style="display: inline;">
                        <button type="button" class="btn btn-primary btn-flat" title="Tambah Data Organisasi"  data-toggle="modal" data-target="#modal_1">
                          <span class="btn-primary" style="border-bottom: none;"></span> Tambah Data
                        </button>
                      </span>
                    </th>
                  </tr>
                  <tr>
                    <th class="text-center" style="width: 20px;">No</th>
                    <th class="text-center">Nama Organisasi</th>
                    <th class="text-center">Jabatan</th>
                    <th class="text-center">Lokasi</th>
                    <th class="text-center">Periode (Tahun)</th>
                    <th class="text-center" style="width: 50px;"></th>
                  </tr>
                  <?php
                    foreach ($ORGANISASI->result() as $key => $row) { ?>
                      <tr>
                        <td><?php echo $key + 1 ?></td>
                        <td>
                          <?php echo $row->NAMA_ORGANISASI ?>
                        </td>
                        <td>
                          <?php echo $row->JABATAN_ORGANISASI ?>
                        </td>
                        <td>
                          <?php echo $row->LOKASI_ORGANISASI ?>
                        </td>
                        <td>
                          <?php echo $row->PERIODE_ORGANISASI ?>
                        </td>
                        <td class="text-center">
                          <span class="input-group-btn">
                            <?php
                                $link = base_url("profile_organisasi/delete/$row->ORGANISASI_KARYAWAN_ID");
                             ?>
                            <button onclick="notifikasi('Yakin Ingin Hapus Organisasi?' , '<?= $link; ?>')" type="button" class="btn btn-danger btn-flat" title="Hapus Data">
                              <span class="fa fa-trash btn-danger" ></span>
                            </button>
                          </span>
                        </td>
                      </tr>
                  <?php }?>
                </table>
  						</div>
  					</div>
  				</div>


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


<!-- Modal pend. formal -->
<div class="modal fade" id="modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form class="" action="<?= base_url('profile_organisasi/proses_add') ?>" method="post">
            <div class="modal-header">
              <h5 class="modal-title" >Bahasa asing yang dikuasai</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
                <table class="table ">

                    <tr>
                      <td width="100">Nama Organisasi</td><td>:</td>
                      <td>
                        <input type="text" name="NAMA_ORGANISASI"  class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td width="100">Jabatan Organisasi</td><td>:</td>
                      <td>
                        <input type="text" name="JABATAN_ORGANISASI" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td width="100">Lokasi</td><td>:</td>
                      <td>
                        <input type="text" name="LOKASI_ORGANISASI" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td width="100">Periode (Tahun)</td><td>:</td>
                      <td>
                        <input type="input" name="PERIODE_ORGANISASI" placeholder="0000" class="form-control" required>
                      </td>
                    </tr>

                </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
      </form>


    </div>
  </div>
</div>
