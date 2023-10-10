<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Store App | Orders</title>
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?=$assets?>AdminLTE_3_0_4/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?=$assets?>AdminLTE_3_0_4/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=$assets?>AdminLTE_3_0_4/dist/css/adminlte.min.css">
  
  <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/dist/css/custom.css">
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
  <div class="content-wrapper" style="margin-top: 20px; padding-top: 20px;">
     <?php if($message) { ?>
      
      <div class="alert alert-success"><?=$message?></div><?php } ?>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
    <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <div class="col-md-12">
            <!-- TABLE: LATEST ORDERS -->
            <div class="card">
              <div class="card-body p-0">
                  <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="custom-content-below-pending-tab" data-toggle="pill" href="#custom-content-below-pending" role="tab" aria-controls="custom-content-below-pending" aria-selected="true">Pending</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="custom-content-below-processing-tab" data-toggle="pill" href="#custom-content-below-processing" role="tab" aria-controls="custom-content-below-processing" aria-selected="false">Processing</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="custom-content-below-ready-tab" data-toggle="pill" href="#custom-content-below-ready" role="tab" aria-controls="custom-content-below-ready" aria-selected="false">Ready</a>
                    </li>
                     <li class="nav-item">
                      <a class="nav-link" href="<?=base_url('storeapp/order_new')?>" > + Add Order </a>
                    </li> 
                  </ul> 
                  <div class="tab-content" id="custom-content-below-tabContent" style="height:500px; overflow-y: scroll;">
                    <div class="tab-pane fade show active" id="custom-content-below-pending" role="tabpanel" aria-labelledby="custom-content-below-pending-tab">
                        Order Pending
                    </div>
                    <div class="tab-pane fade" id="custom-content-below-processing" role="tabpanel" aria-labelledby="custom-content-below-processing-tab">
                        Order Processing
                    </div>
                    <div class="tab-pane fade" id="custom-content-below-ready" role="tabpanel" aria-labelledby="custom-content-below-ready-tab">
                        Order Ready
                    </div>
<!--                    <div class="tab-pane fade" id="custom-content-below-completed" role="tabpanel" aria-labelledby="custom-content-below-completed-tab">
                        Order Completed
                    </div>-->
                  </div>
              </div>
              <!-- /.card-body -->
              
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
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

<!-- AdminLTE App -->
<script src="<?=$assets?>AdminLTE_3_0_4/dist/js/adminlte.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="<?=$assets?>AdminLTE_3_0_4/dist/js/demo.js"></script>

<script>
    $(document).ready(function(){
        
        if(localStorage.getItem('cartItems')) {
            localStorage.removeItem('customer_id');
            localStorage.removeItem('cartItems');
        }
        
        var orderStatus = ['pending','processing','ready'];
        
        orderStatus.forEach(load_orders);
       
    });

    function load_orders(item) {
        var order_status = item;
        var postData = "action=get_orders";
            postData += "&order_status="+order_status;
         
        $.ajax({
            type: "POST",
            url: "<?= base_url('storeapp/ajaxActions') ?>",
            data: postData,
            beforeSend: function () {
                $('#custom-content-below-'+order_status).html('<div class="alert alert-info"><i class="fa fa-refresh fa-spin" ></i> Please Wait! Data Is Loading...</div>');
            },
            success: function (htmlResponse) {
                $('#custom-content-below-'+order_status).html(htmlResponse);
            }
        });
    }

 
      
       
</script>

<?= include_once 'footer_closed_body.php';?>
</body>
</html>
