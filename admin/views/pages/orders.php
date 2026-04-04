<?php
  $orders = getOrdersDashboard();
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Orders</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Orders</li>
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
                    <th>Order ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Order status</th>
                    <th>PayPal Order ID</th>
                    <th>PayPal Transaction ID</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach($orders as $o):?>
                        <tr>
                          <td><?= $o->orderId?></td>
                          <td><?= $o->first_name?> <?= $o->last_name?></td>
                          <td><?= $o->email?></td>
                          <td><?= $o->street_name?> <?=$o->street_number?></td>
                          <td>
                            <span class="btn btn-sm <?= getOrderStatusClass($o->orderStatusName) ?>" style="pointer-events: none; cursor: default;">
                                <?= ucfirst(strtolower($o->orderStatusName)) ?>
                            </span>
                        </td>
                        <td><?= $o->paypal_order_id?></td>
                        <td><?= $o->paypal_transaction_id?></td>
                          <td>
                            <a href="index.php?page=editOrderForm&id=<?= $o->orderId ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="#" class="btn btn-sm btn-danger delete-btn" data-type="order" data-id="<?= $o->orderId ?>">Delete</a>
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