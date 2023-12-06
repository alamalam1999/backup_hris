<?php
include 'app-load.php';
is_login('smtp-setting.view');

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	is_login('smtp-setting.edit');
	$REQUIRE = array('SMTP_HOST','SMTP_PORT','SMTP_USER');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$DIVISI_ID = db_escape(get_input('DIVISI_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		set_option(array(
			'SMTP_HOST' => get_input('SMTP_HOST'),
			'SMTP_PORT' => get_input('SMTP_PORT'),
			'SMTP_SECURE' => get_input('SMTP_SECURE'),
			'SMTP_AUTH' => get_input('SMTP_AUTH'),
			'SMTP_USER' => get_input('SMTP_USER'),
			'SMTP_PASS' => get_input('SMTP_PASS'),
			'SMTP_TESTER' => get_input('SMTP_TESTER')
		));
		$SUCCESS = 'Data berhasil di simpan.';
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container" style="margin-top:70px;">
	<h1 class="border-title">
		SMTP Setting
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<button class="btn btn-success" onclick="document.location.href='smtp-tester.php'"><span class="glyphicon glyphicon-send"></span>&nbsp;&nbsp;Send Tes Mail</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="smtp-setting.php" method="POST">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">SMTP Host</label>
			<div class="col-sm-10">
				<input type="text" name="SMTP_HOST" value="<?php echo set_value('SMTP_HOST',get_option('SMTP_HOST')) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">SMTP Port</label>
			<div class="col-sm-10">
				<input type="text" name="SMTP_PORT" value="<?php echo set_value('SMTP_PORT',get_option('SMTP_PORT')) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">SMTP Secure</label>
			<div class="col-sm-10">
				<?php echo dropdown('SMTP_SECURE',array('tls' => 'tls', 'ssl' => 'ssl (deprecated)'),set_value('SMTP_SECURE',get_option('SMTP_SECURE')),' class="form-control" ') ?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-2 control-label">SMTP Auth</label>
			<div class="col-sm-10">
				<?php echo dropdown('SMTP_AUTH',array('yes' => 'yes', 'no' => 'no'),set_value('SMTP_AUTH',get_option('SMTP_AUTH')),' class="form-control" ') ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">SMTP User</label>
			<div class="col-sm-10">
				<input type="text" name="SMTP_USER" value="<?php echo set_value('SMTP_USER',get_option('SMTP_USER')) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">SMTP Password</label>
			<div class="col-sm-10">
				<input type="password" name="SMTP_PASS" value="<?php echo set_value('SMTP_PASS',get_option('SMTP_PASS')) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">SMTP Tester</label>
			<div class="col-sm-10">
				<input type="text" name="SMTP_TESTER" value="<?php echo set_value('SMTP_TESTER',get_option('SMTP_TESTER')) ?>" class="form-control">
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=DIVISI]').focus();
	$('input').keypress(function (e) {
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