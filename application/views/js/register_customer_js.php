<script type="text/javascript">
  $(document).ready(function() {
    $('#city').select2({
      ajax: {
        url: '<?php echo site_url('auth/lookup_city') ?>',
        dataType: 'json',
        data: function(params) {
          return {
            q: params.term,
            page_limit: 100
          }
        }
      }
    });
  });
</script>