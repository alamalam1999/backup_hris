<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="robots" content="index, follow">
	<meta name="description" content="">
	<meta name="keyword" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>IHRIS</title>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link href="<?php echo base_url() ?>static/favicon.png" rel="SHORTCUT ICON" type="image/png">
	<link href="<?php echo base_url() ?>static/bootstrap/css/bootstrap.cerulean.min.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url() ?>static/css/style.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url() ?>static/css/setter.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url() ?>static/autocomplete/jquery.autocomplete.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url() ?>static/fontawesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	<script src="<?php echo base_url() ?>static/js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/autocomplete/jquery.autocomplete.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/highchart/highcharts.js" type="text/javascript"></script>
	<script src="<?php echo base_url() ?>static/highchart/modules/series-label.js" type="text/javascript"></script>
	
	<?php if(isset($_SERVER['PHP_SELF']) AND basename($_SERVER['PHP_SELF']) != 'index.php'){ ?>
	<link href="<?php echo base_url() ?>static/easyui/themes/default/easyui.css?v=<?php echo rand(11111,99999) ?>" rel="stylesheet">
	<script src="<?php echo base_url() ?>static/easyui/jquery.easyui.min.js" type="text/javascript"></script>
	<?php } ?>
	
	<?php if(isset($CSS) AND is_array($CSS)){ foreach($CSS as $css){ ?>
	<link href="<?php echo base_url().$css ?>" rel="stylesheet" type="text/css">
	<?php }} ?>
	<?php if(isset($JS) AND is_array($JS)){ foreach($JS as $js){ ?>
	<script src="<?php echo base_url().$js ?>" type="text/javascript"></script>
	<?php }} ?>
	<script src="<?php echo base_url() ?>static/js/plugin.js" type="text/javascript"></script>
	
<script>
$(document).ready(function(){
	$("#clock").clock({
		"timestamp" : parseInt('<?php echo time() + date('Z') ?>'),
		"dateFormat" : "d M y, ",
		"timeFormat" : "H:i:s",
	});
});
</script>
</head>