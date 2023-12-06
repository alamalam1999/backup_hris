<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('struktur.edit');
	$EDIT = db_first(" SELECT * FROM struktur WHERE STRUKTUR_ID='$ID' ");
}
if($OP=='delete'){
	is_login('struktur.delete');
	db_execute(" DELETE FROM struktur WHERE STRUKTUR_ID='$ID' ");
	header('location: struktur.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('PARENT_ID','STRUKTUR');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$STRUKTUR_ID = db_escape(get_input('STRUKTUR_ID'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'STRUKTUR','PARENT_ID','ORD',
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('struktur.add');
			db_execute(" INSERT INTO struktur (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE struktur SET ".implode(',',$UPDATE_VAL)." WHERE STRUKTUR_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

$DATA = db_fetch("
	SELECT *
	FROM struktur
	ORDER BY ORD ASC
");
$list = array();
if( count($DATA) > 0 ){
	foreach($DATA as $row){
		$thisref = & $refs[ $row->STRUKTUR_ID ];
		$thisref = array_merge((array) $thisref,(array) $row);
		if ($row->PARENT_ID == 0) {
			$list[] = & $thisref;
		} else {
			$refs[$row->PARENT_ID]['child'][] = & $thisref;
		}
	}
}
$TREE_CHAR = '_____';
$RS = hirearchy($list);
$PARENT_OPTION = array('0' => ' -- AS PARENT --');
if(count($RS)>0){
	foreach($RS as $row){
		$TREE = '';
		for($i=1; $i<$row->DEPTH; $i++){
			$TREE .= $TREE_CHAR;
		}
		$PARENT_OPTION[$row->STRUKTUR_ID] = '<span style="color:#cccccc;">'.$TREE.'</span>' . ' ' . strtoupper($row->STRUKTUR);
	}
}
		
$JS[] = 'static/datepicker/js/bootstrap-datepicker.min.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Struktur Organisasi
		<a href="struktur.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="struktur-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="struktur-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Parent</label>
			<div class="col-sm-10">
				<?php echo dropdown('PARENT_ID',$PARENT_OPTION,set_value('PARENT_ID',$EDIT->PARENT_ID),' class="form-control" ') ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Struktur</label>
			<div class="col-sm-10">
				<input type="text" name="STRUKTUR" value="<?php echo set_value('STRUKTUR',$EDIT->STRUKTUR) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Order</label>
			<div class="col-sm-10">
				<input type="text" name="ORD" value="<?php echo set_value('ORD',$EDIT->ORD) ?>" class="form-control">
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=STRUKTUR]').focus();
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