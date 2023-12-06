<script type="text/javascript">
  $(document).ready(function() {
    $('#sarpelkes').select2({
      ajax: {
        url: '<?php echo site_url('auth/lookup_sarpelkes') ?>',
        dataType: 'json',
        data: function(params) {
          return {
            q: params.term,
            page_limit: 50
          }
        }
      }
    });
  });
</script>