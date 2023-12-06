<?php
include 'app-load.php';
is_login();

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {
	is_login('company.edit');
	$EDIT = db_first(" SELECT * FROM company WHERE COMPANY_ID='$ID' ");
}
if ($OP == 'delete') {
	is_login('company.delete');
	db_execute(" DELETE FROM company WHERE COMPANY_ID='$ID' ");
	header('location: company.php');
	exit;
}

if (isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('COMPANY');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	$COMPANY_ID = db_escape(get_input('COMPANY_ID'));
	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
	} else if (!valid_email(get_input('EMAIL'))) {
		$ERROR[] = 'Email tidak benar, contoh : john_doe@host.com';
	} else {
		$FIELDS = array(
			'COMPANY', 'ALAMAT', 'TELPON', 'EMAIL', 'NPWP', 'BPJS', 'NAMA_BANK', 'NAMA_AKUN', 'NO_REKENING',
		);

		$d = array();
		foreach ($FIELDS as $F) {
			$INSERT_VAL[] = "'" . db_escape(get_input($F)) . "'";
			$UPDATE_VAL[] = $F . "='" . db_escape(get_input($F)) . "'";
		}

		if ($OP == '' or $OP == 'add') {
			is_login('company.add');
			db_execute(" INSERT INTO company (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		} else {
			db_execute(" UPDATE company SET " . implode(',', $UPDATE_VAL) . " WHERE COMPANY_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

$JS[] = 'static/datepicker/js/bootstrap-datepicker.min.js';
$CSS[] = 'static/datepicker/css/bootstrap-datepicker3.min.css';
include 'header.php';
?>

<section class="container-fluid" style="margin-top:25px;">
	<h1 class="border-title">
		<?php echo ucfirst($OP) ?> Company
		<a href="company.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if ($OP == 'edit') {
			echo '<a href="company-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
	</h1>
	<?php include 'msg.php' ?>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="company-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
		<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
		<?php /*<div class="form-group">
			<label for="" class="col-sm-2 control-label">Holding</label>
			<div class="col-sm-10">
				<?php 
				echo dropdown('HOLDING_ID',dropdown_option_default('holding','HOLDING_ID','HOLDING','ORDER BY HOLDING ASC','-- PILIH HOLDING --'),set_value('HOLDING_ID',$EDIT->HOLDING_ID),' class="form-control" id="holding"') 
				?>
			</div>
		</div>*/ ?>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Company</label>
			<div class="col-sm-10">
				<input type="text" name="COMPANY" value="<?php echo set_value('COMPANY', $EDIT->COMPANY) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Alamat</label>
			<div class="col-sm-6">
				<textarea name="ALAMAT" class="form-control" rows="5"><?php echo isset($EDIT->ALAMAT) ? $EDIT->ALAMAT : '' ?></textarea>
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">No Telepon</label>
			<div class="col-sm-10">
				<input type="text" name="TELPON" value="<?php echo set_value('TELPON', $EDIT->TELPON) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Email</label>
			<div class="col-sm-10">
				<input type="text" name="EMAIL" value="<?php echo set_value('EMAIL', $EDIT->EMAIL) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">No NPWP</label>
			<div class="col-sm-10">
				<input type="text" name="NPWP" value="<?php echo set_value('NPWP', $EDIT->NPWP) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">No BPJS</label>
			<div class="col-sm-10">
				<input type="text" name="BPJS" value="<?php echo set_value('BPJS', $EDIT->BPJS) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Nama Bank</label>
			<div class="col-sm-10">
				<input type="text" name="NAMA_BANK" value="<?php echo set_value('NAMA_BANK', $EDIT->NAMA_BANK) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">Atas Nama</label>
			<div class="col-sm-10">
				<input type="text" name="NAMA_AKUN" value="<?php echo set_value('NAMA_AKUN', $EDIT->NAMA_AKUN) ?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label for="" class="col-sm-2 control-label">No Rekening</label>
			<div class="col-sm-10">
				<input type="text" name="NO_REKENING" value="<?php echo set_value('NO_REKENING', $EDIT->NO_REKENING) ?>" class="form-control">
			</div>
		</div>
	</form>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		$('input[name=COMPANY]').focus();
		$('input').keypress(function(e) {
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