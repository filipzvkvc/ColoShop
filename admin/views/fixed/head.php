<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

  <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
      
  <?php
    $currentPage = get('page') ?? 'dashboard';

    if($currentPage == 'dashboard'){
      echo '<link rel="stylesheet" href="assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">';
      echo '<link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">';
      echo '<link rel="stylesheet" href="assets/plugins/jqvmap/jqvmap.min.css">';
      echo '<link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">';
      echo '<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">';
      echo '<link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">';
    }
    elseif($currentPage == 'products' || $currentPage == 'users' || $currentPage == 'orders' || $currentPage == 'editOrderForm' || $currentPage == 'newsletterSubscribers' || $currentPage == 'editProductForm'){
      echo '<link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">';
      echo '<link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">';
      echo '<link rel="stylesheet" href="assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">';
    }
    elseif ($currentPage == 'insertProductForm' || $currentPage == 'editProductForm') {
        echo '<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">';
        echo '<link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">';
        echo '<link rel="stylesheet" href="assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">';
        echo '<link rel="stylesheet" href="assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">';
        echo '<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">';
        echo '<link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">';
        echo '<link rel="stylesheet" href="assets/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">';
        echo '<link rel="stylesheet" href="assets/plugins/bs-stepper/css/bs-stepper.min.css">';
        echo '<link rel="stylesheet" href="assets/plugins/dropzone/min/dropzone.min.css">';
    }

    elseif($currentPage == 'newsletter'){
      echo '<script src="assets/tinymce/js/tinymce/tinymce.min.js"></script>';
      echo "<script>
                    tinymce.init({
                    selector: '#newsletter_content',
                    license_key: 'gpl',
                    height: 400,
                    menubar: false,
                    relative_urls: false,
                    remove_script_host: false, 
                    plugins: 'link image lists code preview fullscreen',
                    toolbar: 'undo redo | styles | bold italic underline | bullist numlist | alignleft aligncenter alignright | link image | code | preview fullscreen',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }'
                    });
              </script>";
    }

  ?>

  <!-- Theme style -->
  <link rel="stylesheet" href="assets/styles/adminlte.min.css">



  <!-- Custom admin styles -->
  <link rel="stylesheet" href="assets/styles/adminStyles.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="page-overlay" id="pageOverlay"></div>
<div class="wrapper">