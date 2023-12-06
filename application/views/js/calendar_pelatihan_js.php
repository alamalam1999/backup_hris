<script type="text/javascript">
  $("#myEvent1").fullCalendar({
    height: 'auto',
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'prev,next today'
    },

    editable: true,

    events: "<?php echo site_url('pelatihan/json') ?>",
    eventClick: function(calEvent, jsEvent, view) {
      alert('Judul Pelatihan: ' + calEvent.title);

      $(this).css('border-color', 'red');
    }

  });
</script>