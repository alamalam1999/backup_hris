<?php
include 'app-load.php';
$TEMPLATE_ID = get_input('TEMPLATE_ID');
$INTERVIEWER = get_input('INTERVIEWER');
$LAMARAN_ID = get_input('LAMARAN_ID');
$INTERVIEW_DATA1 =  db_first("SELECT NOTE,KEPUTUSAN FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER=1 GROUP BY INTERVIEWER");
$INTERVIEW_DATA2 =  db_first("SELECT NOTE,KEPUTUSAN FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER=2 GROUP BY INTERVIEWER");
$INTERVIEW_DATA3 =  db_first("SELECT NOTE,KEPUTUSAN FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER=3 GROUP BY INTERVIEWER");
$INTERVIEW_DATA4 =  db_first("SELECT NOTE,KEPUTUSAN FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND INTERVIEWER=4 GROUP BY INTERVIEWER");
$LAMARAN = db_first(" SELECT * FROM lamaran WHERE LAMARAN_ID='$LAMARAN_ID' ");
$STATUS = $LAMARAN->STATUS_LAMARAN;
$DISABLED = '';
if($STATUS=='SELESAI INTERVIEW') $DISABLED = 'disabled';
?>
<table class="table table-bordered">
	<thead>
		<tr>
			<th rowspan="2" class="text-center" style="width: 30px;">NO</th>
			<th rowspan="2" class="text-center">FAKTOR-FAKTOR YANG DINILAI</th>
			<th colspan="4" class="text-center">NILAI</th>
		</tr>
		<tr>
			<th class="text-center">1</th>
			<th class="text-center">2</th>
			<th class="text-center">3</th>
			<th class="text-center">4</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$rs = db_fetch("SELECT TP.*,P.PERTANYAAN FROM template_pertanyaan TP LEFT JOIN pertanyaan P ON (P.PERTANYAAN_ID=TP.PERTANYAAN_ID) WHERE TEMPLATE_ID ='$TEMPLATE_ID'"); 
		if(count($rs)>0){ foreach($rs as $val=>$row){
			$DETAIL  = db_first(" SELECT * FROM interview WHERE LAMARAN_ID='$LAMARAN_ID' AND PERTANYAAN_ID='$row->PERTANYAAN_ID' AND INTERVIEWER = '$INTERVIEWER'");
		?>
		<tr>
			<td class="text-center"><?php echo $val+1 ?></td>
			<td><?php echo $row->PERTANYAAN ?></td>
			<td class="text-center">
				<input type="radio" class="form-check-input nilai<?php echo $INTERVIEWER ?>" name="<?php echo $row->PERTANYAAN_ID.'-'.$INTERVIEWER ?>" value="1" <?php if (isset($DETAIL->NILAI) && $DETAIL->NILAI=="1" && $DETAIL->PERTANYAAN_ID==$row->PERTANYAAN_ID && $DETAIL->INTERVIEWER==$INTERVIEWER) echo "checked";?> <?php echo $DISABLED ?>>
			</td>
			<td class="text-center">
				<input type="radio" class="form-check-input nilai<?php echo $INTERVIEWER ?>" name="<?php echo $row->PERTANYAAN_ID.'-'.$INTERVIEWER ?>" value="2" <?php if (isset($DETAIL->NILAI) && $DETAIL->NILAI=="2" && $DETAIL->PERTANYAAN_ID==$row->PERTANYAAN_ID  && $DETAIL->INTERVIEWER==$INTERVIEWER) echo "checked";?> <?php echo $DISABLED ?>>
			</td>
			<td class="text-center">
				<input type="radio" class="form-check-input nilai<?php echo $INTERVIEWER ?>" name="<?php echo $row->PERTANYAAN_ID.'-'.$INTERVIEWER ?>" value="3" <?php if (isset($DETAIL->NILAI) && $DETAIL->NILAI=="3" && $DETAIL->PERTANYAAN_ID==$row->PERTANYAAN_ID  && $DETAIL->INTERVIEWER==$INTERVIEWER) echo "checked";?> <?php echo $DISABLED ?>>
			</td>
			<td class="text-center">
				<input type="radio" class="form-check-input nilai<?php echo $INTERVIEWER ?>" name="<?php echo $row->PERTANYAAN_ID.'-'.$INTERVIEWER ?>" value="4" <?php if (isset($DETAIL->NILAI) && $DETAIL->NILAI=="4" && $DETAIL->PERTANYAAN_ID==$row->PERTANYAAN_ID  && $DETAIL->INTERVIEWER==$INTERVIEWER) echo "checked";?> <?php echo $DISABLED ?>>
			</td>
		</tr>
		<?php }} ?>
		<tr>
			<td colspan="2" class="text-right">Total Nilai Rata-Rata</td>
			<td colspan="4">
				<input type="text" class="form-control" id="total_nilai<?php echo $INTERVIEWER ?>" name="TOTAL_NILAI<?php echo $INTERVIEWER ?>" value="<?php echo set_value('TOTAL_NILAI',$DETAIL->TOTAL_NILAI) ?>" readonly>
			</td>
		</tr>
	</tbody>
</table>

<div class="form-group">
	<label class="col-sm-1 control-label">Catatan Interviewer</label>
	<div class="col-sm-8">
		<?php /*<textarea class="form-control" rows="4" name="NOTE" <?php echo $DISABLED ?>><?php echo isset($INTERVIEW_DATA->NOTE) ? $INTERVIEW_DATA->NOTE : '' ?></textarea> */?>
		<input type="text" name="NOTE<?php echo $INTERVIEWER ?>" value="<?php echo set_value('NOTE'.$INTERVIEWER, ${'INTERVIEW_DATA'.$INTERVIEWER}->NOTE) ?>" class="form-control" <?php echo $DISABLED ?>>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-1 control-label">Kesimpulan</label>
	<div class="col-sm-8">
		<?php echo dropdown('KEPUTUSAN'.$INTERVIEWER,array('DISARANKAN'=>'Disarankan','DIPERTIMBANGKAN'=>'Dipertimbangkan','TIDAK DISARANKAN'=>'Tidak Disarankan'),set_value('KEPUTUSAN'.$INTERVIEWER, ${'INTERVIEW_DATA'.$INTERVIEWER}->KEPUTUSAN),' class="form-control"'.$DISABLED) ?>
	</div>
</div>
<?php if($STATUS!='SELESAI INTERVIEW'){ ?>
<div class="form-group" style="padding-left: 20px;">
	<button name="UPDATE_TYPE" type="submit" value="INTERVIEWER<?php echo $INTERVIEWER ?>" class="btn btn-primary" onclick="$('#form').submit()">
		<span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save
	</button>
</div>
<?php } ?>
<script>
$(document).ready(function(){
	$('.nilai<?php echo $INTERVIEWER ?>').change(function () {
		var total = 0,
			valid_labels = 0,
			average;

		$('.nilai<?php echo $INTERVIEWER ?>:checked').each(function () {
			var val = parseInt($(this).val(), 10);
			if (!isNaN(val)) {
				valid_labels += 1;
				total += val;
			}
		});

		console.log(total)

		average = total / valid_labels;
		average = average.toFixed(2);
		$('#total_nilai<?php echo $INTERVIEWER ?>').val(average);
	});
});
</script>