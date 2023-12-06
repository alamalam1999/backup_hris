<!-- reCAPTCHA JavaScript API -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="content-login p-1">
	<div id="app">
		<section class="section">
			<div class="container mt-5">
				<div class="row">
					<div class="col-lg-3">
					</div>
					<div class="col-lg-7">
						<div class="row justify-content-center">
							<div class="col-lg-9">
								<div class="card shadow">
									<div class="card-body">
										<div class="row pb-2">
											<div class="col-lg-12 text-center">
												<img src="<?php echo base_url() ?>assets/images/logo-avicenna.png" class="img-fluid" style="width: 100px;">

												<h2>YPAP</h2>
												<h4 class="text-warning">Employee Self Service</h4>
											</div>
										</div>

										<form method="POST" action="" class="needs-validation" novalidate="">
											<div class="form-group">
												<label for="email">Email</label>
												<input type="hidden" name="login_as" value="karyawan">
												<input id="email" type="email" class="form-control" name="email" tabindex="2" value="<?php echo set_value('email'); ?>" required>
												<?php echo form_error('email', '<small class="text-danger"> ', '</small>'); ?>
												<div class="invalid-feedback">Please fill in your valid email</div>
											</div>

											<div class="form-group">
												<div class="d-block">
													<label for="password" class="control-label">Password</label>
													<?php
													/*
													<div class="float-right">
														<a href="auth/forgot_password" class="text-small font-weight-bold">
															Forgot Password?
														</a>
													</div>
													*/
													?>
												</div>
												<input id="password" type="password" class="form-control" name="password" tabindex="3" required>
												<?php echo form_error('password', '<small class="text-danger"> ', '</small>'); ?>
												<div class="invalid-feedback">please fill in your password</div>
											</div>

											<?php echo $recaptcha; ?>

											<div class="form-group pt-3">
												<button type="submit" class="btn btn-warning btn-lg btn-block font-weight-bold">
													LOGIN
												</button>
											</div>
										</form>

										<!-- <div class="row sm-gutters">
											<div class="col-6">
												<a href="#" class="btn btn-block btn-secondary text-white">
													<i class="fa fa-book"></i> Panduan
												</a>
											</div>
											<div class="col-6">
												<a href="#" class="btn btn-block btn-secondary text-white">
													<i class="fa fa-comments"></i> FAQ
												</a>
											</div>
										</div> -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
</div>