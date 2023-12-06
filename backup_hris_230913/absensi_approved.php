<?php
// belum update field approve_date & user_id di tabel 'tabel_absen'


include 'app-load.php';

$edit = get_input('edit');
$cek_approve = get_input('p');
$bx_cek_setuju = get_input('bx_cek_setuju');

if ($cek_approve) {
	$approve = $cek_approve;
} else $approve = 0;


if ($edit == 1) {
	$cek_arr = get_input('cek_approved');
	$in = arrayToString2($cek_arr, ',');

	$Q2 = " UPDATE tabel_absen
					SET APPROVE = $bx_cek_setuju
					WHERE ID_ABSEN  IN($in)";
	db_execute($Q2);
	// echo $Q2;
	// die();
}

is_login('absensi.view');

$Q = " SELECT A.*, K.NAMA FROM tabel_absen A
				LEFT JOIN karyawan K ON K.KARYAWAN_ID = A.ID_KARYAWAN
				WHERE  A.FROM_TBL = 1 AND A.APPROVE = $approve";
$data = db_fetch($Q);
// echo "<pre>";
// print_r($data); die();

// $JS[] = 'static/tipsy/jquery.tipsy.js';
// $CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<section class="container-fluid" style="padding-top: 20px;">

	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
					<li><a href="javascript:void(0)" id="btn_setuju"><i class="fa fa-plus"></i>&nbsp;&nbsp;Disetujui</a></li>
					<li><a href="javascript:void(0)" id="btn_tolak"><i class="fa fa-minus"></i>&nbsp;&nbsp;Ditolak</a></li>
				</ul>

			</div>
		</div>

		<div class="col-sm-2">
			<?php echo dropdown('SETUJU', array('0' => 'Belum disetuju', '1' => 'Sudah disetujui', '2' => 'Ditolak'), $approve, ' id="ID_SETUJU" class="form-control input-sm" '); ?>
		</div>

	</div>

	<section class="content ">
		<form id="myForm" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
			<input type="hidden" name="edit" value="1">
			<input type="hidden" id="bx_cek_setuju" name="bx_cek_setuju" value="1">
			<div id="t-responsive" class="table-responsive">
				<table id="DataTable" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>No</th>
							<th><input type="checkbox" name="checkall" id="checkall"> All</th>
							<th>Persetujuan</th>
							<th>Nama</th>
							<th>Tgl Jadwal</th>
							<th>Tgl Absen</th>
							<th>Jenis</th>
							<th>Status</th>
							<th>Foto</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no = 1;
						foreach ($data as $key => $a) {
							if ($a->APPROVE == 0) $setuju = "<i class='label label-warning'>Belum disetujui</i>";
							else if ($a->APPROVE == 1) $setuju = "<i class='label label-success'>Sudah disetujui</i>";
							else if ($a->APPROVE == 2) $setuju = "<i class='label label-danger'>Ditolak</i>";

							if ($a->JENIS == 1) $jenis = "Masuk";
							else if ($a->JENIS == 2) $jenis = "Keluar";

							if ($a->STATUS == 1) $status = "Absen";
							else if ($a->STATUS == 2) $status = "Terlambat";

							if (!empty($a->FOTO) and url_exists(base_url() . 'uploads/absen/' . rawurlencode($a->FOTO))) {
								$file = "<a href='" . base_url() . 'uploads/absen/' . rawurlencode($a->FOTO) . "' class='btn btn-warning btn-sm' target='_blank' download><i class='fa fa-photo'></i> Foto</a>";
							} else $file = '-';

							echo "
													<tr>
														<td>" . $no++ . "</td>
														<td>
															<input type='checkbox' name='cek_approved[]' id='cek_approved' value='$a->ID_ABSEN' class='checkme' >
														</td>
														<td>$setuju</td>
														<td>$a->NAMA</td>
														<td>" . cdate($a->TGL_JADWAL, 'd-m-Y') . "</td>
														<td>" . cdate($a->TGL_ABSEN, 'd-m-Y H:i') . "</td>
														<td>$jenis</td>
														<td>$status</td>
														<td>
															$file
														</td>
													</tr>
													";
						}


						?>
					</tbody>
				</table>
			</div>
		</form>

	</section>
</section>




<script type="text/javascript">
	$(document).ready(function() {

		$('#checkall').click(function() {
			$('input:checkbox').not(this).prop('checked', this.checked);
		});

		$("#btn_setuju").click(function() {
			// var sel = $('#cek_approved').val();
			var dataArray = [];
			$("input:checkbox[id=cek_approved]:checked").each(function() {
				dataArray.push($(this).val());
			});
			if (dataArray.length > 0) $("#myForm").submit();
			else alert("Tidak ada data yang di pilih");


		});

		$("#btn_tolak").click(function() {

			var dataArray = [];
			$("input:checkbox[id=cek_approved]:checked").each(function() {
				dataArray.push($(this).val());
			});

			if (dataArray.length > 0) {
				$('#bx_cek_setuju').val(2);
				$("#myForm").submit();
			} else alert("Tidak ada data yang di pilih");



		});

		$('#ID_SETUJU').change(function() {
			var p = $('#ID_SETUJU').val();
			window.location.href = "<?php echo $_SERVER['PHP_SELF'] . "?p=" ?>" + p;

			return false;
		});

		$('#DataTable').DataTable({
			"paging": true,
			"ordering": true,
			"searching": true,
			"info": false
		});


	});
</script>

<?php
include 'footer.php';
?>