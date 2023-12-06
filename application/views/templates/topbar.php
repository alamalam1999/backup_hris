<div id="app">
  <div class="main-wrapper">
    <div class="navbar-bg"></div>

    <nav class="navbar navbar-expand-lg main-navbar">
      <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
          <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>
      </form>
      <ul class="navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
           <!--  <?php if ($this->session->userdata('user_logged')->FOTO != '') { ?>
              <img alt="image profile" src="<?php echo hris() . "uploads/foto/" . $this->session->userdata('user_logged')->FOTO; ?>" class="rounded-circle user-avatar mr-1">
            <?php } else { ?>
              <img alt="image profile" src="<?php echo base_url() ?>assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
            <?php } ?> -->
            <div class="d-sm-none d-lg-inline-block"><?php echo $this->session->userdata('user_logged')->NAMA ?></div>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <?php if ($this->session->userdata('page') == 'karyawan') { ?>
              <a href="<?php echo site_url('profile'); ?>" class="dropdown-item has-icon">
                <i class="far fa-user"></i> Profile
              </a>
              <a href="<?php echo site_url('profile/update_password'); ?>" class="dropdown-item has-icon">
                <i class="fas fa-key"></i> Change Password
              </a>
            <?php } ?>
            <div class="dropdown-divider"></div>
            <a href="<?php echo site_url('auth/logout'); ?>" class="dropdown-item has-icon text-danger">
              <i class="fas fa-sign-out-alt"></i> Logout
            </a>
          </div>
        </li>
      </ul>
    </nav>