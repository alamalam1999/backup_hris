<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT E.*,K.COMPANY_ID,K.PROJECT_ID FROM equipment_used E LEFT JOIN karyawan K ON (K.KARYAWAN_ID=E.KARYAWAN_ID) WHERE EQUIPMENT_USED_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM equipment_used WHERE EQUIPMENT_USED_ID='$ID' ");
	header('location: equipment-used.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('KARYAWAN_ID','TANGGAL_TERIMA');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	//$EQUIPMENT_ID = db_escape(get_input('EQUIPMENT_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{
		$FIELDS = array(
			'KARYAWAN_ID','TANGGAL_TERIMA'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		$DATE = get_input('TANGGAL_TERIMA');

		if($OP=='' OR $OP=='add')
		{
			db_execute(" INSERT INTO equipment_used (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			$EQUIPMENT_KARYAWAN = get_input('EQUIPMENT_ID');
			if(is_array($EQUIPMENT_KARYAWAN) AND count($EQUIPMENT_KARYAWAN)){
				foreach($EQUIPMENT_KARYAWAN as $key=>$val){
					if($val != ''){
						$QTY 			= get_input('QTY');
						$QTY			= isset($QTY[$key]) ? $QTY[$key] : '';
						$EQUIPMENT_ID 	= $val;
						db_execute(" INSERT INTO equipment_used_detail (EQUIPMENT_USED_ID,EQUIPMENT_ID,QTY) VALUES ('$ID','$EQUIPMENT_ID','$QTY') ");
						$EQUIPMENT = db_first("SELECT SALDO FROM equipment_stock WHERE EQUIPMENT_ID ='$EQUIPMENT_ID' ORDER BY EQUIPMENT_STOCK_ID DESC LIMIT 1");
						$SALDO = $EQUIPMENT->SALDO - $QTY;
						db_execute(" INSERT INTO equipment_stock (TGL,EQUIPMENT_ID,EQUIPMENT_USED_ID,MASUK,KELUAR,SALDO,KETERANGAN) VALUES ('$DATE','$EQUIPMENT_ID','$ID','0','$QTY','$SALDO','EMPLOYEE USE') ");
					}
				}
			}
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE equipment_used SET ".implode(',',$UPDATE_VAL)." WHERE EQUIPMENT_USED_ID='$ID' ");
			db_execute(" DELETE FROM equipment_used_detail WHERE EQUIPMENT_USED_ID='$ID' ");
			db_execute(" DELETE FROM equipment_stock WHERE EQUIPMENT_USED_ID='$ID' ");
			$EQUIPMENT_KARYAWAN = get_input('EQUIPMENT_ID');
			if(is_array($EQUIPMENT_KARYAWAN) AND count($EQUIPMENT_KARYAWAN)){
				foreach($EQUIPMENT_KARYAWAN as $key=>$val){
					if($val != ''){
						$QTY 			= get_input('QTY');
						$QTY			= isset($QTY[$key]) ? $QTY[$key] : '';
						$EQUIPMENT_ID 	= $val;
						db_execute(" INSERT INTO equipment_used_detail (EQUIPMENT_USED_ID,EQUIPMENT_ID,QTY) VALUES ('$ID','$EQUIPMENT_ID','$QTY') ");
						$EQUIPMENT = db_first("SELECT SALDO FROM equipment_stock WHERE EQUIPMENT_ID ='$EQUIPMENT_ID' ORDER BY EQUIPMENT_STOCK_ID DESC LIMIT 1");
						$SALDO = $EQUIPMENT->SALDO - $QTY;
						//$DATE = date('Y-m-d');
						db_execute(" INSERT INTO equipment_stock (TGL,EQUIPMENT_ID,EQUIPMENT_USED_ID,MASUK,KELUAR,SALDO,KETERANGAN) VALUES ('$DATE','$EQUIPMENT_ID','$ID','0','$QTY','$SALDO','EMPLOYEE USE') ");
					}
				}
			}
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Equipment
		<a href="equipment-used.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="equipment-used-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="equipment-used-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label class="col-sm-2 control-label">Company</label>
			<div class="col-sm-10">
				<select name="COMPANY_ID" id="COMPANY_ID" class="form-control" style="width: 100%;">
				<?php
					$K = db_first(" SELECT * FROM company WHERE COMPANY_ID='".db_escape(set_value('COMPANY_ID',$EDIT->COMPANY_ID))."' ");
					if(isset($K->COMPANY_ID)){
						echo '<option value="'.$K->COMPANY_ID.'" selected="selected">'.$K->COMPANY.'</option>';
					}
				?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Unit</label>
			<div class="col-sm-10">
				<select name="PROJECT_ID" id="PROJECT_ID" class="form-control" style="width: 100%;">
				<?php
					$K = db_first(" SELECT * FROM project WHERE PROJECT_ID='".db_escape(set_value('PROJECT_ID',$EDIT->PROJECT_ID))."' ");
					if(isset($K->PROJECT_ID)){
						echo '<option value="'.$K->PROJECT_ID.'" selected="selected">'.$K->PROJECT.'</option>';
					}
				?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Karyawan</label>
			<div class="col-sm-10">
				<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control" style="width: 100%;">
				<?php
					$K = db_first(" SELECT KARYAWAN_ID,NIK,NAMA FROM karyawan WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$EDIT->KARYAWAN_ID))."' ");
					if(isset($K->KARYAWAN_ID)){
						echo '<option value="'.$K->KARYAWAN_ID.'" selected="selected">'.$K->NAMA.'</option>';
					}
				?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tanggal</label>
			<div class="col-sm-10">
				<input type="text" name="TANGGAL_TERIMA" value="<?php echo set_value('TANGGAL_TERIMA',$EDIT->TANGGAL_TERIMA) ?>" class="form-control datepicker" autocomplete="off">
			</div>
		</div>

		<div class="form-group">
			<label for="" class="col-sm-2 control-label">
				<a href="#" class="btn btn-info" id="btn-add-equipment"><span class="glyphicon glyphicon-plus"></span>&nbsp;Tambah Equipment</a>
			</label>
			<div class="col-sm-8" style="border-bottom: 1px solid #eee; padding: 0 0 20px 0; margin-left: 15px;">
				<label for="" class="col-sm-6 control-label" style="text-align: left;">
					Nama Equipment
				</label>
				<label for="" class="col-sm-4 control-label" style="text-align: center;">
					Qty
				</label>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">
			</label>
			<div class="col-sm-8">
				<div id="add-equipment">
					<?php $EQUIPMENT_USED = db_fetch("SELECT * FROM equipment_used_detail WHERE EQUIPMENT_USED_ID='$ID'");
					if(count($EQUIPMENT_USED) > 0){ foreach ($EQUIPMENT_USED as $key => $row) { ?>
					<div class="input-group" style="padding-top: 15px;">
						<div class="col-sm-8">
							<select class="form-control EQUIPMENT_ID" name="EQUIPMENT_ID[]">
								<?php 
								$EQUIPMENT = db_fetch("SELECT EQUIPMENT_ID,NAMA FROM equipment WHERE STOK_AWAL > 0");
								if(count($EQUIPMENT)>0){ foreach($EQUIPMENT as $EQ){ ?>
									<option value="<?php echo $EQ->EQUIPMENT_ID ?>" <?php if($row->EQUIPMENT_ID == $EQ->EQUIPMENT_ID) echo 'selected'; ?>>
										<?php echo $EQ->NAMA ?>
									</option>
								<?php }} ?>
							</select>
						</div>
						<div class="col-sm-2">
							<input type="number" name="QTY[]" value="<?php echo $row->QTY ?>" class="form-control">
						</div>
						<span class="input-group-btn">
							<button type="button" class="btn btn-danger btn-flat del-equipment" title="Delete Equipment">
								<span class="glyphicon glyphicon-trash btn-danger"></span>
							</button>
						</span>
					</div>
					<?php }} ?>
				</div>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	delete_equipment();

	$('#COMPANY_ID').select2({
		theme: "bootstrap",
		ajax: {
			url: 'company-ac.php',
			dataType: 'json',
		}
	});

	$('#COMPANY_ID').change(function(){
		data=$(this).select2('data')[0];
		$(this).find(':selected').attr('val',data.id);
	});

	$('#COMPANY_ID').on('select2:select', function (e) {
		$('#PROJECT_ID').val(null).trigger('change');
		$('#KARYAWAN_ID').val(null).trigger('change');
	});

	$('#PROJECT_ID').select2({
		theme: "bootstrap",
		ajax: {
			url:'project-equip.php',
			dataType: 'json',
			data: function (params) {
				company_id = $('#COMPANY_ID').find(':selected').attr('val');
				return {
					q: params.term,
					company_id: company_id,
					page_limit: 20
				}
			}
		}
	});

	$('#PROJECT_ID').change(function(){
		project=$(this).select2('data')[0];
		if (typeof(project) != "undefined"){
			$(this).find(':selected').attr('val',project.id);
		}
	});

	$('#PROJECT_ID').on('select2:select', function (e) {
		$('#KARYAWAN_ID').val(null).trigger('change');
	});

	$('#KARYAWAN_ID').select2({
		theme: "bootstrap",
		ajax: {
			url:'karyawan-equip.php',
			dataType: 'json',
			data: function (params) {
				project_id = $('#PROJECT_ID').find(':selected').attr('val');
				return {
					q: params.term,
					project_id: project_id,
					page_limit: 20
				}
			}
		}
	});

});

$(function() {
	equipment_dtl();
	$('#btn-add-equipment').click(function(i){
		$('#add-equipment').append('<div class="input-group" style="padding-top: 15px;"><div class="col-sm-8"><select class="form-control EQUIPMENT_ID" name="EQUIPMENT_ID[]"></select></div><div class="col-sm-2"><input type="number" name="QTY[]" value="" class="form-control"></div><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-equipment" title="Delete Equipment"><span class="glyphicon glyphicon-trash btn-danger"></span></button></span></div>');
		equipment_dtl();
	});


});

function delete_equipment(){
	$('#add-equipment').on('click', '.del-equipment', function(){
		$(this).closest('div').remove();
	});
}

function equipment_dtl() {
	$('.EQUIPMENT_ID').select2({
		theme: "bootstrap",
		ajax: {
			url:'equipment-dtl.php',
			dataType: 'json',
			data: function (params) {
				company_id = $('#COMPANY_ID').find(':selected').attr('val');
				return {
					q: params.term,
					company_id: company_id,
					page_limit: 20
				}
			}
		}
	});
}

</script>

<?php
include 'footer.php';
?>