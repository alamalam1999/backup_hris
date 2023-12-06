<?php
require 'app-load.php';

is_login();
$CU = current_user();

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array(
		'PASSWORD', 'PASSCONF'
	);
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Fill required field (<span style="color:red;">*</span>';
	} else if (!db_exists(" SELECT 1 FROM user WHERE USER_ID='$CU->USER_ID' AND PASSWORD=md5('" . get_input('CURRENT_PASSWORD') . "') ")) {
		$ERROR[] = 'Wrong current password';
	} else if (get_input('PASSWORD') != get_input('PASSCONF')) {
		$ERROR[] = 'Password and Confirm Password doesnt match';
	} else {

		db_execute(" UPDATE user SET PASSWORD=md5('" . get_input('PASSWORD') . "') WHERE USER_ID='$CU->USER_ID' ");
		$SUCCESS = 'Password has changed';
	}
}

require 'header.php';
?>

<div class="container" style="margin-top:25px;">
	<div class="row">
		<div class="col-md-8 col-offset-2">

			<h3 style="padding-bottom:10px;margin:0 0 20px;border-bottom:1px solid #f2f2f2;">Ganti Password</h3>

			<?php include 'msg.php' ?>
			<form id="form" class="form-horizontal" action="<?php echo self() ?>" method="POST">
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Current Password <span style="color:red;">*</span></label>
					<div class="col-sm-9">
						<input type="password" name="CURRENT_PASSWORD" value="<?php echo set_value('CURRENT_PASSWORD') ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">New Password <span style="color:red;">*</span></label>
					<div class="col-sm-9">
						<input type="password" name="PASSWORD" value="<?php echo set_value('PASSWORD') ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Re-Type New Password <span style="color:red;">*</span></label>
					<div class="col-sm-9">
						<input type="password" name="PASSCONF" value="<?php echo set_value('PASSCONF') ?>" class="form-control">
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

<?php require 'footer.php' ?>