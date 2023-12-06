
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

        <div id="t-responsive" class="table-responsive">
            <table id="tabel_hadir" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <!-- <th>NIK</th> -->
                  <th>NAMA</th>
                  <th>DATE</th>
                  <th>START_TIME</th>
                  <th>FINISH_TIME</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                      $no = 1;
                      foreach ($data as $key => $a) {


                        echo "
                              <tr>
                                <td>".$no++."</td>

                                <!--td>$a->NIK</td-->
                                <td>$a->NAMA</td>
                                <td>".date_indo($a->DATE , 'd-M-Y')."</td>
                                <td>$a->START_TIME</td>
                                <td>$a->FINISH_TIME</td>
                                <td>$a->STATUS</td>

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
     // var p = $('#PERIODE_ID').val();
     // window.location.href = "<?= base_url("jadwal/index/") ?>"+p;

     $("#myFrom").submit();

   return false;
});

$(function() {

  $('#tabel_hadir').DataTable({
    "paging": true,
    "lengthChange": true,
    "searching": false,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "responsive": true,
  });
});
</script>
