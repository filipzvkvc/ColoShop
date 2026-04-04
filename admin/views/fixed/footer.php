<!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- jQuery -->
<script src="assets/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<?php
  $currentPage = get('page') ?? 'dashboard';

  if($currentPage == 'dashboard'){
    echo '<script src="assets/plugins/chart.js/Chart.min.js"></script>';
    echo '<script src="assets/plugins/sparklines/sparkline.js"></script>';
    echo '<script src="assets/plugins/jqvmap/jquery.vmap.min.js"></script>';
    echo '<script src="assets/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>';
    echo '<script src="assets/plugins/jquery-knob/jquery.knob.min.js"></script>';
    echo '<script src="assets/plugins/moment/moment.min.js"></script>';
    echo '<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>';
    echo '<script src="assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>';
    echo '<script src="assets/plugins/summernote/summernote-bs4.min.js"></script>';
    echo '<script src="assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>';

    echo '<script src="assets/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>';
    echo '<script src="assets/plugins/raphael/raphael.min.js"></script>';
    echo '<script src="assets/plugins/jquery-mapael/jquery.mapael.min.js"></script>';
    echo '<script src="assets/plugins/jquery-mapael/maps/usa_states.min.js"></script>';
    // echo '<script src="assets/js/pages/dashboard2.js"></script>';



    echo '<script src="assets/js/adminlte.js"></script>';
    // echo '<script src="assets/js/pages/dashboard.js"></script>';
  }
elseif ($currentPage == 'products' || $currentPage == 'users' || $currentPage == 'orders' || $currentPage == 'editOrderForm' || $currentPage == 'newsletterSubscribers' || $currentPage === 'editProductForm') {
    echo '<script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>';
    echo '<script src="assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>';
    echo '<script src="assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>';
    echo '<script src="assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>';
    echo '<script src="assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>';
    echo '<script src="assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>';
    echo '<script src="assets/plugins/jszip/jszip.min.js"></script>';
    echo '<script src="assets/plugins/pdfmake/pdfmake.min.js"></script>';
    echo '<script src="assets/plugins/pdfmake/vfs_fonts.js"></script>';
    echo '<script src="assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>';
    echo '<script src="assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>';
    echo '<script src="assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>';

    echo '<script src="assets/js/adminlte.min.js"></script>';
    echo '<script>
      $(function () {
        let table1 = $("#example1").DataTable({
          "responsive": true,
          "lengthChange": false,
          "autoWidth": false
        });

        $("#example2").DataTable({
          "paging": true,
          "lengthChange": false,
          "searching": false,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true
        });

        // Move flash messages into the DataTables top-left section
        let flash = $("#flashMessages");
        let target = $("#example1_wrapper .row:first-child .col-md-6:first");
        if (flash.length && target.length) {
          target.prepend(flash);
        }
      });
    </script>';

}
elseif($currentPage === 'insertCategoryForm' || $currentPage === 'editUserForm' || $currentPage === 'insertProductForm' || $currentPage === 'editProductForm' || $currentPage === 'editOrderForm' || $currentPage === 'newsletter' || $currentPage === 'editNewsletterForm' || $currentPage === 'editCommentForm'){
    echo '<script src="assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>';
    
    echo '<script src="assets/plugins/select2/js/select2.full.min.js"></script>';
    echo '<script src="assets/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>';
    echo '<script src="assets/plugins/moment/moment.min.js"></script>';
    echo '<script src="assets/plugins/inputmask/jquery.inputmask.min.js"></script>';
    echo '<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>';
    echo '<script src="assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>';
    echo '<script src="assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>';
    echo '<script src="assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>';
    echo '<script src="assets/plugins/bs-stepper/js/bs-stepper.min.js"></script>';
    echo '<script src="assets/plugins/dropzone/min/dropzone.min.js"></script>';
    echo '<script src="assets/plugins/moment/moment.min.js"></script>';
    

    echo '<script src="assets/js/adminlte.min.js"></script>';
    echo '<script>
            $(function () {
              bsCustomFileInput.init();

              // Initialize Start Date picker
              $("#startdate").datetimepicker({
                format: "DD/MM/YYYY"
              });

              // Initialize End Date picker
              $("#enddate").datetimepicker({
                format: "DD/MM/YYYY"
              });
            });
          </script>';
}
?>

<!-- AdminLTE App -->
<!-- <script src="assets/js/adminlte.js"></script> -->

<!-- AdminLTE App -->
<!-- <script src="assets/js/adminlte.min.js"></script> -->

<!-- AdminLTE for demo purposes -->
<script src="assets/js/demo.js"></script>



<!-- Custom adminMain -->
<script src="assets/js/adminMain.js"></script>

</body>
</html>