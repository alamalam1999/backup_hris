<?php
include 'app-load.php';
is_login('user-module.view');

$OP = get_input('op');
$SUB_OP = get_input('sub_op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if ($OP == 'edit' and empty($ID)) die('<p>Data tidak ditemukan.</p>');

if ($OP == 'edit') {
	is_login('user-module.edit');
	$EDIT = db_first(" SELECT * FROM user_module WHERE MODULE_ID='$ID' ");
}
if ($OP == 'delete') {
	is_login('user-module.delete');
	db_execute(" DELETE FROM user_module WHERE MODULE_ID='$ID' ");
	header('location: user-module.php');
	exit;
}
if ($SUB_OP == 'delete-role') {
	$CURRENT_PARAMS = isset($EDIT->PARAMS) ? json_decode(stripslashes($EDIT->PARAMS)) : array();
	if (!is_array($CURRENT_PARAMS)) $CURRENT_PARAMS = array();
	$SUB_ID = get_input('sub_id');
	unset($CURRENT_PARAMS[$SUB_ID]);
	$CURRENT_PARAMS = array_values($CURRENT_PARAMS);
	$PARAMS_JSON = json_encode($CURRENT_PARAMS);
	db_execute(" UPDATE user_module SET PARAMS='$PARAMS_JSON' WHERE MODULE_ID='$ID' ");
	// refresh data
	$EDIT = db_first(" SELECT * FROM user_module WHERE MODULE_ID='$ID' ");
}

if ($SUB_OP == 'add-role' and isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$PARAMS = $NEW_PARAMS = array();
	$CURRENT_PARAMS = isset($EDIT->PARAMS) ? json_decode(stripslashes($EDIT->PARAMS)) : array();
	if (!is_array($CURRENT_PARAMS)) $CURRENT_PARAMS = array();
	if (get_input('ROLE')) {
		$NEW_PARAMS[] = strtolower(get_input('ROLE'));
		$PARAMS = array_merge($CURRENT_PARAMS, $NEW_PARAMS);
		$PARAMS_JSON = json_encode($PARAMS);
		db_execute(" UPDATE user_module SET PARAMS='$PARAMS_JSON' WHERE MODULE_ID='$ID' ");
		// refresh data
		$EDIT = db_first(" SELECT * FROM user_module WHERE MODULE_ID='$ID' ");
	}
}

if ($SUB_OP == '' and isset($_SERVER['REQUEST_METHOD']) and strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
	$REQUIRE = array('MODULE');
	$ERROR_REQUIRE = 0;
	foreach ($REQUIRE as $REQ) {
		$IREQ = get_input($REQ);
		if ($IREQ == "") $ERROR_REQUIRE = 1;
	}

	if ($ERROR_REQUIRE) {
		$ERROR[] = 'Kolom ' . implode(',', $REQUIRE) . ' wajib di isi.';
	} else {

		$FIELDS = array(
			'PARENT_ID', 'MODULE', 'ORD'
		);

		$d = array();
		foreach ($FIELDS as $F) {
			$INSERT_VAL[] = "'" . db_escape(get_input($F)) . "'";
			$UPDATE_VAL[] = $F . "='" . db_escape(get_input($F)) . "'";
		}

		if ($OP == '' or $OP == 'add') {
			is_login('user-module.add');
			db_execute(" INSERT INTO user_module (" . implode(',', $FIELDS) . ") VALUES (" . implode(',', $INSERT_VAL) . ") ");
			$OP = 'edit';
			$ID = $DB->Insert_Id();
			header('location: ' . $_SERVER['PHP_SELF'] . '?op=edit&m=1&id=' . $ID);
			exit;
		} else {
			db_execute(" UPDATE user_module SET " . implode(',', $UPDATE_VAL) . " WHERE MODULE_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if (get_input('m') == '1') {
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:25px;">

	<h1 style="margin-top:0px;" class="border-title">
		<?php echo ucfirst($OP) ?> User Module
		&nbsp;&nbsp;&nbsp;<a href="user-module.php" class="btn btn-warning">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if ($OP == 'edit') {
			echo '<a href="user-module-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>';
		} ?>
	</h1>
	<?php
	include 'msg.php';

	$DATA = db_fetch("
		SELECT *
		FROM user_module
		ORDER BY ORD ASC
	");
	if (count($DATA) > 0) {
		foreach ($DATA as $row) {
			$thisref = &$refs[$row->MODULE_ID];
			$thisref = array_merge((array) $thisref, (array) $row);
			if ($row->PARENT_ID == 0) {
				$list[] = &$thisref;
			} else {
				$refs[$row->PARENT_ID]['child'][] = &$thisref;
			}
		}
	}
	$TREE_CHAR = '_____';
	$RS = hirearchy($list);
	$PARENT_OPTION = array('0' => ' -- AS PARENT --');
	if (count($RS) > 0) {
		foreach ($RS as $row) {
			$TREE = '';
			for ($i = 1; $i < $row->DEPTH; $i++) {
				$TREE .= $TREE_CHAR;
			}
			$PARENT_OPTION[$row->MODULE_ID] = '<span style="color:#cccccc;">' . $TREE . '</span>' . ' ' . strtoupper($row->MODULE);
		}
	}
	?>

	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<div class="col-md-6">
		<form id="form" class="form-horizontal" action="user-module-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
			<input type="hidden" name="CURR_ID" value="<?php echo $ID ?>">
			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Parent</label>
				<div class="col-sm-10">
					<?php echo dropdown('PARENT_ID', $PARENT_OPTION, set_value('PARENT_ID', $EDIT->PARENT_ID), ' class="form-control" ') ?>
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Module</label>
				<div class="col-sm-10">
					<input type="text" name="MODULE" value="<?php echo set_value('MODULE', $EDIT->MODULE) ?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Order</label>
				<div class="col-sm-10">
					<input type="text" name="ORD" value="<?php echo set_value('ORD', $EDIT->ORD) ?>" class="form-control" maxlength="10">
				</div>
			</div>
		</form>

		<?php if (isset($EDIT->MODULE_ID)) { ?>
			<h3>Role</h3>
			<form action="<?php echo $_SERVER['PHP_SELF'] . '?op=edit&id=' . $ID . '&sub_op=add-role' ?>" method="POST">
				<table class="table table-bordered table-hover table-condensed">
					<thead>
						<tr>
							<th style="width:10%;">NO</th>
							<th>ROLE</th>
							<th style="width:100px;text-align:center;">ACTION</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$CURRENT_PARAMS = isset($EDIT->PARAMS) ? json_decode(stripslashes($EDIT->PARAMS)) : array();
						if (count($CURRENT_PARAMS) > 0) {
							foreach ($CURRENT_PARAMS as $key => $param) {
						?>
								<tr>
									<td><?php echo ($key + 1) ?></td>
									<td><?php echo strtolower($param) ?></td>
									<td style="text-align:center;"><a href="<?php echo $_SERVER['PHP_SELF'] . '?op=edit&id=' . $ID . '&sub_op=delete-role&sub_id=' . $key ?>" class="label label-danger btn-xs">Delete</a></td>
								</tr>
						<?php }
						} ?>
						<tr>
							<td></td>
							<td><input type="text" name="ROLE" value="" class="form-control input-sm"></td>
							<td style="text-align:center;"><button class="btn btn-primary btn-sm" onclick="this.submit()">Add Role</button></td>
						</tr>
					</tbody>
				</table>
			</form>
		<?php } else { ?>
			<div class="alert alert-info alert-dismissible" role="alert">Role bisa di tambahkan setelah module di simpan terlebih dahulu.</div>
		<?php } ?>
	</div>
	<div class="col-md-6">
		<div style="padding-left:20px;border-left:1px dashed #cccccc;">
			<h4>Module</h4>
			<p>Penamaan module disesuaikan dengan nama file module, di rekomendasikan menggunakan huruf besar tanpa menggunakan spasi.</p>
			<p>Spasi di ganti dengan dash "-".</p>
			<p>Contoh penamaan modul : USER, USER-MODULE, COMPANY-PROFILE</p>
			<br>
			<h4>Role</h4>
			<p><b>ROLE</b> adalah variable hak akses yang akan mementukan method atau operasi apa saja yang bisa dilakukan oleh user dalam suatu module.</p>
			<p><b>ROLE</b> berupa operasi seperti view, add, edit, delete dan lain tergantung method yang ada di suatu module</p>
		</div>
	</div>
	<div style="clear:both;border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
</section>

<script>
	$(document).ready(function() {
		$('input[name=MODULE]').focus();
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