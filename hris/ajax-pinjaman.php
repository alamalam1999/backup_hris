<?php
include 'app-load.php';
$periode1 = get_input('periode1');
$periode2 = get_input('periode2');

$PERIODE1 = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$periode1' ");
$PERIODE2 = db_first(" SELECT * FROM periode WHERE PERIODE_ID='$periode2' ");

$date1 = $PERIODE1->TANGGAL_SELESAI;
$date2 = $PERIODE2->TANGGAL_SELESAI;

$PERIODE = db_first(" SELECT COUNT(1) AS BULAN FROM periode WHERE TANGGAL_SELESAI >= '$date1' and TANGGAL_SELESAI <= '$date2'");

$BULAN = empty($PERIODE->BULAN) ? 0 : $PERIODE->BULAN;
echo $BULAN;