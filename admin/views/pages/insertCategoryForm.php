
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add category</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Add category</li>
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
                <h3 class="card-title">Enter category name</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="models/insertCategoryAction.php" method="POST">
                <div class="card-body">
                  <div class="form-group">
                    <input type="text" class="form-control" name="categoryName" id="categoryName" placeholder="Name">
                  </div>
                </div>
                <!-- /.card-body -->

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
  