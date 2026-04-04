<?php
  $cities = getAllFromTable('city');
  $shippingMethods = getAllFromTable('shipping_method');
  $orderStatus = getAllFromTable('order_status');

  $orderId = get('id');

  $orderInformation = getOrderInformation($orderId);

  $orderItems = getOrderItems($orderId);

  if(isset($_SESSION['edit_order_form_data'])){
    $formData = $_SESSION['edit_order_form_data'];
  }
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit order</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Edit order</li>
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
              <form action="models/editOrderAction.php" method="POST">
                <div class="card-body">


                <!-- Email -->
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email"
                    value="<?= isset($formData->email) ? $formData->email : (isset($orderInformation->email) ? $orderInformation->email : '')?>">
                    <?php if (hasFlash('email')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('email')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- First Name -->
                  <div class="mb-3">
                    <label class="form-label">First name</label>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First name"
                    value="<?= isset($formData->first_name) ? $formData->first_name : (isset($orderInformation->first_name) ? $orderInformation->first_name : '')?>">
                    <?php if (hasFlash('first_name')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('first_name')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Last Name -->
                  <div class="mb-3">
                    <label class="form-label">Last name</label>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last name"
                    value="<?= isset($formData->last_name) ? $formData->last_name : (isset($orderInformation->last_name) ? $orderInformation->last_name : '')?>">
                    <?php if (hasFlash('last_name')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('last_name')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Street Name -->
                  <div class="mb-3">
                    <label class="form-label">Street name</label>
                    <input type="text" class="form-control" name="street_name" id="street_name" placeholder="Street name"
                    value="<?= isset($formData->street_name) ? $formData->street_name : (isset($orderInformation->street_name) ? $orderInformation->street_name : '')?>">
                    <?php if (hasFlash('street_name')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('street_name')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Street Number -->
                  <div class="mb-3">
                    <label class="form-label">Street number</label>
                    <input type="text" class="form-control" name="street_number" id="street_number" placeholder="Street number"
                    value="<?= isset($formData->street_number) ? $formData->street_number : (isset($orderInformation->street_number) ? $orderInformation->street_number : '')?>">
                    <?php if (hasFlash('street_number')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('street_number')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Cities Dropdown -->
                  <div class="mb-3">
                    <label class="form-label">City</label>
                    <select name="id_city" class="form-control">
                      <?php foreach ($cities as $c): ?>
                        <option value="<?= htmlspecialchars($c->id_city) ?>" <?= $orderInformation->id_city == $c->id_city ? 'selected' : '' ?>>
                          <?= htmlspecialchars($c->name) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Phone Number -->
                  <div class="mb-3">
                    <label class="form-label">Phone number</label>
                    <input type="tel" class="form-control" name="phone_number" id="phone_number" placeholder="Phone number"
                    value="<?= isset($formData->phone_number) ? $formData->phone_number : (isset($orderInformation->phone_number) ? $orderInformation->phone_number : '')?>">
                    <?php if (hasFlash('phone_number')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('phone_number')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Shipping Method Dropdown -->
                  <div class="mb-3">
                    <label class="form-label">Shipping method</label>
                    <select name="id_shipping_method" class="form-control">
                      <?php foreach ($shippingMethods as $sm): ?>
                        <option value="<?= htmlspecialchars($sm->id_shipping_method) ?>" <?= $orderInformation->id_shipping_method == $sm->id_shipping_method ? 'selected' : '' ?>>
                          <?= htmlspecialchars($sm->name) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Order Status Dropdown -->
                  <div class="mb-3">
                    <label class="form-label">Order status</label>
                    <select name="id_order_status" class="form-control">
                      <?php foreach ($orderStatus as $os): ?>
                        <option value="<?= htmlspecialchars($os->id_order_status) ?>" <?= $orderInformation->id_order_status == $os->id_order_status ? 'selected' : '' ?>>
                          <?= htmlspecialchars($os->name) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <input type="hidden" name="id_order" value="<?= htmlspecialchars($orderId) ?>">
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

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order items</h1>
                </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                <div class="col-12">

                    <div class="card">
                    <div class="card-body">

                        <div id="flashMessages">
                          <?php if (hasFlash('success2')): ?>
                              <div class="OverviewAlert alert alert-success alert-dismissible fade show">
                              <button type="button" class="close" data-dismiss="alert">&times;</button>
                              <?= htmlspecialchars(getFlashData('success2')) ?>
                              </div>
                          <?php endif; ?>

                          <?php if (hasFlash('error2')): ?>
                              <div class="OverviewAlert alert alert-danger alert-dismissible fade show">
                              <button type="button" class="close" data-dismiss="alert">&times;</button>
                              <?= htmlspecialchars(getFlashData('error2')) ?>
                              </div>
                          <?php endif; ?>
                        </div>

                        <table id="example1" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Order item ID</th>
                            <th>Product ID</th>
                            <th>Product name</th>
                            <th>Picture</th>
                            <th>Quantity</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orderItems as $oi):?>
                                    <tr>
                                        <?php
                                            $productInformation = getProductInformation($oi->id_product);
                                            $productSizes = getProductSizes2($oi->id_product);
                                            $productColors = getProductColors2($oi->id_product);
                                        ?>
                                    <td><?= $oi->id_order_item?></td>
                                    <td><?= $productInformation->id_product?></td>
                                    <td><?= $productInformation->name?></td>
                                    <td class='dashImages'><img src="../<?= $productInformation->cover_photo?>"/></td>
                                    <td>
                                    <form method="POST" action="models/editOrderItemAction.php">
                                        <?php
                                            $storedQuantity = $_SESSION['edit_order_form_data']->{$oi->id_order_item} ?? null;
                                            $currentQuantity = $storedQuantity !== null ? $storedQuantity : $oi->quantity;
                                        ?>
                                        <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Quantity" min="0" value="<?= $currentQuantity?>">
                                        <?php if (hasFlash('quantity')): ?>
                                        <small class="text-danger"><?= htmlspecialchars(getFlashData('quantity')) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="id_size" class="form-control">
                                            <?php foreach ($productSizes as $s): ?>
                                                <option value="<?= htmlspecialchars($s->id_size) ?>" 
                                                    <?= $oi->id_size == $s->id_size ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($s->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="id_color" class="form-control">
                                            <?php foreach ($productColors as $c): ?>
                                                <option value="<?= htmlspecialchars($c->id_color) ?>" 
                                                    <?= $oi->id_color == $c->id_color ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($c->name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" name="id_order_item" value="<?= htmlspecialchars($oi->id_order_item) ?>">
                                        <input type="hidden" name="id_order" value="<?= htmlspecialchars($orderId) ?>">

                                        <button type="submit" class="btn btn-sm btn-warning">Edit</button>

                                    </form>               
                                                                <a href="#" class="btn btn-sm btn-danger delete-btn" data-type="orderItem" data-order-item-id="<?= $oi->id_order_item ?>" data-order-id="<?= $orderId ?>">Delete</a>

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
  