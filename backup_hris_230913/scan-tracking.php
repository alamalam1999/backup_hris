<?php

include 'app-load.php';

is_login('scan-tracking.view');

$RESULT = array();

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('DATE','KARYAWAN_ID');
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	}else{
		
		$KARYAWAN_ID = get_input('KARYAWAN_ID');
		$DATE = get_input('DATE');
		$RESULT = db_fetch("
			SELECT A.*, K.NAMA, K.NIK
			FROM log_mesin A
			LEFT JOIN karyawan K ON K.KARYAWAN_ID=A.PIN
			WHERE PIN='$KARYAWAN_ID' AND DATE(DATE)='$DATE'
		");
		
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
		<?php echo ucfirst($OP) ?> Log Scan Tracking
		<button class="btn btn-primary" onclick="$('#form').submit()" style="margin-left:10px;"><i class="fa fa-search"></i>&nbsp;&nbsp;View</button>
	</h1>
	<?php include 'msg.php' ?>
	
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	
	<form id="form" class="form-horizontal" action="scan-tracking.php" method="POST">
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Karyawan</label>
			<div class="col-sm-10">
				<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
				<?php
					$K = db_first(" SELECT KARYAWAN_ID,NIK,NAMA FROM karyawan WHERE KARYAWAN_ID='".db_escape(set_value('KARYAWAN_ID'))."' ");
					if(isset($K->KARYAWAN_ID)){
						echo '<option value="'.$K->KARYAWAN_ID.'" selected="selected">'.$K->NIK.' - '.$K->NAMA.'</option>';
					}
				?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Tanggal</label>
			<div class="col-sm-10">
				<input type="text" name="DATE" value="<?php echo set_value('DATE') ?>" class="form-control datepicker" autocomplete="off">
			</div>
		</div>
	</form>
	
	<div style="border-top:1px dashed #cccccc;">&nbsp;</div>
	
	<table class="table table-bordered">
	<thead>
	<tr>
		<td>No</td>
		<td>NIK</td>
		<td>Nama</td>
		<td>Tgl Scan</td>
	</tr>
	</thead>
	<tbody>
	<?php if(count($RESULT)>0){ foreach($RESULT as $key=>$row){ $no=$key+1; ?>
	<tr>
		<td><?php echo $no ?></td>
		<td><?php echo $row->NIK ?></td>
		<td><?php echo $row->NAMA ?></td>
		<td><?php echo date('d-F-Y, H:i',strtotime($row->DATE)) ?></td>
	</tr>
	<?php }} ?>
	</tbody>
	</table>
</section>

<script>
$(document).ready(function(){
	$('input[name=ADJUSMENT]').focus();
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