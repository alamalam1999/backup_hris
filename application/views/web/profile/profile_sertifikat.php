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
            <h5 class="pt-2 mr-2 text-danger"><?= $title ?></h5>
          </div>
          <div class="row col-md-12">
              <a href="<?php echo site_url('profile'); ?>" class="btn btn-secondary mr-1 mb-1">Profile</a>
              <a href="<?php echo site_url('profile_sertifikat'); ?>" class="btn btn-secondary mr-1 mb-1">Dokumen</a>
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

                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th width=2>No</th>
                      <th >Dokumen</th>
                      <th >Keterangan</th>
                      <th >Tahun</th>
                      <th >Status</th>

                    </tr>

                  </thead>
                  <tbody>
          <?php
              $no = 1;
              foreach ($data_sertifikat->result() as $row)
              {
                $jumlah_row = 0;
               
                $status_approved = 1;

                if($row->FILE_DOK != '' && $row->APPROVED != 'APPROVED')$status_approved = 0;
               

            ?>
                    <tr class="bg-secondary ">
                        <td><?= $no++; ?></td>
                        <td colspan="3"><?= strtoupper($row->DOK_KARYAWAN); ?></td>
                        <td>
                          <?php if($status_approved == 0){?>
                           <!--  
                          <a href="" onclick="notifikasi('Yakin ingin hapus ?','<?php /*echo site_url("profile_sertifikat/delete/$row->DOK_KARYAWAN_ID")*/ ?>')" class="btn btn-danger text-white"> <span class="fa fa-trash" ></span> Hapus</a> -->

                          <button onclick="notifikasi('Yakin Ingin Hapus?' , '<?= site_url("profile_sertifikat/delete/$row->DOK_KARYAWAN_ID") ?>')" type="button" class="btn btn-danger " title="Hapus Data">
                            <span class="fa fa-trash btn-danger"></span> Hapus
                          </button>
                          <?php } ?>
                        </td>
                    </tr>
                    <?php if($row->FILE_DOK != ''){ ?>
                    <tr>
                        <td>-</td>
                        <td><?php if (!empty($row->FILE_DOK) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_DOK))) { ?>
                              <a class="btn btn-success btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_DOK ?>" title="Download" download>
                              <i class="fa fa-download"></i> Download Dokumen
                            </a>
                            <?php } ?>
                           
                        </td>
                        <td> <?php if(!empty($row->KETERANGAN_APPROVED)) {?>
                            <span class="badge badge-danger" ><?=  $row->KETERANGAN_APPROVED; ?></span> 
                            
                            <?php } ?> </td>

                        
                        <td><?= $row->MASA_BERLAKU; ?></td>

                        
                        <td><?= $row->APPROVED; ?></td>

                    </tr>
                    <?php } ?>
              <?php } ?>
                  </tbody>
                  </table>

              </div>
            </div>
          </div>

        <form action="<?php echo $update_url; ?>" method="post" enctype="multipart/form-data">

          <div class="row">
  					<div class="col-md-12">
  						<div class="table-responsive">
  							<table class="table table-bordered dokumen">
  								<tr style="background-color: #E9ECEF; color: #495057;">
  									<th colspan="3">Tambah Dokumen </th>
  									<th class="text-right">
  										
  									</th>
  								</tr>
  								<tr>
  									<th class="text-center">Dokumen</th>
  									<th class="text-center">File</th>
  									<th class="text-center" style="width: 120px;">Tahun Berlaku (YYYY)</th>
  									
  								</tr>
  							
                      <tbody style="border-top: 0;">
  										<tr>
                        <td>
                          <select class="form-control" name="DOK_KARYAWAN" required>
                            <option value="SERTIFIKAT">SERTIFIKAT</option>
                            <option value="IJASAH">IJASAH</option>
                            <option value="SK TETAP">SK TETAP</option>
                            <option value="SK KONTRAK">SK KONTRAK</option>
                            <option value="SIM">SIM</option>
                            <option value="BUKU NIKAH">BUKU NIKAH</option>
                            <option value="LAINNYA">LAINNYA</option>
                          </select>
                          
                        </td>
  											<td rowspan="4">
  												<input type="file" name="FILE_DOK" class="form-control" required>
  											</td>
  											<td>
  												<input type="text" name="MASA_BERLAKU" class="form-control" required>
  											</td>
  											
  										</tr>
  										
                    </tbody>
  								
  							</table>
  						</div>
  					</div>
  				</div>
          <div class="row col-md-12 col-lg-12 col-sm-12 ml-2">
              <input class="btn btn-success" type="submit" name="btn" value="Tambah Data" />

          </div>
        </form>

        </div>

        
    </div>
  </section>
</div>
