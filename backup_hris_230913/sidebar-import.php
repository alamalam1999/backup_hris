<?php

$MOD = remove_ext(basename(strtolower($_SERVER['PHP_SELF'])));
$M[$MOD] = 1;

?>

<ul class="list-group">
	<div class="list-group-item list-group-item-info">
	Import Log
	</div>
	<a href="import-log.php" class="list-group-item <?php echo isset($M['import-log']) ? 'active' : '' ?>">
		<span class="glyphicon glyphicon-flash"></span>&nbsp;&nbsp;&nbsp;
		From Machine
	</a>
	<a href="import-log-file.php" class="list-group-item <?php echo isset($M['import-log-file']) ? 'active' : '' ?>">
		<span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;&nbsp;
		From File
	</a>
</ul>