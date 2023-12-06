<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('project.edit');
	$EDIT = db_first(" SELECT * FROM project WHERE PROJECT_ID='$ID' ");
}
if($OP=='delete'){
	is_login('project.delete');
	db_execute(" DELETE FROM project WHERE PROJECT_ID='$ID' ");
	header('location: project.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('PROJECT');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$PROJECT_ID = db_escape(get_input('PROJECT_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'COMPANY_ID','PROJECT','START_DATE','FINISH_DATE','NOTE','CUTOFF','STATUS','SHOWING'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('project.add');
			db_execute(" INSERT INTO project (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE project SET ".implode(',',$UPDATE_VAL)." WHERE PROJECT_ID='$ID' ");
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/date/jquery.plugin.min.js';
$JS[] = 'static/date/jquery.datepick.min.js';
$JS[] = 'static/date/jquery.datepick-id.js';
$CSS[] = 'static/date/jquery.datepick.css';

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Unit
		<a href="project.php" class="btn btn-warning" style="margin-left:5px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="project-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="project-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Unit</label>
			<div class="col-sm-5">
				<input type="text" name="PROJECT" value="<?php echo set_value('PROJECT',$EDIT->PROJECT) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Company</label>
			<div class="col-sm-5">
				<?php 
				echo dropdown('COMPANY_ID',dropdown_option_default('company','COMPANY_ID','COMPANY','ORDER BY COMPANY ASC','-- pilih company --'),set_value('COMPANY_ID',$EDIT->COMPANY_ID),' class="form-control" id="company"') 
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tipe Cutoff</label>
			<div class="col-sm-5">
				<?php 
				echo dropdown('CUTOFF',array('0' => 'Cut-off Absensi sama dengan Payroll','1' => 'Cut-off Absensi tidak sama dengan Payroll'),set_value('CUTOFF',$EDIT->CUTOFF),' class="form-control" ') 
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tanggal</label>
			<div class="col-sm-5">
			<div class="input-group input-daterange">
				<input type="text" class="form-control datepicker" name="START_DATE" value="<?php echo set_value('START_DATE',$EDIT->START_DATE) ?>" placeholder="Start date" autocomplete="off">
				<div class="input-group-addon">to</div>
				<input type="text" class="form-control datepicker" name="FINISH_DATE" value="<?php echo set_value('FINISH_DATE',$EDIT->FINISH_DATE) ?>" placeholder="Finish date" autocomplete="off">
			</div>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Status</label>
			<div class="col-sm-5">
				<?php 
				echo dropdown('STATUS',array('OPEN' => 'OPEN','CLOSE' => 'CLOSE'),set_value('STATUS',$EDIT->STATUS),' class="form-control" ') 
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tampilkan Saat Filter</label>
			<div class="col-sm-5">
				<?php 
				echo dropdown('SHOWING',array('1' => 'YA','0' => 'TIDAK'),set_value('SHOWING',$EDIT->SHOWING),' class="form-control" ') 
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Note</label>
			<div class="col-sm-5">
				<textarea name="NOTE" class="form-control" rows="5"><?php echo isset($EDIT->NOTE) ? $EDIT->NOTE : '' ?></textarea>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=PROJECT]').focus();
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