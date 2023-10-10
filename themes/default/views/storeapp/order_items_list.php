<table class="table table-hover text-nowrap">
    <thead>
        <tr>
            <th class="side-paddding10">#</th>                     
            <th class="side-paddding10">Items</th>
            <th class="side-paddding10">Status</th>
        </tr>
    </thead>
    <tbody>
    <?php
        if(is_array($order_items)){
            $i= $total_items = $total = 0;
            foreach ($order_items as $item_id => $item) {
                $i++;
                $item_status = $item['item_status'] ? $item['item_status'] : 'pending';
                
                switch ($item_status) {
                    case 'in_progress':
                        //$btnEdit = '<a class="btn btn-default btn-xs" onclick="edit_item('.$item_id.')" ><i class="fa fa-edit"></i></a>';
                        $btnEdit = '<i class="fa fa-edit" id="edit_'.$item_id.'" onclick="edit_item('.$item_id.')"></i>';
                        $iconStatus = '<i class="fa fa-box-open text-primary"></i>';
                        $rowClass = 'text-primary';
                        break;
                    case 'packed':
                        //$btnEdit = '<a class="btn btn-default btn-xs" onclick="edit_item('.$item_id.')" ><i class="fa fa-edit"></i></a>';
                        $btnEdit = '<i class="fa fa-edit" id="edit_'.$item_id.'" onclick="edit_item('.$item_id.')"></i>';
                        $iconStatus = '<i class="fa fa-check text-success"></i>';                        
                        $rowClass = 'text-success';
                        break;
                    
                    case 'deleted':                        
                        $btnEdit = '&nbsp;';
                        $iconStatus = '<i class="fa fa-trash text-danger"></i>';                        
                        $rowClass = 'text-danger text-delete';
                        break;
                    
                     case 'out_of_stock':  
                        $btnEdit = '&nbsp;';
                        $iconStatus = '<i class="fa fa-ban text-danger"></i>';                       
                        $rowClass = 'text-danger text-delete';
                         break;
                    
                    case 'pending':
                    default:
                        
                        $iconStatus = '<i class="fa fa-cart-arrow-down"></i>';
                        //$btnEdit = '<a class="btn btn-default btn-xs"  ><i class="fa fa-edit" onclick="edit_item('.$item_id.')"></i></a>';
                        $btnEdit = '<i class="fa fa-edit" id="edit_'.$item_id.'" onclick="edit_item('.$item_id.')"></i>';                       
                        $rowClass = 'text-default';
                        break;
                }
                
        ?>  
            <tr id="row_item_<?=$item_id?>" class="<?=$rowClass?>">
                <td class="side-paddding10"><?=$i?></td>                     
                <td class="side-paddding10">
                    <div class="text-capitalize"><b> <?=$item['product_name']?>
                        <?php if($item['option_id']) {  ?>
                            <i><?=$item['option_name']?></i> 
                        <?php } ?>
                        </b>
                        <span class="float-right text-primary"><?=$btnEdit?></span>
                    </div>
                    <div>
                        <span>Qty. <?=number_format($item['unit_quantity'],2)?> <?=$item['product_unit_code']?> <b>X</b> Rs.<?=$this->sma->formatDecimal($item['unit_price'],2)?> | <b title="Item Price">Rs.<?=$this->sma->formatDecimal($item['subtotal'],2)?></b></span>
                    </div>
                </td>
                <td class="side-paddding10">
                    <input type="hidden" name="item_id[]" value="<?=$item_id?>" />
                    <input type="hidden" name="item_name[<?=$item_id?>]" id="item_name_<?=$item_id?>" value="<?=$item['product_name']?> <?=$item['option_name']?>" />
                    <input type="hidden" name="item_status[<?=$item_id?>]" id="item_status_<?=$item_id?>" value="<?=$item_status?>" />
                <?php if(!in_array($item_status, ['deleted','out_of_stock'])){ ?>    
                    <input type="hidden" name="real_unit_price[<?=$item_id?>]" value="<?=$item['real_unit_price']?>" />
                    <input type="hidden" name="unit_price[<?=$item_id?>]" value="<?=$item['unit_price']?>" />
                    <input type="hidden" name="tax_method[<?=$item_id?>]" value="<?=$item['tax_method']?>" />
                    <input type="hidden" name="tax[<?=$item_id?>]" value="<?=$item['tax']?>" />
                    <input type="hidden" name="tax_rate_id[<?=$item_id?>]" value="<?=$item['tax_rate_id']?>" />
                    <input type="hidden" name="item_tax[<?=$item_id?>]" value="<?=$item['item_tax']?>" />
                    <input type="hidden" name="quantity[<?=$item_id?>]" value="<?=$item['quantity']?>" />                    
                    <input type="hidden" name="cgst[<?=$item_id?>]" value="<?=$item['cgst']?>" />
                    <input type="hidden" name="sgst[<?=$item_id?>]" value="<?=$item['sgst']?>" />
                    <input type="hidden" name="igst[<?=$item_id?>]" value="<?=$item['igst']?>" />
                    <input type="hidden" name="option_name[<?=$item_id?>]" value="<?=$item['option_name']?>" />
                    <input type="hidden" name="option_id[<?=$item_id?>]" value="<?=$item['option_id']?>" />
                    <input type="hidden" name="item_weight[<?=$item_id?>]" value="<?=$item['item_weight']?>" />
                    <input type="hidden" name="discount[<?=$item_id?>]" value="<?=$item['discount']?>" />
                    <input type="hidden" name="item_discount[<?=$item_id?>]" value="<?=$item['item_discount']?>" />
                    <input type="hidden" name="subtotal[<?=$item_id?>]" value="<?=$item['subtotal']?>" />
                <?php }//end if. 
                    
                $btnStatus ='<div class="btn-group float-right">                    
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <span class="row_item_status_'.$item_id.'">'.$iconStatus.'</span>
                              <div class="dropdown-menu" role="menu">';
                
                $btnStatus .=   '<a class="dropdown-item" href="#" onclick="status_action(\''.$item_id.'\', \'pending\')"><i class="fa fa-cart-arrow-down"></i> Pending</a>';                               
                $btnStatus .=   '<a class="dropdown-item" href="#" onclick="status_action(\''.$item_id.'\', \'in_progress\')"><i class="fa fa-box-open text-primary"></i> In Progress</a>'; 
                $btnStatus .=   '<a class="dropdown-item" href="#" disabled="disabled" onclick="status_action(\''.$item_id.'\', \'packed\')"><i class="fa fa-check text-success"></i> Packed</a>';
                $btnStatus .=   '<div class="dropdown-divider"></div>';
                $btnStatus .=   '<a class="dropdown-item" href="#" onclick="status_action(\''.$item_id.'\', \'deleted\')"><i class="fa fa-trash text-danger"></i> Deleted</a>';
                $btnStatus .=   '<a class="dropdown-item" href="#" onclick="status_action(\''.$item_id.'\', \'out_of_stock\')"><i class="fa fa-ban text-danger"></i> No Stock</a>';
                $btnStatus .=   '</div>
                            </button>
                        </div>';
                
                echo $btnStatus;
               
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
            <th colspan="3" class="side-paddding10">
                <span class="lable-inline">Total Items: <?=$total_items?></span> 
                
                <span class="lable-inline">Subtotal : Rs. <span id="subtotal_display"><?=$this->sma->formatDecimal($total,2)?></span></span>
                <span class="float-right"><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_select_items" title="Add New Items"><i class="fa fa-plus text-white" ></i></a></span>
                
                <input type="hidden" name="grand_total" id="grand_total" value="<?=$total?>" />                
                <input type="hidden" name="settings_rounding" id="settings_rounding" value="<?=$store_settings_rounding?>" />
                <input type="hidden" name="total_items" value="<?=$total_items?>" />
                 
            </th>
        </tr>
    </tfoot>
</table>


<script type="text/javascript">
    $(document).ready(function () {
        
        
    });
    
    function cal_unit_price(){
         
       var product_tax         = parseFloat($('#edit_product_tax').val());
       var unit_quantity       = parseFloat($('#edit_unit_quantity').val());
       var real_unit_price     = parseFloat($('#edit_real_unit_price').val());
       var product_tax         = parseFloat($('#edit_product_tax').val());
       var product_discount    = $('#edit_product_discount').val();
       var tax_method          = parseFloat($('#edit_tax_method').val());
       var item_unit           = $('#edit_unit').val();
       var previous_unit       = $('#previous_unit').val();
       var previous_varient       = $('#previous_varient').val();
       var new_varient     = $('#edit_product_varient').val();
       
     
      var taxrate = $("#edit_product_tax option:selected").attr("rate");
      var varient_price = parseFloat($("#edit_product_varient option:selected").attr("price"));       
      var varient_unit_quantity = parseFloat($("#edit_product_varient option:selected").attr("unit_qty"));       
   
        
        var item_quantity = parseFloat(unit_quantity) * parseFloat(varient_unit_quantity);
    
        if(previous_unit == item_unit) {
            $('#edit_quantity').val(item_quantity);
        }
        
        if(previous_varient == new_varient) {
            var product_unit_weight = $('#edit_unit_weight').val();
            var item_weight = parseFloat(unit_quantity) * parseFloat(product_unit_weight);
            $('#edit_item_weight').val(item_weight);
        } else {            
            $('#edit_unit_weight').val(varient_unit_quantity);
            $('#edit_item_weight').val(item_quantity);
            $('#previous_varient').val(new_varient);
        }
         
    
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
            unit_price += tax;
       }
      
       $('#show_unit_price').html(unit_price);
       $('#edit_unit_price').val(unit_price);
       $('#edit_variant_unit_quantity').val(varient_unit_quantity);
           
    }
    
    function edit_item(item_id){
        
        var postData  = "action=model_edit_order_item";
            postData += "&item_id="+item_id;
            
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
        
        var postData  = "action=edit_order_items";
            postData += "&item_id="+$('#edit_item_id').val();
            postData += "&tax_id="+$('#edit_product_tax').val();
            postData += "&quantity="+$('#edit_quantity').val();
            postData += "&real_unit_price="+$('#edit_real_unit_price').val();
            postData += "&tax_method="+$('#edit_tax_method').val();
            postData += "&unit="+$('#edit_unit').val();
            postData += "&varient="+$('#edit_product_varient').val();
            postData += "&variant_unit_quantity="+$('#edit_variant_unit_quantity').val();
            postData += "&product_id="+$('#edit_product_id').val();
            postData += "&order_id="+$('#edit_order_id').val();
            postData += "&unit_price="+$('#edit_unit_price').val();
            postData += "&product_discount="+$('#edit_product_discount').val();
            postData += "&item_note="+$('#edit_note').val();
            postData += "&item_unit_quantity="+$('#edit_unit_quantity').val();
            postData += "&item_weight="+$('#edit_item_weight').val();
            postData += "&customer_id="+$('#customer_id').val();
             
            $.ajax({
                type: "POST",
                url: "<?= base_url('storeapp/ajaxActions')?>",
                data: postData,
                beforeSend: function () {
                    $('#action_message').addClass('alert alert-info');
                    $('#action_message').html('<i class="fa fa-spinner fa-spin"></i> Please wait...');
                },
                success: function (jsonData) {                                     
                    var obj = $.parseJSON(jsonData);
                     
                    if (obj.status == 'SUCCESS') {
                        $('#action_message').removeClass('alert alert-info');
                        $('#action_message').addClass('alert alert-success');
                        $('#action_message').html('SUCCESS');
                        $('#action_message').show();
                         
                        load_items();
                    }
                    
                    if (obj.status == 'ERROR') {
                        $('#action_message').removeClass('alert alert-info');
                        $('#action_message').addClass('alert alert-danger');
                        $('#action_message').html('No Changes');
                        $('#action_message').show();
                    }
                    
                    setTimeout(function(){ $('#modal_edit').modal('hide'); }, 500);
                   
                }
            });
    }
    
    
    function status_action(item_id, newStatus){
        
        var newStatusIcon = '';
        var rowClass = '';
       
       var item_current_status = $('#item_status_'+item_id).val()
       
       if(item_current_status == newStatus) return false;
       
       var item_name = $('#item_name_'+item_id).val()
//       if(!confirm('Confirm item '+item_name +' is '+newStatus)){
//            return false;
//       } else {
       
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
            
             var postData  = "action=update_order_item_status";
                 postData += "&item_id="+item_id;
                 postData += "&new_status="+newStatus;
            
            $.ajax({
                type: "POST",
                url: "<?= base_url('storeapp/ajaxActions')?>",
                data: postData,
                beforeSend: function () {
                    $('.row_item_status_'+item_id).html('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function (jsonData) {
                    
                    var obj = $.parseJSON(jsonData);
                    
                    if (obj.status == 'SUCCESS') {
                        var checkStatus = ["deleted", "out_of_stock"];                        
                        if( checkStatus.includes(newStatus) > -1 || checkStatus.includes(item_current_status) > -1 ) {
                           
                            load_items();
                        } else {                        
                            $('.row_item_status_'+item_id).html(newStatusIcon);
                            $('#row_item_'+item_id).removeClass();
                            $('#row_item_'+item_id).addClass(rowClass);
                            $('#item_status_'+item_id).val(newStatus);
                        }
                        Toast.fire({
                            icon: 'success',
                            title: 'Action Successful'
                          })
                    }
                    
                    if (obj.status == 'ERROR') {
                        Toast.fire({
                            icon: 'danger',
                            title: 'Action Failed'
                          })
                    }
                }
            });
        
//        }//end else confirm
    }

  
    
function unitToBaseQty(qty, unitObj) {
    switch (unitObj.operator) {
        case '*':
            return parseFloat(qty) * parseFloat(unitObj.operation_value);
            break;
        case '/':
            return parseFloat(qty) / parseFloat(unitObj.operation_value);
            break;
        case '+':
            return parseFloat(qty) + parseFloat(unitObj.operation_value);
            break;
        case '-':
            return parseFloat(qty) - parseFloat(unitObj.operation_value);
            break;
        default:
            return parseFloat(qty);
    }
}

function baseToUnitQty(qty, unitObj) {
    switch (unitObj.operator) {
        case '*':
            return parseFloat(qty) / parseFloat(unitObj.operation_value);
            break;
        case '/':
            return parseFloat(qty) * parseFloat(unitObj.operation_value);
            break;
        case '+':
            return parseFloat(qty) - parseFloat(unitObj.operation_value);
            break;
        case '-':
            return parseFloat(qty) + parseFloat(unitObj.operation_value);
            break;
        default:
            return parseFloat(qty);
    }
}
    
</script>

