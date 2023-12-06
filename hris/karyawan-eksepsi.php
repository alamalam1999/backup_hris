<?php
include 'app-load.php';
include 'header.php';

$MODULE = 'EKSEPSI';
is_login();
$CU = current_user();

set_search($MODULE, array('sort','order','EKSEPSI_ID','EKSEPSI'));
if( get_input('clear') ) clear_search($MODULE, array('EKSEPSI_ID','EKSEPSI'));

$PAGE = (int) get_input('page');
$PER_PAGE = (int) get_input('rows');
$SORT = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'EKSEPSI_ID';
$ORDER = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';

if($PAGE < 1) $PAGE = 1;
$PER_PAGE = empty($PER_PAGE) ? 20 : $PER_PAGE;
$OFFSET = ($PAGE-1)*$PER_PAGE;

$where = '';
$wh = array();
$wh[] = " P.KARYAWAN_ID='$CU->KARYAWAN_ID' ";
if($EKSEPSI_ID = get_search($MODULE,'EKSEPSI_ID') AND !empty($COMPANY_ID)) $wh[] = " EKSEPSI_ID = '$EKSEPSI_ID' ";
if($EKSEPSI = get_search($MODULE,'EKSEPSI') AND !empty($EKSEPSI)) $wh[] = " UCASE(EKSEPSI) LIKE UCASE('%$EKSEPSI%') ";
if(count($wh)>0) $where = " WHERE " . implode(' AND ',$wh);
	
$DATA = db_fetch_limit("
	SELECT *
	FROM eksepsi P 
	LEFT JOIN periode R ON (R.PERIODE_ID=P.PERIODE_ID) 
	LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID) 
	{$where}
	ORDER BY $SORT $ORDER
", $PER_PAGE + 1, $OFFSET);

//LEFT JOIN periode PE ON (PE.PERIODE_ID=P.PERIODE_ID) 

$NEXT_PAGE_TRIGGER = isset($DATA[$PER_PAGE]) ? 1 : 0;
unset($DATA[$PER_PAGE]);
?>

<section class="container-fluid" style="margin-top:70px;">
	<h1>
		Pengajuan Eksepsi
		<a href="karyawan-eksepsi-action.php?op=add" class="btn btn-primary" style="margin-left:10px;"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>
	</h1>

	<form id="form" action="karyawan-eksepsi.php" method="GET">
	<table class="table table-bordered table-hover table-condensed table-striped" style="border-bottom:2px solid #cccccc;">
	<thead>
	<tr>
		<th style="width:5%;text-align:center;">NO</th>
		<th style="width:10%;text-align:center;">PERIODE</th>
		<th style="width:10%;text-align:center;">TGL MULAI</th>
		<th style="width:10%;text-align:center;">TGL SELESAI</th>
		<th style="width:20%;">KARYAWAN</th>
		<th style="width:10%;text-align:center;">JENIS</th>
		<th style="">KETERANGAN</th>
		<th style="width:10%;text-align:center;">STATUS</th>
		<th style="width:10%;text-align:center;">ACTION</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$ST = array(
		'PENDING' => '<span style="color:#0000ff;">PENDING</span>',
		'APPROVED' => '<span style="color:#00c000;">APPROVED</span>',
		'CANCEL' => '<span style="color:#ff0000;">CANCEL</span>',
	);
	$no = ($PAGE=='1') ? 0 : ($PAGE-1)*$PER_PAGE;
	?>
	<?php if(count($DATA)>0){ foreach($DATA as $row){ $no=$no+1; ?>
	<tr>
		<td style="text-align:center;"><?php echo $no ?></td>
		<td style="text-align:center;"><?php echo $row->PERIODE ?></td>
		<td style="text-align:center;"><?php echo cdate($row->TGL_MULAI,'d-M-Y') ?></td>
		<td style="text-align:center;"><?php echo cdate($row->TGL_SELESAI,'d-M-Y') ?></td>
		<td><?php echo $row->NAMA ?></td>
		<td style="text-align:center;"><?php echo $row->JENIS ?></td>
		<td><?php echo $row->KETERANGAN ?></td>
		<td style="text-align:center;"><?php echo isset($ST[$row->STATUS]) ? $ST[$row->STATUS] : '' ?></td>
		<td style="text-align:center;">
			<?php if( ! in_array($row->STATUS,array('CANCEL','APPROVED')) ){ ?>
			<a href="karyawan-eksepsi-action.php?op=edit&id=<?php echo $row->EKSEPSI_ID ?>" title="Update"><span class="label label-primary">UPDATE</span></a>
			<?php } ?>
			<?php if($row->STATUS=='PENDING'){ ?>
			<a href="karyawan-eksepsi-action.php?op=delete&id=<?php echo $row->EKSEPSI_ID ?>" onclick="return confirm('Yakin data akan di hapus?')" title="Delete">
				<span class="label label-danger">CANCEL</span>
			</a>
			<?php } ?>
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
	$('input[name=EKSEPSI]').keypress(function (e) {
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