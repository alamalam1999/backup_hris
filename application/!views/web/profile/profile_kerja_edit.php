<?php
  foreach ($detail->result() as $key => $a) {

 ?>
 <input type="hidden" name="last_id" value="<?= $a->PENGALAMAN_KARYAWAN_ID ?>">
<table class="table">
  <tr>
    <td class="text-right" style="vertical-align: middle; width: 120px;">Nama Perusahaan</td>
    <td><input type="text" name="NAMA_PERUSAHAAN" value="<?= $a->NAMA_PERUSAHAAN ?>" class="form-control"></td>
    <td></td>
    <td class="text-right" style="vertical-align: middle; width: 120px; ">Jabatan Awal</td>
    <td><input type="text" name="JABATAN_AWAL" value="<?= $a->JABATAN_AWAL ?>"  class="form-control"></td>
  </tr>
  <tr>
    <td class="text-right" style="vertical-align: middle;">Bergerak di Bidang </td>
    <td><input type="text" name="BIDANG_USAHA" value="<?= $a->BIDANG_USAHA ?>"  class="form-control"></td>
    <td></td>
    <td class="text-right" style="vertical-align: middle;">Jabatan Akhir</td>
    <td><input type="text" name="JABATAN_AKHIR" value="<?= $a->JABATAN_AKHIR ?>"   class="form-control"></td>
  </tr>
  <tr>
    <td class="text-right" style="vertical-align: middle;">Alamat</td>
    <td><input type="text" name="ALAMAT_PERUSAHAAN" value="<?= $a->ALAMAT_PERUSAHAAN ?>"  class="form-control"></td>
    <td></td>
    <td class="text-right" style="vertical-align: middle;">Gaji Pokok (Rp)</td>
    <td><input type="number" name="GAPOK_SEBELUMNYA"  value="<?= $a->GAPOK_SEBELUMNYA ?>"  class="form-control currency"></td>
  </tr>
  <tr>
    <td class="text-right" style="vertical-align: middle;">Nama Atasan Langsung </td>
    <td><input type="text" name="ATASAN" value="<?= $a->ATASAN ?>"  class="form-control"></td>
    <td></td>
    <td class="text-right" style="vertical-align: middle;">Tunjangan Lainnya </td>
    <td><input type="text" name="TUNJANGAN_LAINNYA" value="<?= $a->TUNJANGAN_LAINNYA ?>"  class="form-control"></td>
  </tr>
  <tr>
    <td class="text-right" style="vertical-align: middle;">No. Telepon</td>
    <td><input type="text" name="NO_TELP_PERUSAHAAN" value="<?= $a->NO_TELP_PERUSAHAAN ?>"  class="form-control"></td>
    <td></td>
    <td class="text-right" style="vertical-align: middle;">Alasan Pengunduran Diri </td>
    <td><input type="text" name="ALASAN_RESIGN" value="<?= $a->ALASAN_RESIGN ?>"  class="form-control"></td>
  </tr>
  <tr>
    <td class="text-right" style="vertical-align: middle;">Periode Kerja</td>
    <td><input type="text" name="PERIODE_BEKERJA" value="<?= $a->PERIODE_BEKERJA ?>"  class="form-control"></td>
    <td></td>
    <td class="text-right" style="vertical-align: middle;">Deskripsi Pekerjaan </td>
    <td><input type="text" name="DESKRIPSI_PEKERJAAN" value="<?= $a->DESKRIPSI_PEKERJAAN ?>"  class="form-control"></td>
  </tr>

</table>

<?php } ?>
