<?php
require 'app-load.php';

$error = '';
if( isset($_SERVER['REQUEST_METHOD']) AND $_SERVER['REQUEST_METHOD']=='POST' )
{
	$u = get_input('username');
	$p = get_input('password');
	if( login($u,$p) )
	{
		header('location: index.php');
	}
	else
	{
		$error = '<p style="color:red;">Username dan password tidak cocok.</p>';
	}
}

$BODY_STYLE = ' class="login" style="background:url('.base_url().'static/img/bg.jpg) no-repeat center center fixed;-webkit-background-size: cover; -moz-background-size: cover;-o-background-size: cover;background-size: cover;" ';
require 'header.php';
?>

 <div class="container">    
	<div id="loginbox" style="margin-top:100px; background-color:#ffffff;" class="mainbox col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
		<div style="padding:15px;text-align:center;">
		<?php /*<div class="panel panel-default" >
			<div class="panel-heading">
				<div class="panel-title">Sign In</div>
			</div>
			<div style="padding-top:30px" class="panel-body" >*/ ?>
				<img src="<?php echo base_url() ?>static/img/airkon.png" alt="" style="height:50px;margin-bottom:15px;">
				<?php
					if($error){
						echo '<div class="alert alert-danger alert-dismissible" role="danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$error.'</div>';
					}
				?>
				<form id="loginform" class="form-horizontal" role="form" method="post" action="<?php echo self() ?>">
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
						<input id="login-username" type="text" class="form-control" name="username" value="" placeholder="username">                                        
					</div>
                       <div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
						<input id="login-password" type="password" class="form-control" name="password" placeholder="password">
					</div>
					<div style="margin-top:10px" class="form-group">
						<div class="col-sm-12 controls">
							<button type="submit" id="btn-login" href="#" class="btn btn-success"><span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;Log In </button>
							<a href="https://dr-apps.id/" style="margin-left:10px;">Halaman beranda</a>
						</div>
					</div>
				</form>
			<?php /*</div>
        </div>*/ ?>
		</div>
	</div>
</div>
	
<script>
$(document).ready(function(){
	$('input[name=username]').focus();
});
</script>

<?php require 'footer.php' ?>