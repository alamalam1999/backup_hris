<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {
	is_login('shift.edit');
	$EDIT = db_first(" SELECT * FROM shift WHERE SHIFT_CODE='$ID' ");
}
if ($OP == 'delete') {
	is_login('shift.delete');
	db_execute(" DELETE FROM shift WHERE SHIFT_CODE='$ID' ");
	header('location: shift.php');
	exit;
}

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('SHIFT_CODE', 'START_TIME', 'START_BEGIN', 'START_END', 'FINISH_TIME', 'FINISH_BEGIN', 'FINISH_END');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
	} else {

		$FIELDS = array(
			'SHIFT_CODE', 'START_TIME', 'START_BEGIN', 'START_END', 'FINISH_TIME', 'FINISH_BEGIN', 'FINISH_END', 'SHIFT_COLOR', 'OVERNIGHT',
			'STATUS', 'LONGITUDE', 'LATITUDE'
		);

		$d = array();
		foreach ($FIELDS as $F) {
			$INSERT_VAL[] = "'" . db_escape(get_input($F)) . "'";
			$UPDATE_VAL[] = $F . "='" . db_escape(get_input($F)) . "'";
		}

		if ($OP == '' or $OP == 'add') {
			is_login('shift.add');
			db_execute(" INSERT INTO shift (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");
			$OP = 'edit';
			#$ID = $DB->Insert_Id();
			$ID = get_input('SHIFT_CODE');
			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		} else {
			db_execute(" UPDATE shift SET " . implode(',', $UPDATE_VAL) . " WHERE SHIFT_CODE='$ID' ");
			$EDIT = db_first(" SELECT * FROM shift WHERE SHIFT_CODE='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/spectrum/spectrum.js';
$CSS[] = 'static/spectrum/spectrum.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:25px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Shift
		<a href="shift.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if ($OP == 'edit') {
			echo '<a href="shift-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="shift-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="row">
			<div class="col-md-7">
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Shift Code</label>
					<div class="col-sm-10">
						<input type="text" name="SHIFT_CODE" value="<?php echo set_value('SHIFT_CODE', $EDIT->SHIFT_CODE) ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Start Time</label>
					<div class="col-sm-10">
						<input type="text" name="START_TIME" value="<?php echo set_value('START_TIME', $EDIT->START_TIME) ?>" class="form-control time">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Begin</label>
					<div class="col-sm-2">
						<input type="text" name="START_BEGIN" value="<?php echo set_value('START_BEGIN', $EDIT->START_BEGIN) ?>" class="form-control">
					</div>
					<label for="" class="col-sm-1 control-label">End</label>
					<div class="col-sm-2">
						<input type="text" name="START_END" value="<?php echo set_value('START_END', $EDIT->START_END) ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Finish Time</label>
					<div class="col-sm-10">
						<input type="text" name="FINISH_TIME" value="<?php echo set_value('FINISH_TIME', $EDIT->FINISH_TIME) ?>" class="form-control time">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Begin</label>
					<div class="col-sm-2">
						<input type="text" name="FINISH_BEGIN" value="<?php echo set_value('FINISH_BEGIN', $EDIT->FINISH_BEGIN) ?>" class="form-control">
					</div>
					<label for="" class="col-sm-1 control-label">End</label>
					<div class="col-sm-2">
						<input type="text" name="FINISH_END" value="<?php echo set_value('FINISH_END', $EDIT->FINISH_END) ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Shift Color</label>
					<div class="col-sm-10">
						<input type="text" name="SHIFT_COLOR" value="<?php echo set_value('SHIFT_COLOR', $EDIT->SHIFT_COLOR) ?>" class="form-control colorpicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Overnight</label>
					<div class="col-sm-10">
						<?php echo dropdown('OVERNIGHT', array('NO' => 'NO', 'YES' => 'YES',), set_value('OVERNIGHT', $EDIT->OVERNIGHT), ' class="form-control" ') ?>
					</div>
				</div>



				<!-- # Tambah AGUNG LONG LAT -->
				<?php
				/*
				<div class="form-group">
					<label for="" class="col-sm-2 control-label"></label>
					<label for="" class="col-sm-9 control-label" style="text-align: left; border-bottom: 2px solid #eee; padding-bottom: 10px;">
						Data tambahan
					</label>
				</div>
				*/
				?>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Latitide</label>
					<div class="col-sm-5">
						<input type="text" name="LATITUDE" value="<?php echo set_value('LATITUDE', $EDIT->LATITUDE) ?>" class="form-control" style="text-align:center;">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Longitude</label>
					<div class="col-sm-5">
						<input type="text" name="LONGITUDE" value="<?php echo set_value('LONGITUDE', $EDIT->LONGITUDE) ?>" class="form-control" style="text-align:center;">
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Status</label>
					<div class="col-sm-5">
						<?php echo dropdown('STATUS', array('OFFLINE' => 'OFFLINE', 'ONLINE' => 'ONLINE',  'WFH' => 'WFH'), set_value('STATUS', $EDIT->STATUS), ' class="form-control" ') ?>
					</div>
				</div>

			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		$('input[name=SHIFT_CODE]').focus();
		$('input').keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$('#form').submit();
			}
		});
		$(".colorpicker").spectrum({
			//color: "#ECC",
			showInput: true,
			className: "full-spectrum",
			showInitial: true,
			showPalette: true,
			showSelectionPalette: true,
			maxSelectionSize: 10,
			preferredFormat: "hex",
			//localStorageKey: "spectrum.demo",
			move: function(color) {

			},
			show: function() {

			},
			beforeShow: function() {

			},
			hide: function() {

			},
			change: function() {

			},
			palette: [
				["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
					"rgb(204, 204, 204)", "rgb(217, 217, 217)", "rgb(255, 255, 255)"
				],
				["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
					"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"
				],
				["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
					"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
					"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
					"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
					"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
					"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
					"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
					"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
					"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
					"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"
				]
			]
		});
	});
</script>

<?php
include 'footer.php';
?>