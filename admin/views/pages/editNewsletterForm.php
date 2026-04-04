<?php
  $newsletterId = get('id');

  $newsletterInformation = getNewsletterInformation2($newsletterId);

  if(isset($_SESSION['edit_newsletter_form_data'])){
    $formData = $_SESSION['edit_newsletter_form_data'];
  }
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit newsletter subscription</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Edit newsletter subscription</li>
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
              <form action="models/editNewsletterAction.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">

                  <!-- Email -->
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                    value="<?= isset($formData->email) ? $formData->email : (isset($newsletterInformation->email) ? $newsletterInformation->email : '')?>">
                    <?php if (hasFlash('email')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('email')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Verified Radio Buttons -->
                  <div class="mb-3">
                    <label class="d-block mb-2">Subscribed</label>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="radio" id="radioYes" value="1" <?= ($newsletterInformation->subscribed === 1) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="radioYes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="radio" id="radioNo" value="0" <?= ($newsletterInformation->subscribed === 0) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="radioNo">No</label>
                    </div>
                  </div>

                  <input type="hidden" name="id_newsletter" value="<?= htmlspecialchars($newsletterId) ?>">
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
  