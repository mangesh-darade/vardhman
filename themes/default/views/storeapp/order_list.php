<div class="table-responsive">
<?php
if (is_array($orders) && count($orders)) {
?>
    <table class="table m-0">                    
        <tbody>
            <?php    
                foreach ($orders as $order_id => $order) {
                    ?> 
                    <tr>
                        <td class="side-paddding10">
                            <div>
                                <a href="<?= base_url('storeapp/order_items/' . $order_id) ?>" class="text-info" ><i class="lable-inline">#<?= $order['order_no'] ?></i></a>
                                <b class="lable-inline"><?= $order['customer'] ?></b>
                                <span class="float-right text-capitalize" title="Order Status"><i class="fa fa-cart-plus"></i> <?= $order['sale_status'] ?></span>
                            </div>                              
                            <div>
                                <small class="link-inline" title="View Items"><a href="<?= base_url('storeapp/order_items/' . $order_id) ?>" class="text-info" ><i class="fa fa-dolly-flatbed"></i> Items: <?= $order['total_items'] ?></a></small>
                                <small class="link-inline" title="Delivery"><i class="fa fa-truck"></i> <?= $order['delivery_status'] ?></small> 
                                <small class="link-inline" title="Payment"><i class="fa fa-rupee-sign"></i> <?= $order['payment_status'] ?></small> 
                                <!--<small class="link-inline" title="View Recept"><a href="<?= base_url('storeapp/order_invoice/' . $order_id) ?>" class="text-info"><i class="fa fa-receipt"></i>&nbsp;Recept</a></small>-->
                                <small class="link-inline link-last" title="Date"><i class="fa fa-calendar-alt"></i> <?= DateTimeFormat($order['date'], 'j M y') ?></small>
                            </div>
                        </td>
                    </tr>
                    <?php
                }//End foreach.                        
            ?>
        </tbody>
    </table>
<?php } else { ?>
    <p class="text-info text-center"><br/>List is empty</p>
<?php } ?>
</div>
<!-- /.table-responsive -->