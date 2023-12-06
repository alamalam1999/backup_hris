<?php
include 'app-load.php';
include 'header.php';

$MODULE = 'TUGAS';
is_login('tugas.view');

set_search($MODULE, array('TUGAS'));
if( get_input('clear') ) clear_search($MODULE, array('TUGAS'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'TUGAS';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($TUGAS = get_search($MODULE,'TUGAS') AND !empty($TUGAS)) $wh[] = " UCASE(TUGAS) LIKE UCASE('%$TUGAS%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);
	
$DATA = db_fetch_limit("
	SELECT *
	FROM tugas A
	{$where}
	ORDER BY $SORT $ORDER
", $PER_PAGE + 1, $OFFSET);

$NEXT_PAGE_TRIGGER = isset($DATA[$PER_PAGE]) ? 1 : 0;
unset($DATA[$PER_PAGE]);
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1>
		<span style="color:#aaaaaa;">Appraisal</span> - Tugas
		<a href="tugas-action.php?op=add" class="btn btn-primary" style="margin-left:10px;"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>
	</h1>

	<form id="form" action="tugas.php" method="GET">
	<table class="table table-bordered table-hover table-condensed table-striped" style="border-bottom:2px solid #cccccc;">
	<thead>
	<tr>
		<th style="width:60px;text-align:center;">NO</th>
		<th style="">TUGAS</th>
		<th style="width:10%">KARYAWAN</th>
		<th style="width:10%;">FILE TUGAS</th>
		<th style="width:10%;">TGL MULAI</th>
		<th style="width:10%;">TGL SELESAI</th>
		<th style="width:10%;">STATUS</th>
		<th style="width:120px;text-align:center;">ACTION</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td></td>
		<td><input type="text" name="TUGAS" value="<?php echo get_search($MODULE,'TUGAS') ?>" style="width:100%;" class="form-control input-sm" placeholder=""></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
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
		<td><?php echo $row->TUGAS ?></td>
		<td><?php echo $row->KARYAWAN ?></td>
		<td><?php echo $row->FILE_TUGAS ?></td>
		<td><?php echo tgl($row->START_DATE) ?></td>
		<td><?php echo tgl($row->END_DATE) ?></td>
		<td><?php echo tgl($row->END_DATE) ?></td>
		<td><?php echo $row->STATUS ?></td>
		<td style="text-align:center;">
		<td style="text-align:center;">
			<a href="tugas-action.php?op=edit&id=<?php echo $row->TUGAS_ID ?>" title="Update"><span class="label label-primary">UPDATE</span></a>
			<a href="tugas-action.php?op=delete&id=<?php echo $row->TUGAS_ID ?>" onclick="return confirm('Yakin data akan di hapus?')" title="Delete">
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
</section>

<script>
$(document).ready(function(){
	$('input[name=TUGAS]').keypress(function (e) {
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