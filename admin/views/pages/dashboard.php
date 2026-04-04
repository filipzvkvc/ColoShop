<?php
  $productCount = getTableCount('products');
  $userCount = getTableCount('user');
  $wishlistedProductsCount = getTableCount('wishlist');

  $newsletterCount = getTableCount('newsletter');



  $unverifiedUsers = [];

  $usersQuery = getAllFromTable('user');


  foreach($usersQuery as $uq){
    if($uq->verified != 1){
      $unverifiedUsers[] = $uq->id_user;
    }
  }


  $unverifiedNewsletters = [];

  $newslettersQuery = getAllFromTable('newsletter');

  foreach($newslettersQuery as $nq){
      if($nq->subscribed != 1){
          $unverifiedNewsletters[] = $nq->id_newsletter;
      }
  }



  $recentlyAddedProducts = getRecenltyAddedProducts();


  $logData = logData('[USER_LOGIN]');
  $ids = [];

  $today = date('Y-m-d');

  foreach($logData as $lg){
    $dateFromLog = date('Y-m-d', strtotime(substr($lg->time, 6)));

    if($dateFromLog === $today){
      array_push($ids, substr($lg->id, 4));
    }

  }

  $recentUsers = getRecentUsers($ids);

  $orderCount = getOrderCountDashboard();
