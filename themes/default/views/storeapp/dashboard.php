<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>Store App | Dashboard</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?=$assets?>AdminLTE_3_0_4/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?=$assets?>AdminLTE_3_0_4/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=$assets?>AdminLTE_3_0_4/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=$assets?>AdminLTE_3_0_4/dist/css/custom.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed control-sidebar-slide-open text-sm accent-success">
<div class="wrapper">
  <!-- Navbar -->
   <?= include_once 'header_navigation.php';?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?= include_once 'sidebar.php';?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-2 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                  <a href="<?=base_url('storeapp/orders')?>" class=" text-white">
                    <h3><i class="fas fa-shopping-cart"></i></h3>
                    <p>New Orders</p>
                  </a>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <!--<a href="<?=base_url('storeapp/orders')?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>-->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-2 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <a href="<?=base_url('storeapp/payments')?>" class=" text-white">
                    <h3><small><i class="fa fa-rupee-sign"></i></small></h3>
                    <p>Due Payments</p>
                </a>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <!--<a href="#" class="small-box-footer text-white">More info <i class="fas fa-arrow-circle-right"></i></a>-->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-2 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <a href="<?=base_url('storeapp/customers')?>" class=" text-white">
                  <h3><i class="fa fa-users"></i></h3>
                  <p>Customers</p>
                </a>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <!--<a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>-->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-2 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <a href="<?=base_url('storeapp/products')?>" class=" text-white">
                    <h3><i class="fa fa-box"></i></h3>
                    <p>Products & Stocks</p>
                </a>
              </div>
              <div class="icon">
                <i class="fa fa-chart-pie"></i>
              </div>
              <!--<a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>-->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-2 col-6">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
                  <a href="<?=base_url('reports')?>" class=" text-white">
                    <h3><i class="fa fa-list"></i></h3>
                    <p>Reports</p>
                  </a>
              </div>
              <div class="icon">
                <i class="fa fa-user"></i>
              </div>
              <!--<a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>-->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-2 col-6">
            <!-- small box -->
            <div class="small-box bg-secondary">
              <div class="inner">
                  <a href="<?=base_url('storeapp/sales')?>" class=" text-white">
                    <h3><i class="fas fa-balance-scale-right"></i></h3>
                    <p>Sales</p>
                  </a>
              </div>
              <div class="icon">
                <i class="fa fa-chart-bar"></i>
              </div>
              <!--<a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>-->
            </div>
          </div>
           
        </div>
        <!-- /.row -->
        </div>
    </section>
    <!-- Main content -->
    
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <?= include_once 'sidebar_right.php';?>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <?= include_once 'footer_copyright.php';?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?=$assets?>AdminLTE_3_0_4/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="<?=$assets?>AdminLTE_3_0_4/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=$assets?>AdminLTE_3_0_4/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=$assets?>AdminLTE_3_0_4/dist/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="<?=$assets?>AdminLTE_3_0_4/dist/js/demo.js"></script>

<!-- PAGE SCRIPTS -->
<script src="<?=$assets?>AdminLTE_3_0_4/dist/js/pages/dashboard2.js"></script>


<?= include_once 'footer_closed_body.php';?>
</body>
</html>
