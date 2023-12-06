<?php
include 'app-load.php';
is_login();

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$allow_ext = array('xls');
	$filename = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
	$tmp_name = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
	$ext = strtolower(substr(strrchr($filename, "."), 1));

	if( ! is_uploaded_file($tmp_name)){
		$ERROR[] = 'Tidak ada file yang diupload.';
	}else if( ! in_array($ext,array('dat')) ){
		$ERROR[] = 'Ekstensi tidak diperbolehkan. Ekstensi yang dibolehkan xls.';
	}else{

		$FILE = file($tmp_name);
		
		$FIELDS = array(
			'PIN','DATE',
		);
				
		foreach($FIELDS as $F){
			$COL[] = '`'.$F.'`';
		}
		
		$isegment = 1000;
		$segment = 0;
		if(count($FILE)>0)
		{
			foreach($FILE as $file)
			{
				$parts = explode("\t",$file);
				$PIN = isset($parts[0]) ? intval($parts[0]) : '';
				$DATE = isset($parts[1]) ? $parts[1] : '';
				
				if(!empty($PIN) AND !empty($DATE))
				{
					
					$d = array();
					foreach($FIELDS as $F){
						$VAL[$F] = "'".${$F}."'";
					}

					$TMP[$segment][] = '('.implode(',',$VAL).')';
					if($i >= $isegment){
						$isegment = $isegment + 1000;
						$segment = $segment + 1;
					}
				}
			}

			$TOTAL = 0;
			if(count($TMP) > 0){ foreach($TMP as $tmp){
				db_execute(" INSERT IGNORE log_mesin (".implode(',',$COL).") VALUES ".implode(',',$tmp) );
				$TOTAL = $TOTAL + $DB->Affected_Rows();
			}}
			$SUCCESS = $TOTAL . ' data berhasil di simpan.';
		}
	}
}

include 'header.php';
?>

<section class="container-fluid" style="margin-top:70px;">
	
	<div class="row">
	<div class="col-lg-3">
		<?php include 'sidebar-import.php' ?>
	</div>
	<div class="col-lg-9">
	
		<h1 class="border-title">
			<?php echo ucfirst($OP) ?>Import Log from File
			<button id="btn-import" onclick="$('#form').submit()" class="btn btn-primary" style="margin-left:10px;"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;&nbsp;&nbsp;Import</button>
		</h1>
		<?php include 'msg.php' ?>
		<form id="form" class="form-horizontal" action="<?php echo self() ?>" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="TMP" value="<?php echo rand(11111,99999) ?>">
			<div class="form-group">
				<label class="col-sm-2 control-label">File *.dat</label>
				<div class="col-sm-10">
					<input type="file" name="file" value="" class="form-control">
				</div>
			</div>
		</form>

		<p style="padding: 30px 0 0 20px;">
			Penarikan Data Absensi (Download .dat) dapat dilakukan di <a href="http://solutioncloud.co.id/" target="_blank"><strong>http://solutioncloud.co.id/</strong></a>
		</p>
		<p>
		<ol>
			<li>
				Mesin BPS-YPAP = Nomor Mesin : <strong>BWXP224860092</strong>, Password : <strong>solution</strong>
			</li>
			<li>
				Mesin Avicenna Jagakarsa = Nomor Mesin : <strong>BWXP224860006</strong>, Password : <strong>solution</strong>
			</li>
			<li>
				Mesin Avicenna Cinere = Nomor Mesin : <strong>BWXP225160001</strong>, Password : <strong>solution</strong>
			</li>
			<li>
				Mesin Avicenna Pamulang = Nomor Mesin : <strong>BWXP212161478</strong>, Password : <strong>solution</strong>
			</li>
		</ol>
		</p>
	
	</div>
	</div> <!-- end row -->
</section>

<script type="text/javascript">

</script>

<?php
include 'footer.php';
?>