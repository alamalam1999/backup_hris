<?php
if(isset($ERROR) AND count($ERROR)>0){
	echo '<div class="alert alert-danger alert-dismissible" role="alert" style="width:50%;position:fixed;right:20px;top:70px;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	foreach($ERROR as $e){
		echo '<p>'.$e.'</p>';
	}
	echo '</div>';
}
if(isset($SUCCESS)){
	echo '<div class="alert alert-success alert-dismissible" role="alert" style="width:50%;position:fixed;right:20px;top:70px;">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$SUCCESS.'</div>';
}
