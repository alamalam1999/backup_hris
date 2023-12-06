<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-bordered formal">
        <tr style="background-color: #E9ECEF; color: #495057;">
          <th colspan="5">Pendidikan formal</th>
          <th colspan="4" class="text-right">
            <span class="input-group-btn" style="display: inline;">
              <button type="button" class="btn btn-primary btn-flat" title="Tambah Data Pendidikan" data-toggle="modal" data-target="#modal_1">
                <span class="btn-primary" style="border-bottom: none;"></span> Tambah Data
              </button>
            </span>
          </th>
        </tr>
        <tr>
          <th rowspan="2" class="text-center" style="width: 20px;">No</th>
          <th rowspan="2" class="text-center" style="width: 180px;">Tingkat</th>
          <th rowspan="2" class="text-center">Jurusan</th>
          <th rowspan="2" class="text-center">Nama Sekolah / Institusi</th>
          <th rowspan="2" class="text-center">Lokasi</th>
          <th colspan="2" class="text-center">Periode(Tahun)</th>
          <th rowspan="2" class="text-center" style="width: 98px;">GPA</th>
          <th rowspan="2" class="text-center" style="width: 50px;"></th>
        </tr>
        <tr>
          <th class="text-center" style="width: 100px;">Mulai</th>
          <th class="text-center" style="width: 100px;">Lulus</th>
        </tr>
        <?php

          foreach ($PEND_FORMAL->result() as $key => $row) { ?>
            <tr>
              <td><?php echo $key + 1 ?></td>
              <td><?= $row->TINGKAT ?></td>
              <td><?php echo $row->JURUSAN ?></td>
              <td><?php echo $row->INSTITUSI ?></td>
              <td><?php echo $row->LOKASI ?></td>
              <td><?php echo $row->TAHUN_MULAI ?></td>
              <td><?php echo $row->TAHUN_SELESAI ?></td>
              <td><?php echo $row->GPA ?></td>
              <td>
                <span class="input-group-btn">
                  <?php
                      $link = base_url("profile_pendidikan/delete/1/$row->PENDIDIKAN_KARYAWAN_ID");
                   ?>
                  <button onclick="notifikasi('Yakin Ingin Hapus Pendidikan?' , '<?= $link; ?>')" type="button" class="btn btn-danger " title="Hapus Data">
                    <span class="fa fa-trash btn-danger" ></span>
                  </button>
                </span>
              </td>
            </tr>
        <?php } ?>
      </table>


    </div>
  </div>
</div>



<!-- Modal pend. formal -->
<div class="modal fade" id="modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form class="" action="<?= base_url('profile_pendidikan/proses_add_pend_formal') ?>" method="post">
            <div class="modal-header">
              <h5 class="modal-title" >Pendidikan Formal</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
                <table class="table ">
                    <tr>
                      <td width="130">Tingkat</td><td width="5">:</td>
                      <td>
                          <select class="form-control" name="TINGKAT">
                            <option value="SD" >SD</option>
                            <option value="SMP" >SMP</option>
                            <option value="SMA" >SMA</option>
                            <option value="SMK" >SMK</option>
                            <option value="D3" >DIPLOMA (D3)</option>
                            <option value="S1" >SARJANA (S1)</option>
                            <option value="S2" >PASCA SARJANA (S2)</option>
                          </select>

                      </td>
                    </tr>

                    <tr>
                      <td >Jurusan</td><td>:</td>
                      <td>
                        <input type="text" name="JURUSAN" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Institusi</td><td>:</td>
                      <td><input type="text" name="INSTITUSI"  class="form-control" required></td>
                    </tr>

                    <tr>
                      <td >Lokasi</td><td>:</td>
                      <td>
                        <input type="text" name="LOKASI" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Tahun Mulai</td><td>:</td>
                      <td>
                        <input type="text" name="TAHUN_MULAI" class="form-control" placeholder="0000" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Tahun Lulus</td><td>:</td>
                      <td>
                        <input type="text" name="TAHUN_SELESAI" class="form-control"  placeholder="0000" required>
                      </td>
                    </tr>

                    <tr>
                      <td >GPA</td><td>:</td>
                      <td>
                        <input type="text" name="GPA"  class="form-control" required>
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
