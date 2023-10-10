<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Store App | Sales</title>

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/fontawesome-free/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- DataTables -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <!-- daterange picker -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/daterangepicker/daterangepicker.css">
  
        <!-- Select2 -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/select2/css/select2.min.css">
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

        <!-- Theme style -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/dist/css/adminlte.min.css">
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/dist/css/custom.css">
        <!-- Google Font: Source Sans Pro -->
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
        
    </head>
    <body class="sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed control-sidebar-slide-open text-sm accent-success">
        <div class="wrapper">
            <!-- Navbar -->
            <?= include_once 'header_navigation.php'; ?>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <?= include_once 'sidebar.php'; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper" style="margin-top: 20px; padding-top: 20px;">
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">                                    
                                    <div class="col-md-3 col-6">                                         
                                        <div class="form-group">
                                            <select class="form-control form-control-sm action_filter" id="payment_status">                                               
                                                <option value="0">Both Sales</option> 
                                                <option value="pending">Unpaid Sales</option>
                                                <option value="paid">Paid Sales</option>      
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                  <span class="input-group-text"><i class="far fa-clock"></i></span>
                                                </div>
                                                <input type="text" class="form-control form-control-sm float-right" id="sales_durations" >
                                            </div>
                                        </div>
                                    </div>                                   
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm select2 action_filter" id="customer" >
                                                <option value="0" selected="selected">All Customer</option>
                                                <?php
                                                if (is_array($customers)) {
                                                    foreach ($customers as $customer) {
                                                        $company = (!empty($customer['company']) && $customer['company'] != '-') ? " ," . $customer['company'] : '';
                                                        echo '<option value="' . $customer['id'] . '" >' . ucfirst($customer['name']) . $company . ' (#' . $customer['id'] . ')</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>                                    
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" id="list_filter" style="height:400px;">
                                <div class="screen_middle text-info"><i class="fa fa-spinner fa-spin"></i> Please wait! data is loading...</div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div><!--/. container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Main Footer -->
            <?= include_once 'footer_copyright.php'; ?>
        </div>
        <!-- ./wrapper -->

        <div id="model_container"></div>


        <!-- REQUIRED SCRIPTS -->
        <!-- jQuery -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
       
        <!-- DataTables -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

        <!-- Select2 -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/select2/js/select2.full.min.js"></script>
        
        <!-- date-range-picker -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/moment/moment.min.js"></script>
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/daterangepicker/daterangepicker.js"></script>

        <!-- AdminLTE App -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/adminlte.js"></script>

        <!-- OPTIONAL SCRIPTS -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/demo.js"></script>

        <script type="text/javascript">
 
            $(document).ready(function(){
                //Date range picker
                $('#sales_durations').daterangepicker({
                    locale: {
                        format: 'YYYY/MM/DD'
                      }                      
                });
                               
                //Initialize Select2 Elements
                $('.select2').select2();

                $('.action_filter').change(function () {
                      
                    load_sales();
                });
                $('.applyBtn').click(function () {                    
                    setTimeout(function(){ 
                        load_sales(); 
                    }, 100);
                });

               load_sales();
               
               
                 
            });

            function load_sales() {
                
                var payment_status = $('#payment_status').val();
                var sale_date = $('#sales_durations').val();
                var customer = $('#customer').val();

                var postData  = "action=get_sales";
                    postData += "&payment_status=" + payment_status;
                    postData += "&sale_date=" + sale_date;
                    postData += "&customer=" + customer;
              
                $.ajax({
                    type: "POST",
                    url: "<?= base_url('storeapp/ajaxActions') ?>",
                    data: postData,
                    beforeSend: function () {
                        $('#list_filter').html('<div class="screen_middle"><i class="fa fa-spinner fa-spin"></i> Please wait! data is loading...</div>');
                    },
                    success: function (htmlData) {

                        $('#list_filter').html(htmlData);
                    }
                });
            }

        </script>

        <?= include_once 'footer_closed_body.php'; ?>
    </body>
</html>
