<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style media="screen">

table {
  border-collapse: collapse;
}
  tr,th, td{
    border: 1px solid #ddd;

  }
</style>
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="title-header  container-fluid"><?= $title ?></h4>
          <form  action="<?= base_url("jadwal") ?>" id="myFrom" method="post">
                  <div class="float-left mr-2"  style="width: 200px;">
                        <div class="form-group">
                            <?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY PERIODE_ID DESC'),$periode,' id="PERIODE_ID" class="form-control" ') ?>
                        </div>
                  </div>
            </form>
      </div>

      <div class="card-body">

        <div id="" class="table-responsive">
            <table id="tabel" class="table table-bordered  table-striped">
              <thead>
                <tr style="font-weight: 700;" >

                  <td rowspan="2" style="vertical-align: middle;">No

                  </td>
                  <td rowspan="2" style="vertical-align: middle;">Nama</td>
                  <td rowspan="2" style="vertical-align: middle;">Periode</td>
                  <td rowspan="2" style="vertical-align: middle;">Quota Cuti</td>
                  <td rowspan="2" style="vertical-align: middle;">Pot Cuti Sebelumnya</td>
                  <td rowspan="2" style="vertical-align: middle;">Pot Cuti Sekarang</td>
                  <td rowspan="2" style="vertical-align: middle;">Sita Cuti</td>

                  <td colspan="4" style="text-align: center">GAJI POKOK</td>

                  <td colspan="5" style="text-align: center">TUNJANGAN TETAP</td>

                  <td colspan="9" style="text-align: center">TUNJANGAN TIDAK TETAP</td>

                  <td rowspan="2" style="vertical-align: middle;">Total Tunjangan</td>
                  <td rowspan="2" style="vertical-align: middle;">Penghasilan</td>

                  <td colspan="6" style="text-align: center">POTONGAN</td>

                  <td rowspan="2" style="vertical-align: middle;">Total Potongan</td>
                  <td rowspan="2" style="vertical-align: middle;">Total Diterima</td>
                </tr>
                <tr style="font-weight: 700;">
                  <!-- <th colspan="2">No</th>
                  <th colspan="2">Nama</th>
                  <th colspan="2">Quota Cuti</th>
                  <th colspan="2">Pot Cuti Sebelumnya</th>
                  <th colspan="2">Pot Cuti Sekarang</th>
                  <th colspan="2">Sita Cuti</th> -->

                  <td>GP Baru</td>
                  <td>GP Prorata</td>
                  <td>Tidak Masuk</td>
                  <td>GP Nett</td>

                  <td>T Jabatan</td>
                  <td>T Keahlian</td>
                  <td>T Komunikasi</td>
                  <td>T Proyek</td>
                  <td>T Shift</td>

                  <td>Backup</td>
                  <td>Lembur HK</td>
                  <td>Lembur HL</td>
                  <td>IHB</td>
                  <td>Medical</td>
                  <td>I. Kehadiran</td>
                  <td>T. Makan</td>
                  <td>T. Transport</td>
                  <td>Adjusment</td>

                  <!-- <th rowspan="2">Total Tunjangan</th>
                  <th rowspan="2">Penghasilan</th> -->


                  <td>BPJS JHT</td>
                  <td>BPJS JP</td>
                  <td>BPJS KES</td>
                  <td>Angsuran</td>
                  <td>Pinjaman</td>
                  <td>Adjusment</td>

                  <!-- <th rowspan="2">Total Potongan</th>
                  <th rowspan="2">Total Diterima</th> -->
                </tr>
              </thead>
              <tbody>
                <?php
                      $no = 1;
                      foreach ($data as $key => $a) {


                        echo "
                              <tr>
                                <td>".$no++."<br>
                                  <a href='". base_url('penggajian/download/'.$a->PENGGAJIAN_ID) ."' target='_blank' title='Slip Gaji' download><i class='fa fa-print text-info'></i></a>
                                </td>
                                <td>$a->NAMA</td>
                                <td>".getPeriode($a->PERIODE_ID,'PERIODE')."</td>
                                <td>$a->KUOTA_CUTI</td>
                                <td>$a->CUTI_PERIODE_SEBELUMNYA</td>
                                <td>$a->CUTI_PERIODE_INI</td>
                                <td>$a->SISA_CUTI</td>

                                <td>$a->GAJI_POKOK</td>
                                <td>$a->GAJI_POKOK_PRORATA</td>
                                <td>$a->TIDAK_MASUK</td>
                                <td>$a->GAJI_POKOK_NET</td>

                                <td>$a->TUNJ_JABATAN</td>
                                <td>$a->TUNJ_KEAHLIAN</td>
                                <td>$a->TUNJ_KOMUNIKASI</td>
                                <td>$a->TUNJ_PROYEK</td>
                                <td>$a->TUNJ_SHIFT</td>

                                <td>$a->TUNJ_BACKUP</td>
                                <td>$a->LHK</td>
                                <td>$a->LHL</td>
                                <td>$a->IHB</td>
                                <td>$a->MEDICAL</td>
                                <td>$a->TUNJ_KEHADIRAN</td>
                                <td>$a->TUNJ_MAKAN</td>
                                <td>$a->TUNJ_TRANSPORT</td>
                                <td>$a->ADJUSMENT_PLUS</td>

                                <td>$a->TOTAL_TUNJANGAN</td>
                                <td>$a->TOTAL_GAJI_BERSIH</td>

                                <td>$a->BPJS_JHT_PERUSAHAAN</td>
                                <td>$a->BPJS_JP_PERUSAHAAN</td>
                                <td>$a->BPJS_KES_PERUSAHAAN</td>
                                <td>$a->ANGSURAN</td>
                                <td>$a->PINJAMAN</td>
                                <td>$a->ADJUSMENT_MINUS</td>

                                <td>$a->TOTAL_POTONGAN</td>
                                <td>$a->TOTAL_GAJI_BERSIH</td>

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

$('#PERIODE_ID').change(function(){


     $("#myFrom").submit();

   return false;
});

$(function() {

  $('#tabel').DataTable({
    "paging": false,
    "lengthChange": false,
    "searching": false,
    "ordering": false,
    "info": true,
    "autoWidth": false,
    "responsive": false,
  });
});
</script>
