
<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header">
        <div class="row ml-4">
          <div class="row col-md-12">
            <h5 class="pt-2 mr-2 text-danger"><?= $title ?></h5>
          </div>
          <div class="row col-md-12">
              <a href="<?php echo site_url('profile'); ?>" class="btn btn-secondary mr-1 mb-1">Profile</a>
              <a href="<?php echo site_url('profile_sertifikat'); ?>" class="btn btn-secondary mr-1 mb-1">Dokumen</a>
              <a href="<?php echo site_url('profile_pendidikan'); ?>" class="btn btn-secondary mr-1 mb-1">Pendidikan</a>
              <a href="<?php echo site_url('profile_organisasi'); ?>" class="btn btn-secondary mr-1 mb-1">Organisasi</a>
              <a href="<?php echo site_url('profile_keluarga'); ?>" class="btn btn-secondary mr-1 mb-1">Keluarga</a>
              <a href="<?php echo site_url('profile_kerja'); ?>" class="btn btn-secondary mr-1 mb-1">Pengalaman Kerja</a>
          </div>
          <div class="row col-md-12">
            <button type="button" class="btn btn-primary btn-flat" title="Tambah Data Pengalaman"   data-toggle="modal" data-target="#modal_1">
              <span class="fa fa-plus " style="border-bottom: none;"></span> Tambah Data
            </button>
          </div> 
        </div>
        
      </div>


        <div class="card-body">

          <div class="row">
            <div class="col-md-12 pengalaman">
              
    					<?php foreach ($PENGALAMAN_KARYAWAN->result() as $key => $row) { ?>

                <div class="row col-sm-12 col-lg-12 col-md-12">
                  <label class="col-md-12 col-form-label  mb-2 bg-info text-white pl-3" ><?= ucwords($row->NAMA_PERUSAHAAN) ?> ( <?= ucwords($row->PERIODE_BEKERJA) ?> )<span data-collapse="#card-surat-<?php echo $key ?>" class="pt-0 float-right btn btn-lg btn-sm btn-icon " style="height: 80%;" href="#"><i class="fas fa-minus"></i></span></label>
                  
                    <div class="card">
                      
                      <div class="collapse show" id="card-surat-<?php echo $key ?>">
                        <div class="card-body">

                          <div class="row mb-2">
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 pt-0">Nama Perusahaan</label>
                              <input type="text"  value="<?= $row->NAMA_PERUSAHAAN ?>" class="form-control col-md-8">
                            </div>
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 pt-0">Bergerak di Bidang</label>
                              <input type="text"  value="<?= $row->BIDANG_USAHA ?>" class="form-control col-md-8">
                            </div>
                          </div>
                          <div class="row mb-2">
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 pt-0">Alamat</label>
                              <input type="text"  value="<?= $row->ALAMAT_PERUSAHAAN ?>" class="form-control col-md-8">
                            </div>
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 pt-0">No Telp Perusahaan</label>
                              <input type="text"  value="<?= $row->NO_TELP_PERUSAHAAN ?>" class="form-control col-md-8">
                            </div>
                          </div>
                          <div class="row mb-2">
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Nama Atasan Langsung</label>
                              <input type="text"  value="<?= $row->ATASAN ?>" class="form-control col-md-8">
                            </div>
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Alasan Pengunduran Diri</label>
                              <input type="text"  value="<?= $row->ALASAN_RESIGN ?>" class="form-control col-md-8">
                            </div>
                          </div>
                          <div class="row mb-2">
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Periode Kerja</label>
                              <input type="text"  value="<?= $row->PERIODE_BEKERJA ?>" class="form-control col-md-8">
                            </div>
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Deskripsi Pekerjaan</label>
                              <input type="text"  value="<?= $row->DESKRIPSI_PEKERJAAN ?>" class="form-control col-md-8">
                            </div>
                          </div>
                          <div class="row mb-2">
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Jabatan Awal</label>
                              <input type="text"  value="<?= $row->JABATAN_AWAL ?>" class="form-control col-md-8">
                            </div>
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Jabatan Akhir</label>
                              <input type="text"  value="<?= $row->JABATAN_AKHIR ?>" class="form-control col-md-8">
                            </div>
                          </div>
                          
                          <div class="row mb-2">
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Gaji Pokok (Rp)</label>
                              <input type="text" value="<?= currency($row->GAPOK_SEBELUMNYA) ?>" class="form-control col-md-8">
                            </div>
                            <div class="row col-md-6 col-sm-12">
                              <label class="col-md-4 col-form-label pr-0 ">Tunjangan Lainnya</label>
                              <input type="text" value="<?= $row->TUNJANGAN_LAINNYA ?>" class="form-control col-md-8">
                            </div>
                          </div>

                          <span class="input-group-btn" >
                            <?php
                                $link = base_url("profile_kerja/delete/$row->PENGALAMAN_KARYAWAN_ID");
                             ?>
                            <button onclick="notifikasi('Yakin Ingin Hapus Pengelaman Kerja?' , '<?= $link; ?>')" type="button"  class="btn btn-danger btn-flat " title="Hapus Data" >
                              <span class="fa fa-trash " ></span> Hapus Data
                            </button>

                            <button   onclick="open_form_kelas('<?= $row->PENGALAMAN_KARYAWAN_ID ?>')" type="button"  class="btn btn-info btn-flat " title="Hapus Data" >
                              <span class="fa fa-edit " ></span> Ubah Data
                            </button>
                          </span>

                        </div>
                      </div>
                    </div>
                </div>


                
    					<?php }?>
    				</div>
  				</div>


          <!-- <div class="row">
  					<div class="col-md-12">
  						<div class="table-responsive">

  						</div>
  					</div>
  				</div> -->

        </div>

    </div>
  </section>
