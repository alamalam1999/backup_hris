<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM kota WHERE KOTA_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM kota WHERE KOTA_ID='$ID' ");
	header('location: kota.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('KOTA','PROVINSI_ID');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'KOTA','PROVINSI_ID'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			db_execute(" INSERT INTO kota (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE kota SET ".implode(',',$UPDATE_VAL)." WHERE KOTA_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Kabupaten/Kota
		<a href="kota.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="kota-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="kota-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
	<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
	<div class="form-group">
		<label class="col-sm-2 control-label">Kota<span style="color:#ff0000;">*</span></label>
		<div class="col-sm-10">
			<input type="text" name="KOTA" value="<?php echo set_value('KOTA',$EDIT->KOTA) ?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label">Provinsi<span style="color:#ff0000;">*</span></label>
		<div class="col-sm-10">
			<?php echo dropdown('PROVINSI_ID',dropdown_option('provinsi','PROVINSI_ID','PROVINSI','ORDER BY PROVINSI ASC'),set_value('PROVINSI_ID',$EDIT->PROVINSI_ID),' class="form-control" ') ?>
		</div>
	</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=KOTA]').focus();
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