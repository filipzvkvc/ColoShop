<?php
  $categories = getAllFromTable('categories');
  $colors = getAllFromTable('color');
  $sizes = getAllFromTable('size');
  $discount = getAllFromTable('discount');

  if(isset($_SESSION['insert_product_form_data'])){
    $formData = $_SESSION['insert_product_form_data'];
  }
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add new product</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">New product</li>
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
              <form action="models/insertProductAction.php" method="POST" enctype="multipart/form-data">
                <div class="card-body">
                  <!-- Product name -->
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Product name"
                      value="<?= isset($formData->product_name) ? $formData->product_name : ''?>">
                    <?php if (hasFlash('product_name')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('product_name')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- Description -->
                  <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description" placeholder="Description" rows="4"><?= isset($formData->description) ? $formData->description : ''?></textarea>
                    <?php if (hasFlash('description')): ?>
                      <small class="text-danger"><?= htmlspecialchars(getFlashData('description')) ?></small>
                    <?php endif; ?>
                  </div>

                  <!-- COVER PHOTO -->
                    <div class="mb-3">
                        <label class="form-label">Cover photo</label><br>

                        <?php if (!empty($formData->cover_photo)): ?>
                          <div class="position-relative d-inline-block mb-2" id="coverPhotoWrapper">
                            <img src="../<?= htmlspecialchars($formData->cover_photo) ?>"
                                class="img-fluid rounded border p-1"
                                style="max-height: 200px;">

                            <button type="button"
                                    class="btn btn-danger btn-sm d-flex justify-content-center align-items-center position-absolute remove-picture-btn"
                                    style="top:3px; right:3px; width:30px; height:30px; padding:0; font-size:15px;"
                                    data-type="insert_cover_temp"
                                    data-path="<?= htmlspecialchars($formData->cover_photo) ?>"
                                    data-img-selector="#coverPhotoWrapper">
                                X
                            </button>
                        </div>
                      <?php endif; ?>



                      <input type="file" class="form-control" name="cover_photo" id="cover_photo">

                      <?php if (hasFlash('cover_photo')): ?>
                          <small class="text-danger"><?= htmlspecialchars(getFlashData('cover_photo')) ?></small>
                      <?php endif; ?>
                  </div>


                  <!-- ADDITIONAL PICTURES -->
                  <div class="mb-3">
                      <label class="form-label">Additional pictures</label>


                      <?php if (hasFlash('additional_pictures')): ?>
                          <small class="text-danger"><?= htmlspecialchars(getFlashData('additional_pictures')) ?></small>
                      <?php endif; ?>

                      <?php if (!empty($formData->additional_pictures)): ?>
                          <div class="d-flex flex-wrap gap-2 mt-2 mb-2" style="gap: 10px;">

                              <?php foreach ($formData->additional_pictures as $index => $pic): ?>
                               <div class="position-relative" style="width:100px;height:100px;" id="additionalPicWrapper_<?= $index ?>">
                                  <img src="../<?= htmlspecialchars($pic) ?>" class="rounded border p-1"
                                      style="width:100%; height:100%; object-fit:cover;">

                                  <button type="button"
                                          class="btn btn-danger btn-sm d-flex justify-content-center align-items-center position-absolute remove-picture-btn"
                                          style="top:3px; right:3px; width:20px; height:20px; padding:0; font-size:12px;"
                                          data-type="insert_additional_temp"
                                          data-path="<?= htmlspecialchars($pic) ?>"
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
                           <?= isset($formData->categoryId) && $formData->categoryId == $c->id_categories ? 'selected' : '' ?>>
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
                      <?= isset($formData->genderId) && $formData->genderId == 1 ? 'checked' : ''?>>
                      <label class="form-check-label" for="radioMale">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="radio" id="female" value="2"
                      <?= isset($formData->genderId) && $formData->genderId == 2 ? 'checked' : ''?>>
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
                          <option value="<?= $c->id_color ?>"
                            <?= isset($formData->colorIds) && in_array($c->id_color, $formData->colorIds) ? 'selected' : '' ?>>
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
                          <option value="<?= $s->id_size ?>"
                            <?= isset($formData->sizeIds) && in_array($s->id_size, $formData->sizeIds) ? 'selected' : '' ?>>
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
                        value="<?= isset($formData->price) ? $formData->price : ''?>">
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
                        <option value="" hidden>Select discount value</option>
                        <option value="0" <?=isset($formData->discount) && $formData->discount == 0 ? 'selected' : '' ?>>No discount</option>
                        <?php foreach ($discount as $d): ?>
                          <option value="<?= htmlspecialchars($d->id_discount) ?>"
                          <?= isset($formData->discount) && $formData->discount == $d->id_discount ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d->value) ?>%
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <?php if (hasFlash('discount')): ?>
                        <small class="text-danger"><?= htmlspecialchars(getFlashData('discount')) ?></small>
                      <?php endif; ?>
                    </div>


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