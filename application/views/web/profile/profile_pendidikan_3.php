<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">

      <table class="table table-bordered bahasa">
        <tr style="background-color: #E9ECEF; color: #495057;">
          <th colspan="5">Bahasa asing yang dikuasai</th>
          <th colspan="4" class="text-right">
            <span class="input-group-btn" style="display: inline;">
              <button type="button" class="btn btn-primary btn-flat" title="Tambah Data Bahasa"  data-toggle="modal" data-target="#modal_3">
                <span class="btn-primary"></span> Tambah Data
              </button>
            </span>
          </th>
        </tr>
        <tr>
          <th rowspan="2" class="text-center" style="width: 20px;">No</th>
          <th rowspan="2" class="text-center">Bahasa</th>
          <th colspan="3" class="text-center">Lisan</th>
          <th colspan="3" class="text-center">Tulisan</th>
          <th rowspan="2" class="text-center" style="width: 50px;"></th>
        </tr>
        <tr>
          <th class="text-center" style="width: 100px;">Kurang</th>
          <th class="text-center" style="width: 100px;">Cukup</th>
          <th class="text-center" style="width: 100px;">Baik</th>
          <th class="text-center" style="width: 100px;">Kurang</th>
          <th class="text-center" style="width: 100px;">Cukup</th>
          <th class="text-center" style="width: 100px;">Baik</th>
        </tr>
        <?php
          foreach ($BHS_ASING->result() as $key => $row) { ?>
            <tr>
              <td><?php echo $key + 1 ?></td>
              <td><?php echo $row->BAHASA ?></td>

              <td class="text-center">
              <?php if ($row->LISAN == 'kurang') echo "<i class='fa fa-check text-success'></i>" ?>
              </td>
              <td class="text-center">
                <?php if ($row->LISAN == 'cukup') echo "<i class='fa fa-check text-success'></i>" ?>
              </td>
              <td class="text-center">
                <?php if ($row->LISAN == 'baik') echo "<i class='fa fa-check text-success'></i>" ?>
              </td>


              <td class="text-center">
                <?php if ($row->TULISAN == 'kurang') echo "<i class='fa fa-check text-success'></i>" ?>
              </td>
              <td class="text-center">
                <?php if ($row->TULISAN == 'cukup') echo "<i class='fa fa-check text-success'></i>" ?>
              </td>
              <td class="text-center">
                <?php if ($row->TULISAN == 'baik') echo "<i class='fa fa-check text-success'></i>" ?>
              </td>
              <td class="text-center" style="width: 50px;">
                <span class="input-group-btn">
                  <?php
                      $link = base_url("profile_pendidikan/delete/3/$row->BAHASA_KARYAWAN_ID");
                   ?>
                  <button type="button"  onclick="notifikasi('Yakin Ingin Hapus Bahasa?' , '<?= $link; ?>')" class="btn btn-danger btn-flat" title="Hapus Data">
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

<!-- Modal pend. formal -->
<div class="modal fade" id="modal_3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <form class="" action="<?= base_url('profile_pendidikan/proses_add_bahasa') ?>" method="post">
            <div class="modal-header">
              <h5 class="modal-title" >Bahasa asing yang dikuasai</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
                <table class="table ">

                    <tr>
                      <td width="100">Bahasa</td><td>:</td>
                      <td>
                        <input type="text" name="BAHASA" class="form-control" required>

                      </td>
                    </tr>

                    <tr>
                      <td >Lisan</td><td>:</td>
                      <td>
                        <select class="form-control" name="LISAN">
                            <option value="kurang">Kurang</option>
                            <option value="cukup">Cukup</option>
                            <option value="baik">Baik</option>
                        </select>
                      </td>
                    </tr>

                    <tr>
                      <td >Tulisan</td><td>:</td>
                      <td>
                        <select class="form-control" name="TULISAN">
                            <option value="kurang">Kurang</option>
                            <option value="cukup">Cukup</option>
                            <option value="baik">Baik</option>
                        </select>
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
