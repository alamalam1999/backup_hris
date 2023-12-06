<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');

/*if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM equipment WHERE EQUIPMENT_ID='$ID' ");
}

if($OP=='delete'){
	db_execute(" DELETE FROM equipment WHERE EQUIPMENT_ID='$ID' ");
	header('location: master-equipment.php');
	exit;
}*/

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('TGL','MASUK');
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
			'NO_KWITANSI','TGL','MASUK','KETERANGAN'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		$NO_KWITANSI	= db_escape(get_input('NO_KWITANSI'));
		$TGL			= db_escape(get_input('TGL'));
		$SALDO			= db_escape(get_input('SALDO'));
		$MASUK			= db_escape(get_input('MASUK'));
		$KETERANGAN		= db_escape(get_input('KETERANGAN'));
		$SALDO = $SALDO + $MASUK;
		if($OP=='add')
		{
			db_execute(" INSERT INTO equipment_stock (TGL,EQUIPMENT_ID,NO_KWITANSI,MASUK,SALDO,KETERANGAN) VALUES ('$TGL','$ID','$NO_KWITANSI','$MASUK','$SALDO','$KETERANGAN') ");
			//$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=add&m=1&id='.$ID);
			exit;
		}

		/*else
		{
			db_execute(" UPDATE equipment SET ".implode(',',$UPDATE_VAL)." WHERE EQUIPMENT_ID='$ID' ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		}*/
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
		<?php echo ucfirst($OP) ?> Stock &nbsp;
		<a href="equipment-stock.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php /* if($OP=='edit'){ echo '<a href="master-equipment-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } */?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<div class="row">
		<div class="col-md-5">
			<form id="form" class="form-horizontal" action="equipment-stock-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
				<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Equipment</label>
					<div class="col-sm-9">
						<?php $EQUIP = db_first(" SELECT NAMA FROM equipment WHERE EQUIPMENT_ID='$ID' ") ?>
						<input type="text" name="NAMA" value="<?php echo set_value('NAMA',$EQUIP->NAMA) ?>" class="form-control" disabled>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Saldo</label>
					<div class="col-sm-9">
						<?php $STOCK = db_first(" SELECT SALDO FROM equipment_stock WHERE EQUIPMENT_ID='$ID' ORDER BY EQUIPMENT_STOCK_ID DESC LIMIT 1 ") ?>
						<input type="number" name="SALDO" value="<?php echo set_value('QTY',$STOCK->SALDO) ?>" class="form-control" readonly>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">No Kwitansi</label>
					<div class="col-sm-9">
						<input type="text" name="NO_KWITANSI" value="<?php echo set_value('NO_KWITANSI',$EDIT->NO_KWITANSI) ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Tanggal</label>
					<div class="col-sm-9">
						<input type="text" name="TGL" value="<?php echo set_value('TGL',$EDIT->TGL) ?>" class="form-control datepicker">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Qty</label>
					<div class="col-sm-9">
						<input type="number" name="MASUK" value="<?php echo set_value('MASUK',$EDIT->MASUK) ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-3 control-label">Keterangan</label>
					<div class="col-sm-9">
						<textarea class="form-control" rows="4" name="KETERANGAN"><?php echo isset($EDIT->KETERANGAN) ? $EDIT->KETERANGAN : '' ?></textarea>
					</div>
				</div>
			</form>
		</div>
		<div class="col-md-7">
			<table class="table">
				<thead>
					<tr>
						<th style="width: 2%;">No</th>
						<th>Nama</th>
						<th style="width: 13%;">Tanggal</th>
						<th>No Kwitansi</th>
						<th style="width: 5%;">Masuk</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>
					<?php $EQUIPMENT_STOCK = db_fetch("SELECT * FROM equipment_stock WHERE EQUIPMENT_ID='$ID' AND KETERANGAN != 'FIRST STOCK' AND KETERANGAN != 'EMPLOYEE USE' ORDER BY EQUIPMENT_STOCK_ID ASC");
					if(count($EQUIPMENT_STOCK)>0){ foreach($EQUIPMENT_STOCK as $key=>$row){ ?>
					<tr>
						<td><?php echo $key+1 ?></td>
						<td><?php echo $EQUIP->NAMA ?></td>
						<td><?php echo tgl($row->TGL) ?></td>
						<td><?php echo $row->NO_KWITANSI ?></td>
						<td class="text-center"><?php echo $row->MASUK ?></td>
						<td><?php echo $row->KETERANGAN ?></td>
					</tr>
					<?php }} ?>
				</tbody>
			</table>
		</div>
	</div>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('.datepicker').datepicker({
		autoclose: true,
		format: 'yyyy-mm-dd',
		todayHighlight: true,
		orientation: 'bottom'
	});
});
</script>

<?php
include 'footer.php';
?>