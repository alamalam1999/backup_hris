<script type="text/javascript">
  $(document).ready(function() {
    $('#finalisasi').click(function() {
      swal({
          title: 'Anda Yakin?',
          text: 'Data yang telah di submit tidak akan bisa diubah kembali !',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((submit) => {
          if (submit) {
            document.getElementById('link_finalisasi').click();
          } else {
            swal('Silahkan Finalisasikan Data Anda');
          }
        });
    });
  });
</script>