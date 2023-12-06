<?php
require 'app-load.php';

is_login();

$CU = current_user();

$EKS = db_first(" SELECT COUNT(1) as cnt FROM eksepsi WHERE STATUS='PENDING' ");
$EKSEPSI = isset($EKS->cnt) ? $EKS->cnt : 0;

$LAM = db_first(" SELECT COUNT(1) as cnt FROM lamaran ");
$LAMARAN = isset($LAM->cnt) ? $LAM->cnt : 0;

$BIRTH_TRESHOLD = date('Y-m-d', mktime(0, 0, 0, date('m') + 5, date('d'), date('Y')));
$ULTAH = db_fetch("
	SELECT * FROM karyawan
	WHERE NIK <> '' AND STR_TO_DATE( CONCAT(YEAR(CURDATE()), '-', MONTH(TGL_LAHIR), '-', DAY(TGL_LAHIR) ), '%Y-%m-%d' ) = CURDATE()
");
$NEXT_ULTAH = db_fetch("
	SELECT * FROM karyawan
	WHERE
	NIK <> '' AND
	(STR_TO_DATE( CONCAT(YEAR(CURDATE()), '-', MONTH(TGL_LAHIR), '-', DAY(TGL_LAHIR) ), '%Y-%m-%d' ) > CURDATE()) AND
	(STR_TO_DATE( CONCAT(YEAR(CURDATE()), '-', MONTH(TGL_LAHIR), '-', DAY(TGL_LAHIR) ), '%Y-%m-%d' ) <= '$BIRTH_TRESHOLD')
	ORDER BY MONTH(TGL_LAHIR) ASC
	LIMIT 10
");

/*$JS[] = 'static/fullcalendar/moment.min.js';
$JS[] = 'static/fullcalendar/fullcalendar.min.js';
$CSS[] = 'static/fullcalendar/fullcalendar.min.css';
*/
require 'header.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>

<script src="static/moment/moment.min.js"></script>
<script src="static/fullcalendar/main.min.js"></script>
<script src="static/fullcalendar-daygrid/main.min.js"></script>
<script src="static/fullcalendar-timegrid/main.min.js"></script>
<script src="static/fullcalendar-interaction/main.min.js"></script>
<script src="static/fullcalendar-bootstrap/main.min.js"></script>

<?php
	$COUNT_KARYAWAN = db_first(" SELECT COUNT(KARYAWAN_ID) AS TOTAL FROM karyawan WHERE ST_KERJA='AKTIF' ")->TOTAL;

	$COUNT_EKSEPSI = db_first(" SELECT COUNT(EKSEPSI_ID) AS TOTAL FROM eksepsi WHERE  
	(
		TGL_MULAI < DATE_SUB(NOW(), INTERVAL 1 month)
		&& (PROSES_APPROVED = '1')
	)
	")->TOTAL;

	$COUNT_PAYROLL = db_first(" SELECT COUNT(TOTAL_GAJI_BERSIH) AS TOTAL FROM penggajian PN
	LEFT JOIN periode PR ON PR.PERIODE_ID = PN.PERIODE_ID
	 WHERE  
	(
		PR.TANGGAL_MULAI < DATE_SUB(NOW(), INTERVAL 1 month)
	)
	 ")->TOTAL;

	$T1 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 1 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T2 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 2 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T3 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 3 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T4 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 4 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T5 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 5 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T6 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 6 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T7 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 7 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T8 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 8 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T9 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 9 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T10 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 10 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T11 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 11 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;
	$T12 = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE TERLAMBAT = 1 AND ( MONTH(TGL_ABSEN) = 12 AND YEAR(TGL_ABSEN) = '2022' )  ")->TOTAL;

	$COUNT_ABSEN = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE STATUS = 0 AND JENIS_EKSEPSI = '' AND ( YEAR(TGL_JADWAL) = '2022' )  ")->TOTAL;
	$COUNT_CUTI = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE STATUS = 0 AND JENIS_EKSEPSI = 'CT' AND ( YEAR(TGL_JADWAL) = '2022' )  ")->TOTAL;
	$COUNT_SAKIT = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE STATUS = 0 AND JENIS_EKSEPSI = 'SAKIT' AND ( YEAR(TGL_JADWAL) = '2022' ) ")->TOTAL;
	$COUNT_HADIR = db_first(" SELECT COUNT(ID_ABSEN) AS TOTAL FROM tabel_absen WHERE STATUS = 1 AND JENIS_EKSEPSI = '' AND ( YEAR(TGL_JADWAL) = '2022' )  ")->TOTAL;

	$TOTAL_COUNT = round($COUNT_ABSEN+$COUNT_CUTI+$COUNT_SAKIT+$COUNT_HADIR);

	
	$ABSEN = round(($COUNT_ABSEN / $TOTAL_COUNT) * 100, 2);
	$CUTI = round(($COUNT_CUTI / $TOTAL_COUNT) * 100, 2);
	$SAKIT = round(($COUNT_SAKIT / $TOTAL_COUNT) * 100, 2);
	$HADIR = round(($COUNT_HADIR / $TOTAL_COUNT) * 100, 2);

	

?>
<div class="front-banner">
	
	<div class="container-fluid" style="padding-top:30px;">
		<div class="row">
			<!-- Grafik keterlambatan -->
			<div class="col-lg-6">
				<div class="panel panel-default" style="border-radius: 0; border: none;">
					<div class="panel-heading" style="background-color: #ffffff; border-radius: 0;">
						<h3 class="panel-title" style="color: #ffa500;">
							<strong>GRAFIK KETERLAMBATAN TAHUN 2022</strong>
						</h3>
					</div>
					<div class="panel-body">
						<canvas id="myChart" width="400" height="210"></canvas>
						<script>
							var ctx = document.getElementById('myChart').getContext('2d');
							var myChart = new Chart(ctx, {
								type: 'bar',
								data: {
									labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul','Ags', 'Sep', 'Okt', 'Nov', 'Des'],
									datasets: [{
										label: 'Jumlah Karyawan Terlambat',
										data: [<?= $T1 ?>, <?= $T2 ?>, <?= $T3 ?>, <?= $T4 ?>, <?= $T5 ?>, <?= $T6 ?>, <?= $T7 ?>, <?= $T8 ?>, <?= $T9 ?>, <?= $T10 ?>, <?= $T11 ?>, <?= $T12 ?>],
										backgroundColor: [
											'rgba(255, 99, 132, 0.2)',
											'rgba(54, 162, 235, 0.2)',
											'rgba(255, 206, 86, 0.2)',
											'rgba(75, 192, 192, 0.2)',
											'rgba(153, 102, 255, 0.2)',
											'rgba(153, 202, 255, 0.2)',
											'rgba(255, 159, 64, 0.2)'
										],
										borderColor: [
											'rgba(255, 99, 132, 1)',
											'rgba(54, 162, 235, 1)',
											'rgba(255, 206, 86, 1)',
											'rgba(75, 192, 192, 1)',
											'rgba(153, 102, 255, 1)',
											'rgba(153, 202, 255, 1)',
											'rgba(255, 159, 64, 1)'
										],
										borderWidth: 1
									}]
								},
								options: {
									scales: {
										yAxes: [{
											ticks: {
												beginAtZero: true
											}
										}]
									}
								}
							});
						</script>
					</div>
				</div>
			</div>
			<!-- Summary Data -->
			
			<div class="col-lg-2">
				<div class="row">
					<div class="col-lg-12" style="margin: 0;">
						<div class="media" style="border-radius: 8px; background-color: #fff; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
							<div class="media-left">
								<i class="fa fa-cog" aria-hidden="true" style="font-size: 64px; color: #52a4ba;"></i>
							</div>
							<div class="media-body" style="vertical-align: middle; text-align: right;">
								
								<h5 class="media-heading" style="color: #333;"><strong>TOTAL KARYAWAN <br><br><span class="h4"><?php echo $COUNT_KARYAWAN ?></span></strong></h5>
							</div>
						</div>
					</div>

					<div class="col-lg-12" style="margin: 10px 0;">
						<div class="media" style="border-radius: 8px; background-color: #fff; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
							<div class="media-left">
								<i class="fa fa-life-ring" aria-hidden="true" style="font-size: 54px; color: #f5c004;"></i>
							</div>
							<div class="media-body" style="vertical-align: middle; text-align: right;">
								<h5 class="media-heading" style="color: #333;"><strong>EKSEPESI BULAN LALU <br><br><span class="h4"><?php echo $COUNT_EKSEPSI ?></span></strong></h5>
							</div>
						</div>
					</div>

					<div class="col-lg-12" style="margin: 10px 0 20px;">
						<div class="media" style="border-radius: 8px; background-color: #fff; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
							<div class="media-left">
								<i class="fa fa-usd" aria-hidden="true" style="font-size: 64px; color: red;"></i>
							</div>
							<div class="media-body" style="vertical-align: middle; text-align: right;">
								<h5 class="media-heading" style="color: #333;"><strong>PAYROLL BULAN LALU <br><br><span class="h4"><?= currency($COUNT_PAYROLL) ?></span></strong></h5>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- Grafik absensi -->
			<div class="col-lg-4">
				<?php if (has_access('dashboard.admin')) { ?>
					<div id="chart1"></div>
				<?php } else { ?>
					<!-- <a href="karyawan-eksepsi.php" class="btn btn-info" style="font-size:20px;width:100%;margin-bottom:10px;">Pengajuan Eksepsi</a>
					<a href="karyawan-lembur.php" class="btn btn-warning" style="font-size:20px;width:100%;margin-bottom:10px;">Pengajuan Lembur</a>
					<a href="absensi.php" class="btn btn-primary" style="font-size:20px;width:100%;">Lihat Absensi</a> -->
				<?php } ?>
			</div>
		</div>

		<div class="row" style="padding: 5px 0 70px;">
			<?php if (has_access('dashboard.admin')) { ?>
				<!-- Shortcut menu -->
				<div class="col-lg-12">
					<div class="row">
						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="karyawan.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/karyawan.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MODUL <br> KARYAWAN</h4>
									</div>
								</div>
							</a>
						</div>

						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="penggajian.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/payroll.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MODUL <br> PAYROLL</h4>
									</div>
								</div>
							</a>
						</div>

						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="eksepsi.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/eksepsi.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MODUL <br> EKSEPSI</h4>
									</div>
								</div>
							</a>
						</div>

						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="mesin.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/mesin.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MESIN <br> ABSENSI</h4>
									</div>
								</div>
							</a>
						</div>

						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="absensi.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/absensi.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MODUL <br> ABSENSI</h4>
									</div>
								</div>
							</a>
						</div>

						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="periode.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/periode.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MODUL <br> PERIODE</h4>
									</div>
								</div>
							</a>
						</div>

						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="lamaran.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/lamaran.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MODUL <br> LAMARAN</h4>
									</div>
								</div>
							</a>
						</div>

						<div class="col-lg-4" style="margin: 10px 0;">
							<a href="jadwal.php">
								<div class="media" style="border-radius: 8px; background-color: #1f2837; padding: 15px; box-shadow: 3px 4px 6px 0px #666;">
									<div class="media-left">
										<img class="media-object" src="static/img/shortcut/jadwal.jpg" alt="">
									</div>
									<div class="media-body" style="vertical-align: middle;">
										<h4 class="media-heading" style="color: #ddd;">MODUL <br> JADWAL</h4>
									</div>
								</div>
							</a>
						</div>

					</div>
				</div>
			<?php } ?>
			<!-- Calendar -->
			<?php
			/*
			<div class="col-md-6">
				<div id="calendar" style="background-color: #fff; padding: 20px; margin-top: 10px;"></div>
			</div>
			*/
			?>
		</div>


		<div class="row" style="margin-top:15px;">
			<!-- <div class="col-md-6">
				<div class="panel panel-info">
					<div class="panel-body">
						<h5 style="color:#333;margin-top:0;margin-bottom:20px;font-weight:bold;"><span style="color:#ff0000;"><i class="fa fa-calendar"></i></span>&nbsp;&nbsp;Karyawan Berulang Tahun</h5>
						<ul class="media-list">
							<?php if (count($ULTAH) > 0) {
								foreach ($ULTAH as $key => $row) { ?>
							<?php
									$style = '';
									if ($key != count($ULTAH) - 1) {
										$style = 'border-bottom:1px solid #eee;';
									}
							?>
							<li class="media" style="<?php echo $style ?>">
								<div class="media-left">
									<a href="#">
										<img class="media-object" src="<?php echo base_url() . 'static/img/avatar.png'; ?><?php /*echo gravatar($row->EMAIL,100)*/ ?>" alt="" style="width:30px;">
									</a>
								</div>
								<div class="media-body">
									<h6 class="media-heading"><?php echo $row->NAMA ?></h6>
									<p><?php echo 'on <b>' . date('d-M', strtotime($row->TGL_LAHIR)) ?></b></p>
								</div>
							</li>
							<?php }
							} else { ?>
							<li>Tidak ada</li>
							<?php } ?>
						</ul>
						
						<h5 style="color:#333;margin-bottom:20px;font-weight:bold;"><i class="fa fa-calendar"></i>&nbsp;&nbsp;Karyawan yang akan Berulang Tahun</h5>
						<ul class="media-list">
							<?php if (count($NEXT_ULTAH) > 0) {
								foreach ($NEXT_ULTAH as $key => $row) { ?>
							<?php
									$style = '';
									if ($key != count($NEXT_ULTAH) - 1) {
										$style = 'border-bottom:1px solid #eee;';
									}
							?>
							<li class="media" style="<?php echo $style ?>">
								<div class="media-left">
									<a href="#">
										<img class="media-object" src="<?php echo base_url() . 'static/img/avatar.png'; ?><?php /*echo gravatar($row->EMAIL,100)*/ ?>" alt="" style="width:30px;">
									</a>
								</div>
								<div class="media-body">
									<h6 class="media-heading"><?php echo $row->NAMA ?></h6>
									<p><?php echo 'on <b>' . date('d-M', strtotime($row->TGL_LAHIR)) ?></b></p>
								</div>
							</li>
							<?php }
							} else { ?>
							<li>Tidak ada</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div> -->
			<!-- <div class="col-md-6 bg-green">
				<div id="calendar" style="background-color: #fff; padding: 20px;"></div>
			</div> -->
		</div>

	</div>
</div>

<script type="text/javascript">
	$(function() {
		/* initialize the external events
		 -----------------------------------------------------------------*/
		/*function ini_events(ele) {
		  ele.each(function () {

		    // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
		    // it doesn't need to have a start or end
		    var eventObject = {
		      title: $.trim($(this).text()) // use the element's text as the event title
		    }

		    // store the Event Object in the DOM element so we can get to it later
		    $(this).data('eventObject', eventObject)

		    // make the event draggable using jQuery UI
		    $(this).draggable({
		      zIndex        : 1070,
		      revert        : true, // will cause the event to go back to its
		      revertDuration: 0  //  original position after the drag
		    })

		  })
		}*/

		/*ini_events($('#external-events div.external-event'))*/

		/* initialize the calendar
		 -----------------------------------------------------------------*/
		//Date for the calendar events (dummy data)
		var date = new Date()
		var d = date.getDate(),
			m = date.getMonth(),
			y = date.getFullYear()

		var Calendar = FullCalendar.Calendar;
		var Draggable = FullCalendarInteraction.Draggable;

		var containerEl = document.getElementById('external-events');
		var checkbox = document.getElementById('drop-remove');
		var calendarEl = document.getElementById('calendar');

		// initialize the external events
		// -----------------------------------------------------------------

		/*new Draggable(containerEl, {
		  itemSelector: '.external-event',
		  eventData: function(eventEl) {
		    console.log(eventEl);
		    return {
		      title: eventEl.innerText,
		      backgroundColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
		      borderColor: window.getComputedStyle( eventEl ,null).getPropertyValue('background-color'),
		      textColor: window.getComputedStyle( eventEl ,null).getPropertyValue('color'),
		    };
		  }
		});*/

		var calendar = new Calendar(calendarEl, {
			plugins: ['bootstrap', 'interaction', 'dayGrid', 'timeGrid'],
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,timeGridDay'
			},
			'themeSystem': 'bootstrap',
			//Random default events
			events: [{
					title: 'All Day Event',
					start: new Date(y, m, 1),
					backgroundColor: '#f56954', //red
					borderColor: '#f56954', //red
					allDay: true
				},
				{
					title: 'Long Event',
					start: new Date(y, m, d - 5),
					end: new Date(y, m, d - 2),
					backgroundColor: '#f39c12', //yellow
					borderColor: '#f39c12' //yellow
				},
				{
					title: 'Meeting',
					start: new Date(y, m, d, 10, 30),
					allDay: false,
					backgroundColor: '#0073b7', //Blue
					borderColor: '#0073b7' //Blue
				},
				{
					title: 'Lunch',
					start: new Date(y, m, d, 12, 0),
					end: new Date(y, m, d, 14, 0),
					allDay: false,
					backgroundColor: '#00c0ef', //Info (aqua)
					borderColor: '#00c0ef' //Info (aqua)
				},
				{
					title: 'Birthday Party',
					start: new Date(y, m, d + 1, 19, 0),
					end: new Date(y, m, d + 1, 22, 30),
					allDay: false,
					backgroundColor: '#00a65a', //Success (green)
					borderColor: '#00a65a' //Success (green)
				},
				{
					title: 'Click for Google',
					start: new Date(y, m, 28),
					end: new Date(y, m, 29),
					url: 'http://google.com/',
					backgroundColor: '#3c8dbc', //Primary (light-blue)
					borderColor: '#3c8dbc' //Primary (light-blue)
				}
			],
			editable: true,
			droppable: true, // this allows things to be dropped onto the calendar !!!
			drop: function(info) {
				// is the "remove after drop" checkbox checked?
				if (checkbox.checked) {
					// if so, remove the element from the "Draggable Events" list
					info.draggedEl.parentNode.removeChild(info.draggedEl);
				}
			}
		});

		calendar.render();
		// $('#calendar').fullCalendar()

		/* ADDING EVENTS */
		/*var currColor = '#3c8dbc' //Red by default
		//Color chooser button
		var colorChooser = $('#color-chooser-btn')
		$('#color-chooser > li > a').click(function (e) {
		  e.preventDefault()
		  //Save color
		  currColor = $(this).css('color')
		  //Add color effect to button
		  $('#add-new-event').css({
		    'background-color': currColor,
		    'border-color'    : currColor
		  })
		})
		$('#add-new-event').click(function (e) {
		  e.preventDefault()
		  //Get value and make sure it is not null
		  var val = $('#new-event').val()
		  if (val.length == 0) {
		    return
		  }

		  //Create events
		  var event = $('<div />')
		  event.css({
		    'background-color': currColor,
		    'border-color'    : currColor,
		    'color'           : '#fff'
		  }).addClass('external-event')
		  event.html(val)
		  $('#external-events').prepend(event)

		  //Add draggable funtionality
		  ini_events(event)

		  //Remove event from text input
		  $('#new-event').val('')
		})*/
	});

	Highcharts.setOptions({
		colors: ['#4b9291', '#59273d', '#4a5758', '#e66f72']
	});

	// Build the chart
	Highcharts.chart('chart1', {
		legend: {
			backgroundColor: '#FCFFC5'
		},
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false,
			type: 'pie',
			backgroundColor: '#2f2f31',
			height: 370,
		},
		title: {
			text: 'GRAFIK ABSENSI TAHUN 2022',
			style: {
				color: '#dfdfdf'
			}
		},
		tooltip: {
			pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					format: '{point.percentage:.1f} %',
					connectorColor: 'silver',
					color: '#a0abba',
					style: {
						color: 'red'
					}
				},
				showInLegend: true
			}
		},
		series: [{
			name: 'CN',
			data: [
				<?php
				

				
				?> {
					name: 'ABSEN',
					y: <?php echo $ABSEN ?>,
					//sliced: true,
					//selected: true
				},
				{
					name: 'CUTI',
					y: <?php echo $CUTI ?>
				},
				{
					name: 'SAKIT',
					y: <?php echo $SAKIT ?>
				},
				{
					name: 'HADIR',
					y: <?php echo $HADIR ?>
				},
			]
		}]
	});
</script>

<?php require 'footer.php' ?>