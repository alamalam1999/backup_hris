<script type="text/javascript">
  function assign() {
    var id_tld_member = $("#select_personel").select2().find(":selected").data("id");
    var nama = $("#select_personel").select2().find(":selected").data("nama");
    var nik = $("#select_personel").select2().find(":selected").data("nik");
    var tgl_lahir = $("#select_personel").select2().find(":selected").data("tgl_lahir");
    var tempat_lahir = $("#select_personel").select2().find(":selected").data("tempat_lahir");
    var jenis_kelamin = $("#select_personel").select2().find(":selected").data("jenis_kelamin");

    $('#id_tld_member').val(id_tld_member);
    $('#nama_personel').val(nama);
    $('#nik').val(nik);
    $('#tgl_lahir').val(tgl_lahir);
    $('#tempat_lahir').val(tempat_lahir);
    $('#jenis_kelamin').val(jenis_kelamin);
  }
</script>