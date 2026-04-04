<?php
  $categories = getAllFromTable('categories');
  $colors = getAllFromTable('color');
  $sizes = getAllFromTable('size');
  $discount = getAllFromTable('discount');

  $productId = get('id');

  $_SESSION['edit_product_id'] = $productId;

  $productInformation = getProductInformation($productId);

  if(isset($_SESSION['edit_product_form_data'])){
      $formData = $_SESSION['edit_product_form_data'];
  }

  $tempCover = $formData->cover_photo ?? null;
  $currentCover = $productInformation->cover_photo;

  $coverToShow = $tempCover ? "../$tempCover" : "../$currentCover";
  $isTempCover = $tempCover !== null;

  $realAdditionalPictures = $productInformation->additional_pictures ?? [];
  $tempAdditionalPictures = $formData->additional_pictures ?? []; 

  $additionalPicturesToShow = [];

  foreach ($tempAdditionalPictures as $pic) {
      $additionalPicturesToShow[] = [
          "src" => "../" . $pic,
          "isTemp" => true
      ];
  }

  foreach ($realAdditionalPictures as $pic) {
      if(!in_array("../".$pic, array_column($additionalPicturesToShow, 'src'))){
          $additionalPicturesToShow[] = [
              "src" => "../" . $pic,
              "isTemp" => false
          ];
      }
  }

  if (isset($formData->colorIds)) {
      $selectedColors = $formData->colorIds;
  } else {
      $selectedColors = array_map(fn($c) => $c['id_color'], getProductColors($productId));
  }

  if (isset($formData->sizeIds)) {
      $selectedSizes = $formData->sizeIds;
  } else {
      $selectedSizes = array_map(fn($s) => $s['id_size'], getProductSizes($productId));
  }

  $comments = getProductComments($productId);

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Edit product information</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Edit product</li>
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
                <h3 class="card-title">Enter new product information</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form action="models/editProductAction.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                  <!-- Product name -->
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input class="form-control" name="product_name" id="product_name" placeholder="Product name"
                      value="<?= isset($formData->product_name) ? $formData->product_name : (isset($productInformation->name) ? $productInformation->name : '')?>">
                    <?php if (hasFlash('product_name')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('product_name')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Description -->
                  <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description" placeholder="Description" rows="4"><?= isset($formData->description) ? $formData->description : (isset($productInformation->description) ? $productInformation->description : '')?></textarea>
                    <?php if (hasFlash('description')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('description')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- COVER PHOTO -->
                  <div class="mb-3">
                      <label class="form-label">Cover photo</label><br>

                      <?php if (!empty($coverToShow)): ?>
                          <div class="position-relative d-inline-block mb-2" id="coverPhotoWrapper">
                              <img src="<?= htmlspecialchars($coverToShow) ?>"
                                  class="img-fluid rounded border p-1"
                                  style="max-height: 200px;"
                                  alt="Cover photo">

                              <?php if ($isTempCover || $currentCover !== 'assets/images/default_product_picture.jpg'): ?>
                                  <button type="button"
                                          class="btn btn-danger btn-sm d-flex justify-content-center align-items-center position-absolute remove-picture-btn"
                                          style="top:3px; right:3px; width:30px; height:30px; padding:0; font-size:15px;"
                                          data-type="<?= $isTempCover ? 'edit_cover_temp' : 'edit_cover_real' ?>"
                                          data-path="<?= htmlspecialchars($isTempCover ? $tempCover : $currentCover) ?>"
                                          data-img-selector="#coverPhotoWrapper">
                                      X
                                  </button>
                              <?php endif; ?>
                          </div>
                      <?php endif; ?>

                      <input type="file" class="form-control" name="cover_photo" id="cover_photo">
 

                  </div>

                  <!-- ADDITIONAL PICTURES -->
                  <div class="mb-3">
                      <label class="form-label">Additional pictures</label>

                      <?php if (hasFlash('additional_pictures')): ?>
                          <small class="text-danger"><?= htmlspecialchars(getFlashData('additional_pictures')) ?></small>
                      <?php endif; ?>

                      <?php if (!empty($additionalPicturesToShow)): ?>
                          <div class="d-flex flex-wrap gap-2 mt-2 mb-2">

                              <?php foreach ($additionalPicturesToShow as $index => $pic): ?>
                                  <div class="position-relative" style="width:100px;height:100px;" id="additionalPicWrapper_<?= $index ?>">
                                      <img src="<?= htmlspecialchars($pic['src']) ?>" class="rounded border p-1"
                                          style="width:100%; height:100%; object-fit:cover;" alt="Additional photo">

                                      <button type="button"
                                              class="btn btn-danger btn-sm d-flex justify-content-center align-items-center position-absolute remove-picture-btn"
                                              style="top:3px; right:3px; width:20px; height:20px; padding:0; font-size:12px;"
                                              data-type="<?= $pic['isTemp'] ? 'edit_additional_temp' : 'edit_additional_real' ?>"
                                              data-path="<?= htmlspecialchars(str_replace('../', '', $pic['src'])) ?>"
                                              data-img-selector="#additionalPicWrapper_<?= $index ?>">
                                          X
                                      </button>
                                  </div>
                              <?php endforeach; ?>

                          </div>
                      <?php endif; ?>

                      <input type="file" class="form-control" name="additional_pictures[]" multiple>
                  </div>


                  <!-- Categories Dropdown -->
                  <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="id_category" class="form-control">
                      <option value="" hidden>Select category</option>
                      <?php foreach ($categories as $c): ?>
                        <option value="<?= htmlspecialchars($c->id_categories) ?>" 
                          <?php 
                              if (isset($formData->categoryId)) {
                                  echo ($formData->categoryId == $c->id_categories) ? 'selected' : '';
                              } else {
                                  echo ($productInformation->id_categories == $c->id_categories) ? 'selected' : '';
                              }
                          ?>>
                          <?= htmlspecialchars($c->name) ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                     <?php if (hasFlash('category')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('category')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Gender Radio Buttons -->
                  <div class="mb-3">
                    <label class="d-block mb-2">Gender</label>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="radio" id="male" value="1"
                      <?= isset($formData->genderId) && $formData->genderId == 1 ? 'checked' : (isset($productInformation->id_gender) && $productInformation->id_gender == 1 ? 'checked' : '')?>>
                      <label class="form-check-label" for="radioMale">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="radio" id="female" value="2"
                      <?= isset($formData->genderId) && $formData->genderId == 2 ? 'checked' : (isset($productInformation->id_gender) && $productInformation->id_gender == 2 ? 'checked' : '')?>>
                      <label class="form-check-label" for="radioFemale">Female</label>
                    </div>
                  </div>
                    <?php if (hasFlash('gender')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('gender')) ?></small>
                    <?php endif; ?>


                    <!-- Colors Dropdown -->
                    <div class="mb-3">
                      <label class="form-label">Color</label>
                      <select name="id_color[]" class="form-control" multiple>
                        <option value="" hidden>Select color</option>
                        <?php foreach ($colors as $c): ?>
                          <option value="<?= htmlspecialchars($c->id_color) ?>"
                            <?= in_array($c->id_color, $selectedColors) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c->name) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <?php if (hasFlash('color')): ?>
                        <small class="text-danger"><?= htmlspecialchars(getFlashData('color')) ?></small>
                      <?php endif; ?>
                    </div>

                    <!-- Sizes Dropdown -->
                    <div class="mb-3">
                      <label class="form-label">Size</label>
                      <select name="id_size[]" class="form-control" multiple>
                        <option value="" hidden>Select size</option>
                        <?php foreach ($sizes as $s): ?>
                          <option value="<?= htmlspecialchars($s->id_size) ?>"
                            <?= in_array($s->id_size, $selectedSizes) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s->name) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <?php if (hasFlash('size')): ?>
                        <small class="text-danger"><?= htmlspecialchars(getFlashData('size')) ?></small>
                      <?php endif; ?>
                    </div>

                    <!-- Product price -->
                     
                  <div class="mb-3">
                    <label class="d-block mb-2">Price</label>
                    <div class="form-check form-check-inline">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text">
                            <i class="fas fa-dollar-sign"></i>
                          </span>
                        </div>
                        <input type="number" class="form-control" name="price" id="price" placeholder="0.00" min="0" step="0.01"
                        value="<?= isset($formData->price) ? $formData->price : (isset($productInformation->value) ? $productInformation->value : '')?>">
                      </div>
                    </div>
                  </div>
                    <?php if (hasFlash('price')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('price')) ?></small>
                    <?php endif; ?>

                    <!-- Discount Dropdown -->
                    <div class="mb-3">
                      <label class="form-label">Discount</label>
                      <select name="id_discount" class="form-control">
                        <option value="0"
                        <?= $productInformation->id_discount === null ? 'selected' : ''?>>No discount</option>
                        <?php foreach ($discount as $d): ?>
                          <option value="<?= htmlspecialchars($d->id_discount) ?>"
                          <?= isset($formData->discount) && $formData->discount == $d->id_discount ? 'selected' : 
                          (isset($productInformation->id_discount) && $productInformation->id_discount == $d->id_discount ? 'selected' : '')
                        ?>>
                            <?= htmlspecialchars($d->value) ?>%
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <?php if (hasFlash('discount')): ?>
                        <small class="text-danger"><?= htmlspecialchars(getFlashData('discount')) ?></small>
                      <?php endif; ?>
                    </div>

                    <!-- Active Status Dropdown -->
                    <div class="mb-3">
                      <label class="form-label">Status</label>
                      <select name="is_active" class="form-control">

                        <option value="1"
                          <?= 
                            (isset($formData->is_active) && $formData->is_active == 1) ? 'selected' : 
                            (isset($productInformation->is_active) && $productInformation->is_active == 1 ? 'selected' : '') 
                          ?>
                        >Active</option>

                        <option value="0"
                          <?= 
                            (isset($formData->is_active) && $formData->is_active == 0) ? 'selected' : 
                            (isset($productInformation->is_active) && $productInformation->is_active == 0 ? 'selected' : '') 
                          ?>
                        >Inactive</option>

                      </select>

                      <?php if (hasFlash('is_active')): ?>
                        <small class="text-danger"><?= htmlspecialchars(getFlashData('is_active')) ?></small>
                      <?php endif; ?>
                    </div>




                    <input type="hidden" name="id_product" value="<?=$productId?>">
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
                    <h1>Reviews</h1>
                </div>
                </div>
            </div>
        </section>


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
                            <th>Comment ID</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach($comments as $c):?>
                                    <tr>
                                      <td><?= $c->id_comment?></td>
                                      <td><?= date("Y-m-d", strtotime($c->date)) ?></td>
                                      <td><?= $c->first_name?> <?= $c->last_name?></td>
                                      <td>
                                        <a href="index.php?page=editCommentForm&id=<?= $c->id_comment ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="#" class="btn btn-sm btn-danger delete-btn" data-type="comment" data-id="<?= $c->id_comment ?>">Delete</a>
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