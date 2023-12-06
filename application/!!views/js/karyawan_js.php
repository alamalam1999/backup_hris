<script type="text/javascript">
  $(document).ready(function() {

    $('#PROVINSI').select2({
      ajax: {
        url: '<?= base_url('data_json/getPropinsi') ?>',
        dataType: 'json',
        data: function(params) {
          return {
            q: params.term,
            page_limit: 10
          }
        }
      }
    });

    $('#PROVINSI').change(function(){
      data=$(this).select2('data')[0];
      $(this).find(':selected').attr('data-kode',data.kode);
    });

    $('#PROVINSI').on('select2:select', function (e) {
      $('#KOTA').val(null).trigger('change');
    });

    $('#KOTA').select2({
      ajax: {
        url: '<?= base_url('data_json/getKota') ?>',
        dataType: 'json',
        data: function (params) {
          provinsi_id = $('#PROVINSI').find(':selected').attr('data-kode');
          return {
            q: params.term,
            provinsi_id: provinsi_id,
            page_limit: 20
          }
        }
      }
    });

    $('#PROVINSI_KTP').select2({
      ajax: {
        url: '<?= base_url('data_json/getPropinsi') ?>',
        dataType: 'json',
        data: function(params) {
          return {
            q: params.term,
            page_limit: 10
          }
        }
      }
    });


    $('#PROVINSI_KTP').change(function(){
      data=$(this).select2('data')[0];
      $(this).find(':selected').attr('data-kode',data.kode);
    });

    $('#PROVINSI_KTP').on('select2:select', function (e) {
      $('#KOTA_KTP').val(null).trigger('change');
    });


      $('#KOTA_KTP').select2({
        ajax: {
          url: '<?= base_url('data_json/getKota') ?>',
          dataType: 'json',
          data: function (params) {
            provinsi_id = $('#PROVINSI_KTP').find(':selected').attr('data-kode');
            return {
              q: params.term,
              provinsi_id: provinsi_id,
              page_limit: 20
            }
          }
        }
      });



  });
</script>
