<?php
include 'app-load.php';
include 'header.php';

$MODULE = 'SP';
is_login('surat-peringatan.view');

set_search($MODULE, array('NAMA'));
if( get_input('clear') ) clear_search($MODULE, array('NAMA'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'NAMA';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'asc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
if($NAMA = get_search($MODULE,'NAMA') AND !empty($NAMA)) $wh[] = " UCASE(K.NAMA) LIKE UCASE('%$NAMA%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);

/*
echo "SELECT A.*,K.NIK,K.NAMA
	FROM sp A
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=A.KARYAWAN_ID)
	{$where}";
die();
*/

$DATA = db_fetch_limit("
	SELECT A.*,K.NIK,K.NAMA
	FROM sp A
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=A.KARYAWAN_ID)
	{$where}
	ORDER BY $SORT $ORDER
", $PER_PAGE + 1, $OFFSET);

$NEXT_PAGE_TRIGGER = isset($DATA[$PER_PAGE]) ? 1 : 0;
unset($DATA[$PER_PAGE]);
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1>
		<span style="color:#aaaaaa;">Appraisal</span> - Surat Peringatan
		<a href="surat-peringatan-action.php?op=add" class="btn btn-primary" style="margin-left:10px;"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>
	</h1>

	<form id="form" action="surat-peringatan.php" method="GET">
	<table class="table table-bordered table-hover table-condensed table-striped" style="border-bottom:2px solid #cccccc;">
	<thead>
	<tr>
		<th style="width:50px;text-align:center;">NO</th>
		<th style="width:20%;">NAMA</th>
		<th style="">PELANGGARAN</th>
		<th style="">KETERANGAN</th>
		<th style="width:5%;" class="text-center">SANKSI</th>
		<th style="width:8%;" class="text-center">TANGGAL</th>
		<th style="width:120px;text-align:center;">ACTION</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td></td>
		<td><input type="text" name="NAMA" value="<?php echo get_search($MODULE,'NAMA') ?>" style="width:100%;" class="form-control input-sm" placeholder=""></td>
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
		<td><?php echo $row->NAMA ?></td>
		<td><?php echo $row->PELANGGARAN ?></td>
		<td><?php echo $row->KETERANGAN ?></td>
		<td class="text-center"><?php echo $row->SANKSI ?></td>
		<td class="text-center"><?php echo tgl($row->TANGGAL) ?></td>
		<td style="text-align:center;">
			<a href="surat-peringatan-action.php?op=edit&id=<?php echo $row->SP_ID ?>" title="Update"><span class="label label-primary">UPDATE</span></a>
			<a href="surat-peringatan-action.php?op=delete&id=<?php echo $row->SP_ID ?>" onclick="return confirm('Yakin data akan di hapus?')" title="Delete">
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
	$('input[name=NAMA]').keypress(function (e) {
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