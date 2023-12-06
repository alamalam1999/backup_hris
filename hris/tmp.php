<?php

include 'app-load.php';

is_login('tmp.view');

$MODULE = 'TMP';

if (isset($_GET['generate'])) {
    is_login('tmp.generate');

    $PERIODE_ID = get_input('PERIODE_ID');
    $PROJECT_ID = get_input('PROJECT_ID');

    $PERIODE = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$PERIODE_ID' ");

    if (!isset($PERIODE->PERIODE_ID)) {
        header('location: tmp.php');
        exit;
    }

    if ($PERIODE->STATUS_PERIODE == 'CLOSED') {
        header('location: tmp.php?m=closed');
        exit;
    }

    $TAHUN = $PERIODE->TAHUN;
    $BULAN = $PERIODE->BULAN;
    $TGL_MULAI = $PERIODE->TANGGAL_MULAI;
    $TGL_SELESAI = $PERIODE->TANGGAL_SELESAI;

    db_execute(" DELETE FROM tmp WHERE PERIODE_ID='$PERIODE_ID' AND PROJECT_ID='$PROJECT_ID' ");

    $karyawan = db_fetch("
		SELECT K.*, J.* 
		FROM karyawan K
		LEFT JOIN jabatan J ON J.JABATAN_ID=K.JABATAN_ID
		WHERE J.PROJECT_ID='$PROJECT_ID' AND ST_KERJA='AKTIF'
	");

    if (count($karyawan) > 0) {
        foreach ($karyawan as $k) {
            $rs = db_fetch("
				SELECT *, K.SHIFT_CODE
				FROM shift_karyawan K
					LEFT JOIN shift S ON S.SHIFT_CODE = K.SHIFT_CODE
				WHERE
					KARYAWAN_ID ='$k->KARYAWAN_ID' AND
					(DATE >= '$TGL_MULAI' AND DATE <= '$TGL_SELESAI')
			");

            $SHIFT = array();
            if (count($rs) > 0) {
                foreach ($rs as $row) {
                    $SHIFT[$k->KARYAWAN_ID][$row->DATE] = $row;
                }
            }

            $SCAN = scan_tmp($k->KARYAWAN_ID, $TGL_MULAI, $TGL_SELESAI, $SHIFT);

            $TOTAL_HK = isset($SCAN['total_working_day']) ? $SCAN['total_working_day'] : '';
            $TOTAL_ATT = isset($SCAN['total_attendance']) ? $SCAN['total_attendance'] : '';
            $TOTAL_ALL_SAKIT = isset($SCAN['total_all_sakit']) ? $SCAN['total_all_sakit'] : '';
            $TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : '';
            $TOTAL_ALL_IJIN_CUTI = isset($SCAN['total_all_ijin_cuti']) ? $SCAN['total_all_ijin_cuti'] : '';
            $TOTAL_ABS = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : '';
            $TOTAL_LATE_EARLY = isset($SCAN['total_late_early']) ? $SCAN['total_late_early'] : '';
            $TOTAL_DINAS = isset($SCAN['total_dinas']) ? $SCAN['total_dinas'] : '';

            //$TOTAL_FP1 = isset($SCAN['total_fp1']) ? $SCAN['total_fp1'] : '';

            $TOTAL_FP1 = 0;
            $RANGE = date_range($TGL_MULAI, $TGL_SELESAI);
            if (count($RANGE) > 0) {
                foreach ($RANGE as $date) {
                    $S = isset($SCAN[$date]) ? $SCAN[$date] : array();
                    $_ABS = isset($S['absent']) ? $S['absent'] : 0;
                    $SCAN_IN = isset($S['scan_in']) ? $S['scan_in'] : '';
                    $SCAN_OUT = isset($S['scan_out']) ? $S['scan_out'] : '';


                    if ($_ABS == '1') {
                        if ($SCAN_IN || $SCAN_OUT) {
                            $TOTAL_FP1 += 1;
                        }
                    }
                }
            }

            $TOTAL_REAL_ATT = $TOTAL_ATT;
            $TOTAL_ATT = $TOTAL_ATT + $TOTAL_FP1;

            $TUNJ_MAKAN = 0;
            $TOTAL_TUNJ_AWAL_MAKAN = 0;
            $TOTAL_TUNJ_MAKAN = 0;

            if ($k->R_TUNJ_MAKAN == '1') {
                $TOTAL_WORKING_DAY = isset($SCAN['total_working_day']) ? $SCAN['total_working_day'] : 0;
                $TUNJ_MAKAN = $k->TUNJ_MAKAN;
                $TOTAL_TUNJ_AWAL_MAKAN = $TUNJ_MAKAN * $TOTAL_WORKING_DAY;
                $TOTAL_TUNJ_MAKAN = $TUNJ_MAKAN * $TOTAL_WORKING_DAY;

                if ($k->R_POT_ABSEN_TUNJ_MAKAN == '1') {
                    $TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
                    $TOTAL_TUNJ_MAKAN = $TOTAL_TUNJ_MAKAN - ($k->TUNJ_MAKAN * $TOTAL_ABSENT);
                }

                if ($k->R_POT_NSKD_TUNJ_MAKAN == '1') {
                    $TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
                    $TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
                    $TOTAL_TUNJ_MAKAN = $TOTAL_TUNJ_MAKAN - ($k->TUNJ_MAKAN * ($TOTAL_SAKIT + $TOTAL_IJIN));
                }

                if ($k->R_POT_LATE_TUNJ_MAKAN == '1') {
                    $TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : 0;
                    $TOTAL_TUNJ_MAKAN = $TOTAL_TUNJ_MAKAN - ($k->TUNJ_MAKAN * $TOTAL_LATE);
                    if ($TOTAL_TUNJ_MAKAN < 0) $TOTAL_TUNJ_MAKAN = 0;
                }

                if ($k->R_POT_EARLY_TUNJ_MAKAN == '1') {
                    $TOTAL_EARLY = isset($SCAN['total_early']) ? $SCAN['total_early'] : 0;
                    $TOTAL_TUNJ_MAKAN = $TOTAL_TUNJ_MAKAN - ($k->TUNJ_MAKAN * $TOTAL_EARLY);
                    if ($TOTAL_TUNJ_MAKAN < 0) $TOTAL_TUNJ_MAKAN = 0;
                }
            }

            $TUNJ_TRANSPORT = 0;
            $TOTAL_TUNJ_AWAL_TRANSPORT = 0;
            $TOTAL_TUNJ_TRANSPORT = 0;

            if ($k->R_TUNJ_TRANSPORT == '1') {
                $TOTAL_WORKING_DAY = isset($SCAN['total_working_day']) ? $SCAN['total_working_day'] : 0;
                $TUNJ_TRANSPORT = $k->TUNJ_TRANSPORT;
                $TOTAL_TUNJ_AWAL_TRANSPORT = $TUNJ_TRANSPORT * $TOTAL_WORKING_DAY;
                $TOTAL_TUNJ_TRANSPORT = $TUNJ_TRANSPORT * $TOTAL_WORKING_DAY;

                if ($k->R_POT_ABSEN_TUNJ_TRANSPORT == '1') {
                    $TOTAL_ABSENT = isset($SCAN['total_absent']) ? $SCAN['total_absent'] : 0;
                    $TOTAL_TUNJ_TRANSPORT = $TOTAL_TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * $TOTAL_ABSENT);
                }

                if ($k->R_POT_NSKD_TUNJ_TRANSPORT == '1') {
                    $TOTAL_SAKIT = isset($SCAN['total_sakit']) ? $SCAN['total_sakit'] : 0;
                    $TOTAL_IJIN = isset($SCAN['total_ijin']) ? $SCAN['total_ijin'] : 0;
                    $TOTAL_TUNJ_TRANSPORT = $TOTAL_TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * ($TOTAL_SAKIT + $TOTAL_IJIN));
                }

                if ($k->R_POT_LATE_TUNJ_TRANSPORT == '1') {
                    $TOTAL_LATE = isset($SCAN['total_late']) ? $SCAN['total_late'] : 0;
                    $TOTAL_TUNJ_TRANSPORT = $TOTAL_TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * $TOTAL_LATE);
                    if ($TOTAL_TUNJ_TRANSPORT < 0) $TOTAL_TUNJ_TRANSPORT = 0;
                }

                if ($k->R_POT_EARLY_TUNJ_TRANSPORT == '1') {
                    $TOTAL_EARLY = isset($SCAN['total_early']) ? $SCAN['total_early'] : 0;
                    $TOTAL_TUNJ_TRANSPORT = $TOTAL_TUNJ_TRANSPORT - ($k->TUNJ_TRANSPORT * $TOTAL_EARLY);
                    if ($TOTAL_TUNJ_TRANSPORT < 0) $TOTAL_TUNJ_TRANSPORT = 0;
                }
            }

            $TOTAL_TUNJANGAN_AWAL = $TOTAL_TUNJ_AWAL_KOMUNIKASI + $TOTAL_TUNJ_AWAL_MAKAN + $TOTAL_TUNJ_AWAL_TRANSPORT;

            $TOTAL_PELANGGARAN = $TOTAL_LATE_EARLY;

            $TRANSPORT_DINAS = 0;
            $TRANSPORT_DINAS = db_first(" SELECT SUM(TRANSPORT_DINAS) AS TRANSPORT_DINAS FROM eksepsi WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ")->TRANSPORT_DINAS;

            $LEMBUR = 0;
            $LEMBUR = db_first(" SELECT SUM(UANG_LEMBUR) AS UANG_LEMBUR FROM lembur WHERE KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' AND STAFF=0 ")->UANG_LEMBUR;

            $DITRANSFER = 0;
            $DITRANSFER = $LEMBUR + $TRANSPORT_DINAS + $TOTAL_TUNJ_MAKAN + $TOTAL_TUNJ_TRANSPORT;

            $KETERANGAN = '';


            $DATA_DINAS = db_fetch(" SELECT * FROM eksepsi WHERE JENIS='DINAS' AND KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
            if (count($DATA_DINAS) > 0) {
                foreach ($DATA_DINAS as $key => $value) {
                    if ($value->TGL_MULAI == $value->TGL_SELESAI) {
                        $TANGGAL_DINAS = tgl($value->TGL_MULAI);
                    } else {
                        $TANGGAL_DINAS = tgl($value->TGL_MULAI) . ' - ' . tgl($value->TGL_SELESAI);
                    }

                    $KETERANGAN .= '[Dinas] ' . $TANGGAL_DINAS . ' : ' . $value->KETERANGAN . '(' . $value->JENIS_DINAS . ') \n';
                }
            }

            if ($SCAN['late_notes']) {
                $KETERANGAN .= $SCAN['late_notes'];
            }

            if ($SCAN['early_notes']) {
                $KETERANGAN .= $SCAN['early_notes'];
            }

            $DATA_IJIN = db_fetch(" SELECT * FROM eksepsi WHERE JENIS='IJIN' AND KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
            if (count($DATA_IJIN) > 0) {
                foreach ($DATA_IJIN as $key => $value) {
                    if ($value->TGL_MULAI == $value->TGL_SELESAI) {
                        $TANGGAL_IJIN = tgl($value->TGL_MULAI);
                    } else {
                        $TANGGAL_IJIN = tgl($value->TGL_MULAI) . ' - ' . tgl($value->TGL_SELESAI);
                    }

                    $KETERANGAN .= '[Izin] ' . $TANGGAL_IJIN . ' : ' . $value->KETERANGAN . '\n';
                }
            }

            $DATA_SAKIT = db_fetch(" SELECT * FROM eksepsi WHERE (JENIS='SAKIT' OR JENIS='SKD') AND KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
            if (count($DATA_SAKIT) > 0) {
                foreach ($DATA_SAKIT as $key => $value) {
                    if ($value->TGL_MULAI == $value->TGL_SELESAI) {
                        $TANGGAL_SAKIT = tgl($value->TGL_MULAI);
                    } else {
                        $TANGGAL_SAKIT = tgl($value->TGL_MULAI) . ' - ' . tgl($value->TGL_SELESAI);
                    }

                    $KETERANGAN .= '[Sakit] ' . $TANGGAL_SAKIT . ' : ' . $value->KETERANGAN . '\n';
                }
            }

            $DATA_CUTI = db_fetch(" SELECT * FROM eksepsi WHERE (JENIS='CI' OR JENIS='CT' AND JENIS='CM') AND KARYAWAN_ID='$k->KARYAWAN_ID' AND PERIODE_ID='$PERIODE_ID' AND STATUS='APPROVED' ");
            if (count($DATA_CUTI) > 0) {
                foreach ($DATA_CUTI as $key => $value) {
                    if ($value->TGL_MULAI == $value->TGL_SELESAI) {
                        $TANGGAL_CUTI = tgl($value->TGL_MULAI);
                    } else {
                        $TANGGAL_CUTI = tgl($value->TGL_MULAI) . ' - ' . tgl($value->TGL_SELESAI);
                    }

                    $KETERANGAN .= '[Cuti] ' . $TANGGAL_CUTI . ' : ' . $value->KETERANGAN . '\n';
                }
            }

            db_execute("
				INSERT IGNORE tmp
				(
				    PERIODE_ID, PROJECT_ID, KARYAWAN_ID, TOTAL_HK, TOTAL_ATT, TOTAL_ALL_SAKIT, TOTAL_ALL_IJIN_CUTI, TOTAL_ABS, TOTAL_FP1, TOTAL_LATE_EARLY, TOTAL_REAL_ATT, TOTAL_DINAS, TUNJ_KOMUNIKASI, TUNJ_MAKAN, TUNJ_TRANSPORT, TOTAL_TUNJ_MAKAN, TOTAL_TUNJ_TRANSPORT, TOTAL_TUNJ_KOMUNIKASI, TOTAL_TUNJANGAN_AWAL, TOTAL_PELANGGARAN, TRANSPORT_DINAS, LEMBUR, DITRANSFER, KETERANGAN
				)
				VALUES
				(
				    '$PERIODE_ID', '$PROJECT_ID', '$k->KARYAWAN_ID', '$TOTAL_HK', '$TOTAL_ATT', '$TOTAL_ALL_SAKIT', '$TOTAL_ALL_IJIN_CUTI', '$TOTAL_ABS', '$TOTAL_FP1', '$TOTAL_LATE_EARLY', '$TOTAL_REAL_ATT', '$TOTAL_DINAS', '$TUNJ_KOMUNIKASI', '$TUNJ_MAKAN', '$TUNJ_TRANSPORT', '$TOTAL_TUNJ_MAKAN', '$TOTAL_TUNJ_TRANSPORT', '$TOTAL_TUNJ_KOMUNIKASI', '$TOTAL_TUNJANGAN_AWAL', '$TOTAL_PELANGGARAN', '$TRANSPORT_DINAS', '$LEMBUR', '$DITRANSFER', '$KETERANGAN'
				)
			");
        }
    }

    //die();

    header('location: tmp.php?m=1');
    exit;
}

if (isset($_GET['export'])) {
    $PERIODE_ID = get_input('PERIODE_ID');
    $PROJECT_ID = get_input('PROJECT_ID');
    header('location: export-tmp.php?PERIODE_ID=' . $PERIODE_ID . '&PROJECT_ID=' . $PROJECT_ID);
}

$JS[] = 'static/tipsy/jquery.tipsy.js';
$CSS[] = 'static/tipsy/tipsy.css';
include 'header.php';
?>

<?php
if (get_input('m') == '1') {
    $SUCCESS = 'TMP berhasil dibuat';
}
if (get_input('m') == 'closed') {
    $ERROR[] = 'Tidak dapat membuat TMP<br>Periode sudah di tutup';
}
include 'msg.php';
?>

<section class="container-fluid">

    <div class="row" style="margin:10px 0;">
        <div class="col-sm-2">
            <div class="dropdown">
                <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
                    <i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dd1">
                    <li><a href="javascript:void(0)" id="btn-generate"><i class="fa fa-cog"></i>&nbsp;&nbsp;Generate</a></li>
                    <li><a href="javascript:void(0)" id="btn-export"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export</a></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-2">
            <?php echo dropdown('PERIODE_ID', dropdown_option('periode', 'PERIODE_ID', 'PERIODE', 'ORDER BY PERIODE_ID DESC'), get_search('TMP', 'PERIODE_ID'), ' id="PERIODE_ID" class="form-control input-sm" ') ?>
        </div>
        <div class="col-sm-2">
            <?php echo dropdown('PROJECT_ID', project_option_filter(0), get_search('TMP', 'PROJECT_ID'), ' id="PROJECT_ID" class="form-control input-sm" ') ?>
        </div>
        <div class="col-sm-2">
            <input type="text" id="NAMA" value="<?php echo get_search('TMP', 'NAMA') ?>" class="form-control input-sm" placeholder="Nama....">
        </div>
        <div class="col-sm-4">
            <h1 style="margin:0;text-align:right;">TMP</h1>
        </div>
    </div>

    <section class="content">
        <div id="t-responsive" class="table-responsive">
            <table id="t" style="min-height:200px;"></table>
        </div>
    </section>
</section>

<script>
    $(document).ready(function() {
        $('#t').datagrid({
            queryParams: {
                'PERIODE_ID': $('#PERIODE_ID').val(),
                'PROJECT_ID': $('#PROJECT_ID').val(),
                'NAMA': $('#NAMA').val()
            },
            url: 'tmp-json.php',
            fit: true,
            border: true,
            nowrap: false,
            striped: true,
            collapsible: true,
            remoteSort: true,
            sortName: 'JABATAN',
            sortOrder: 'asc',
            singleSelect: false,
            pagination: true,
            pageSize: 50,
            pageList: [50, 100],
            rownumbers: true,
            frozenColumns: [
                [{
                        field: 'KARYAWAN_ID',
                        title: 'ID',
                        width: 80,
                        sortable: true,
                        align: 'center'
                    },
                    {
                        field: 'NIK',
                        title: 'NIK',
                        width: 100,
                        sortable: true,
                        align: 'left'
                    },
                    {
                        field: 'NAMA',
                        title: 'Nama',
                        width: 180,
                        sortable: true,
                        align: 'left'
                    },
                    {
                        field: 'JABATAN',
                        title: 'Level Jabatan',
                        width: 140,
                        sortable: true,
                        align: 'center'
                    }
                ]
            ],
            columns: [
                [

                    {
                        field: 'TOTAL_HK',
                        title: 'Hari Efektif Kerja',
                        width: 130,
                        sortable: false,
                        align: 'center',
                        rowspan: 2
                    },
                    {
                        field: 'TOTAL_ATT',
                        title: 'Total Kehadiran',
                        width: 100,
                        sortable: false,
                        align: 'center',
                        rowspan: 2
                    },
                    {
                        title: 'Keterangan',
                        sortable: false,
                        align: 'center',
                        colspan: 7
                    },
                    {
                        title: 'Tunjangan',
                        sortable: false,
                        align: 'center',
                        colspan: 2
                    },
                    {
                        field: 'TOTAL_TUNJANGAN_AWAL',
                        title: 'Total Tunjangan (Sebelum Pemotongan)',
                        sortable: false,
                        align: 'right',
                        rowspan: 2
                    },
                    {
                        field: 'TOTAL_PELANGGARAN',
                        title: 'Total Pelanggaran',
                        sortable: false,
                        align: 'center',
                        rowspan: 2
                    },
                    {
                        field: 'TRANSPORT_DINAS',
                        title: 'Transport Dinas',
                        sortable: false,
                        align: 'right',
                        rowspan: 2
                    },
                    {
                        field: 'LEMBUR',
                        title: 'Lembur Non Staff',
                        sortable: false,
                        align: 'right',
                        rowspan: 2
                    },
                    {
                        field: 'DITRANSFER',
                        title: 'Yang ditransfer ke Rekening',
                        sortable: false,
                        align: 'right',
                        rowspan: 2
                    },
                    /*
                    {
                        field: 'KETERANGAN',
                        title: 'Keterangan',
                        sortable: false,
                        align: 'right',
                        rowspan: 2
                    },
                    */
                ],
                [{
                        field: 'TOTAL_ALL_SAKIT',
                        title: 'Sakit',
                        width: 100,
                        sortable: false,
                        align: 'center'
                    },
                    {
                        field: 'TOTAL_ALL_IJIN_CUTI',
                        title: 'Izin / Cuti',
                        width: 100,
                        sortable: false,
                        align: 'center'
                    },
                    {
                        field: 'TOTAL_ABS',
                        title: 'Tanpa Ket.',
                        width: 100,
                        sortable: false,
                        align: 'center'
                    },
                    {
                        field: 'TOTAL_FP1',
                        title: 'FP hanya sekali',
                        width: 100,
                        sortable: false,
                        align: 'center'
                    },
                    {
                        field: 'TOTAL_LATE_EARLY',
                        title: 'Telat / Pulang Cepat',
                        width: 120,
                        sortable: false,
                        align: 'center'
                    },
                    {
                        field: 'TOTAL_REAL_ATT',
                        title: 'Kehadiran',
                        width: 100,
                        sortable: false,
                        align: 'center'
                    },
                    {
                        field: 'TOTAL_DINAS',
                        title: 'Penugasan',
                        width: 100,
                        sortable: false,
                        align: 'center'
                    },
                    {
                        field: 'TOTAL_TUNJ_TRANSPORT',
                        title: 'Transport',
                        width: 100,
                        sortable: false,
                        align: 'right'
                    },
                    {
                        field: 'TOTAL_TUNJ_MAKAN',
                        title: 'Makan',
                        width: 100,
                        sortable: false,
                        align: 'right'
                    },
                    {
                        field: 'TOTAL_LATE_EARLY',
                        title: 'Telat / Pulang Cepat',
                        sortable: false,
                        align: 'center',
                    },
                ]
            ],
            onLoadSuccess: function(data) {
                $('.tip').tipsy({
                    opacity: 1,
                });
            }
        });
        $(window).resize(function() {
            datagrid();
        });

        $('#btn-generate').click(function() {
            window.location = 'tmp.php?generate=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
            return false;
        });
        $('#btn-export').click(function() {
            window.location = 'tmp.php?export=1&PERIODE_ID=' + $('#PERIODE_ID').val() + '&PROJECT_ID=' + $('#PROJECT_ID').val();
            return false;
        });

        $('#btn-search').click(function() {
            doSearch();
            return false;
        });

        $('#PERIODE_ID, #PROJECT_ID').change(function() {
            doSearch();
            return false;
        });

        $('.input-search, #NAMA').keypress(function(e) {
            if (e.which == 13) {
                doSearch();
                e.preventDefault();
            }
        });

        $('#btn-reset').click(function() {
            $('#PERIODE_ID').val("");
            $('#PROJECT_ID').val("");
            $('#NAMA').val("");
            doSearch();
            return false;
        });
        datagrid();
    });

    function datagrid() {
        var wind = parseInt($(window).height());
        var top = parseInt($('.navbar').outerHeight());
        $('#t-responsive').height(wind - top - 70);
        $('#t').datagrid('resize');
    }

    function doSearch() {
        $('#t').datagrid('load', {
            PERIODE_ID: $('#PERIODE_ID').val(),
            PROJECT_ID: $('#PROJECT_ID').val(),
            NAMA: $('#NAMA').val(),
        });
    }
</script>

<?php
include 'footer.php';
?>