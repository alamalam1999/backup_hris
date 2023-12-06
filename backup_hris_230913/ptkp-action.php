<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('ptkp.edit');
	$EDIT = db_first(" SELECT * FROM ptkp WHERE PTKP_ID='$ID' ");
}
if($OP=='delete'){
	is_login('ptkp.delete');
	db_execute(" DELETE FROM ptkp WHERE PTKP_ID='$ID' ");
	header('location: ptkp.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('NAMA','NILAI');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$PTKP_ID = db_escape(get_input('PTKP_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'NAMA','NILAI'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('ptkp.add');
			db_execute(" INSERT INTO ptkp (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE ptkp SET ".implode(',',$UPDATE_VAL)." WHERE PTKP_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/datepicker/js/bootstrap-datepicker.min.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> PTKP
		<a href="ptkp.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="ptkp-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="ptkp-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<?php /*<div class="form-group">
			<label for="" class="col-sm-2 control-label">Holding</label>
			<div class="col-sm-10">
				<?php 
				echo dropdown('HOLDING_ID',dropdown_option_default('holding','HOLDING_ID','HOLDING','ORDER BY HOLDING ASC','-- PILIH HOLDING --'),set_value('HOLDING_ID',$EDIT->HOLDING_ID),' class="form-control" id="holding"') 
				?>
			</div>
		</div>*/ ?>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Status PTKP</label>
			<div class="col-sm-6">
				<input type="text" name="NAMA" value="<?php echo set_value('NAMA',$EDIT->NAMA) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Nilai</label>
			<div class="col-sm-6">
				<input type="number" name="NILAI" value="<?php echo set_value('NILAI',$EDIT->NILAI) ?>" class="form-control">
			</div>
		</div>
		
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=PTKP]').focus();
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