<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">

        <title>Store App | Order Items</title>

        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/fontawesome-free/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

        <!-- Select2 -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/select2/css/select2.min.css">
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

        <!-- Bootstrap4 Duallistbox -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">

        <!-- Toastr -->
        <link rel="stylesheet" href="<?= $assets ?>AdminLTE_3_0_4/plugins/toastr/toastr.min.css">

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
                        <form name="form_order_items" method="post" action="<?=base_url('storeapp/ajaxActions')?>" >
                        <!-- Main row -->
                        <div class="row">          
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Order No. <?= $order['order_no'] ?>  (Customer Name: <?= $order['customer'] ?>)</h3>                
                                    </div>
                                    <!-- /.card-header -->
                                    <div id="order_items" class="card-body table-responsive p-0">
                                        <h6 class="screen_middle"> Item List Will Display Here...</h6>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>           
                        </div>
                        <!-- /.row -->
                        <div class="row">          
                            <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Order Status</label>
                                                        <?php
                                                        $order_status = $order['sale_status'];
                                                        $$order_status = ' selected="selected" ';
                                                        ?>
                                                        <select class="form-control form-control-sm select2bs4" name="order_status">
                                                            <option value="pending" <?= $pending ?> >Pending</option>
                                                            <option value="processing" <?= $processing ?>>Processing</option>
                                                            <option value="ready" <?= $ready ?>>Ready</option>
                                                            <option value="completed" <?= $completed ?>>Completed</option>
                                                            <option value="cancel" <?= $cancel ?>>Cancel</option>                    
                                                        </select>
                                                    </div>
                                                    <!-- /.form-group -->
                                                    <div class="form-group">
                                                        <label>Order Discount (Rs. / %)</label>
                                                        <input type="text" name="order_discount" id="order_discount" value="<?=$order['order_discount_id']?$order['order_discount_id']:0?>" placeholder="Discount Rs. / %" class="form-control form-control-sm" onblur="calculate_order_value()" />
                                                    </div>
                                                    <!-- /.form-group -->  
                                                    <div class="form-group">
                                                        <label>Delivery / Shipping Amt.</label>
                                                        <input type="text" name="shipping" id="shipping" placeholder="Shipping Amount" value="<?=$order['shipping']?>" class="form-control form-control-sm" onblur="calculate_order_value()" />
                                                    </div>
                                                    <!-- /.form-group -->  
                                                    <div class="form-group">
                                                        <label>Staff Note</label>
                                                        <input type="text" name="staff_note" id="staff_note" value="<?=$order['staff_note']?>"  placeholder="Enter Staff Note Here" class="form-control form-control-sm" />
                                                    </div>
                                                    <!-- /.form-group -->  
                                                </div>
                                                <!-- /.col -->
                                                <div class="col-md-6">                                                      
                                                    <div class="form-group">                                                        
                                                        <label class="text-capitalize">Order Total Amount : <span id="show_order_amount"></span></label>                                                        
                                                    </div>
                                                    <?php
                                                    
                                                    if(is_array($payments) && $payments[0]['id'] && $payments[0]['amount'] > 0){
                                                    ?>
                                                    <div class="form-group">
                                                        <table class="table table-bordered table-sm">
                                                            <tr>
                                                                <th>Payment Date</th>
                                                                <th>Paid Amount</th>
                                                                <th>Paid By</th>                                                                
                                                            </tr>
                                                        <?php 
                                                        foreach ($payments as $key => $payment) {
                                                        ?>
                                                            <tr>
                                                                <td><?=$payment['date']?></td>
                                                                <td>Rs. <?=$this->sma->formatDecimal($payment['amount'],2)?></td>
                                                                <td><?=$payment['paid_by']?></td>                                                                
                                                            </tr>
                                                        <?php 
                                                        $total_paid += $payment['amount'];
                                                        
                                                        }//end foreach. ?>
                                                            <tr>
                                                                <th>Total Paid</th>                                                                 
                                                                <th>Rs. <?=$this->sma->formatDecimal($total_paid)?></th>
                                                                <th></th>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <?php
                                                   }
                                                    ?>
                                                    <div class="form-group">
                                                        <label class="text-capitalize">Balance Amount : <span id="show_balance_amount"></span></label><br/>
                                                        <label class="text-capitalize">Payment Status : <?=$order['payment_status']?></label>
                                                    </div>                                                    
                                                    <div class="form-group div_payment_action">
                                                        <label>Payment Action </label>                                                       
                                                        <select name="payment_action" id="payment_action" class="form-control form-control-sm">                        
                                                            <option value="due">No Payment</option>
                                                            <option value="paid">Add Payment</option>
                                                        </select>
                                                    </div>
                                                    <!-- /.form-group -->
                                                    <div class="form-group hideme div_payment">
                                                        <label>Payment Mode</label>                                                        
                                                        <select class="form-control form-control-sm select2bs4 payment_fields" name="payment_mode" id="payment_mode" >
                                                            <option value="">Select</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="upi">UPI/BhimApp</option>                    
                                                            <option value="paytm">PayTM</option>                    
                                                            <option value="Googlepay">GooglePay</option>                    
                                                            <option value="phonepay">PhonePay</option>                    
                                                            <option value="Cheque">Cheque</option>                    
                                                        </select>
                                                    </div>
                                                    <!-- /.form-group -->
                                                    <div class="form-group hideme div_payment">
                                                        <label>Payment Amount</label>
                                                        <input type="text" class="form-control form-control-sm payment_fields" name="payment_amount" id="payment_amount" placeholder="Amount" onblur="return check_valid_amount();" />
                                                    </div>
                                                    <!-- /.form-group -->  
                                                    <div class="form-group hideme div_payment">
                                                        <label>Transaction Reff./Cheque No.</label>
                                                        <input type="text" class="form-control form-control-sm payment_fields" name="transaction_no" id="transaction_no" placeholder="Transaction Reff./Cheque No." />
                                                    </div>
                                                    <!-- /.form-group -->  
                                                </div>
                                                <!-- /.col -->              
                                            </div>              
                                            <!-- /.row -->            
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">                                           
                                            <input type="hidden" name="rounding" id="rounding" value="0" />
                                            <input type="hidden" name="order_total" id="order_total" />
                                            <input type="hidden" name="action" value="update_order" />
                                            <input type="hidden" name="order_id" id="order_id" value="<?= $order['id'] ?>" />
                                            <input type="hidden" name="customer_id" id="customer_id" value="<?= $order['customer_id'] ?>" /> 
                                            <input type="hidden" name="payment_id" id="payment_id" value="<?=$payment['id']?>" />
                                            <input type="hidden" name="paid_amount" id="paid_amount" value="<?=$total_paid?>" />
                                            <input type="hidden" name="balance_amount" id="balance_amount" />
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div> 
                                    </div>                               
                            </div>

                        </div><!-- /.row -->
                    </form>
                    </div><!--/. container-fluid -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <!-- Control Sidebar -->
            <?= include_once 'sidebar_right.php'; ?>
            <!-- /.control-sidebar -->

            <!-- Main Footer -->
            <?= include_once 'footer_copyright.php'; ?>
        </div>
        <!-- ./wrapper -->

       <div id="model_container"></div>

        <!-- Add Item Model -->
        <div class="modal fade" id="modal_select_items"  style="width: 100%;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Add Items ( Order No. <?= $order['order_no'] ?> ) </h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">                       
                        <div class="row">
                            <div class="col-md-6 col-sm-6 ">
                                <div class="form-group">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="product_keyword" id="product_keyword"  placeholder="Enter Item Name / Code" class="form-control">
                                        <span class="input-group-append">
                                            <button type="button" class="btn btn-info btn-flat" id="productByKeyword">Search</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 ">
                                <select id="productByCategories" class="form-control form-control-sm select2">
                                    <option>Select Category</option>
                                    <?php
                                    if(is_array($categories['main'])){
                                        foreach ($categories['main'] as $cid => $category) {
                                            echo '<option value="'.$cid.'" searchfield="category_id" style="font-weight:bolder;text-transform: capitalize;">'.$category->name.'</option>';
                                            if(isset($categories[$cid]) && is_array($categories[$cid])){
                                                foreach ($categories[$cid] as $scid => $subcategory) {
                                                    echo '<option value="'.$scid.'" searchfield="subcategory_id" style="text-transform: capitalize;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$subcategory->name.'</option>';
                                                }
                                            }
                                        }//end foreach                                                                              
                                    }//end if. 
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="row" id="search_products" style="height: 320px; overflow-y: auto; margin-top: 10px"> 
                            <h5 class="screen_middle">Products Will Display Here...</h5>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <div class="modal-footer justify-content-between">
                        <input type="hidden" id="incartproducts" value="0" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>          
                        <button type="button" class="btn btn-primary" onclick="add_products()"><span id="cart_items">0</span> Items Selected</button>

                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        <!-- /Add Item Model -->

        <!-- REQUIRED SCRIPTS -->
        <!-- jQuery -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- bs-custom-file-input -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

        <!-- Select2 -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/select2/js/select2.full.min.js"></script>
        <!-- Bootstrap4 Duallistbox -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>

        <!-- SweetAlert2 -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/sweetalert2/sweetalert2.min.js"></script>
        <!-- Toastr -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/plugins/toastr/toastr.min.js"></script>

        <!-- AdminLTE App -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/adminlte.js"></script>

        <!-- OPTIONAL SCRIPTS -->
        <script src="<?= $assets ?>AdminLTE_3_0_4/dist/js/demo.js"></script>

        <script type="text/javascript">

            $(document).ready(function () {

                load_items();              
               
                $('#payment_action').change(function(){
                    
                     var balance_amount = $('#balance_amount').val();
                     
                    switch($(this).val()){
                        case 'due':                            
                            $('.div_payment').hide();
                            $('#payment_amount').val(0);
                            $('.payment_fields').attr('disabled','disabled');
                            break;
                        case 'paid':
                            $('.div_payment').show(); 
                            $('#payment_amount').val(balance_amount);
                            $('.payment_fields').removeAttr('disabled');
                            break;
                    }//end switch.
                                          
                    if( parseInt(balance_amount) == 0) {
                        $('.payment_fields').attr('disabled','disabled');
                    }
                    
                });
                
                
                $('#productByCategories').change(function(){
                        
                    var searchValue = $('#productByCategories').val();
                    var searchField = $('#productByCategories option:selected').attr("searchfield");
 
                    search_products('CATEGORY', searchField, searchValue);
                    
                });
                
                $('#productByKeyword').click(function(){
                        
                    var searchValue = $('#product_keyword').val();
                    var searchField = '';
                    
                    if(searchValue.trim() == '' || searchValue.trim().length < 3) {
                        alert('Please enter minimum 3 characters.');
                        $('#product_keyword').val('');
                        return false;
                    } else {                    
                        search_products('KEYWORDS', searchField, searchValue);
                    }
                    
                });

            }); 
            
            function check_valid_amount(){
                
               var balance_amount = $('#balance_amount').val();
               var payment_amount = $('#payment_amount').val();
               
               if(payment_amount > balance_amount || payment_amount < 0) {
                   
                    $('#payment_amount').val(balance_amount);
                    alert("Payment Amount Is Invalid");
                    return false;
               }                
            }
            
            function calculate_order_value(){
                
                var settings_rounding     = parseFloat($('#settings_rounding').val());
                var grand_total           = parseFloat($('#grand_total').val());
                var paid_amount           = parseFloat($('#paid_amount').val());
               
                var shipping              = parseFloat($('#shipping').val());
                
                var order_discount  = $('#order_discount').val();
                var discount = 0;
                
                var ds = order_discount ? order_discount : '0';
                if (ds.indexOf("%") !== -1) {
                    var pds = ds.split("%");
                    if (!isNaN(pds[0])) {
                        discount = parseFloat(((grand_total) * parseFloat(pds[0])) / 100);
                    } else {
                        discount = parseFloat(ds);
                    }
                } else {
                    discount = parseFloat(ds);
                }
                
                shipping = shipping ? shipping : 0;
                
                var order_total = grand_total - discount + shipping;
                
                if (settings_rounding > 0) {
                    var round_total = roundNumber(order_total, settings_rounding);
                    var rounding = (round_total - order_total);
                }
                
                paid_amount = paid_amount ? paid_amount : '0';
                
                var balance = parseFloat(round_total - paid_amount);
                
                $('#balance_amount').val(balance);
                $('#payment_amount').val(balance);
                $('#paid_amount').val(paid_amount);
                
                $('#order_total').val(round_total);
                $('#show_order_amount').html('Rs. '+round_total+'.00');
                $('#show_balance_amount').html('Rs. '+balance+'.00');
                $('#rounding').val(rounding);
                
                if(balance > 0) {
                    $('.div_payment_action').show();
                } else {
                    $('.div_payment_action').hide();
                }
                 
            }
            
            function roundNumber(number, toref) {
                var rn = number;
                switch (toref) {
                    case 1:
                        rn = Math.round(number * 20) / 20;
                        break;
                    case 2:
                        rn = Math.round(number * 2) / 2;
                        break;
                    case 3:
                        rn = Math.round(number);
                        break;
                    case 4:
                        rn = Math.ceil(number);
                        break;
                    default:
                        rn = number;
                }
                return rn;
            }

            function load_items() {

                var postData = "action=get_order_items";
                postData += "&order_id=<?= $order['id'] ?>";

                $.ajax({
                    type: "POST",
                    url: "<?= base_url('storeapp/ajaxActions') ?>",
                    data: postData,
                    beforeSend: function () {
                        $('#action_msg').html('<div class="alert alert-info"><i class="fa fa-refresh fa-spin" ></i> Please Wait! Data Is Loading...</div>');
                    },
                    success: function (htmlResponse) {

                        $('#order_items').html(htmlResponse);
                        
                        setTimeout(function(){ calculate_order_value(); }, 500);
                    }
                });
            }
            
            function search_products(searchBy, searchField, searchValue){
            
                var postData = "action=search_products";
                postData += "&search_by="+searchBy;
                postData += "&searchField="+searchField;
                postData += "&searchValue="+searchValue;
                postData += "&order_id=<?= $order['id'] ?>";

                $.ajax({
                    type: "POST",
                    url: "<?= base_url('storeapp/ajaxActions') ?>",
                    data: postData,
                    beforeSend: function () {
                        $('#search_products').html('<div class="text-info"><i class="fa fa-refresh fa-spin" ></i> Please Wait! Data Is Loading...</div>');
                    },
                    success: function (htmlResponse) {

                        $('#search_products').html(htmlResponse);
                    }
                });
            }
            
            function add_products(){
                
                if($('#cart_items').html() > 0){                    
                   var incartproducts = $('#incartproducts').val();
                } else {
                    alert('Please select products');
                    return false;
                }
                
                var postData = "action=add_order_items";
                    postData += "&incartproducts="+incartproducts;
                    postData += "&order_id=<?= $order['id'] ?>";
                    postData += "&customer_id="+$('#customer_id').val();

                $.ajax({
                    type: "POST",
                    url: "<?= base_url('storeapp/ajaxActions') ?>",
                    data: postData,
                    beforeSend: function () {
                        $('#search_products').html('<div class="text-info"><i class="fa fa-refresh fa-spin" ></i> Please Wait! Data Is Loading...</div>');
                    },
                    success: function (jsonData) {
                        
                         var obj = $.parseJSON(jsonData);
                    
                        if (obj.status == 'SUCCESS') {
                            $('#search_products').html('<div class="alert alert-success">SUCCESS</div>');                            
                        }
                        
                        if (obj.status == 'ERROR') {
                            $('#search_products').html('<div class="alert alert-danger">ERROR</div>');                            
                        }
                        load_items();
                        setTimeout(function(){ $('#modal_select_items').modal('hide'); }, 1000);
                    }
                });
            }
            
        </script>

        <?= include_once 'footer_closed_body.php'; ?>
    </body>
</html>
