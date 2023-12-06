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
          <form  action="<?= base_url("penggajian") ?>" id="myFrom" method="post">
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
                <tr style="font-weight: 700; font-size: 12px;" >

                  <td rowspan="2" style="vertical-align: middle;">No

                  </td>
                  <!-- <td rowspan="2" style="vertical-align: middle;">Nama</td>
                  <td rowspan="2" style="vertical-align: middle;">Periode</td> -->
                  <!-- <td rowspan="2" style="vertical-align: middle;">Quota Cuti</td>
                  <td rowspan="2" style="vertical-align: middle;">Pot Cuti Sebelumnya</td>
                  <td rowspan="2" style="vertical-align: middle;">Pot Cuti Sekarang</td>
                  <td rowspan="2" style="vertical-align: middle;">Sita Cuti</td> -->

                  <td colspan="4" style="text-align: center">GAJI POKOK</td>
                  <td colspan="2" style="text-align: center">KEL JAM AJAR</td>

                  <td colspan="6" style="text-align: center">TUNJANGAN TETAP</td>

                  <td colspan="4" style="text-align: center">TUNJANGAN TIDAK TETAP</td>

                  <!-- <td rowspan="2" style="vertical-align: middle;">Total Tunjangan</td>
                  <td rowspan="2" style="vertical-align: middle;">Penghasilan</td> -->

                  <td colspan="6" style="text-align: center">BPJS</td>
                  <td colspan="9" style="text-align: center">POTONGAN</td>
                  <td colspan="2" style="text-align: center">ADJUSTMENT</td>

                  <td rowspan="2" style="vertical-align: middle;">Total Diterima</td>
                </tr>
                <tr style="font-weight: 700; font-size: 12px;">
                  <!-- <th colspan="2">No</th>
                  <th colspan="2">Nama</th>
                  <th colspan="2">Quota Cuti</th>
                  <th colspan="2">Pot Cuti Sebelumnya</th>
                  <th colspan="2">Pot Cuti Sekarang</th>
                  <th colspan="2">Sita Cuti</th> -->

                  <td>Gaji Pokok</td>
                  <td>GP Prorata</td>
                  <td>Tunj Keluarga</td>
                  <td>TOTAL</td>

                  <td>Jam</td>
                  <td>Nilai</td>

                  <td>OTTW</td>
                  <td>K/WK.SEK</td>
                  <td>S.Kurikulum</td>
                  <td>K.MGMP</td>
                  <td>Walikelas</td>
                  <td>TOTAL</td>

                  <td style="width: 300px;">Makan Transport</td>
                  <td>Lembur HK</td>
                  <td>Lembur HL</td>
                  <td>TOTAL</td>

                  <!-- <th rowspan="2">Total Tunjangan</th>
                  <th rowspan="2">Penghasilan</th> -->


                  <td>BPJS JKK</td>
                  <td>BPJS JHT</td>
                  <td>BPJS JKM</td>
                  <td>BPJS JP</td>
                  <td>BPJS KES</td>
                  <td>TOTAL</td>

                  <td>P.KOP DINATERA</td>
                  <td>I.KOP DINATERA</td>
                  <td>P.KOP AVICENNA</td>
                  <td>I.KOP AVICENNA</td>
                  <td>P. BANK BWS</td>
                  <td>E. KLAIM</td>
                  <td>B. PEND ANAK</td>
                  <td>B. LAPTOP</td>
                  <td>TOTAL</td>

                  <td>Plus</td>
                  <td>Minus</td>
                  

                  <!-- <th rowspan="2">Total Potongan</th>
                  <th rowspan="2">Total Diterima</th> -->
                </tr>
              </thead>
              <tbody>
                <?php
                      $no = 1;
                      foreach ($data as $key => $a) { ?>


                        
                              <tr style="font-weight: 700; font-size: 12px;">
                                <td><?= $no++ ?><br>
                                  <!-- <a href=" <?= site_url('penggajian/download/'.$a->PENGGAJIAN_ID) ?>" target='_blank' title='Slip Gaji' download><i class='fa fa-print text-info'></i></a> -->
                                </td>
                                <!-- <td><?= $a->NAMA ?></td>
                                <td><?= getPeriode($a->PERIODE_ID,'PERIODE') ?></td> -->
                                

                                <td><?= currency($a->GAJI_POKOK) ?></td>
                                <td><?= currency($a->GAJI_POKOK_PRORATA) ?></td>
                                <td><?= currency($a->TUNJ_KELUARGA) ?></td>
                                <td><?= currency($a->GAJI_POKOK + $a->GAJI_POKOK_PRORATA + $a->TUNJ_KELUARGA) ?> </td>

                               <td><?= currency($a->KELEBIHAN_JAM_AJAR) ?></td>
                               <td><?= currency($a->NILAI_KELEBIHAN_JAM_AJAR) ?></td>
                 

                                <td><?= currency($a->TUNJ_OTTW) ?></td>
                                <td><?= currency($a->TUNJ_LAINNYA_1) ?></td>
                                <td><?= currency($a->TUNJ_LAINNYA_2) ?></td>
                                <td><?= currency($a->TUNJ_LAINNYA_3) ?></td>
                                <td><?= currency($a->TUNJ_LAINNYA_4) ?></td>
                                <td><?= currency($a->TUNJ_OTTW+$a->TUNJ_LAINNYA_1+$a->TUNJ_LAINNYA_2+$a->TUNJ_LAINNYA_3+$a->TUNJ_LAINNYA_4+$a->TUNJ_LAINNYA_5) ?></td>
                 

                                <td><?= currency($a->TUNJ_TRANSPORT + $a->TUNJ_MAKAN) ?></td>
                                <td><?= currency($a->LHK) ?></td>
                                <td><?= currency($a->LHL) ?></td>
                                <td><?= currency($a->LHK + $a->LHL + $a->TUNJ_MAKAN + $a->TUNJ_TRANSPORT) ?></td>
                               
                                <td><?= currency($a->BPJS_JKK) ?></td>
                                <td><?= currency($a->BPJS_JHT) ?></td>
                                <td><?= currency($a->BPJS_JKM) ?></td>
                                <td><?= currency($a->BPJS_JP) ?></td>
                                <td><?= currency($a->BPJS_KES) ?></td>
                                <td><?= currency($a->BPJS_KES+$a->BPJS_JHT+$a->BPJS_JKK+$a->BPJS_JKM) ?></td>

                             
                                <td><?= currency($a->PINJAMAN_KOPERASI_DINATERA) ?></td>
                                <td><?= currency($a->IURAN_KOPERASI_DINATERA) ?></td>
                                <td><?= currency($a->PINJAMAN_KOPERASI_AVICENNA) ?></td>
                                <td><?= currency($a->IURAN_KOPERASI_AVICENNA) ?></td>
                                <td><?= currency($a->PINJAMAN_BANK_BWS) ?></td>
                                <td><?= currency($a->EKSES_KLAIM) ?></td>
                                <td><?= currency($a->BIAYA_PEND_ANAK) ?></td>
                                <td><?= currency($a->BIAYA_LAPTOP) ?></td>
                                <td><?= currency($a->PINJAMAN) ?></td>

                             

                                <td><?= currency($a->ADJUSMENT_PLUS) ?></td>
                                <td><?= currency($a->ADJUSMENT_MINUS) ?></td>

                                <td><?= currency($a->TOTAL_GAJI_BERSIH) ?></td>


                              </tr>
                              
                    <?php  }


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
