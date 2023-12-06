<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('kpi.edit');
	$EDIT = db_first(" SELECT * FROM kpi WHERE KPI_ID='$ID' ");
}
if($OP=='delete'){
	is_login('kpi.delete');
	db_execute(" DELETE FROM kpi WHERE KPI_ID='$ID' ");
	header('location: kpi.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('INDICATOR','UNIT');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{

		$FIELDS = array(
			'INDICATOR','UNIT','STRUKTUR_ID',
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('kpi.add');
			db_execute(" INSERT INTO kpi (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			db_execute(" UPDATE kpi SET ".implode(',',$UPDATE_VAL)." WHERE KPI_ID='$ID' ");
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
$PARENT_OPTION = array('0' => ' -- JABATAN --');
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
		<?php echo ucfirst($OP) ?> Key Performance Index
		<a href="kpi.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="kpi-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="kpi-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Indicator</label>
			<div class="col-sm-10">
				<input type="text" name="INDICATOR" value="<?php echo set_value('INDICATOR',$EDIT->INDICATOR) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Unit</label>
			<div class="col-sm-10">
				<?php 
				echo dropdown('UNIT',array('PERSEN'=>'PERSEN','RUPIAH'=>'RUPIAH','ANGKA'=>'ANGKA','JAM'=>'JAM'),set_value('UNIT',$EDIT->UNIT),' class="form-control" id="UNIT"') 
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Jabatan</label>
			<div class="col-sm-10">
				<?php echo dropdown('STRUKTUR_ID',$PARENT_OPTION,set_value('STRUKTUR_ID',$EDIT->STRUKTUR_ID),' class="form-control" ') ?>
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=INDICATOR]').focus();
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