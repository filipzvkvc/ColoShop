<?php
  $roles = getAllFromTable('role');

  $userId = get('id');

  $userInformation = getUserInformation($userId);

  if(isset($_SESSION['user'])){
    $currentUser = $_SESSION['user'];
  }

  if(isset($_SESSION['edit_user_form_data'])){
    $formData = $_SESSION['edit_user_form_data'];
  }

  $_SESSION['edit_user_id'] = $userId;

  $tempProfile = $_SESSION['edit_user_form_data']->profile_photo_temp ?? null;
  $currentProfile = $userInformation->profile_picture;

  $profileToShow = $tempProfile ? "../$tempProfile" : "../$currentProfile";
  $isTemp = $tempProfile !== null;

?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit user</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Edit user</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <?php

          if (hasFlash('success')): ?>
            <div class="formAlert alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <?= htmlspecialchars(getFlashData('success')) ?>
            </div>
          <?php endif; ?>

          <?php if (hasFlash('error')): ?>
            <div class="formAlert alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert">&times;</button>
              <?= htmlspecialchars(getFlashData('error')) ?>
            </div>
          <?php endif; ?>
          
        <div class="row">
          <!-- left column -->
          <div class="col-md-6">
            <!-- general form elements -->
             
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Enter new information</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="models/editUserAction.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                  <!-- First Name -->
                  <div class="mb-3">
                    <label class="form-label">First name</label>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First name"
                    value="<?= isset($formData->first_name) ? $formData->first_name : (isset($userInformation->first_name) ? $userInformation->first_name : '')?>">
                    <?php if (hasFlash('first_name')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('first_name')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Last Name -->
                  <div class="mb-3">
                    <label class="form-label">Last name</label>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last name"
                    value="<?= isset($formData->last_name) ? $formData->last_name : (isset($userInformation->last_name) ? $userInformation->last_name : '')?>">
                    <?php if (hasFlash('last_name')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('last_name')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Email -->
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                    value="<?= isset($formData->email) ? $formData->email : (isset($userInformation->email) ? $userInformation->email : '')?>">
                    <?php if (hasFlash('email')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('email')) ?></small>
                    <?php endif; ?>
                  </div>


                  <!-- Profile photo -->
                  <div class="position-relative d-inline-block mb-2">
                      <img id="profileImage"
                          src="<?= $profileToShow ?>"
                          class="img-fluid rounded mb-2 border p-1"
                          style="max-height: 200px;">

                      <?php if ($isTemp || $currentProfile !== 'assets/images/default_profile_picture.jpg'): ?>
                          <button type="button"
                                  class="btn btn-danger btn-sm position-absolute remove-picture-btn"
                                  style="top:3px; right:3px; width:30px; height:30px;"
                                  data-type="<?= $isTemp ? 'profile_photo_temp' : 'profile_photo' ?>"
                                  data-path="<?= $isTemp ? $tempProfile : $currentProfile ?>"
                                  data-img-selector="#profileImage">
                              X
                          </button>
                      <?php endif; ?>
                  </div>


                  <?php if (hasFlash('profile_photo')): ?>
                      <small class="text-danger d-block mb-2"><?= htmlspecialchars(getFlashData('profile_photo')) ?></small>
                  <?php endif; ?>

                  <input type="file" class="form-control" name="profile_photo" id="profile_photo">


                  <!-- Role Dropdown -->
                  <?php if($currentUser->id_user != $userId): ?>
                  <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="id_role" class="form-control">
                      <?php foreach ($roles as $r): ?>
                        <option value="<?= htmlspecialchars($r->id_role) ?>" <?= $userInformation->id_role == $r->id_role ? 'selected' : '' ?>>
                          <?= htmlspecialchars($r->name) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <?php endif; ?>

                  <!-- Verified Radio Buttons -->
                  <div class="mb-3">
                    <label class="d-block mb-2">Verified</label>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="radio" id="radioYes" value="1" <?= ($userInformation->verified === 1) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="radioYes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="radio" id="radioNo" value="0" <?= ($userInformation->verified === 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="radioNo">No</label>
                    </div>
                  </div>

                  <input type="hidden" name="id_user" value="<?= htmlspecialchars($userId) ?>">
                </div>

                  <div class="card-footer formButton">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </form>

            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>

    

      
    <!-- /.content -->
  </div>
  