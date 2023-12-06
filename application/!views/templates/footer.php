</body>

</html>



<script src="<?php echo base_url() ?>assets/modules/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="<?php echo base_url() ?>assets/modules/popper.js/dist/umd/popper.min.js"></script>
<script src="<?php echo base_url() ?>assets/modules/jquery.nicescroll/dist/jquery.nicescroll.min.js"></script>
<script src="<?php echo base_url() ?>assets/modules/moment/min/moment.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/stisla.js"></script>

<!-- JS Libraies -->
<script src="<?php echo base_url() ?>assets/modules/select2/dist/js/select2.full.min.js"></script>
<script src="<?php echo base_url() ?>assets/modules/sweetalert/dist/sweetalert.min.js"></script>
<script src="<?php echo base_url() ?>assets/modules/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="<?php echo base_url() ?>assets/modules/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- Page Specific JS File -->

<!-- page script datatable-->
<!-- DataTables -->
<script src="<?= base_url(); ?>/assets/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>/assets/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>/assets/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url(); ?>/assets/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

<!-- Template JS File -->
<script src="<?php echo base_url() ?>assets/js/scripts.js"></script>
<script src="<?php echo base_url() ?>assets/js/custom.js"></script>

<?php if (isset($js_script)) $this->load->view($js_script); ?>

<script>
	$(document).ready(function() {
		/* Alert Notification */
		<?php if ($this->session->flashdata('success')) { ?>
			swal('<?php echo $this->session->flashdata('success'); ?>', {
				icon: 'success',
			});
		<?php } else if ($this->session->flashdata('error')) { ?>
			swal('<?php echo $this->session->flashdata('error'); ?>', {
				icon: 'error',
			});
		<?php } else if ($this->session->flashdata('failed')) { ?>
			swal('<?php echo $this->session->flashdata('failed'); ?>', {
				icon: 'error',
			});
		<?php } ?>





		// $('.date-range').daterangepicker({
		// 	locale: {
		// 		format: 'YYYY-MM-DD'
		// 	},
		// 	drops: 'down',
		// 	opens: 'right'
		// });

	});



	$(function() {
		$('input[name="dates"]').daterangepicker({
			autoUpdateInput: false,
			locale: {
				cancelLabel: 'Clear'
			}
		});

		$('input[name="dates"]').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
		});

		$('input[name="dates"]').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
		});
	});
</script>

<script>
	$(function() {

		$('#example1').DataTable({
			"paging": true,
			"lengthChange": true,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": false,
			"responsive": true,
		});
	});

	$(function() {

		$('#example2').DataTable({
			"paging": false,
			"lengthChange": false,
			"searching": true,
			"ordering": true,
			"info": true,
			"autoWidth": true,
			"responsive": true,
		});
	});
</script>

<script type="text/javascript">
	function notifikasi(pesan = "", link = "") {
		var result = confirm(pesan);
		if (result) {
			window.location = link;
		}
	}
</script>