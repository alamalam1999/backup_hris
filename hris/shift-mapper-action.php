<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('shift-mapper.edit');
	$EDIT = db_first(" SELECT * FROM shift_mapper WHERE MAPPER_ID='$ID' ");
}
if($OP=='delete'){
	is_login('shift-mapper.delete');
	db_execute(" DELETE FROM shift_mapper WHERE MAPPER_ID='$ID' ");
	header('location: shift-mapper.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('PROJECT_ID','VAR','VAL');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'PROJECT_ID','VAR','VAL'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('shift-mapper.add');
			db_execute(" INSERT INTO shift_mapper (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE shift_mapper SET ".implode(',',$UPDATE_VAL)." WHERE MAPPER_ID='$ID' ");
			$EDIT = db_first(" SELECT * FROM shift_mapper WHERE MAPPER_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Shift Mapper
		<a href="shift-mapper.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="shift-mapper-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="shift-mapper-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Unit</label>
					<div class="col-sm-8">
						<?php 
						echo dropdown('PROJECT_ID',project_option_filter(0),set_value('PROJECT_ID',$EDIT->PROJECT_ID),' class="form-control" ') 
						?>
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Source Code</label>
					<div class="col-sm-8">
						<input type="text" name="VAR" value="<?php echo set_value('VAR',$EDIT->VAR) ?>" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label for="" class="col-sm-4 control-label">Destination Code</label>
					<div class="col-sm-8">
						<select name="VAL" id="VAL" class="form-control">
						<?php
							$K = db_first(" SELECT SHIFT_CODE,START_TIME,FINISH_TIME FROM shift WHERE SHIFT_CODE='".db_escape(set_value('VAL',$EDIT->VAL))."' ");
							if(isset($K->SHIFT_CODE)){
								echo '<option value="'.$K->SHIFT_CODE.'" selected="selected">'.$K->SHIFT_CODE.' ('.$K->START_TIME.'-'.$K->FINISH_TIME.')</option>';
							}
						?>
						</select>
					</div>
				</div>
				<?php /*<div class="form-group">
					<label for="" class="col-sm-2 control-label">Destination Code</label>
					<div class="col-sm-10">
						<input type="text" name="VAL" value="<?php echo set_value('VAL',$EDIT->VAL) ?>" class="form-control">
					</div>
				</div>*/ ?>
			</div>
			<div class="col-md-6">
			
				<p>Shift Mapper berguna untuk mapping kode jadwal yang biasa digunakan akan tetapi tidak unique di semua proyek menjadi kode jadwal yang unique di semua proyek</p>
				<p>Misalkan kode R1 di proyek A bisa tidak sama di proyek B, maka di masing-masing proyek harus di mapping R1 di proyek A dan B termasuk kedalam kode sistem yang mana.</p>
				<p><b>Contoh : </b><br>
				R1 Proyek A = 06:00-14:00<br>
				R1 Proyek B = 06:00-15:00<br>
				maka pada mapper harus di set :<br>
				R1 Proyek A = A0601<br>
				R1 Proyek B = A0602<br>
				</p>
				
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=VAR]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});
	ac();
});
function ac()
{
	$('#VAL').select2({
		theme: "bootstrap",
		ajax: {
			url: 'shift-ac.php',
			dataType: 'json',
		}
	});
}
</script>

<?php
include 'footer.php';
?>