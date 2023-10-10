              
<table class="table table-sm">
    <thead>
        <tr>
            <th colspan="2">Products</th>                      
            <th>Quantity</th>
            <th>&nbsp;</th>
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
            <td><b><?=$product['name']?></b> <i><?=$product['option_name']?></i><br/><i><?=$product['code']?> Rs. <span id="price_<?=$product_ref?>"><?=$product_price?></span></i></td>                      
            <td><input type="number" class="form-control form-control-sm" style="width:50px;" id="qty_<?=$product_ref?>" min="1" max="9999" value="1" /></td>                      
            <td>
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

        var prodReffId = $(this).attr('id');

             if($(this).attr('incart') == 1){
                 $(this).attr('incart', '0');
                 $(this).removeClass('btn-success');
                 $(this).addClass('btn-default');
                 $('.row_'+prodReffId).removeClass('text-success');
                 incartproducts = incartproducts.replace(',' + prodReffId, '');  
                 
                 cartItemsCount -= 1;                 
     
             } else {
                 $(this).attr('incart', '1');
                 $(this).removeClass('btn-default');
                 $(this).addClass('btn-success');                        
                 $('.row_'+prodReffId).addClass('text-success');
                incartproducts = incartproducts + ',' + prodReffId; 
                
                cartItemsCount += 1;                 
             }
             
        $('#cart_items').html(cartItemsCount);     
        $('#incartproducts').val(incartproducts);
        
    });
                
        
 });
</script>
