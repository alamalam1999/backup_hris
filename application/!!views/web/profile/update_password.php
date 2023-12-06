<!-- Main Content -->
<div class="main-content">
  <section class="section">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="title-header">Change Password</h4>
      </div>

      <form action="" method="post" enctype="multipart/form-data">
        <div class="card-body">
          <div class="form-group">
            <label>Old Password</label>
            <input type="password" name="old_pass" class="form-control" required>
          </div>
          <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_pass" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_pass" class="form-control" required>
          </div>
        </div>

        <div class="card-footer">
          <input class="btn btn-success" type="submit" name="update_password" value="Update" />
        </div>
      </form>
    </div>
  </section>
</div>