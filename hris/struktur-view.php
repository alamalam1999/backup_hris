<?php
include 'app-load.php';

is_login('struktur.view');

$rs = db_fetch("
	SELECT STRUKTUR_ID,STRUKTUR,PARENT_ID
	FROM struktur
	ORDER BY ORD ASC
");
	
$list = array();
if(count($rs) > 0){
	foreach($rs as $row){
		$emp = db_fetch(" SELECT NAMA FROM karyawan WHERE STRUKTUR_ID='$row->STRUKTUR_ID' ");
		$row->title = $row->STRUKTUR;
		$row->name = $row->STRUKTUR;
		if(count($emp) > 0){
			if(count($emp)==1){
				$row->title = $emp[0]->NAMA;
			}else{
				$row->title = '<a href="karyawan.php?STRUKTUR_ID='.$row->STRUKTUR_ID.'">'.count($emp).' person</a>';
			}
		}
		$thisref = & $refs[ $row->STRUKTUR_ID ];
		$thisref = array_merge((array) $thisref,(array) $row);
		if ($row->PARENT_ID == 0) {
			$list[] = & $thisref;
		} else {
			$refs[$row->PARENT_ID]['children'][] = & $thisref;
		}
	}
}
//print_r($list);die;
$DATA = json_encode($list);
$DATA = substr($DATA,1,strlen($DATA));
$DATA = substr($DATA,0,strlen($DATA)-1);

$JS[] = 'static/orgchart/html2canvas.min.js';
$JS[] = 'static/orgchart/jspdf.min.js';
$JS[] = 'static/orgchart/orgchart.js';
$CSS[] = 'static/orgchart/orgchart.css';
include 'header.php';
?>
<style>
#chart {
  position: relative;
  display: inline-block;
  top: 10px;
  left: 10px;
  height: 420px;
  width: calc(100% - 24px);
  border: 2px dashed #aaa;
  border-radius: 5px;
  overflow: auto;
  text-align: center;
}
</style>
<script type="text/javascript" src="https://cdn.rawgit.com/stefanpenner/es6-promise/master/dist/es6-promise.auto.min.js"></script>
<section class="container-fluid">
	<section class="content">
		<h1 class="border-title">
			Struktur Organisasi
			<a href="struktur.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		</h1>
	
		<div id="chart"></div>
			
	</section>
</section>

<script>
$(document).ready(function(){
	
	DATA = <?php echo $DATA.';' ?>

    var oc = $('#chart').orgchart({
      'data' : DATA,
      'nodeContent': 'title',
	  'pan': true,
      'zoom': true,
      'exportButton': true,
      'exportFilename': 'Struktur-Organisasi',
      'exportFileextension' : 'pdf',
    });
	 oc.$chartContainer.on('touchmove', function(event) {
      event.preventDefault();
    });
});
</script>

<?php
include 'footer.php';
?>