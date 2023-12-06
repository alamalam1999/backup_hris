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

      <form action="<?php echo $update_url; ?>" method="post" enctype="multipart/form-data">
        <div class="card-body">

         

          <div class="row">
  					<div class="col-md-12">
  						<div class="table-responsive">

                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th width=2>No</th>
                      <th >Dokumen</th>
                      <th >Jenis</th>
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
                if($row->FILE_IJAZAH != '')$jumlah_row++;
                if($row->FILE_SERTIFIKAT != '')$jumlah_row++;
                if($row->FILE_SIO != '')$jumlah_row++;
                if($row->FILE_KTA != '')$jumlah_row++;
                
                $status_approved = 1;

                if($row->FILE_IJAZAH != '' && $row->APPROVED_IJAZAH != 'APPROVED')$status_approved = 0;
                if($row->FILE_SERTIFIKAT != '' && $row->APPROVED_SERTIFIKAT != 'APPROVED')$status_approved = 0;
                if($row->FILE_SIO != '' && $row->APPROVED_SIO != 'APPROVED')$status_approved = 0;
                if($row->FILE_KTA != '' && $row->APPROVED_KTA != 'APPROVED')$status_approved = 0;

            ?>
                    <tr class="bg-secondary ">
                        <td><?= $no++; ?></td>
                        <td colspan="3"><?= strtoupper($row->DOK_KARYAWAN); ?></td>
                        <td>
                          <?php if($status_approved == 0){?>
                          <a href="" onclick="notifikasi('Yakin ingin hapus ?','<?=site_url("profile_sertifikat/delete/$row->DOK_KARYAWAN_ID") ?>')" class="btn btn-danger text-white"> <span class="fa fa-trash" ></span> Hapus</a>
                        <?php } ?>
                        </td>
                    </tr>
                    <?php if($row->FILE_IJAZAH != ''){ ?>
                    <tr>
                        <td>-</td>
                        <td><?php if (!empty($row->FILE_IJAZAH) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_IJAZAH))) { ?>
                              <a class="btn btn-success btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_IJAZAH ?>" title="Download" download>
                              <i class="fa fa-download"></i> Download Dokumen
                            </a>
                          <?php } ?>
                          <?php if(!empty($row->KETERANGAN_IJAZAH)) {?>
                            <span class="badge badge-danger" ><?=  $row->KETERANGAN_IJAZAH; ?></span> 
                            
                           <?php } ?>
                        </td>
                        <td> IJAZAH</td>

                        
                        <td><?= $row->MASA_IJAZAH; ?></td>

                        
                        <td><?= $row->APPROVED_IJAZAH; ?></td>

                    </tr>
                    <?php } ?>
                    <?php if($row->FILE_SERTIFIKAT != ''){ ?>

                    <tr>
                        <td>-</td>
                        <td>
                          <?php if (!empty($row->FILE_SERTIFIKAT) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_SERTIFIKAT))) { ?>
                              <a class="btn btn-success btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_SERTIFIKAT ?>" title="Download" download>
                              <i class="fa fa-download"></i> Download Dokumen
                            </a>
                          <?php } ?>
                          <?php if(!empty($row->KETERANGAN_SERTIFIKAT)) {?>
                            <span class="badge badge-danger" ><?=  $row->KETERANGAN_SERTIFIKAT; ?></span> 
                            
                           <?php } ?>
                        </td>
                        <td> SERTIFIKAT</td>
                        
                        <td><?= $row->MASA_SERTIFIKAT; ?></td>
                        <td><?= $row->APPROVED_SERTIFIKAT; ?></td>

                    </tr>
                    <?php } ?>
                    <?php if($row->FILE_SIO != ''){ ?>
                    <tr>
                        <td>-</td>
                        <td>
                          <?php if (!empty($row->FILE_SIO) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_SIO))) { ?>
                              <a class="btn btn-success btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_SIO ?>" title="Download" download>
                              <i class="fa fa-download"></i> Download Dokumen
                            </a>
                          <?php } ?>
                          <?php if(!empty($row->KETERANGAN_SIO)) {?>
                            <span class="badge badge-danger" ><?=  $row->KETERANGAN_SIO; ?></span> 
                            
                           <?php } ?>
                        </td>
                        <td> SK Kontrak</td>
                        
                        <td><?= $row->MASA_SIO; ?></td>
                        <td><?= $row->APPROVED_SIO; ?></td>

                    </tr>
                    <?php } ?>
                    <?php if($row->FILE_KTA != ''){ ?>
                    <tr>
                        <td>-</td>
                        <td>
                          <?php if (!empty($row->FILE_KTA) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_KTA))) { ?>
                              <a class="btn btn-success btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_KTA ?>" title="Download" download>
                              <i class="fa fa-download"></i> Download Dokumen
                            </a>
                          <?php } ?>
                          <?php if(!empty($row->KETERANGAN_KTA)) {?>
                            <span class="badge badge-danger" ><?=  $row->KETERANGAN_KTA; ?></span> 
                            
                           <?php } ?>
                        </td>
                        <td> SK Tetap</td>
                        
                        <td><?= $row->MASA_KTA; ?></td>
                        <td><?= $row->APPROVED_KTA; ?></td>

                    </tr>
                    <?php } ?>

          <?php } ?>
                  </tbody>
                  </table>

              </div>
            </div>
          </div>

          <div class="row">
  					<div class="col-md-12">
  						<div class="table-responsive">
  							<table class="table table-bordered dokumen">
  								<tr style="background-color: #E9ECEF; color: #495057;">
  									<th colspan="4">Tambah Dokumen </th>
  									<th class="text-right">
  										<span class="input-group-btn" style="display: inline;">
  											<button type="button" class="btn btn-primary btn-flat" title="Tambah Data Dokumen" style="width: 150px;" id="add-dokumen">
  												<span class="glyphicon glyphicon-plus btn-primary" style="border-bottom: none;"></span> Tambah Data
  											</button>
  										</span>
  									</th>
  								</tr>
  								<tr>
  									<th class="text-center">Dokumen</th>
  									<th class="text-center">(Ada/Tidak) / Jenis</th>
  									<th class="text-center">File</th>
  									<th class="text-center" style="width: 120px;">Tahun Berlaku (YYYY)</th>
  									<th class="text-center" style="width: 50px;"></th>
  								</tr>
  								<?php
                  if ($data_sertifikat->num_rows() == "XXX") {
                  //if ($data_sertifikat->num_rows() > 0) {
  									foreach ($data_sertifikat->result() as $row) { ?>
                      <tbody style="border-top: 0;">
  										<tr>
  											<td rowspan="4">
  												<input type="text" name="DOK_KARYAWAN[]" value="<?php echo $row->DOK_KARYAWAN ?>" class="form-control">
  											</td>
  											<td>
  												<input type="checkbox" name="IJAZAH[]" value="ADA" <?php if ($row->IJAZAH == 'ADA') echo 'checked'; ?> disabled> IJAZAH
  											</td>
  											<td>
  												<input type="file" name="FILE_IJAZAH[]" class="form-control"> <br>
  												<span>
  													<?php if (!empty($row->FILE_IJAZAH) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_IJAZAH))) { ?>

  														<input type="hidden" name="CURR_IJAZAH[]" value="<?php if($row->FILE_IJAZAH) echo $row->FILE_IJAZAH; else echo "xxx"; ?>">
  														<a class="btn btn-primary btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_IJAZAH ?>" title="Download" download>
  															<i class="glyphicon glyphicon-download"></i> Download Dokumen
  														</a>
  														<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
  															<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
  														</a>
  													<?php } ?>
  												</span>
  											</td>
  											<td>
  												<input type="text" name="MASA_IJAZAH[]" value="<?php echo $row->MASA_IJAZAH ?>" class="form-control">
  											</td>
  											<td rowspan="4" class="text-center"> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-dokumen" title="Hapus Data : agar file terhapus click tombol save paling bawah."> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Data</button>
  												</span>
  											</td>
  										</tr>
  										<tr>
  											<td>
  												<input type="checkbox" name="SERTIFIKAT[]" value="ADA" <?php if ($row->SERTIFIKAT == 'ADA') echo 'checked'; ?> disabled> SERTIFIKAT
  											</td>
  											<td>
  												<input type="file" name="FILE_SERTIFIKAT[]" class="form-control"> <br>
  												<span>
  													<?php if (!empty($row->FILE_SERTIFIKAT) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_SERTIFIKAT))) { ?>
  														<input type="hidden" name="CURR_SERTIFIKAT[]" value="<?php if($row->FILE_SERTIFIKAT) echo $row->FILE_SERTIFIKAT; else echo "xxx"; ?>">

  														<a class="btn btn-primary btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_SERTIFIKAT ?>" download title="Download">
  															<span class="glyphicon glyphicon-download"></span> Download Dokumen
  														</a>
  														<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
  															<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
  														</a>
  													<?php } ?>
  												</span>
  											</td>
  											<td>
  												<input type="text" name="MASA_SERTIFIKAT[]" value="<?php echo $row->MASA_SERTIFIKAT ?>" class="form-control">
  											</td>
  										</tr>
  										<tr>
  											<td>
  												<input type="checkbox" name="SIO[]" value="ADA" <?php if ($row->SIO == 'ADA') echo 'checked'; ?> disabled> SK Tetap
  											</td>
  											<td>
  												<input type="file" name="FILE_SIO[]"  class="form-control"> <br>
  												<span>
  													<?php if (!empty($row->FILE_SIO) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_SIO))) { ?>

  														<input type="hidden" name="CURR_SIO[]" value="<?php if($row->FILE_SIO) echo $row->FILE_SIO; else echo "xxx"; ?>">
  														<a class="btn btn-primary btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_SIO ?>" download title="Download">
  															<span class="glyphicon glyphicon-download"></span> Download Dokumen
  														</a>
  														<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
  															<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
  														</a>
  													<?php } ?>
  												</span>
  											</td>
  											<td>
  												<input type="text" name="MASA_SIO[]" value="<?php echo $row->MASA_SIO ?>" class="form-control">
  											</td>
  										</tr>
  										<tr>
  											<td>
  												<input type="checkbox" name="KTA[]" value="ADA" <?php if ($row->KTA == 'ADA') echo 'checked'; ?> disabled> SK Kontrak
  											</td>
  											<td>
  												<input type="file" name="FILE_KTA[]"  class="form-control"> <br>
  												<span>
  													<?php if (!empty($row->FILE_KTA) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_KTA))) { ?>

  														<input type="hidden" name="CURR_KTA[]" value="<?php if($row->FILE_KTA) echo $row->FILE_KTA; else echo "xxx"; ?>">
  														<a class="btn btn-primary btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_KTA ?>" download title="Download">
  															<span class="glyphicon glyphicon-download"></span> Download Dokumen
  														</a>
  														<a class="btn btn-warning btn-flat btn-sm btn-del-doc" href="Javascript:void(0)" title="Delete : agar file terhapus click tombol save paling bawah.">
  															<i class="glyphicon glyphicon-remove-circle"></i> Delete Dokumen
  														</a>
  													<?php } ?>
  												</span>
  											</td>
  											<td>
  												<input type="text" name="MASA_KTA[]" value="<?php echo $row->MASA_KTA ?>" class="form-control">
  											</td>
  										</tr>
                    </tbody>
  								<?php }
  								} ?>
  							</table>
  						</div>
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
