<script type="text/javascript">
  $(document).ready(function() {

    /* eksternal - internal tipe and location */

    tipe_permohonan = $('#tipe_permohonan').val();
    if (tipe_permohonan == 'eksternal') {
      $('#city').val($('#city_eksternal').val());
      $('.eksternal-internal').show();
      $('.eksternal').show();
      $('.internal').hide();
      $('.internal input[type="checkbox"]').prop('checked', false);
    } else {
      $('#city').val($('#city_internal').val());
      $('.eksternal-internal').show();
      $('.eksternal').hide();
      $('.internal').show();
      $('.eksternal input[type="checkbox"]').prop('checked', false);
    }

    $('#tipe_permohonan').on('change', function() {
      tipe_permohonan = this.value;
      if (tipe_permohonan == 'eksternal') {
        $('#city').val($('#city_eksternal').val());
        $('.eksternal-internal').show();
        $('.eksternal').show();
        $('.internal').hide();
        $('.internal input[type="checkbox"]').prop('checked', false);
      } else {
        $('#city').val($('#city_internal').val());
        $('.eksternal-internal').show();
        $('.eksternal').hide();
        $('.internal').show();
        $('.eksternal input[type="checkbox"]').prop('checked', false);
      }
    });

    var layanan_kalibrasi = $('#layanan_kalibrasi').data("layanan_kalibrasi");
    var layanan_pengujian = $('#layanan_pengujian').data("layanan_pengujian");
    var layanan_pengujian_kalibrasi = $('#layanan_pengujian_kalibrasi').data("layanan_pengujian_kalibrasi");
    var layanan_kalibrasi_proteksi_radiasi = $('#layanan_kalibrasi_proteksi_radiasi').data("layanan_kalibrasi_proteksi_radiasi");
    var layanan_inspeksi = $('#layanan_inspeksi').data("layanan_inspeksi");
    var layanan_pelayanan_uji_kesesuaian = $('#layanan_pelayanan_uji_kesesuaian').data("layanan_pelayanan_uji_kesesuaian");
    var layanan_pelayanan_penggantian_alat = $('#layanan_pelayanan_penggantian_alat').data("layanan_pelayanan_penggantian_alat");
    var layanan_uji_produk = $('#layanan_uji_produk').data("layanan_uji_produk");
    var layanan_pengukuran_paparan_radiasi_proteksi = $('#layanan_pengukuran_paparan_radiasi_proteksi').data("layanan_pengukuran_paparan_radiasi_proteksi");

    layanan_kalibrasi == 1 ? layanan_kalibrasi = 1 : layanan_kalibrasi = 0;
    layanan_pengujian == 1 ? layanan_pengujian = 1 : layanan_pengujian = 0;
    layanan_pengujian_kalibrasi == 1 ? layanan_pengujian_kalibrasi = 1 : layanan_pengujian_kalibrasi = 0;
    layanan_kalibrasi_proteksi_radiasi == 1 ? layanan_kalibrasi_proteksi_radiasi = 1 : layanan_kalibrasi_proteksi_radiasi = 0;
    layanan_inspeksi == 1 ? layanan_inspeksi = 1 : layanan_inspeksi = 0;
    layanan_pelayanan_uji_kesesuaian == 1 ? layanan_pelayanan_uji_kesesuaian = 1 : layanan_pelayanan_uji_kesesuaian = 0;
    layanan_pelayanan_penggantian_alat == 1 ? layanan_pelayanan_penggantian_alat = 1 : layanan_pelayanan_penggantian_alat = 0;
    layanan_uji_produk == 1 ? layanan_uji_produk = 1 : layanan_uji_produk = 0;
    layanan_pengukuran_paparan_radiasi_proteksi == 1 ? layanan_pengukuran_paparan_radiasi_proteksi = 1 : layanan_pengukuran_paparan_radiasi_proteksi = 0;

    $('#id-alat').select2({
      ajax: {
        url: '<?php echo site_url('permohonan/lookup_alat') ?>',
        dataType: 'json',
        data: function(params) {
          return {
            q: params.term,
            layanan_kalibrasi: layanan_kalibrasi,
            layanan_pengujian: layanan_pengujian,
            layanan_pengujian_kalibrasi: layanan_pengujian_kalibrasi,
            layanan_kalibrasi_proteksi_radiasi: layanan_kalibrasi_proteksi_radiasi,
            layanan_inspeksi: layanan_inspeksi,
            layanan_pelayanan_uji_kesesuaian: layanan_pelayanan_uji_kesesuaian,
            layanan_pelayanan_penggantian_alat: layanan_pelayanan_penggantian_alat,
            layanan_uji_produk: layanan_uji_produk,
            layanan_pengukuran_paparan_radiasi_proteksi: layanan_pengukuran_paparan_radiasi_proteksi,
            tipe_permohonan: tipe_permohonan,
            page_limit: 50
          }
        }
      }
    });

    $('#id-alat').on('select2:select', function(e) {
      let data = e.params.data;
      if (data) {
        $('#harga_alat').val(data.tarif);
      }
    });

    $('#customer_approve').on('change', function() {
      if ($(this).is(':checked')) {
        $('#btn_permohonan').removeClass('disabled');
      } else {
        $('#btn_permohonan').addClass('disabled', 'disabled');
      }
    });
  });

  function calc_alat() {
    var qty = document.getElementById("qty");
    var harga_alat = document.getElementById("harga_alat");
    var total = document.getElementById("total");

    total.value = qty.value * harga_alat.value;
  }
</script>