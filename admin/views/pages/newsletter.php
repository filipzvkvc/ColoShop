<?php
  if(isset($_SESSION['send_newsletter'])){
    $formData = $_SESSION['send_newsletter'];
  }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Newsletter</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
            <li class="breadcrumb-item active">Newsletter</li>
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
            <h3 class="card-title">Newsletter</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="models/sendNewsletter.php" method="POST">
                <div class="card-body">
                    <!-- Naslov -->
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" placeholder="Enter subject..."
                        value="<?= isset($formData->subject) ? $formData->subject : ''?>">
                    </div>
                    <?php if (hasFlash('subject')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('subject')) ?></small>
                    <?php endif; ?>

                    <!-- Sadržaj (TinyMCE editor) -->
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea id="newsletter_content" name="content"></textarea>
                    </div>

                    <?php if (hasFlash('content')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('content')) ?></small>
                    <?php endif; ?>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane"></i> Send
                    </button>
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