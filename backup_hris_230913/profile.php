<?php
include 'app-load.php';
is_login();
$CU = current_user();

$ID = $CU->USER_ID;
$EDIT = new stdClass;
$EDIT = db_first(" SELECT * FROM user WHERE USER_ID='$ID' ");

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('NAMA', 'TELPON');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$USER_ID = db_escape(get_input('USER_ID'));
	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Field ' . implode(',', $REQUIRE) . ' required';
	} else {

		foreach ($_POST as $key => $val) {
			$FIELDS[$key] = $key;
		}
		unset($FIELDS['CURRENT_ID']);

		$d = array();
		foreach ($FIELDS as $F) {
			$UPDATE_VAL[$F] = $F . "='" . db_escape(get_input($F)) . "'";
		}

		$UPDATE_VAL['PASSWORD'] = "PASSWORD='" . md5(get_input('PASSWORD')) . "'";
		if (get_input('PASSWORD') == "") {
			unset($UPDATE_VAL['PASSWORD']);
		}

		/*
		$FIELDS[] = 'UPDATED_ON';
		$FIELDS[] = 'UPDATED_BY';
		$UPDATE_VAL['UPDATED_ON'] = "UPDATED_ON=NOW()";
		$UPDATE_VAL['UPDATED_BY'] = "UPDATED_BY='$CU->NAMA'";
		*/

		db_execute(" UPDATE user SET " . implode(',', $UPDATE_VAL) . " WHERE USER_ID='$ID' ");
		$SUCCESS = 'Saved.';

		$EDIT = db_first(" SELECT * FROM user WHERE USER_ID='$ID' ");
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Saved';
}

include 'header.php';

?>

<div class="container" style="margin-top:70px;">
	<div class="row">
		<div class="col-md-12">
			<h3 style="padding-bottom:10px;margin:0 0 20px;border-bottom:1px solid #f2f2f2;">Your Profile
				<button class="btn btn-primary" onclick="$('#form').submit()" style="margin-left:20px;"><span class="fa fa-save fa-mr"></span> Save</button>
			</h3>

			<?php include 'msg.php' ?>

			<form id="form" class="form-horizontal" action="profile.php" method="POST">
				<div class="form-group">
					<label for="" class="col-md-4 control-label">Nama Lengkap</label>
					<div class="col-md-4">
						<input type="text" name="NAMA" value="<?php echo set_value('NAMA', $EDIT->NAMA) ?>" class="form-control">
					</div>
				</div>

				<?php
				/*
				<div class="form-group">
					<label for="" class="col-md-2 control-label">Birth Date</label>
					<div class="col-md-10">
						<input type="text" name="BIRTH_DATE" value="<?php echo set_value('BIRTH_DATE',$EDIT->BIRTH_DATE) ?>" class="form-control datepicker">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Gender</label>
					<div class="col-sm-10">
						<?php echo dropdown('GENDER',array('MALE'=>'MALE','FEMALE'=>'FEMALE'),set_value('GENDER',$EDIT->GENDER),' class="form-control" ') ?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-md-2 control-label">Email</label>
					<div class="col-md-10">
						<input type="text" name="EMAIL" value="<?php echo set_value('EMAIL',$EDIT->EMAIL) ?>" class="form-control">
					</div>
				</div>
				*/ 
			 	?>

				<div class="form-group">
					<label for="" class="col-md-4 control-label">Nomor HP / Telepon</label>
					<div class="col-md-4">
						<input type="text" name="TELPON" value="<?php echo set_value('TELPON', $EDIT->TELPON) ?>" class="form-control">
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('input[name=USER]').focus();
		$('input').keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$('#form').submit();
			}
		});
	});
</script>

<?php
include 'footer.php';
?>