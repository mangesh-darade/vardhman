<table class="table table-hover text-wrap">
    <thead>
        <tr>
            <th class="side-paddding10">#</th>                     
            <th class="side-paddding10">Product Name</th>
            <th class="side-paddding10" width="60px">Quantity</th>
            <th class="side-paddding10" width="60px">Unit&nbsp;Price</th>
            <th class="side-paddding10 text-center"><i class="fa fa-trash"></i></th>
        </tr>
    </thead>
    <tbody>
    <?php
      
        if(is_array($order_items)){
            $i= $total_items = $total = 0;
            foreach ($order_items as $product_ref => $item) {
                $i++;
                $item_id = $item['item_id'];
                $item_status = 'pending';
        ?>  
            <tr id="row_item_<?=$item_id?>" class="<?=$rowClass?>">
                <td class="side-paddding10"><?=$i?></td>                     
                <td class="side-paddding10">
                    <div class="text-capitalize"><b> <?=$item['name']?>
                        <?php if($item['option_id']) {  ?>
                            <i id="product_option_name_<?=$product_ref?>"><?=$item['option_name']?></i> 
                        <?php } ?>
                        </b>
                    </div>                    
                </td>
                <td class="side-paddding10">
                    <input type="number" min="1" max="100" step="1" size="3" name="unit_quantity[<?=$product_ref?>]" id="unit_quantity_<?=$product_ref?>" value="<?=number_format($item['unit_quantity'],0)?>" class="form-control form-control-sm" />
                </td>
                <td class="side-paddding10">
                    <input type="number" name="unit_price[<?=$product_ref?>]" id="unit_price_<?=$product_ref?>" value="<?=number_format($item['price'],2)?>" class="form-control form-control-sm" />
                </td>
                
                <td class="side-paddding10" style="text-align:center; vertical-align: middle; font-weight: bold;">
                    <span><i class="fa fa-trash text-danger"></i></span>
                    <?php $item['subtotal'] = number_format((float)$item['unit_quantity']*(float)$item['price'],2)?>
                    <input type="hidden" name="item_id[<?=$product_ref?>]" value="<?=$item_id?>" />
                    <input type="hidden" name="item_name[<?=$product_ref?>]" id="item_name_<?=$product_ref?>" value="<?=$item['name']?> <?=$item['option_name']?>" />
                    <input type="hidden" name="item_option_id[<?=$product_ref?>]" id="item_option_id_<?=$product_ref?>" value="<?=$item['option_id']?>" />
                    <input type="hidden" name="item_option_name[<?=$product_ref?>]" id="item_option_name_<?=$product_ref?>" value="<?=$item['option_name']?>" />
                    <input type="hidden" name="item_status[<?=$product_ref?>]" id="item_status_<?=$product_ref?>" value="<?=$item_status?>" />
                <?php if(!in_array($item_status, ['deleted','out_of_stock'])){ ?>    
                    <input type="hidden" name="unit_price[<?=$product_ref?>]" id="unit_price_<?=$product_ref?>" value="<?=$item['price']?>" />
                    <input type="hidden" name="tax_method[<?=$product_ref?>]" id="tax_method_<?=$product_ref?>" value="<?=$item['tax_method']?>" />
                    <input type="hidden" name="tax[<?=$product_ref?>]" id="tax_<?=$product_ref?>" value="<?=$item['tax']?>" />
                    <input type="hidden" name="tax_rate_id[<?=$product_ref?>]" id="tax_rate_id_<?=$product_ref?>" value="<?=$item['tax_rate_id']?>" />
                    <input type="hidden" name="item_tax[<?=$product_ref?>]" id="item_tax_<?=$product_ref?>" value="<?=$item['tax_rate']?>" />
                    <input type="hidden" name="quantity[<?=$product_ref?>]" id="quantity_<?=$product_ref?>" value="<?=$item['quantity']?>" />                    
                    <input type="hidden" name="discount[<?=$product_ref?>]" id="discount_<?=$product_ref?>" value="<?=$item['discount']?>" />
                    <input type="hidden" name="item_discount[<?=$product_ref?>]" id="item_discount_<?=$product_ref?>" value="<?=$item['item_discount']?>" />
                    <input type="hidden" name="subtotal[<?=$product_ref?>]" id="subtotal_<?=$product_ref?>" value="<?=$item['subtotal']?>" />
                    <input type="hidden" name="unit_id[<?=$product_ref?>]" id="unit_id_<?=$product_ref?>" value="<?=$item['unit_id']?>" />
                    <input type="hidden" name="unit_name[<?=$product_ref?>]" id="unit_name_<?=$product_ref?>" value="<?=$item['unit_name']?>" />
                    <?php }//end if. 
               
                ?> 
                </td>
            </tr> 
        <?php   
                if(!in_array($item_status, ['deleted','out_of_stock'])){
                    $total += $item['subtotal']; 
                    $total_items++;
                }                
            }
                    
        }    ?>                   
    </tbody>
     <tfoot>
        <tr>
            <th class="side-paddding10 text-left" colspan="2">
                <a class="btn btn-primary btn-sm text-white" data-toggle="modal" data-target="#modal_select_items" id="add_new_items" title="Add New Items"><i class="fa fa-plus" ></i> Add Items</a>
            </th>
            <th colspan="3" class="side-paddding10 text-right">
                <span class="lable-inline">Subtotal : Rs. <span id="subtotal_display"><?=$this->sma->formatDecimal($total,2)?></span></span>
                
                <input type="hidden" name="grand_total" id="grand_total" value="<?=$total?>" />                
                <input type="hidden" name="settings_rounding" id="settings_rounding" value="<?=$store_settings_rounding?>" />
                <input type="hidden" name="total_items" id="total_items" value="<?=$total_items?>" />
             </th>
           
        </tr>
    </tfoot>
