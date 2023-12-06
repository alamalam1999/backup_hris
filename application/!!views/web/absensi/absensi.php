<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header ">
        <h4 class="title-header container-fluid"><?= $title ?></h4>
        <form action="<?= base_url("absensi") ?>" id="myFrom" method="post">
          <div class="float-left mr-2" style="width: 200px;">
            <div class="form-group">
              <?php echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), $periode, ' id="PERIODE_ID" class="form-control" ') ?>
            </div>
          </div>
        </form>
      </div>

      <div class="card-body">
        <div id="t-responsive" class="table-responsive">
          <table id="tabel_hadir" class="table table-sm table-bordered table-striped">
            <thead>
              <tr>
                <th class="text-center">No</th>
                <th class="text-center">Persetujuan</th>
                <th>Tgl Jadwal</th>
                <th>Tgl Absen</th>
                <th class="text-center">Jenis</th>
                <th>Note</th>
                <th>Foto</th>
                <th class="text-center">Status</th>
                <th class="text-center">Kehadiran</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              foreach ($absen as $key => $row) {
                if ($row->APPROVE == 0) {
                  $setuju = "<i class='badge badge-warning'>Proses</i>";
                } else if ($row->APPROVE == 1) {
                  $setuju = "<i class='badge badge-success'>Disetujui</i>";
                } else if ($row->APPROVE == 2) {
                  $setuju = "<i class='badge badge-danger'>Ditolak</i>";
                }

                if ($row->JENIS == 1) {
                  $jenis = "Masuk";
                } else if ($row->JENIS == 2) {
                  $jenis = "Keluar";
                } else {
                  $jenis = "-";
                }


                if ($row->STATUS == 1) {
                  if ($row->JENIS_EKSEPSI == 'IJIN_LATE') {
                    $status = "HADIR - IJIN LATE/EARLY";
                  } else if ($row->JENIS_EKSEPSI == 'DINAS') {
                    $status = "HADIR - DINAS";
                  } else if ($row->JENIS_EKSEPSI == 'SM') {
                    $status = "HADIR - SCAN MANUAL";
                  } else {
                    $status = "HADIR";
                  }
                } else if ($row->STATUS == 0) {
                  if ($row->JENIS_EKSEPSI == 'SAKIT') {
                    $status = "SAKIT";
                  } else if ($row->JENIS_EKSEPSI == 'IJIN') {
                    $status = "IJIN";
                  } else if ($row->JENIS_EKSEPSI == 'SKD') {
                    $status = "SKD";
                  } else {
                    $status = "ABSEN";
                  }
                }

                if ($row->TERLAMBAT == 1) {
                  if ($row->JENIS == 1) {
                    $kehadiran = "LATE";
                    } else if ($row->JENIS == 2) {
                      $kehadiran = "EARLY";
                    } 
                }else{
                 if($status == "ABSEN")$kehadiran = '';
                 if($status == "HADIR")$kehadiran = 'ONTIME';
                  
                }

                if (!empty($row->FOTO) and url_exists(hris('url') . 'uploads/absen/' . rawurlencode($row->FOTO))) {
                  $file = "<a href='" . hris('url') . 'uploads/absen/' . rawurlencode($row->FOTO) . "' class='btn btn-warning btn-sm' target='_blank' download><i class='fa fa-photo'></i> Foto</a>";
                } else {
                  $file = "-";
                }

                if($kehadiran != 'ONTIME'){
                  $bg_grid = 'bg-danger text-white';
                }else{
                  $bg_grid = '';

                }

                echo "
                  <tr class='" . $bg_grid ."'>
                    <td class='text-center'>" . $no++ . "</td>
                    <td class='text-center'>$setuju</td>                              
                    <td>" . date_indo($row->TGL_JADWAL, 'd-M-Y') . "</td>
                    <td>" . date_indo_full($row->TGL_ABSEN, 'd-M-Y H:i') . "</td>
                    <td class='text-center'>$jenis</td>
                    <td>$row->NOTE</td>
                    <td>$file</td>
                    <td class='text-center'>$status</td>
                    <td class='text-center'>$kehadiran</td>
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

<script type="text/javascript">
  $('#PERIODE_ID').change(function() {
    // var p = $('#PERIODE_ID').val();
    // window.location.href = "<?php /* echo base_url("absensi/index/") */ ?>"+p;

    $("#myFrom").submit();
    return false;
  });

  $(function() {

    $('#tabel_hadir').DataTable({
      "paging": true,
      "pageLength": 50,
      "lengthChange": true,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>