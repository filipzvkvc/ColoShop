<?php
    $commentId = get('id');

    $comment = getCommentById($commentId);

    if(isset($_SESSION['edit_comment_form_data'])){
      $formData = $_SESSION['edit_comment_form_data'];
  }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit comment information</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Edit product comment</li>
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
                            <h3 class="card-title">Enter new comment information</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form action="models/editCommentAction.php" method="POST">
                            <div class="card-body">

                                <!-- Content -->
                                <div class="mb-3">
                                    <label class="form-label">Content</label>
                                    <textarea class="form-control"name="content" rows="4"><?= isset($_SESSION['edit_comment_form_data']->content) ? htmlspecialchars($_SESSION['edit_comment_form_data']->content) : htmlspecialchars($comment->content)?></textarea>

                                    <?php if (hasFlash('content')): ?>
                                        <small class="text-danger"><?= htmlspecialchars(getFlashData('content')) ?></small>
                                    <?php endif; ?>
                                </div>

                                <input type="hidden" name="id_comment" value="<?= $commentId ?>">
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