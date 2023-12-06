<?php
include 'app-load.php';
include 'header.php';

$MODULE = 'KOTA';
is_login('kota.view');

set_search($MODULE, array('sort','order','KOTA','PROVINSI'));
if( get_input('clear') ) clear_search($MODULE, array('KOTA','PROVINSI'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'KOTA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($PROVINSI = get_search($MODULE,'PROVINSI') AND !empty($PROVINSI)) $wh[] = " UCASE(PROVINSI) LIKE UCASE('%$PROVINSI%') ";
if($KOTA = get_search($MODULE,'KOTA') AND !empty($KOTA)) $wh[] = " UCASE(KOTA) LIKE UCASE('%$KOTA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);
	
$DATA = db_fetch_limit("
	SELECT *
	FROM kota A
	LEFT JOIN provinsi B ON (B.PROVINSI_ID=A.PROVINSI_ID)
	{$where}
	ORDER BY PROVINSI ASC, $SORT $ORDER
", $PER_PAGE + 1, $OFFSET);

$NEXT_PAGE_TRIGGER = isset($DATA[$PER_PAGE]) ? 1 : 0;
unset($DATA[$PER_PAGE]);
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1><span style="color:#aaaaaa;">
		Master</span> - Kabupaten/Kota
		<a href="kota-action.php?op=add" class="btn btn-primary" style="margin-left:10px;">
		<span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>
	</h1>
		
	<form id="form" action="kota.php" method="GET">
	<table class="table table-bordered table-hover table-condensed table-striped" style="border-bottom:2px solid #cccccc;">
	<thead>
	<tr>
		<th style="width:60px;text-align:center;">NO</th>
		<th style="">KOTA</th>
		<th style="width:200px;">PROVINSI</th>
		<th style="width:120px;text-align:center;">ACTION</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td></td>
		<td><input type="text" name="KOTA" value="<?php echo get_search($MODULE,'KOTA') ?>" style="width:100%;" class="form-control input-sm" placeholder=""></td>
		<td><input type="text" name="PROVINSI" value="<?php echo get_search($MODULE,'PROVINSI') ?>" style="width:100%;" class="form-control input-sm" placeholder=""></td>
		<td>
			<button type="submit" class="btn btn-sm btn-warning" style="width:100%;">
			<span class="glyphicon glyphicon-search"></span>&nbsp;&nbsp;Search</button>
		</td>
	</tr>
	<?php
	$no = ($PAGE=='1') ? 0 : ($PAGE-1)*$PER_PAGE;
	?>
	<?php if(count($DATA)>0){ foreach($DATA as $row){ $no=$no+1; ?>
	<tr>
		<td style="text-align:center;"><?php echo $no ?></td>
		<td><?php echo $row->KOTA ?></td>
		<td><?php echo $row->PROVINSI ?></td>
		<td style="text-align:center;">
			<a href="kota-action.php?op=edit&id=<?php echo $row->KOTA_ID ?>" title="Update"><span class="label label-primary">UPDATE</span></a>
			<a href="kota-action.php?op=delete&id=<?php echo $row->KOTA_ID ?>" onclick="return confirm('Yakin data akan di hapus?')" title="Delete">
				<span class="label label-danger">DEL</span>
			</a>
		</td>
	</tr>
	<?php }} ?>
	</tbody>
	</table>
	</form>
	<?php
	$PREV = $NEXT = 0;
	$PREV_URL = $NEXT_URL = 'javascript:void(0)';
	$PD = $ND = 'disabled';
	if($PAGE > 1){
		$PREV = $PAGE - 1;
		$PREV_URL = $_SERVER['PHP_SELF'].'?page='.$PREV;
		$PD = '';
	}
	if($NEXT_PAGE_TRIGGER=='1'){
		$NEXT = $PAGE + 1;
		$NEXT_URL = $_SERVER['PHP_SELF'].'?page='.$NEXT;
		$ND = '';
	}
	?>
	<div class="box-footer">
		<nav style="text-align:center;">
			<ul class="pagination" style="margin:0;">
			<li class="<?php echo $PD ?>">
				<a href="<?php echo $PREV_URL ?>" aria-label="Previous">
				<span aria-hidden="true">&laquo;</span> Prev
				</a>
			</li>
			<li><span style="color:#000;"><?php echo 'Page : '.$PAGE; ?></span></li>
			<li class="<?php echo $ND ?>">
				<a href="<?php echo $NEXT_URL ?>" aria-label="Next">
				Next <span aria-hidden="true">&raquo;</span>
				</a>
			</li>
			</ul>
		</nav>
	</div>
</section>

<script>
$(document).ready(function(){
	$('input[name=KOTA],input[name=PROVINSI]').keypress(function (e) {
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