              
<table class="table">
    <thead>
        <tr>
            <th>#</th>                      
            <th>Product (S)</th>                      
            <th>Add</th>
        </tr>
    </thead>
    <tbody>
       <?php
        if(count($product_list) > 0 && is_array($product_list)){
          foreach ($product_list as $key => $product) {
              
                $now = strtotime(date('Y-m-d H:i:s'));
                $promo_price = 0;
                if($product['promotion'] == 1 && strtotime($product['start_date']) <= $now && strtotime($product['end_date']) >= $now ){
                   $promo_price = $product_price = (float)$product['promo_price'] + (float)$product['option_price']; 
                } else {
                   $product_price = (float)$product['price'] + (float)$product['option_price']; 
                }   
                
                if($product['tax_method'] == 1){
                    $product_price += (float)($product_price * $product['tax_rate'] / 100);
                }
               
                $product_ref = $product['id'] . ($product['option_id']?'_'.$product['option_id']:'');
                
                 
                
       ?>
        <tr class="row_<?=$product_ref?>">
            <td>
            <?php             
                if(!empty($product['image']) && @getimagesize($thumbs.$product['image'])){
                    echo '<img src="'.$thumbs.$product['image'].'" alt="'.$product['name'].'" style="height:40px;" />';
                } else {
                    echo '<img src="'.$thumbs.'no_image.jpg" alt="no_image" style="height:40px;" />';
                }
            ?> 
            </td>                      
            <td><b><?=$product['name']?></b> <i><?=$product['option_name']?></i><br/>
                <i><?=$product['code']?></i> <input type="number" min="1" max="100" step="1" name="item_unit_quantity_<?=$product_ref;?>" id="item_unit_quantity_<?=$product_ref;?>" value="1" /> <span id="price_<?=$product_ref?>" class="float-right">Rs. <?=$product_price?></span></td>                      
            <td>                
                <input type="hidden" id="item_id_<?=$product_ref;?>" value="<?=$product['id']?>" />
                <input type="hidden" id="item_name_<?=$product_ref;?>" value="<?=$product['name']?>" />
                <input type="hidden" id="item_code_<?=$product_ref;?>" value="<?=$product['code']?>" />
                
                <input type="hidden" id="item_option_id_<?=$product_ref;?>" value="<?=$product['option_id']?>" />
                <input type="hidden" id="item_option_name_<?=$product_ref;?>" value="<?=$product['option_name']?>" />
                <input type="hidden" id="item_option_unit_quantity_<?=$product_ref;?>" value="<?=$product['option_unit_quantity']?>" />
                <input type="hidden" id="item_price_<?=$product_ref;?>" value="<?=$product_price?>" />
                <input type="hidden" id="item_tax_method_<?=$product_ref;?>" value="<?=$product['tax_method']?>" />
                <input type="hidden" id="item_tax_rate_id_<?=$product_ref;?>" value="<?=$product['tax_rate_id']?>" />
                <input type="hidden" id="item_tax_rate_<?=$product_ref;?>" value="<?=$product['tax_rate']?>" />
                <?php
                    $option_unit_weight = ($product['option_id'] && (float)$product['option_unit_quantity']) ? $product['option_unit_quantity'] : 0;
                    
                    $product_unit_weight = (float)$option_unit_weight ? $option_unit_weight : ((float)$product['weight'] ? $product['weight'] : 1);
                    
                    $quantity = ($product['option_id'] && (float)$product['option_unit_quantity']) ? $product['option_unit_quantity'] : 1;
                ?>
                <input type="hidden" id="item_unit_weight_<?=$product_ref;?>" value="<?=$product_unit_weight?>" />
                <input type="hidden" id="item_qty_<?=$product_ref;?>" value="<?=$quantity?>" />                
                <input type="hidden" id="item_unit_id_<?=$product_ref;?>" value="<?=$product['sale_unit']?>" />
                <input type="hidden" id="item_unit_<?=$product_ref;?>" value="<?=$product['unit_name']?>" />
                <input class="hideme" type="checkbox" value="<?=$product_ref;?>" name="cart_item[]" id="chk_<?=$product_ref;?>" />
                <label for="chk_<?=$product_ref;?>" class="btn btn-default btn-sm add_to_cart" incart="0" id="<?=$product_ref;?>"><i class="fa fa-cart-arrow-down"></i></label>
            </td>
        </tr>        
       <?php }//End foreach.
        }//end if.
       ?>         
    </tbody>                  
