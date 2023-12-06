<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM equipment WHERE EQUIPMENT_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM equipment WHERE EQUIPMENT_ID='$ID' ");
	db_execute(" DELETE FROM equipment_stock WHERE EQUIPMENT_ID='$ID' ");
	db_execute(" DELETE FROM equipment_used_detail WHERE EQUIPMENT_ID='$ID' ");
	header('location: master-equipment.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('NAMA');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$EQUIPMENT_ID = db_escape(get_input('EQUIPMENT_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'COMPANY_ID','NAMA','KETERANGAN','STOK_AWAL'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			db_execute(" INSERT INTO equipment (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			$DATE = date('Y-m-d');
			$MASUK = get_input('STOK_AWAL');
			db_execute(" INSERT INTO equipment_stock (TGL,EQUIPMENT_ID,EQUIPMENT_USED_ID,MASUK,KELUAR,SALDO,KETERANGAN) VALUES ('$DATE','$ID','0','$MASUK','0','$MASUK','FIRST STOCK') ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE equipment SET ".implode(',',$UPDATE_VAL)." WHERE EQUIPMENT_ID='$ID' ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/datepicker/js/bootstrap-datepicker.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Equipment
		<a href="master-equipment.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="master-equipment-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="master-equipment-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label class="col-sm-2 control-label">Company</label>
			<div class="col-sm-10">
				<?php echo dropdown('COMPANY_ID',dropdown_option('company','COMPANY_ID','COMPANY','ORDER BY COMPANY ASC'),set_value('COMPANY_ID',$EDIT->COMPANY_ID),' class="form-control" ') ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Equipment</label>
			<div class="col-sm-10">
				<input type="text" name="NAMA" value="<?php echo set_value('NAMA',$EDIT->NAMA) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Keterangan</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="4" name="KETERANGAN"><?php echo isset($EDIT->KETERANGAN) ? $EDIT->KETERANGAN : '' ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Stok Awal</label>
			<div class="col-sm-10">
				<input type="number" name="STOK_AWAL" value="<?php echo set_value('STOK_AWAL',$EDIT->STOK_AWAL) ?>" class="form-control" <?php if($OP=='edit') echo 'readonly'; ?>>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	
});
</script>

<?php
include 'footer.php';
?>