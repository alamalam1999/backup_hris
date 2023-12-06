<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('holiday.edit');
	$EDIT = db_first(" SELECT * FROM holiday WHERE HOLIDAY_ID='$ID' ");
}
if($OP=='delete'){
	is_login('holiday.delete');
	db_execute(" DELETE FROM holiday WHERE HOLIDAY_ID='$ID' ");
	header('location: holiday.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('DATE','HOLIDAY');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{
		$FIELDS = array(
			'DATE','HOLIDAY',
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}
	
		$YEAR = date('Y',strtotime(get_input('DATE')));
		$FIELDS[] = 'YEAR';
		$INSERT_VAL[] = "'".$YEAR."'";
		$UPDATE_VAL[] = "YEAR='".$YEAR."'";

		if($OP=='' OR $OP=='add')
		{
			is_login('holiday.add');
			db_execute(" INSERT INTO holiday (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE holiday SET ".implode(',',$UPDATE_VAL)." WHERE HOLIDAY_ID='$ID' ");
			
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
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
		<?php echo ucfirst($OP) ?> Holiday
		<a href="holiday.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="holiday-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="holiday-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">	
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Date</label>
			<div class="col-sm-10">
				<input type="text" name="DATE" value="<?php echo set_value('DATE',$EDIT->DATE) ?>" class="form-control datepicker" autocomplete="off">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Description</label>
			<div class="col-sm-10">
				<input type="text" name="HOLIDAY" value="<?php echo set_value('HOLIDAY',$EDIT->HOLIDAY) ?>" class="form-control">
			</div>
		</div>
		<?php /*<div class="form-group">
			<label for="" class="col-sm-2 control-label">Status</label>
			<div class="col-sm-10">
				<?php echo dropdown('STATUS',array('PENDING'=>'PENDING','APPROVED'=>'APPROVED','NOT APPROVED'=>'NOT APPROVED'),set_value('STATUS',$EDIT->STATUS),' class="form-control" ') ?>
			</div>
		</div>*/ ?>
	</form>
	<div style="border-top:1px dashed #cccccc;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=HOLIDAY]').focus();
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