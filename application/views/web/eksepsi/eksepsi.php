
<!-- Main Content -->
<div class="main-content">

  <section class="section">
    <div class="card shadow">
      <div class="card-header d-block">
        <h4 class="title-header"><?= $title ?>
          <a href="<?= base_url("eksepsi/add") ?>" class="btn btn-success float-right" ><i class="fa fa-plus"></i> Tambah</a>
        </h4>

      </div>

      <div class="card-body">

          <div class="responsive">
            <table id="example1" class="table table-bordered table-hover table-striped">
              <thead>
              <tr>
                <th>No</th>
                <th>PERIODE</th>
                <th>KARYAWAN</th>
                <th>JENIS</th>
                <th>TANGGAL</th>
                <th>STATUS</th>
                <th>KETERANGAN</th>
                <th>FILE</th>

              </tr>
              </thead>
              <tbody>
              <?php
              $no = 1;
                foreach ($data->result() as $key => $a) {
                  $tanggal = date_indo($a->TGL_MULAI);
                  if($a->TGL_SELESAI AND $a->TGL_SELESAI > $a->TGL_MULAI) $tanggal .= " - ". date_indo($a->TGL_SELESAI);


                  //get File
                  $file = "";
                  if(!empty($a->FILE) AND url_exists(hris().'uploads/skd/'.rawurlencode($a->FILE)))
                  {
                    $file = "<a href='".hris('url',"uploads/skd/$a->FILE")."' class='btn btn-info btn-sm' download> <i class='fa fa-download'></i></a>";
                  }
                  echo "
                        <tr>
                          <td>".$no++."</td>
                          <td>".getPeriode($a->PERIODE_ID,'PERIODE')."</td>
                          <td>".getKaryawan($a->KARYAWAN_ID,'NAMA')."</td>
                          <td>".getJenisCuti($a->JENIS)."</td>
                          <td>$tanggal</td>
                          <td>$a->STATUS</td>
                          <td>$a->KETERANGAN</td>
                          <td>
                            $file
                          </td>
                        </tr>

                        ";
                }

               ?>
            </tbody>
            </table>
          </div>


      </div>

    </div>
  </section>
</div>
