<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$SUB_OP = get_input('sub_op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	$EDIT = db_first(" SELECT * FROM potongan WHERE POTONGAN_ID='$ID' ");
}
if($OP=='delete'){
	db_execute(" DELETE FROM potongan WHERE POTONGAN_ID='$ID' ");
	header('location: potongan.php');
	exit;
}
if($SUB_OP=='delete-potongan'){
	$SUB_ID = get_input('sub_id');
	db_execute(" DELETE FROM potongan_detail WHERE POTONGAN_DETAIL_ID='$SUB_ID' ");
	$DATA_DETAIL = db_first(" SELECT SUM(JUMLAH_POTONGAN) AS TOTAL_POTONGAN FROM potongan_detail WHERE POTONGAN_ID='$ID' ");
	$TOTAL_POTONGAN = $DATA_DETAIL->TOTAL_POTONGAN;
	db_execute(" UPDATE potongan SET TOTAL_POTONGAN='$TOTAL_POTONGAN' ");
	// refresh data
	$EDIT = db_first(" SELECT * FROM potongan WHERE POTONGAN_ID='$ID' ");
}

if( $SUB_OP=='add-potongan' AND isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$NAMA_POTONGAN = get_input('NAMA_POTONGAN');
	$JUMLAH_POTONGAN = get_input('JUMLAH_POTONGAN');
	if($NAMA_POTONGAN)
	{
		db_execute(" INSERT INTO potongan_detail (POTONGAN_ID,NAMA_POTONGAN,JUMLAH_POTONGAN) VALUES ('$ID','$NAMA_POTONGAN','$JUMLAH_POTONGAN') ");
		$DATA_DETAIL = db_first(" SELECT SUM(JUMLAH_POTONGAN) AS TOTAL_POTONGAN FROM potongan_detail WHERE POTONGAN_ID='$ID' ");
		$TOTAL_POTONGAN = $DATA_DETAIL->TOTAL_POTONGAN;
		db_execute(" UPDATE potongan SET TOTAL_POTONGAN='$TOTAL_POTONGAN' ");
		// refresh data
		$EDIT = db_first(" SELECT * FROM potongan WHERE POTONGAN_ID='$ID' ");
	}
}

