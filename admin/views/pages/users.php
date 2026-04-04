<?php
  if(isset($_SESSION['user'])){
    $user = $_SESSION['user'];
  }
  $users = getUsersDashboard();
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Users</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

            <div class="card">
              <div class="card-body">

                <div id="flashMessages">
                  <?php if (hasFlash('success')): ?>
                    <div class="OverviewAlert alert alert-success alert-dismissible fade show">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <?= htmlspecialchars(getFlashData('success')) ?>
                    </div>
                  <?php endif; ?>

                  <?php if (hasFlash('error')): ?>
                    <div class="OverviewAlert alert alert-danger alert-dismissible fade show">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <?= htmlspecialchars(getFlashData('error')) ?>
                    </div>
                  <?php endif; ?>
                </div>

                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Profile picture</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach($users as $u):?>
                        <tr>
                          <td><?= $u->id?></td>
                          <td><?= $u->first_name?> <?= $u->last_name?></td>
                          <td><?= $u->email?></td>
                          <td class='dashImages'><img src="../<?= $u->profile_picture?>"/></td>
                          <td><?= $u->role_name?></td>
                          <td>
                            <?php if($u->verified == 1): ?>
                              <span class="btn btn-sm btn-success" style="pointer-events: none; cursor: default;">Verified</span>
                              <?php else: ?>
                                <span class="btn btn-sm btn-danger" style="pointer-events: none; cursor: default;">Not verified</span>
                            <?php endif ?>
                          </td>
                          <td>
                            <a href="index.php?page=editUserForm&id=<?= $u->id ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php if($u->id !== $user->id_user):?>
                              <a href="#" class="btn btn-sm btn-danger delete-btn" data-type="user" data-id="<?= $u->id ?>">Delete</a>
                            <?php endif?>
                          </td>
                        </tr>
                    <?php endforeach?>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

    <!-- /.content -->
  </div>
  
   <!-- Custom Confirm Modal -->
<div id="confirmModal" class="custom-modal">
    <div class="modal-content">
        <p id="confirmMessage"></p>
        <div class="modal-buttons">
            <button id="confirmYes" class="modal-btn yes-btn">Yes</button>
            <button id="confirmNo" class="modal-btn no-btn">No</button>
        </div>
    </div>
</div>