<?php
include 'app-load.php';
is_login();


$MESIN = db_fetch(" SELECT * FROM mesin ORDER BY MESIN_ID ASC ");
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">

	<div class="row">
		<div class="col-lg-3">
			<?php include 'sidebar-import.php' ?>
		</div>
		<div class="col-lg-9">

			<h1 class="border-title">
				<?php echo ucfirst($OP) ?>Import Log
				<button id="btn-import" class="btn btn-primary" style="margin-left:10px;"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;&nbsp;&nbsp;Import</button>
			</h1>
			<?php include 'msg.php' ?>
			<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
			<form id="form" class="form-horizontal" action="mesin-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
				<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
				<?php if (count($MESIN) > 0) {
					foreach ($MESIN as $m) { ?>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="mesin[]" value="<?php echo $m->IP ?>"> <b><?php echo $m->NAMA . '</b> - ' . $m->IP ?>
									&nbsp;&nbsp;&nbsp;&nbsp;<?php echo (fp_connect($m->IP, $m->PORT)) ? '<span class="label label-success">ONLINE</span>' : '<span class="label label-danger">OFFLINE</span>' ?>
							</label>
						</div>
				<?php }
				} ?>
				<div style="padding:20px;">
					<div id="loading"></div>
					<div id="result"></div>
				</div>
			</form>
			<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>

		</div>
	</div> <!-- end row -->
</section>

<script type="text/javascript">
	var showLoading = function() {
		/**If element not found, do nothing*/
		if (!$("#loading").length > 0) return false;
		var d = $("#loading");
		d.html('Loading');
		var intervalLoading = setInterval(function() {
			d.html().length >= 13 ? d.html('Loading') : d.append('.');
		}, 300);
		return intervalLoading;
	}

	// jQuery on an empty object, we are going to use this as our queue
	var ajaxQueue = $({});
	var currentRequest = null;
	$.ajaxQueue = function(ajaxOpts) {
		// Hold the original complete function.
		var oldComplete = ajaxOpts.complete;
		// Queue our ajax request.
		ajaxQueue.queue(function(next) {
			// Create a complete callback to fire the next event in the queue.
			ajaxOpts.complete = function() {
				// Fire the original complete if it was there.
				if (oldComplete) {
					oldComplete.apply(this, arguments);
				}
				// Run the next query in the queue.
				next();
			};
			// Run the query.
			currentRequest = $.ajax(ajaxOpts);
		});
	};

	// Ajax Call
	function ajaxCall(id, total, loading) {
		$('#btn-import').hide();
		$.ajaxQueue({
			type: "POST",
			url: 'ajax.php?op=import_log&ip=' + id,
			dataType: 'json',
			async: true,
			cache: false,
			success: function(result) {
				status = '';
				if (result.status > 0) status = '<span style="color:#00cc00;">' + result.status + '</span>';
				else status = '<span style="color:#ff0000;">' + result.status + '</span>';
				$('#result').append('Process <b>' + num + '</b> of <b>' + total + '</b> queue. Machine ' + id + ' : ' + status + ' item saved.<br>');
				if (num == total) {
					$('#result').append($('<div/>').html('<br><br><span style="color:#00cc00;">Done</span>.'));
					clearTimeout(loading);
					$('#loading').html('');
					$('#btn-import').show();
				}
				num++;
			},
			error: function(e) {
				num++;
			}
		});
	}
	var num = 1;
	// On Click Event
	$("#btn-import").on("click", function(e) {
		e.preventDefault();
		ajaxQueue.clearQueue();
		if (currentRequest) {
			currentRequest.abort();
		}
		$('#result').html('');
		var DATA = $('input:checkbox:checked').map(function() {
			return this.value;
		}).get();
		TOTAL = DATA.length;
		if (TOTAL < 1) {
			$('#result').html('No machine selected.');
			return;
		}
		num = 1;
		loading = showLoading();
		for (var i = 0; i < TOTAL; i++) {
			if (i in DATA) {
				ajaxCall(DATA[i], TOTAL, loading);
			}
		}
	});
</script>

<?php
include 'footer.php';
?>