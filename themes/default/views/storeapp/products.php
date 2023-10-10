<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Store App | Products Stocks</title>

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
                                    <div class="col-md-6 col-sm-12 ">
                                        <div class="form-group">
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="product_keyword" id="product_keyword"  placeholder="Enter Item Name / Code" class="form-control">
                                                <span class="input-group-append">
                                                    <button type="button" class="btn btn-info btn-flat" id="productByKeyword">Search</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 ">
                                        <select id="productByCategories" class="form-control-sm select2" style="width:100%;" >
                                            <option>Select Category</option>
                                            <?php
                                            if (is_array($categories['main'])) {
                                                foreach ($categories['main'] as $cid => $category) {
                                                    echo '<option value="' . $cid . '" searchfield="category_id" style="font-weight:bolder;text-transform: capitalize;">' . $category->name . '</option>';
                                                    if (isset($categories[$cid]) && is_array($categories[$cid])) {
                                                        foreach ($categories[$cid] as $scid => $subcategory) {
                                                            echo '<option value="' . $scid . '" searchfield="subcategory_id" style="text-transform: capitalize;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $subcategory->name . '</option>';
                                                        }
                                                    }
                                                }//end foreach                                                                              
                                            }//end if. 
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive p-0" id="search_products" style="height:330px;">
                                <div class="screen_middle text-info"> Use above filter to get products.</div>
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
        <!-- Select2 -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/select2/js/select2.full.min.js"></script>


        <!-- AdminLTE App -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/adminlte.js"></script>

        <!-- OPTIONAL SCRIPTS -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/demo.js"></script>

        <script type="text/javascript">

            $(document).ready(function () {
                //Initialize Select2 Elements
                $('.select2').select2();

                $('#productByCategories').change(function () {

                    var searchValue = $('#productByCategories').val();
                    var searchField = $('#productByCategories option:selected').attr("searchfield");

                    search_products('CATEGORY', searchField, searchValue);

                });

                $('#productByKeyword').click(function () {

                    var searchValue = $('#product_keyword').val();
                    var searchField = '';

                    if (searchValue.trim() == '' || searchValue.trim().length < 3) {
                        alert('Please enter minimum 3 characters.');
                        $('#product_keyword').val('');
                        return false;
                    } else {
                        search_products('KEYWORDS', searchField, searchValue);
                    }

                });


            });

            function search_products(searchBy, searchField, searchValue) {

                var postData = "action=products_list";
                postData += "&search_by=" + searchBy;
                postData += "&searchField=" + searchField;
                postData += "&searchValue=" + searchValue;

                $.ajax({
                    type: "POST",
                    url: "<?= base_url('storeapp/ajaxActions') ?>",
                    data: postData,
                    beforeSend: function () {
                        $('#search_products').html('<div class="text-info"><i class="fa fa-spinner fa-spin" ></i> Please Wait! Data Is Loading...</div>');
                    },
                    success: function (htmlResponse) {
                        $('#search_products').html(htmlResponse);
                    }
                });
            }

        </script>

        <?= include_once 'footer_closed_body.php'; ?>
    </body>
</html>
