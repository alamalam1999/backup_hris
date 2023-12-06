<?php
include 'app-load.php';
is_login('smtp-setting.view');

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	is_login('smtp-setting.edit');
	$REQUIRE = array('GMAP_KEY');
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
			'GMAP_KEY' => get_input('GMAP_KEY'),
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
		Gmap Setting
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<div class="row">
		<div class="col-sm-8">
		<form id="form" class="form-horizontal" action="gmap-setting.php" method="POST">
			<?php /*<div class="form-group">
				<label class="col-sm-2 control-label">Enable</label>
				<div class="col-sm-10">
					<?php echo dropdown('RECAPTCHA_ENABLE',array('0' => 'No', '1' => 'Yes'),set_value('RECAPTCHA_ENABLE',get_option('RECAPTCHA_ENABLE')),' class="form-control" ') ?>
				</div>
			</div>*/ ?>
			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Gmap Key</label>
				<div class="col-sm-10">
					<input type="text" name="GMAP_KEY" value="<?php echo set_value('GMAP_KEY',get_option('GMAP_KEY')) ?>" class="form-control">
				</div>
			</div>
		</form>
		</div>
		<div class="col-sm-4">
			<p>Jika ingin mengaktifkan Gmap silakan dapatkan API key dengan mengunjungi halaman <a href="https://developers.google.com/maps/documentation/embed/get-api-key" target="_blank">https://developers.google.com/maps/documentation/embed/get-api-key</a></p>
		</div>
	</div>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=GMAP_KEY]').focus();
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