<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Store App | Customers</title>

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/fontawesome-free/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

        <!-- DataTables -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">

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
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                
                                <!-- /.card-header -->
                                <div class="card-body" style="padding:5px;">
                                    <table id="example1" class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>                                                
                                                <th>Contact</th>                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                <?php
                                 
                                 
                                    if(is_array($customers)){
                                       
                                        foreach ($customers as $key => $customer) {
                                ?>
                                           <tr>
                                               <td class="text-capitalize">
                                                    CODE: #<?=$customer['id']?><br/>
                                                    <?=$customer['name']?>
                                               </td>
                                               <td>
                                                    <?= $customer['phone']?><br/>
                                                    <?= $customer['email']?>
                                               </td>   
                                                 
                                            </tr> 
                                <?php
                                        }//end foreach
                                    }
                                ?>
                                          
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
                </section>
                <!-- /.content -->
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

        <!-- AdminLTE App -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/adminlte.js"></script>

        <!-- OPTIONAL SCRIPTS -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/demo.js"></script>

        <script type="text/javascript">

            $(document).ready(function () {

                $('#example1').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": true,
                    "responsive": false,
                });

            });

        </script>

        <?= include_once 'footer_closed_body.php'; ?>
    </body>
</html>
