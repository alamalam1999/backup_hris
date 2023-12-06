<?php
include 'app-load.php';
include 'header.php';

$MODULE = 'MODULE';
is_login('user-module.view');

set_search($MODULE, array('sort', 'order', 'MODULE'));
if (get_input('clear')) clear_search($MODULE, array('MODULE'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'ORD';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if ($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE - 1) * $PER_PAGE;

$where = '';
$wh = array();
if ($_MODULE = get_search($MODULE, 'MODULE') and !empty($_MODULE)) $wh[] = " UCASE(MODULE) LIKE UCASE('%$_MODULE%') ";
if (count($wh) > 0) $where = " WHERE " . implode(' AND ', $wh);

$refs = array();
$list = array();
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

$RS = hirearchy($list);
//echo '<br><br><br>'; print_r($RS);
?>

<section class="container-fluid" style="margin-top:25px;">
	<h1>User Module&nbsp;&nbsp;&nbsp;
		<a href="user-module-action.php?op=add" class="btn btn-sm btn-primary">
			<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>
	</h1>

	<form id="form" action="user-module.php" method="GET">
		<table class="table table-bordered table-hover table-condensed" style="border-bottom:2px solid #cccccc;">
			<thead>
				<tr>
					<th style="width:60px;text-align:center;">NO</th>
					<th style="">MODULE NAME</th>
					<th style="">ROLE</th>
					<th style="width:90px;text-align:center;">ORDER</th>
					<th style="width:140px;text-align:center;">ACTION</th>
				</tr>
			</thead>
			<tbody>
				<?php $no = 0;
				if (count($RS) > 0) {
					foreach ($RS as $row) {
						$no = $no + 1; ?>
						<?php
						$TREE_CHAR = '+----';
						$TREE = '';
						for ($i = 1; $i < $row->DEPTH; $i++) {
							$TREE .= $TREE_CHAR;
						}
						?>
						<tr>
							<td style="text-align:center;"><?php echo $no ?></td>
							<td><?php echo '<span style="color:#cccccc;">' . $TREE . '</span>' . ' ' . $row->MODULE ?></td>
							<td style=""><?php echo str_replace(array('"', '[', ']', ','), array('', '', '', '&nbsp;|&nbsp;'), $row->PARAMS) ?></td>
							<td style="text-align:center;"><?php echo $row->ORD ?></td>
							<td style="text-align:center;">
								<a href="user-module-action.php?op=edit&id=<?php echo $row->MODULE_ID ?>" title="Update"><span class="label label-primary">UPDATE</span></a>
								<a href="user-module-action.php?op=delete&id=<?php echo $row->MODULE_ID ?>" onclick="return confirm('Yakin data akan di hapus?')" title="Delete">
									<span class="label label-danger">DEL</span>
								</a>
							</td>
						</tr>
				<?php }
				} ?>
			</tbody>
		</table>
	</form>

</section>

<script>
	$(document).ready(function() {
		$('input[name=MODULE]').keypress(function(e) {
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