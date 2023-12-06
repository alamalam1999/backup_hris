<script type="text/javascript">
  $(document).ready(function() {
    $('#barcode_tld').select2({
      ajax: {
        url: '<?php echo site_url('tld_personil/lookup_barcode') ?>',
        dataType: 'json',
        data: function(params) {
          return {
            q: params.term,
            page_limit: 200
          }
        }
      }
    });
  });
</script>