              
<table class="table table-sm">
    <thead>
        <tr>
            <th>#</th>                      
            <th>Product (S)</th>                      
            <th>Quantity</th>
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
                
                $stock_qty =  $product['option_id'] ? $product['option_quantity']: $product['quantity'];
       ?>
        <tr>
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
            <td>
                <?=$stock_qty?>
            </td>
        </tr> 
        
       <?php }//End foreach.
        }//end if.
       ?>       
                  
    </tbody>                  
</table>
