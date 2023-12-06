<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">


      <table class="table table-bordered">
        <tr style="background-color: #E9ECEF; color: #495057;">
          <th colspan="7">Susunan Keluarga ( Istri / Suami dan Anak - Anak )</th>
          <th colspan="2" class="text-right">
            <span class="input-group-btn" style="display: inline;">
              <button type="button" class="btn btn-primary btn-flat" title="Tambah Data Keluarga"   data-toggle="modal" data-target="#modal_1">
                <span class="btn-primary" ></span> Tambah Data
              </button>
            </span>
          </th>
        </tr>
        <tr>
          <th class="text-center" style="width: 20px;">No</th>
          <th class="text-center">Anggota</th>
          <th class="text-center">Nama</th>
          <th class="text-center">L/P</th>
          <th class="text-center">Tempat Lahir</th>
          <th class="text-center">Tgl Lahir</th>
          <th class="text-center">Pendidikan</th>
          <th class="text-center">Pekerjaan</th>
          <th></th>
        </tr>
        <?php
          foreach ($KEL_INTI->result() as $key => $row) { ?>
            <tr>
              <td><?php echo $key + 1 ?></td>
              <td>
                <select class="form-control" name="ANGGOTA_KELUARGA_INTI" disabled>
                  <option value="SUAMI" <?php if ($row->ANGGOTA_KELUARGA == 'SUAMI') echo "selected"; ?>>SUAMI</option>
                  <option value="ISTRI" <?php if ($row->ANGGOTA_KELUARGA == 'ISTRI') echo "selected"; ?>>ISTRI</option>
                  <option value="ANAK1" <?php if ($row->ANGGOTA_KELUARGA == 'ANAK1') echo "selected"; ?>>ANAK 1</option>
                  <option value="ANAK2" <?php if ($row->ANGGOTA_KELUARGA == 'ANAK2') echo "selected"; ?>>ANAK 2</option>
                  <option value="ANAK3" <?php if ($row->ANGGOTA_KELUARGA == 'ANAK3') echo "selected"; ?>>ANAK 3</option>
                </select>
              </td>
              <td>
                <?php echo $row->NAMA_KELUARGA ?>
              </td>
              <td>
                <select class="form-control" name="GENDER_INTI" disabled>
                  <option value="L" <?php if ($row->GENDER == 'L') echo "selected"; ?>>L</option>
                  <option value="P" <?php if ($row->GENDER == 'P') echo "selected"; ?>>P</option>
                </select>
              </td>
              <td>
                <?php echo $row->TP_LAHIR_KELUARGA ?>
              </td>
              <td>
                <?php echo $row->TGL_LAHIR_KELUARGA ?>
              </td>
              <td>
                <?php echo $row->PENDIDIKAN_KELUARGA ?>
              </td>
              <td>
                <?php echo $row->PEKERJAAN_KELUARGA ?>
              </td>
              <td>
                <span class="input-group-btn">
                  <?php
                      $link = base_url("profile_keluarga/delete/$row->KELUARGA_KARYAWAN_ID");
                   ?>
                  <button onclick="notifikasi('Yakin Ingin Hapus keluarga?' , '<?= $link; ?>')"  type="button" class="btn btn-danger btn-flat del-keluarga-inti" title="Hapus Data">
                    <span class="fa fa-trash btn-danger" style="border-bottom: none;"></span>
                  </button>
                </span>
              </td>
            </tr>
        <?php }?>
      </table>



    </div>
  </div>
</div>



<!-- Modal pend. formal -->
<div class="modal fade" id="modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form class="" action="<?= base_url('profile_keluarga/proses_add') ?>" method="post">
            <input type="hidden" name="JENIS" value="INTI">

            <div class="modal-header">
              <h5 class="modal-title" >Keluarga Inti</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
                <table class="table ">
                    <tr>
                      <td width="130">Tipe Anggota</td><td width="5">:</td>
                      <td>
                        <select class="form-control" name="ANGGOTA_KELUARGA">
                          <option value="SUAMI">SUAMI</option>
                          <option value="ISTRI">ISTRI</option>
                          <option value="ANAK1">ANAK 1</option>
                          <option value="ANAK2">ANAK 2</option>
                          <option value="ANAK3">ANAK 3</option>
                        </select>

                      </td>
                    </tr>

                    <tr>
                      <td >Nama Keluarga</td><td>:</td>
                      <td>
                        <input type="text" name="NAMA_KELUARGA" value="" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Gender</td><td>:</td>
                      <td>

                        <select class="form-control" name="GENDER" >
                          <option value="L">L</option>
                          <option value="P">P</option>
                        </select>

                      </td>
                    </tr>

                    <tr>
                      <td >Tempat Lahir</td><td>:</td>
                      <td>
                          <input type="text" name="TP_LAHIR_KELUARGA" value="" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Tanggal Lahir</td><td>:</td>
                      <td>
                          <input type="date" name="TGL_LAHIR_KELUARGA" value="" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Pendidikan</td><td>:</td>
                      <td>
                          <input type="text" name="PENDIDIKAN_KELUARGA" value="" class="form-control" required>
                      </td>
                    </tr>

                    <tr>
                      <td >Pekerjaan</td><td>:</td>
                      <td>
                          <input type="text" name="PEKERJAAN_KELUARGA" value="" class="form-control" required>
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
