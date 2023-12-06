<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-bordered nonf">
        <tr style="background-color: #E9ECEF; color: #495057;">
          <th colspan="5">Pendidikan Non Formal</th>
          <th colspan="2" class="text-right">
            <span class="input-group-btn" style="display: inline;">
              <button type="button" class="btn btn-primary btn-flat" title="Tambah Data Pendidikan"  data-toggle="modal" data-target="#modal_2">
                <span class="btn-primary" style="border-bottom: none;"></span> Tambah Data
              </button>
            </span>
          </th>
        </tr>
        <tr>
          <th class="text-center" style="width: 20px;">No</th>
          <th class="text-center">Nama Kursus / Training</th>
          <th class="text-center" style="width: 180px;">File</th>
          <th class="text-center">Tempat</th>
          <th class="text-center">Periode Mulai</th>
          <th class="text-center">Keterangan</th>
          <th class="text-center" style="width: 50px;"></th>
        </tr>
        <?php
          foreach ($PEND_NONFORMAL->result() as $key => $row) { ?>
            <tr>
              <td><?php echo $key + 1 ?></td>
              <td><?php echo $row->NAMA_KURSUS ?></td>
              <td>
                 <?php if (!empty($row->FILE_KURSUS) and url_exists(hris('url') . 'uploads/karyawan/' . rawurlencode($row->FILE_KURSUS))) { ?>
                              <a class="btn btn-success btn-flat btn-sm" href="<?php echo hris() . "uploads/karyawan/" . $row->FILE_KURSUS ?>" title="Download" download>
                             Download 
                            </a>
                          <?php } ?>
                          <?php if(!empty($row->KETERANGAN_APPROVED)) {?>
                            <span class="badge badge-danger" ><?=  $row->KETERANGAN_APPROVED; ?></span> 
                            
                           <?php } ?>
              </td>
              <td><?php echo $row->TEMPAT ?></td>
              <td class="text-center">
                <?php echo date_indo($row->PERIODE_MULAI) ?>
              </td>
              <td><?php echo $row->KETERANGAN ?></td>
              <td class="text-center">
                <span class="input-group-btn">
                  <?php
                      $link = base_url("profile_pendidikan/delete/2/$row->KURSUS_KARYAWAN_ID");
                   ?>
                   <?= $row->APPROVED ?>
                  <?php if($row->APPROVED == 'PENDING') {?>

                  <button type="button"  onclick="notifikasi('Yakin Ingin Hapus Kursus?' , '<?= $link; ?>')"  class="btn btn-danger btn-flat" title="Hapus Data">
                    <span class="fa fa-trash btn-danger"></span>
                  </button>
                <?php } ?>
                </span>
              </td>
            </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

<!-- Modal pend. formal -->
<div class="modal fade" id="modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form class="" action="<?= base_url('profile_pendidikan/proses_add_pend_non_formal') ?>" method="post" enctype="multipart/form-data">
            <div class="modal-header">
              <h5 class="modal-title" >Pendidikan Non Formal</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
                <table class="table ">

                    <tr>
                      <td width="150">Nama Kursus</td><td>:</td>
                      <td>
                        <input type="text" name="NAMA_KURSUS" class="form-control" required>
                      </td>
                    </tr>
                     <tr>
                      <td >File</td><td>:</td>
                      <td>
                        <input type="file" name="FILE_KURSUS"  class="form-control">

                      </td>
                    </tr>
                    <tr>
                      <td >Lokasi</td><td>:</td>
                      <td>
                        <input type="text" name="TEMPAT" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Periode Mulai</td><td>:</td>
                      <td>
                        <input type="date" name="PERIODE_MULAI" class="form-control" required>
                      </td>
                    </tr>

                    <!-- <tr>
                      <td >Tanggal Selesai</td><td>:</td>
                      <td>
                        <input type="date" name="PERIODE_SELESAI" class="form-control" required>
                      </td>
                    </tr> -->

                    <tr>
                      <td >Keterangan</td><td>:</td>
                      <td>
                        <textarea name="KETERANGAN" class="form-control" rows="8" cols="80"></textarea>
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