</div>




<!-- Modal  add -->
<div class="modal fade" id="modal_1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <form class="" action="<?= base_url('profile_kerja/proses_add') ?>" method="post">
            <div class="modal-header">
              <h5 class="modal-title" >Pengalaman kerja</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">
              <div class="row mb-2">
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 pt-0">Nama Perusahaan</label>
                  <input type="text" name="NAMA_PERUSAHAAN" value="" class="form-control col-md-8">
                </div>
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 pt-0">Bergerak di Bidang</label>
                  <input type="text" name="BIDANG_USAHA" value="" class="form-control col-md-8">
                </div>
              </div>
              <div class="row mb-2">
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 pt-0">Alamat</label>
                  <input type="text" name="ALAMAT_PERUSAHAAN" value="" class="form-control col-md-8">
                </div>
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 pt-0">No Telp Perusahaan</label>
                  <input type="text" name="NO_TELP_PERUSAHAAN" value="" class="form-control col-md-8">
                </div>
              </div>
              <div class="row mb-2">
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Nama Atasan Langsung</label>
                  <input type="text" name="ATASAN" value="" class="form-control col-md-8">
                </div>
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Alasan Pengunduran Diri</label>
                  <input type="text" name="ALASAN_RESIGN" value="" class="form-control col-md-8">
                </div>
              </div>
              <div class="row mb-2">
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Periode Kerja</label>
                  <input type="text" name="PERIODE_BEKERJA" value="" class="form-control col-md-8">
                </div>
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Deskripsi Pekerjaan</label>
                  <input type="text" name="DESKRIPSI_PEKERJAAN" value="" class="form-control col-md-8">
                </div>
              </div>
              <div class="row mb-2">
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Jabatan Awal</label>
                  <input type="text" name="JABATAN_AWAL" value="" class="form-control col-md-8">
                </div>
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Jabatan Akhir</label>
                  <input type="text" name="JABATAN_AKHIR" value="" class="form-control col-md-8">
                </div>
              </div>
              
              <div class="row mb-2">
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Gaji Pokok (Rp)</label>
                  <input type="text" name="GAPOK_SEBELUMNYA" value="" class="form-control col-md-8">
                </div>
                <div class="row col-md-6 col-sm-12">
                  <label class="col-md-4 col-form-label pr-0 ">Tunjangan Lainnya</label>
                  <input type="text" name="TUNJANGAN_LAINNYA" value="" class="form-control col-md-8">
                </div>
              </div>

              
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
      </form>


    </div>
  </div>
</div>


<!-- Modal edit -->
<div class="modal fade" id="modal_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <form id="form_edit" action="" method="post">
            <div class="modal-header">
              <h5 class="modal-title" >Pengalaman kerja</h5>
              <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button> -->
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
      </form>


    </div>
  </div>
</div>

<script type="text/javascript">

function open_form_kelas(id=null) {

    $('#modal_2').modal('show');
    $('#form_edit').attr("action","<?= site_url('profile_kerja/proses_edit') ?>");
    $('.modal-title').html('Edit Pengelaman Kerja');
    $('.modal-body').load('profile_kerja/show_form_edit/'+id);
}

</script>