</table>

<script type="text/javascript">

 $(document).ready(function () {
    
     var incartproducts = $('#incartproducts').val();
     var incartArr = incartproducts.split(',');
     var cartItemsCount = incartArr.length ? (incartArr.length - 1) : 0;
     $('#cart_items').html(cartItemsCount);
     
    incartArr.forEach(function(item) {
        $('.row_'+item).addClass('text-success');
        $('#'+item).attr('incart', '1');
        $('#'+item).removeClass('btn-default');
        $('#'+item).addClass('btn-success'); 
    });
     
    $('.add_to_cart').click(function(){
        var cartItem = {};
        
        var prodReffId = $(this).attr('id');

             if($(this).attr('incart') == 1){
                 localStorage.removeItem('cartItems');
                 $(this).attr('incart', '0');
                 $(this).removeClass('btn-success');
                 $(this).addClass('btn-default');
                 $('.row_'+prodReffId).removeClass('text-success');
                 incartproducts = incartproducts.replace(',' + prodReffId, '');  
                 
                 cartItemsCount -= 1;                 
                 
                 delete itemRows[prodReffId];
             } else {
                 
                 $(this).attr('incart', '1');
                 
                 $(this).removeClass('btn-default');
                 $(this).addClass('btn-success');                        
                 $('.row_'+prodReffId).addClass('text-success');
                incartproducts = incartproducts + ',' + prodReffId; 
                
               var qty = $('#item_qty_'+ prodReffId).val();
               var unit_quantity = $('#item_unit_quantity_'+ prodReffId).val();
               var unit_weight = $('#item_unit_weight_'+ prodReffId).val();
               
                cartItemsCount += 1; 
                cartItem.item_id = $('#item_id_'+ prodReffId).val();
                cartItem.name = $('#item_name_'+ prodReffId).val();
                cartItem.code = $('#item_code_'+ prodReffId).val();
                cartItem.unit_weight = unit_weight;
                cartItem.item_weight = unit_weight;
                cartItem.option_id = $('#item_option_id_'+ prodReffId).val();
                cartItem.option_name = $('#item_option_name_'+ prodReffId).val();
                cartItem.option_unit_quantity = $('#item_option_unit_quantity_'+ prodReffId).val();
                cartItem.price = $('#item_price_'+ prodReffId).val();
                cartItem.tax_method = $('#item_tax_method_'+ prodReffId).val();
                cartItem.tax_rate_id = $('#item_tax_rate_id_'+ prodReffId).val();
                cartItem.tax_rate = $('#item_tax_rate_'+ prodReffId).val();
                cartItem.unit_quantity = unit_quantity;
                cartItem.quantity = formatDecimal(parseFloat(unit_quantity) * parseFloat(qty));
                cartItem.unit_weight = unit_weight;
                cartItem.item_weight = formatDecimal(parseFloat(unit_quantity) * parseFloat(unit_weight));
                cartItem.unit_id = $('#item_unit_id_'+ prodReffId).val();                              
                cartItem.unit_name = $('#item_unit_'+ prodReffId).val();               
                cartItem.discount = 0;               
                
                itemRows[prodReffId] = cartItem;
             }
             
        $('#cart_items').html(cartItemsCount);     
        $('#incartproducts').val(incartproducts);
        
        localStorage.setItem('cartItems', JSON.stringify(itemRows));
        
     
//      console.log( '-----------------------------' );
//      cartItems = JSON.parse(localStorage.getItem('cartItems'));
//       console.log(cartItems);
        
    });
                
        
 });
</script>
