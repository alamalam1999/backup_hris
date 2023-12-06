
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header d-block">
        <h4 class="title-header"><?= $title ?>

        </h4>
      </div>

      <div class="card-body">

          <div class="responsive">
            <form id="form"  action="<?= base_url("eksepsi/add_proses") ?>" method="POST" enctype="multipart/form-data">

              <div class="form-group">
                <label for="" class="col-sm-2 control-label">Periode <i class="text-danger">*</i></label>
                <div class="col-sm-10">
                  <?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY TANGGAL_MULAI DESC'),null,' class="form-control" required' ) ?>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-2 control-label">Jenis <i class="text-danger">*</i></label>
                <div class="col-sm-10">
                  <select class="form-control" name="JENIS" required>
                      <option value="SAKIT">SAKIT</option>
                      <option value="IJIN">IJIN</option>
                      <option value="IJIN_LE">IJIN LATE/EARLY</option>
                      <option value="SKD">SURAT KETERANGAN DOKTER</option>
                      <option value="CT">CUTI TAHUNAN</option>
                      <option value="CI">CUTI ISTIMEWA</option>
                      <option value="CM">CUTI MELAHIRKAN</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-2 control-label">Tgl Mulai <i class="text-danger">*</i></label>
                <div class="col-sm-10">
                  <input type="text" name="TGL_MULAI" value="" class="form-control datepicker"  required>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-2 control-label">Tgl Selesai <i class="text-danger">*</i></label>
                <div class="col-sm-10">
                  <input type="text" name="TGL_SELESAI" value="" class="form-control datepicker"  required>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-2 control-label">Keterangan <i class="text-danger">*</i></label>
                <div class="col-sm-10">
                  <input type="text" name="KETERANGAN" value="" class="form-control"  required>
                </div>
              </div>

              <div class="form-group">
                <label for="" class="col-sm-2 control-label">File</label>
                <div class="col-sm-10">
                  <input type="file" name="FILE" class="form-control">
                </div>
              </div>

              <button type="submit"  class="btn btn-success" ><i class="fa fa-save"></i> Simpan</button>


            </form>
          </div>


      </div>

    </div>
  </section>
</div>