</table>


<script type="text/javascript">
        
    function cal_unit_price(){
         
       var product_tax         = parseFloat($('#edit_product_tax').val());
       var quantity            = parseFloat($('#edit_quantity').val());
       var real_unit_price     = parseFloat($('#edit_real_unit_price').val());
       var product_tax         = parseFloat($('#edit_product_tax').val());
       var product_discount    = $('#edit_product_discount').val();
       var tax_method          = parseFloat($('#edit_tax_method').val());
       var item_unit           = $('#edit_unit').val();
       var previous_unit       = $('#previous_unit').val();
       var product_varient     = $('#edit_product_varient').val();
       var product_id          = $('#edit_product_id').val();
       
       var edit_product_reff = product_id +'_'+product_varient;
        if(previous_unit == item_unit) {
            $('#edit_base_quantity').val(quantity);
        }  
     
      var edit_unit_code = $("#edit_unit option:selected").attr("unit_code");
      var taxrate = $("#edit_product_tax option:selected").attr("rate");
      var varient_price = parseFloat($("#edit_product_varient option:selected").attr("price"));       
    
      varient_price = (varient_price ? varient_price : 0);
      
      var ds = product_discount ? product_discount : '0';
        if (ds.indexOf("%") !== -1) {
            var pds = ds.split("%");
            if (!isNaN(pds[0])) {
                item_discount = parseFloat(((real_unit_price) * parseFloat(pds[0])) / 100);
            } else {
                item_discount = parseFloat(ds);
            }
        } else {
            item_discount = parseFloat(ds);
        }
      
      var unit_price = (real_unit_price + varient_price) - item_discount;
       
       //Tax Exclusive
       if(tax_method == 1){
         
         var tax = (unit_price * taxrate / 100);             
            unit_price = unit_price + tax;
       }
      
       $('#show_unit_price').html(unit_price);
       $('#edit_unit_price').val(unit_price);
       $('#edit_unit_code').val(edit_unit_code);
       $('#edit_product').val(edit_product_reff);
       
        var previous_option =  $('#previous_option').val();
        var previous_product = product_id + (previous_option ? '_'+ previous_option : '');
       
        var incartproducts = $('#incartproducts').val();                
            incartproducts = incartproducts.replace(',' + previous_product, ','+edit_product_reff);
            $('#incartproducts').val(incartproducts); 
           
    }
   
    function edit_item(item_id){
       
        var product = $('#items_'+item_id).val();
        
        var postData  = "action=model_edit_order_new_item";
            postData += "&item_id="+item_id;
            postData += "&product="+product;
            
            $.ajax({
                type: "POST",
                url: "<?= base_url('storeapp/ajaxActions')?>",
                data: postData,
                beforeSend: function () {                    
                    $('#edit_'+item_id).removeClass('fa-edit');
                    $('#edit_'+item_id).addClass('fa-spinner fa-spin');
                },
                success: function (ModelData) {
                    
                    $('#model_container').html(ModelData);                    
                    $('#modal_edit').modal('show');
                    
                    $('#edit_'+item_id).removeClass('fa-spinner fa-spin');
                    $('#edit_'+item_id).addClass('fa-edit');
                }
            });
    }
    
    function edit_order_items(){
        
        var item_id = $('#edit_item_id').val();
        var quantity = $('#edit_quantity').val();
        var unit_price = $('#edit_unit_price').val();
        var item_unit_quantity = $('#edit_base_quantity').val();
        var unit = $('#edit_unit').val();
        var unit_code = $('#edit_unit_code').val();
        var tax_method = $('#edit_tax_method').val();
        var tax = $('#edit_product_tax').val();
        var tax_rate_id = $('#edit_product_tax').val();
        var discount = $('#edit_product_discount').val();
        var items = $('#edit_product').val();
        var option_name = $('#item_option_name').val();
        var option_id = $('#edit_product_varient').val();
        var subtotal = unit_price * quantity;
              
        $('#unit_price_'+item_id).val(unit_price);
        $('#tax_method_'+item_id).val(tax_method);
        $('#tax_'+item_id).val(tax);
        $('#tax_rate_id_'+item_id).val(tax_rate_id);
        $('#quantity_'+item_id).val(quantity);
        $('#discount_'+item_id).val(discount);
        $('#items_'+item_id).val(items);
        $('#item_option_id_'+item_id).val(option_id);
        $('#item_option_name_'+item_id).val(option_name);
        $('#subtotal_'+item_id).val(subtotal);
        
        $('#product_option_name_'+item_id).html(option_name); 
        
        $('#item_qty_price_'+item_id).html(quantity+' <b>X</b> Rs.'+unit_price +' / '+ unit_code);
            
      //  setTimeout(function(){ $('#modal_edit').modal('hide'); }, 500);
        
        calculate_grand_total();
    }
    
    function calculate_grand_total(){
        
        var cart_count = parseInt($('#cart_count').html());
        var grand_total = 0;
        for(var i=0; i < cart_count, i++){
           grand_total = grand_total + parseInt($('#subtotal_'+i).val());          
        }
       
    }
    
    function status_action(item_id, newStatus){
        
        var newStatusIcon = '';
        var rowClass = '';
       
       var item_current_status = $('#item_status_'+item_id).val()
       
       if(item_current_status == newStatus) return false; 
       
            switch(newStatus){
                case 'pending':
                   newStatusIcon = '<i class="fa fa-cart-arrow-down"></i>';
                   rowClass = 'text-default';
                    break;
                case 'in_progress':
                   newStatusIcon = '<i class="fa fa-box-open text-primary"></i>';
                   rowClass = 'text-primary';
                    break;
                case 'packed':
                   newStatusIcon = '<i class="fa fa-check text-success"></i>';
                   rowClass = 'text-success';
                    break;
                case 'deleted':
                   newStatusIcon = '<i class="fa fa-trash text-danger"></i>';
                   rowClass = 'text-danger text-delete';
                    break;
                case 'out_of_stock':
                   newStatusIcon = '<i class="fa fa-ban text-danger"></i>';
                   rowClass = 'text-danger text-delete';
                    break;                
            }//end switch
            
            var checkStatus = ["deleted", "out_of_stock"];                 

            if( checkStatus.includes(newStatus)) {
                var prodReffId  = $('#prodreff_'+item_id).val();  
                var incartproducts = $('#incartproducts').val();  
                
                incartproducts = incartproducts.replace(',' + prodReffId, '');
                $('#incartproducts').val(incartproducts); 
              
                add_products();

            } else {                        
                $('.row_item_status_'+item_id).html(newStatusIcon);
                $('#row_item_'+item_id).removeClass();
                $('#row_item_'+item_id).addClass(rowClass);
                $('#item_status_'+item_id).val(newStatus);
            }
            
    }

//    function unitToBaseQty(qty, unitObj) {
//    switch (unitObj.operator) {
//        case '*':
//            return parseFloat(qty) * parseFloat(unitObj.operation_value);
//            break;
//        case '/':
//            return parseFloat(qty) / parseFloat(unitObj.operation_value);
//            break;
//        case '+':
//            return parseFloat(qty) + parseFloat(unitObj.operation_value);
//            break;
//        case '-':
//            return parseFloat(qty) - parseFloat(unitObj.operation_value);
//            break;
//        default:
//            return parseFloat(qty);
//    }
//}
//
//    function baseToUnitQty(qty, unitObj) {
//    switch (unitObj.operator) {
//        case '*':
//            return parseFloat(qty) / parseFloat(unitObj.operation_value);
//            break;
//        case '/':
//            return parseFloat(qty) * parseFloat(unitObj.operation_value);
//            break;
//        case '+':
//            return parseFloat(qty) - parseFloat(unitObj.operation_value);
//            break;
//        case '-':
//            return parseFloat(qty) + parseFloat(unitObj.operation_value);
//            break;
//        default:
//            return parseFloat(qty);
//    }
//}
//    
</script>

