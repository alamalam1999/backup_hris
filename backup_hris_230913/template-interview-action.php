<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM template WHERE TEMPLATE_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM template WHERE TEMPLATE_ID='$ID' ");
	db_execute(" DELETE FROM template_pertanyaan WHERE TEMPLATE_ID='$ID' ");
	header('location: template-interview.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('TEMPLATE');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$TEMPLATE_ID = db_escape(get_input('TEMPLATE_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'TEMPLATE'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			db_execute(" INSERT INTO template (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			$PERTANYAAN_INT = get_input('PERTANYAAN_ID');
			if(is_array($PERTANYAAN_INT) AND count($PERTANYAAN_INT)){
				foreach($PERTANYAAN_INT as $key=>$val){
					if($val != ''){
						$PERTANYAAN_ID 	= $val;
						db_execute(" INSERT INTO template_pertanyaan (TEMPLATE_ID,PERTANYAAN_ID) VALUES ('$ID','$PERTANYAAN_ID') ");
					}
				}
			}
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE template SET ".implode(',',$UPDATE_VAL)." WHERE TEMPLATE_ID='$ID' ");
			db_execute(" DELETE FROM template_pertanyaan WHERE TEMPLATE_ID='$ID' ");
			$PERTANYAAN_INT = get_input('PERTANYAAN_ID');
			if(is_array($PERTANYAAN_INT) AND count($PERTANYAAN_INT)){
				foreach($PERTANYAAN_INT as $key=>$val){
					if($val != ''){
						$PERTANYAAN_ID 	= $val;
						db_execute(" INSERT INTO template_pertanyaan (TEMPLATE_ID,PERTANYAAN_ID) VALUES ('$ID','$PERTANYAAN_ID') ");
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

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Template Interview
		<a href="template-interview.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="template-interview-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="template-interview-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Template Interview</label>
			<div class="col-sm-10">
				<input type="text" name="TEMPLATE" value="<?php echo set_value('TEMPLATE',$EDIT->TEMPLATE) ?>" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="" class="col-sm-2 control-label">
				<a href="#" class="btn btn-info" id="btn-add-pertanyaan"><span class="glyphicon glyphicon-plus"></span>&nbsp;Tambah Pertanyaan</a>
			</label>
			<div class="col-sm-8" style="border-bottom: 1px solid #eee; padding: 0 0 20px 0; margin-left: 15px;">
				<label for="" class="col-sm-6 control-label" style="text-align: left;">
					Pertanyaan Interview
				</label>
			</div>
		</div>

		<div class="form-group">
			<label for="" class="col-sm-2 control-label">
			</label>
			<div class="col-sm-8">
				<div id="add-pertanyaan">
					<?php $TEMPLATE_PERTANYAAN = db_fetch("SELECT * FROM template_pertanyaan WHERE TEMPLATE_ID='$ID'");
					if(count($TEMPLATE_PERTANYAAN) > 0){ foreach ($TEMPLATE_PERTANYAAN as $key => $row) { ?>
					<div class="input-group" style="padding-top: 15px;">
						<div class="col-sm-8">
							<select class="form-control" name="PERTANYAAN_ID[]">
								<?php 
								$PERTANYAAN = db_fetch("SELECT PERTANYAAN_ID,PERTANYAAN FROM pertanyaan");
								if(count($PERTANYAAN)>0){ foreach($PERTANYAAN as $PR){ ?>
									<option value="<?php echo $PR->PERTANYAAN_ID ?>" <?php if($row->PERTANYAAN_ID == $PR->PERTANYAAN_ID) echo 'selected'; ?>>
										<?php echo $PR->PERTANYAAN ?>
									</option>
								<?php }} ?>
							</select>
						</div>

						<span class="input-group-btn">
							<button type="button" class="btn btn-danger btn-flat del-pertanyaan" title="Delete Pertanyaan">
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
	$('input[name=TEMPLATE]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});
	delete_pertanyaan();
});

$(function() {
	$('#btn-add-pertanyaan').click(function(i){
		$('#add-pertanyaan').append('<div class="input-group" style="padding-top: 15px;"><div class="col-sm-8"><select class="form-control" name="PERTANYAAN_ID[]"><?php $PERTANYAAN = db_fetch("SELECT PERTANYAAN_ID,PERTANYAAN FROM pertanyaan"); if(count($PERTANYAAN)>0){ foreach($PERTANYAAN as $EQ){ ?><option value="<?php echo $EQ->PERTANYAAN_ID ?>"><?php echo $EQ->PERTANYAAN ?></option><?php }} ?></select></div><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-pertanyaan" title="Delete Pertanyaan"><span class="glyphicon glyphicon-trash btn-danger"></span></button></span></div>');
		return false;
	});
});

function delete_pertanyaan(){
	$('#add-pertanyaan').on('click', '.del-pertanyaan', function(){
		$(this).closest('div').remove();
	});
}
</script>

<?php
include 'footer.php';
?>