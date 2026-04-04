  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="assets/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="index.php?page=dashboard" class="nav-link">Home</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="index.php?page=logout">
          <p>Log out</p>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
   <?php
      $currentPage = get('page');
      
      if ($currentPage !== 'insertProductForm') {

          if (isset($_SESSION['insert_product_form_data'])) {

              $data = $_SESSION['insert_product_form_data'];

              if (!empty($data->cover_photo)) {

                  $coverPath = '../' . $data->cover_photo;

                  if (file_exists($coverPath)) {
                      unlink($coverPath);
                  }
              }

              if (!empty($data->additional_pictures) && is_array($data->additional_pictures)) {

                  foreach ($data->additional_pictures as $photo) {

                      $photoPath = '../' . $photo;

                      if (file_exists($photoPath)) {
                          unlink($photoPath);
                      }
                  }
              }

              unset($_SESSION['insert_product_form_data']);
          }
      }


      if ($currentPage !== 'editProductForm') {

          if (isset($_SESSION['edit_product_form_data'])) {

              $data = $_SESSION['edit_product_form_data'];

              if (!empty($data->cover_photo)) {
                  $coverPath = '../' . $data->cover_photo;
                  if (file_exists($coverPath)) {
                      unlink($coverPath);
                  }
              }

              if (!empty($data->additional_pictures) && is_array($data->additional_pictures)) {
                  foreach ($data->additional_pictures as $photo) {
                      $photoPath = '../' . $photo;
                      if (file_exists($photoPath)) {
                          unlink($photoPath);
                      }
                  }
              }
              
              unset($_SESSION['edit_product_form_data']);
          }
      }


      if ($currentPage !== 'editUserForm') {

          if (isset($_SESSION['edit_user_form_data'])) {

              $data = $_SESSION['edit_user_form_data'];

              if (!empty($data->profile_photo_temp)) {

                  $tempPath = '../' . $data->profile_photo_temp;

                  if (file_exists($tempPath)) {
                      unlink($tempPath);
                  }
              }

              unset($_SESSION['edit_user_form_data']);
          }
      }


      if($currentPage != 'editOrderForm'){
        if(isset($_SESSION['edit_order_form_data'])){
          unset($_SESSION['edit_order_form_data']);
        }
      }

      if($currentPage != 'newsletter'){
        if(isset($_SESSION['send_newsletter'])){
          unset($_SESSION['send_newsletter']);
        }
      }

      if($currentPage != 'editNewsletterForm'){
        if(isset($_SESSION['edit_newsletter_form_data'])){
          unset($_SESSION['edit_newsletter_form_data']);
        }
      }

 	if($currentPage != 'editCommentForm'){
        if(isset($_SESSION['edit_comment_form_data'])){
          unset($_SESSION['edit_comment_form_data']);
        }
      }
   ?>