?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="index.php?page=dashboard">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?=$productCount?></h3>

                <p>Products</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>


           <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $wishlistedProductsCount ?></h3>

                <p>Wishlisted products</p>
              </div>
              <div class="icon">
                <i class="ion ion-heart"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>

          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $userCount ?></h3>

                <p>Registered users</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>

          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->

            <?php if($unverifiedUsers):?>
                <div class="small-box bg-orange">
            <?php else:?>
                <div class="small-box bg-info">
            <?php endif?>
              <div class="inner">
                <h3>
                  <?php if($unverifiedUsers):?>
                    <?= count($unverifiedUsers)?>
                  <?php else:?>
                    0
                  <?php endif?>
                </h3>

                <p>Unverified users</p>
              </div>
              <div class="icon">
                  <?php if($unverifiedUsers):?>
                    <i class="ion ion-person"></i>
                  <?php else:?>
                    <i class="ion ion-checkmark"></i>
                  <?php endif?>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->


          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $orderCount ?></h3>

                <p>Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-clipboard"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>


          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?= $newsletterCount ?></h3>

                <p>Newsletter subscriptions</p>
              </div>
              <div class="icon">
                <i class="ion ion-email"></i>
              </div>
              <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>


          <!-- ./col -->
          <div class="col-lg-3 col-6">
              <!-- small box -->
              <?php if($unverifiedNewsletters): ?>
                  <div class="small-box bg-orange">
              <?php else: ?>
                  <div class="small-box bg-info">
              <?php endif ?>
                  <div class="inner">
                      <h3>
                          <?php if($unverifiedNewsletters): ?>
                              <?= count($unverifiedNewsletters) ?>
                          <?php else: ?>
                              0
                          <?php endif ?>
                      </h3>

                      <p>Unverified newsletter subscriptions</p>
                  </div>
                  <div class="icon">
                      <?php if($unverifiedNewsletters): ?>
                          <i class="ion ion-email-unread"></i>
                      <?php else: ?>
                          <i class="ion ion-checkmark"></i>
                      <?php endif ?>
                  </div>
                  <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
              </div>
          </div>
          <!-- ./col -->


        </div>
        
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
              <div class="card-header border-transparent">
                <h3 class="card-title">Page visit</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table m-0">
                    <thead>
                    <tr>
                      <th>Name</th>
                      <th>Number of visitors</th>
                      <th>Percentage</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                      $pageNames = [];

                      $pageVisitLog = logData('[PAGE_VISIT]');

                      foreach($pageVisitLog as $p){
                        $pageNames[] = substr($p->page, 6);
                      }

                      $uniquePageNames = array_unique($pageNames);

                      $pageVisitSum = pageVisitSum($pageNames);
                    ?>
                    <?php foreach($uniquePageNames as $u):?>
                      <?php
                        $pageVisitPerPage = pageVisitPerPage($pageNames, $u);
                      ?>
                      <tr>
                        <td>
                          <?php
                            if($u == 'single'){
                              echo 'singleProductPage';
                            }
                            elseif($u == 'user'){
                              echo 'userProfileEdit';
                            }
                            else{
                              echo $u;
                            }
                          ?>
                        </td>

                        <td><?= number_format($pageVisitPerPage) ?></td>
                        <td><?= pageVisitPerPagePercentage($pageVisitPerPage, $pageVisitSum)?>%</span>
                      
                        </td>
                      </tr>

                    <?php endforeach?>

                    </tbody>
                  </table>
                </div>
                <!-- /.table-responsive -->
              </div>
              <!-- /.card-body -->
              <!-- <div class="card-footer clearfix">
                <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All Orders</a>
              </div> -->
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->

              <div class="col-md-6">
                <!-- USERS LIST -->
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Latest logins</h3>

                    <div class="card-tools">
                      <!-- <span class="badge badge-danger">8 New Members</span> -->
                      <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <ul class="users-list clearfix">
                      <?php if($recentUsers != null):?>
                        <?php foreach($recentUsers as $u):?>
                          <li>
                            <img src="../<?=$u->profile_picture?>" alt="User Image">
                            <a class="users-list-name" href="index.php?page=editUserForm&id=<?=$u->id_user?>"><?=$u->first_name?> <?=$u->last_name?></a>
                            <span class="users-list-date">
                              <?php
                                $entriesForUser = [];

                                foreach($logData as $l){
                                  if(substr($l->id, 4) == $u->id_user){
                                    $entriesForUser[] = $l;
                                  }
                                }

                                $lastTime = getLastRow($entriesForUser)->time;

                                $timestamp = strtotime(substr($lastTime, 6));

                                $hoursAgo = floor((time() - $timestamp) / 3600);
                                $minutesAgo = floor((time() - $timestamp) / 60);
                                $secondsAgo = floor((time() - $timestamp));

                                if($hoursAgo > 0){
                                  echo $hoursAgo . ' h. ago';
                                }
                                elseif($minutesAgo > 0){
                                  echo $minutesAgo . ' min. ago';
                                }
                                elseif($secondsAgo > 0){
                                  echo $secondsAgo . ' sec. ago';
                                }

                              ?>
                            </span>
                          </li>
                        <?php endforeach?>
                      <?php else:?>
                        <p id="noUsersText">There are no logged in users from the last 24 hours!</p>
                      <?php endif?>
                    </ul>
                    <!-- /.users-list -->
                  </div>
                  <!-- /.card-body -->
                  <div class="card-footer text-center">
                    <a href="index.php?page=users">View All Users</a>
                  </div>
                  <!-- /.card-footer -->
                </div>
                <!--/.card -->
              </div>
            <!--/.direct-chat -->

            <!-- /.card -->
          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->

          <section class="col-lg-5 connectedSortable">
            <!-- Info Boxes Style 2 -->

            <!-- PRODUCT LIST -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Recently Added Products</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                  <?php foreach($recentlyAddedProducts as $p):?>
                      <li class="item">
                        <div class="product-img">
                          <img src="../<?=$p->cover_photo?>" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                          <a href="index.php?page=editProductForm&id=<?=$p->id?>" class="product-title"><?=$p->name?>
                            <span class="badge badge-warning float-right">$<?=round($p->price, 2)?></span></a>
                          <span class="product-description">
                            <?=$p->description?>
                          </span>
                        </div>
                      </li>
                  <?php endforeach?>
                  <!-- /.item -->
                </ul>
              </div>
              <!-- /.card-body -->
              <div class="card-footer text-center">
                <a href="index.php?page=products" class="uppercase">View All Products</a>
              </div>
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </section>
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  