<?php

include 'app-load.php';
is_login('user-level.view');

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('user-level.edit');
	$EDIT = db_first(" SELECT * FROM user_level WHERE LEVEL_ID='$ID' ");
}

if($OP=='delete'){
	is_login('user-level.delete');
	db_execute(" DELETE FROM user_level WHERE LEVEL_ID='$ID' ");
	header('location: user-level.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('LEVEL');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'LEVEL','ROLE'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INP = get_input($F);
			if($F=='ROLE')
			{
				if(is_array($INP) AND count($INP)>0)
				{
					$t = array();
					foreach($INP as $inp)
					{
						$t[] = $inp;
					}
				}
				$INP = json_encode($t);
			}
			$INSERT_VAL[] = "'".db_escape($INP)."'";
			$UPDATE_VAL[] = $F."='".db_escape($INP)."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('user-level.add');
			db_execute(" INSERT INTO user_level (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			is_login('user-level.edit');
			db_execute(" UPDATE user_level SET ".implode(',',$UPDATE_VAL)." WHERE LEVEL_ID='$ID' ");
			
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container" style="margin-top:70px;">
	<h1 style="margin-top:0px;" class="border-title">
		<?php echo ucfirst($OP) ?> User Level
		<a href="user-level.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="user-level-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	
	<?php include 'msg.php' ?>
	
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="user-level-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
	<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
	<div class="form-group">
		<label for="" class="col-sm-2 control-label">User Level</label>
		<div class="col-sm-10">
			<input type="text" name="LEVEL" value="<?php echo set_value('LEVEL',$EDIT->LEVEL) ?>" class="form-control">
		</div>
	</div>
	<div style="border-top:1px dashed #cccccc; margin-top:30px;">&nbsp;</div>
	<h3 style="margin-top:-15px;">Privileges</h3>
	<div style="border-top:1px dashed #cccccc; margin-bottom:0px;">&nbsp;</div>
	<?php
	$refs = array();
	$list = array();
	$DATA = db_fetch("
		SELECT *
		FROM user_module
		ORDER BY ORD ASC
	");
	if( count($DATA) > 0 ){
		foreach($DATA as $row){
			$thisref = & $refs[ $row->MODULE_ID ];
			$thisref = array_merge((array) $thisref,(array) $row);
			if ($row->PARENT_ID == 0) {
				$list[] = & $thisref;
			} else {
				$refs[$row->PARENT_ID]['child'][] = & $thisref;
			}
		}
	}
	$SAVED = json_decode(stripslashes($EDIT->ROLE),TRUE);
	print_role($list,$SAVED);
	?>
	</form>
</section>

<script>
$(document).ready(function(){
	$('input[name=LEVEL]').focus();
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