if( $SUB_OP=='' AND isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('PERIODE_ID','KARYAWAN_ID');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$POTONGAN_ID = db_escape(get_input('POTONGAN_ID'));
	$PERIODE_ID = get_input('PERIODE_ID');
	$KARYAWAN_ID = get_input('KARYAWAN_ID');
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{
		$FIELDS = array(
			'PERIODE_ID','KARYAWAN_ID','STATUS','KETERANGAN'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}
		
		$KARYAWAN_ID = get_input('KARYAWAN_ID');
		$rs = db_first(" SELECT PROJECT_ID FROM karyawan K LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID WHERE KARYAWAN_ID='$KARYAWAN_ID'  ");
		$PROJECT_ID = isset($rs->PROJECT_ID) ? $rs->PROJECT_ID : '';
		$FIELDS[] = 'PROJECT_ID';
		$INSERT_VAL[] = "'".$PROJECT_ID."'";
		$UPDATE_VAL[] = "PROJECT_ID='".$PROJECT_ID."'";

		if($OP=='' OR $OP=='add')
		{
			$exist = db_fetch(" SELECT 1 FROM potongan WHERE PERIODE_ID='$PERIODE_ID' AND KARYAWAN_ID='$KARYAWAN_ID' ");
			if( count($exist) > 0 ){
				$ERROR[] = 'Data yang anda input sudah terdaftar di database.';
			}else{
				db_execute(" INSERT INTO potongan (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
				$OP = 'edit';
				$ID = $DB->Insert_Id();
				header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
				exit;
			}
		}
		else
		{
			db_execute(" UPDATE potongan SET ".implode(',',$UPDATE_VAL)." WHERE POTONGAN_ID='$ID' ");
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
		<?php echo ucfirst($OP) ?> Potongan
		<a href="potongan.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="potongan-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="potongan-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Periode</label>
			<div class="col-sm-10">
				<?php echo dropdown('PERIODE_ID',dropdown_option('periode','PERIODE_ID','PERIODE','ORDER BY TANGGAL_MULAI DESC'),set_value('PERIODE_ID',$EDIT->PERIODE_ID),' class="form-control" ') ?>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Karyawan</label>
			<div class="col-sm-10">
				<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
				<?php
					$K = db_first(" SELECT KARYAWAN_ID,NIK,NAMA FROM karyawan WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID',$EDIT->KARYAWAN_ID))."' ");
					if(isset($K->KARYAWAN_ID)){
						echo '<option value="'.$K->KARYAWAN_ID.'" selected="selected">'.$K->NIK.' - '.$K->NAMA.'</option>';
					}
				?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Total Potongan</label>
			<div class="col-sm-10">
				<input type="text" name="TOTAL_POTONGAN" value="<?php echo set_value('TOTAL_POTONGAN',$EDIT->TOTAL_POTONGAN) ?>" class="form-control" readonly>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Keterangan</label>
			<div class="col-sm-10">
				<input type="text" name="KETERANGAN" value="<?php echo set_value('KETERANGAN',$EDIT->KETERANGAN) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Status</label>
			<div class="col-sm-10">
				<?php echo dropdown('STATUS',array('PENDING'=>'PENDING','APPROVED'=>'APPROVED','NOT APPROVED'=>'NOT APPROVED'),set_value('STATUS',$EDIT->STATUS),' class="form-control" ') ?>
			</div>
		</div>
		<?php /*<div class="form-group">
			<label for="" class="col-sm-2 control-label">Status</label>
			<div class="col-sm-10">
				<p class="form-control-static"><?php echo isset($EDIT->STATUS) ? $EDIT->STATUS : '' ?></p>
			</div>
		</div>*/ ?>
	</form>
	<div style="border-top:1px dashed #cccccc;">&nbsp;</div>
	<?php if(isset($EDIT->POTONGAN_ID)){ ?>
		<h3 style="margin-top: 0; color: #ff6600;">Detail Potongan</h3>
		<form action="<?php echo $_SERVER['PHP_SELF'].'?op=edit&id='.$ID.'&sub_op=add-potongan' ?>" method="POST">
		<table class="table table-bordered table-hover table-condensed">
		<thead>
		<tr>
			<th style="width:50px;" class="text-center">NO</th>
			<th>JENIS POTONGAN</th>
			<th class="text-right">JUMLAH POTONGAN</th>
			<th style="width:100px;text-align:center;">ACTION</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td></td>
			<td>
				<!-- <input type="text" name="ROLE" value="" class="form-control input-sm"> -->
				<select name="NAMA_POTONGAN" class="form-control input-sm">
					<option value="ANGSURAN">Angsuran</option>
					<option value="PINJAMAN">Pinjaman</option>
				</select>
			</td>
			<td><input type="text" name="JUMLAH_POTONGAN" class="form-control input-sm"></td>
			<td style="text-align:center;"><button class="btn btn-primary btn-sm" onclick="this.submit()">+ ADD</button></td>
		</tr>
		<?php
		$EDIT_DETAIL = db_fetch(" SELECT * FROM potongan_detail WHERE POTONGAN_ID='$ID' ");
		if(count($EDIT_DETAIL)>0){ foreach($EDIT_DETAIL as $key => $row){
		?>
		<tr>
			<td class="text-center"><?php echo ($key+1) ?></td>
			<td><?php echo $row->NAMA_POTONGAN ?></td>
			<td class="text-right"><?php echo $row->JUMLAH_POTONGAN ?></td>
			<td style="text-align:center;"><a href="<?php echo $_SERVER['PHP_SELF'].'?op=edit&id='.$ID.'&sub_op=delete-potongan&sub_id='.$row->POTONGAN_DETAIL_ID ?>" class="label label-danger btn-xs">Delete</a></td>
		</tr>
		<?php }} ?>
		</tbody>
		</table>
		</form>
	<?php }else{ ?>
		<div class="alert alert-info alert-dismissible" role="alert">Simpan terlebih dahulu, untuk menambah detail potongan.</div>
	<?php } ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=POTONGAN]').focus();
	$('input').keypress(function (e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});
	$('#KARYAWAN_ID').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-ac.php',
			dataType: 'json',
		}
	});
});
</script>

<?php
include 'footer.php';
?>