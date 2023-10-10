<?php if ($GP['products-index'] || $GP['products-add'] || $GP['products-barcode'] || $GP['products-adjustments'] || $GP['products-stock_count'] || $GP['products-import'] || $GP['products-batches']) { ?>
    <li class="mm_products">
        <a class="dropmenu" href="#">
            <i class="fa fa-barcode"></i>
            <span class="text"> <?= lang('products'); ?>
            </span> <span class="chevron closed"></span>
        </a>
        <ul>
            <li id="products_index">
                <a class="submenu" href="<?= site_url('products'); ?>">
                    <i class="fa fa-barcode"></i><span
                        class="text"> <?= lang('list_products'); ?></span>
                </a>
            </li>
            <?php if ($GP['products-add']) { ?>
                <li id="products_add">
                    <a class="submenu" href="<?= site_url('products/add'); ?>">
                        <i class="fa fa-plus-circle"></i><span
                            class="text"> <?= lang('add_product'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($GP['products-barcode']) { ?>
                <li id="products_sheet">
                    <a class="submenu"
                       href="<?= site_url('products/print_barcodes'); ?>">
                        <i class="fa fa-tags"></i><span
                            class="text"> <?= lang('print_barcode_label'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($GP['products-import']) { ?>    
                <li id="products_import_csv">
                    <a class="submenu" href="<?= site_url('products/import_csv'); ?>">
                        <i class="fa fa-file-text"></i><span class="text"> <?= lang('import_products'); ?></span>
                    </a>
                </li>
            <?php } ?> 
            <?php if ($GP['products-adjustments']) { ?>
                <li id="products_quantity_adjustments">
                    <a class="submenu" href="<?= site_url('products/quantity_adjustments'); ?>">
                        <i class="fa fa-filter"></i><span class="text"> <?= lang('quantity_adjustments'); ?></span>
                    </a>
                </li>
                <li id="products_add_adjustment">
                    <a class="submenu" href="<?= site_url('products/add_adjustment'); ?>">
                        <i class="fa fa-filter"></i><span class="text"> <?= lang('add_adjustment'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($GP['products-stock_count']) { ?>
                <li id="products_stock_counts">
                    <a class="submenu"
                       href="<?= site_url('products/stock_counts'); ?>">
                        <i class="fa fa-list-ol"></i>
                        <span class="text"> <?= lang('stock_counts'); ?></span>
                    </a>
                </li>
                <li id="products_count_stock">
                    <a class="submenu"
                       href="<?= site_url('products/count_stock'); ?>">
                        <i class="fa fa-plus-circle"></i>
                        <span class="text"> <?= lang('count_stock'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($GP['products-batches']) { ?>
                <li id="products_batches">
                    <a href="<?= site_url('products/batches') ?>">
                        <i class="fa fa-database"></i><span  class="text"> <?= lang('Manage Batches'); ?></span> <img src="<?= site_url('themes/default/assets/images/new.gif') ?>" height="30px" alt="new">
                    </a>
                </li>
                <!-- End Batch No -->
            <?php } ?>
        </ul>
    </li>
<?php } ?>

<?php if ($GP['orders-eshop_order'] || $GP['orders-order_items'] || $GP['orders-order_items_stocks']) {  ?>
<li class="mm_orders <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
    <a class="dropmenu" href="#">
        <i class="fa fa-bar-chart"></i>
        <span class="text"> <?= lang('orders'); ?> 
        </span> <span class="chevron closed"></span>
    </a>
    <ul> 
        <?php if ($GP['orders-eshop_order'] && $Settings->active_eshop) { ?>
        <li id="orders_eshop_order">
            <a class="submenu" href="<?= site_url('orders/eshop_order'); ?>">
                <i class="fa fa-list-ol"></i>
                <span class="text"> Eshop Orders</span>
            </a>
        </li>
        <?php } if ($GP['orders-order_items']) { ?>
        <li id="orders_order_items">
            <a class="submenu" href="<?= site_url('orders/order_items'); ?>">
                <i class="fa fa-list-ol"></i>
                <span class="text"> <?= lang('Order_Items_List'); ?></span>
            </a>
        </li>
        <?php } if ($GP['orders-order_items_stocks']) { ?>
        <?php if (in_array($Settings->pos_type, ['fruits_vegetables', 'fruits_vegetabl', 'grocerylite', 'grocery'])) { ?>
            <li id="orders_order_items_stocks">
                <a class="submenu" href="<?= site_url('orders/order_items_stocks'); ?>">
                    <i class="fa fa-list-ol"></i>
                    <span class="text"> <?= lang('Order Products Quantity'); ?></span>
                </a>
            </li>
        <?php } } ?>
    </ul>
</li> 
<?php } ?>    
<?php if ($GP['sales-index'] || $GP['sales-add'] || $GP['sales-deliveries'] || $GP['sales-gift_cards'] || $GP['eshop_sales-sales'] || $GP['offline-sales']) { ?>
    <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
        <a class="dropmenu" href="#">
            <i class="fa fa-heart"></i>
            <span class="text"> <?= lang('sales'); ?>
            </span> <span class="chevron closed"></span>
        </a>
        <ul>
            <?php if ($GP['sales-index']) { ?>
                <li id="sales_index">
                    <a class="submenu" href="<?= site_url('sales'); ?>">
                        <i class="fa fa-heart"></i><span class="text"> <?= lang('list_sales'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php if (POS && $GP['pos-index']) { ?>
                <li id="pos_sales">
                    <a class="submenu" href="<?= site_url('pos/sales'); ?>">
                        <i class="fa fa-heart"></i><span
                            class="text"> <?= lang('pos_sales'); ?></span>
                    </a>
                </li>
            <?php } ?>

            <?php if ($GP['eshop_sales-sales'] && $Settings->active_eshop) { ?>
                <li id="eshop_sales_sales">
                    <a class="submenu" href="<?= site_url('eshop_sales/sales'); ?>">
                        <i class="fa fa-heart"></i>
                        <span class="text"> Eshop Sales</span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($GP['offline-sales']) { ?>
                <li id="offline_sales">
                    <a class="submenu" href="<?= site_url('offline/sales'); ?>">
                        <i class="fa fa-heart"></i>
                        <span class="text">  Offline Sales</span>
                    </a>
                </li>
            <?php } ?> 
            <?php if ($Settings->pos_type == 'restaurant' && $Settings->active_urbanpiper) { ?>
                <?php if ($GP['urban_piper_sales']) { ?>
                    <li class="urbanpiper_sales"> 
                        <a class="submenu" href="<?= site_url('urban_piper/sales'); ?>">
                            <i class="fa fa-plus-circle"></i>
                            <span class="text"> <?= lang('Urban Piper Sales'); ?></span>
                        </a>
                    </li>  
                <?php } ?>
            <?php } ?> 

            <?php if ($GP['sales-add']) { ?>
                <li id="sales_add">
                    <a class="submenu" href="<?= site_url('sales/add'); ?>">
                        <i class="fa fa-plus-circle"></i><span
                            class="text"> <?= lang('add_sale'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['sales-deliveries']) {
                ?>
                <li id="sales_deliveries">
                    <a class="submenu" href="<?= site_url('sales/deliveries'); ?>">
                        <i class="fa fa-truck"></i><span
                            class="text"> <?= lang('deliveries'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['sales-gift_cards']) {
                ?>
                <li id="sales_gift_cards">
                    <a class="submenu" href="<?= site_url('sales/gift_cards'); ?>">
                        <i class="fa fa-gift"></i><span class="text"> <?= lang('gift_cards'); ?></span>
                    </a>
                </li>
            <?php } if ($GP['sales_add_csv']) { ?>

                <li id="sales_sale_by_csv">
                    <a class="submenu" href="<?= site_url('sales/sale_by_csv'); ?>">
                        <i class="fa fa-plus-circle"></i>
                        <span class="text"> <?= lang('add_sale_by_csv'); ?></span>
                    </a>
                </li>   
            <?php } if ($GP['all_sale_lists']) { ?>
                <li id="sales_all_sale_lists">
                    <a class="submenu" href="<?= site_url('sales/all_sale_lists'); ?>">
                        <i class="fa fa-plus-circle"></i>
                        <span class="text"> <?= lang('All_Sale_List'); ?> <img src="<?= site_url('themes/default/assets/images/new.gif') ?>" height="30px" alt="new"></span>
                    </a>
                </li>
            <?php } if ($GP['sales-challans']) { ?>
                 <li id="sales_challans">
                    <a class="submenu" href="<?= site_url('sales/challans'); ?>">
                        <i class="fa fa-plus-circle"></i>
                        <span class="text"> <?= lang('Challans List'); ?> </span>
                    </a>
                </li>  
            <?php } if ($GP['sales-add_challans']) { ?>    
                <li id="sales_add">
                    <a class="submenu" href="<?= site_url('sales/add?sale_action=chalan'); ?>">
                        <i class="fa fa-plus-circle"></i>
                        <span class="text"> <?= lang('Add Challan'); ?></span>
                    </a>
                </li>
            <?php } ?>    
        </ul>
    </li>
<?php }
if ($GP['sales-add_challans'] || $GP['sales-challans']) {
?>
<li class="mm_challans">
    <a class="dropmenu" href="#">
        <i class="fa fa-file-text-o"></i>
        <span class="text"> <?= lang('Challans'); ?> <img src="<?= site_url('themes/default/assets/images/new.gif') ?>" height="30px" alt="new"></span>
        <span class="chevron closed"></span>
    </a>
    <ul>
    <?php if ($GP['sales-challans']) { ?>
        <li id="sales_challans">
            <a class="submenu" href="<?= site_url('sales/challans'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('Challans List'); ?> </span>
            </a>
        </li> 
    <?php } if ($GP['sales-add_challans']) { ?>
        <li id="sales_challans">
            <a class="submenu" href="<?= site_url('sales/add?sale_action=chalan'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('Add Challan'); ?></span>
            </a>
        </li>
    <?php } ?>
    </ul>
</li>
<?php } ?>
<?php if ($Settings->active_urbanpiper) { ?>

    <!--  Urbanpiper -->
    <li class="mm_urban_piper" >
        <a class="dropmenu" href="#">
            <i class="fa fa-magnet"></i>
            <span class="text"> <?= lang('Urbanpiper'); ?> </span>
            <span class="chevron closed"></span>
        </a>
        <ul> 
            <?php if ($GP['urbanpiper_settings']) { ?>
                <li id="urban_piper_settings">
                    <a href="<?= site_url('urban_piper/settings') ?>">
                        <i class="fa fa-cogs" aria-hidden="true"></i>
                        <span  class="text" > Urbanpiper Settings </span>   
                    </a>
                </li>
            <?php } ?>
            <?php if ($GP['urbanpiper_maange_stores']) { ?>
                <li id="urban_piper_store_info">
                    <a href="<?= site_url('urban_piper/store_info') ?>">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span  class="text" > Manage Stores </span>
                    </a>    
                </li>
            <?php } ?>
            <?php if ($GP['urbanpiper_maange_catalogue']) { ?>
                <li id="urban_piper_product_platform">
                    <a href="<?= site_url('urban_piper/product_platform') ?>">
                        <i class="fa fa-archive" aria-hidden="true"></i>
                        <span  class="text" > Manage Catalogue </span>
                    </a> 

                </li>
            <?php } ?>
            <?php if ($GP['urbanpiper_maange_order']) { ?>
                <li id="urban_piper_index">
                    <a href="<?= site_url('urban_piper') ?>">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span  class="text" > Manage Orders </span>
                    </a>    
                </li>
            <?php } ?>
        </ul>
    </li> 
    <!-- Urbanpiper -->

<?php }//end if ?>

<?php if ($GP['crm_portal']) {
    ?>
    <li class="">
        <a class="submenu" href="<?= site_url('smsdashboard'); ?>">
            <i class="fa fa-envelope"></i><span
                class="text"> <?= lang('CRM Portal'); ?></span>
        </a>
    </li>
<?php } if ($GP['quotes-index'] || $GP['quotes-add']) { ?>
    <li class="mm_quotes">
        <a class="dropmenu" href="#">
            <i class="fa fa-heart-o"></i>
            <span class="text"> <?= lang('quotes'); ?> </span>
            <span class="chevron closed"></span>
        </a>
        <ul>
            <li id="sales_index">
                <a class="submenu" href="<?= site_url('quotes'); ?>">
                    <i class="fa fa-heart-o"></i><span
                        class="text"> <?= lang('list_quotes'); ?></span>
                </a>
            </li>
            <?php if ($GP['quotes-add']) { ?>
                <li id="sales_add">
                    <a class="submenu" href="<?= site_url('quotes/add'); ?>">
                        <i class="fa fa-plus-circle"></i><span
                            class="text"> <?= lang('add_quote'); ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>

<?php if ($GP['purchases-index'] || $GP['purchases-add'] || $GP['purchases-expenses'] || $GP['purchases-notification']) { ?>
    <li class="mm_purchases">
        <a class="dropmenu" href="#">
            <i class="fa fa-star"></i>
            <span class="text"> <?= lang('purchases'); ?>
            </span> <span class="chevron closed"></span>
        </a>
        <ul>
            <li id="purchases_index">
                <a class="submenu" href="<?= site_url('purchases'); ?>">
                    <i class="fa fa-star"></i><span
                        class="text"> <?= lang('list_purchases'); ?></span>
                </a>
            </li>
            <?php if ($GP['purchases-add']) { ?>
                <li id="purchases_add">
                    <a class="submenu" href="<?= site_url('purchases/add'); ?>">
                        <i class="fa fa-plus-circle"></i><span
                            class="text"> <?= lang('add_purchase'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($GP['purchases-expenses']) { ?>
                <li id="purchases_expenses">
                    <a class="submenu"
                       href="<?= site_url('purchases/expenses'); ?>">
                        <i class="fa fa-dollar"></i><span
                            class="text"> <?= lang('list_expenses'); ?></span>
                    </a>
                </li>
                <li id="purchases_add_expense">
                    <a class="submenu"
                       href="<?= site_url('purchases/add_expense'); ?>"
                       data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-plus-circle"></i><span
                            class="text"> <?= lang('add_expense'); ?></span>
                    </a>
                </li>
            <?php } if ($GP['purchase_add_csv']) { ?>

                <li id="purchases_purchase_by_csv">
                    <a class="submenu"
                       href="<?= site_url('purchases/purchase_by_csv'); ?>">
                        <i class="fa fa-plus-circle"></i>
                        <span class="text"> <?= lang('add_purchase_by_csv'); ?></span>
                    </a>
                </li>  
            <?php }  if($Settings->synced_data_sales){
                if($GP['purchases-notification']){ ?>
                   
                    <li id="purchases_noification">
                       <a class="submenu" href="<?= site_url('purchases/purchase_notification'); ?>">
                           <i class="fa fa-dollar"></i>
                           <span class="text"> <?= lang('Purchase_Notification'); ?></span>
                       </a>
                   </li>
            <?php
                } 
            } ?>    
        </ul>
    </li>
<?php } ?>
<?php if ($GP['transfers-index'] || $GP['transfers-add'] || $GP['transfers_add_csv'] || $GP['transfers-request'] || $GP['transfers-add_request']) { ?>
   <li class="mm_transfers">
        <a class="dropmenu" href="#">
            <i class="fa fa-exchange"></i>
            <span class="text"> <?= lang('transfers'); ?> </span>
            <span class="chevron closed"></span>
        </a>
        <ul>        
        <?php if ($GP['transfers-index']) { ?>
        <li id="transfers_index">
            <a class="submenu" href="<?= site_url('transfers'); ?>">
                <i class="fa fa-star-o"></i><span
                    class="text"> <?= lang('list_transfers'); ?></span>
            </a>
        </li>
        <?php } ?>
        <?php if ($GP['transfers-add']) { ?>
        <li id="transfers_add">
            <a class="submenu" href="<?= site_url('transfers/add'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_transfer'); ?></span>
            </a>
        </li>
        <?php } ?>
        <?php if ($GP['transfers_add_csv']) { ?>
        <li id="transfers_transfer_by_csv">
            <a class="submenu"
               href="<?= site_url('transfers/transfer_by_csv'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_transfer_by_csv'); ?></span>
            </a>
        </li>
        <?php } ?>
        <?php if ($GP['transfers-request']) { ?>
        <li id="transfers_request">
            <a class="submenu"
               href="<?= site_url('transfers/request'); ?>">
                <i class="fa fa-exchange"></i><span
                    class="text"> <?= lang('Requests'); ?></span>
            </a>
        </li>
        <?php } ?>
        <?php if ($GP['transfers-add_request']) { ?>
         <li id="transfers_add_request">
            <a class="submenu"
               href="<?= site_url('transfers/add_request'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('Add Request'); ?></span>
            </a>
        </li>
        <?php } ?>
    </ul>
    </li>
<?php } ?>

<?php if ($GP['customers-index'] || $GP['customers-add'] || $GP['suppliers-index'] || $GP['suppliers-add']) { ?>
    <li class="mm_auth mm_customers mm_suppliers mm_billers">
        <a class="dropmenu" href="#">
            <i class="fa fa-users"></i>
            <span class="text"> <?= lang('people'); ?> </span>
            <span class="chevron closed"></span>
        </a>
        <ul>
            <?php if ($GP['customers-index']) { ?>
                <li id="customers_index">
                    <a class="submenu" href="<?= site_url('customers'); ?>">
                        <i class="fa fa-users"></i><span class="text"> <?= lang('list_customers'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['customers-add']) {
                ?>
                <li id="customers_index">
                    <a class="submenu" href="<?= site_url('customers/add'); ?>"
                       data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-plus-circle"></i><span
                            class="text"> <?= lang('add_customer'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['suppliers-index']) {
                ?>
                <li id="suppliers_index">
                    <a class="submenu" href="<?= site_url('suppliers'); ?>">
                        <i class="fa fa-users"></i><span
                            class="text"> <?= lang('list_suppliers'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['suppliers-add']) {
                ?>
                <li id="suppliers_index">
                    <a class="submenu" href="<?= site_url('suppliers/add'); ?>"
                       data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-plus-circle"></i><span
                            class="text"> <?= lang('add_supplier'); ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>

<?php if ($GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts'] || $GP['reports-products'] || $GP['reports-monthly_sales'] || $GP['reports-sales'] || $GP['reports-payments'] || $GP['reports-purchases'] || $GP['reports-customers'] || $GP['reports-suppliers'] || $GP['reports-expenses'] || $GP['reports-warehouse_sales_report'] || $GP['reports-gst_reports']) { ?>
    <li class="mm_reports">
        <a class="dropmenu" href="#">
            <i class="fa fa-bar-chart-o"></i>
            <span class="text"> <?= lang('reports'); ?> </span>
            <span class="chevron closed"></span>
        </a>
        <ul>
            <?php if ($GP['reports-quantity_alerts']) { ?>
                <li id="reports_quantity_alerts">
                    <a href="<?= site_url('reports/quantity_alerts') ?>">
                        <i class="fa fa-sort-amount-desc"></i><span
                            class="text"> <?= lang('product_quantity_alerts'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['reports-expiry_alerts']) {
                ?>
                <?php if ($Settings->product_expiry) { ?>
                    <li id="reports_expiry_alerts">
                        <a href="<?= site_url('reports/expiry_alerts') ?>">
                            <i class="fa fa-bar-chart-o"></i><span class="text"> <?= lang('product_expiry_alerts'); ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php
            }
            if ($GP['reports-products']) {
                ?>
                <li id="reports_products">
                    <a href="<?= site_url('reports/products') ?>">
                        <i class="fa fa-filter"></i><span
                            class="text"> <?= lang('products_report'); ?></span>
                    </a>
                </li>
                <li id="reports_adjustments">
                    <a href="<?= site_url('reports/adjustments') ?>">
                        <i class="fa fa-barcode"></i><span
                            class="text"> <?= lang('adjustments_report'); ?></span>
                    </a>
                </li>
                <li id="reports_categories">
                    <a href="<?= site_url('reports/categories_report') ?>">
                        <i class="fa fa-folder-open"></i><span
                            class="text"> <?= lang('categories_report'); ?></span>
                    </a>
                </li>
                <li id="reports_brands">
                    <a href="<?= site_url('reports/brands') ?>">
                        <i class="fa fa-cubes"></i><span
                            class="text"> <?= lang('brands_report'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['reports-daily_sales']) {
                ?>
                <li id="reports_daily_sales">
                    <a href="<?= site_url('reports/daily_sales') ?>">
                        <i class="fa fa-calendar-o"></i><span class="text"> <?= lang('daily_sales'); ?></span>
                    </a>
                </li>
                <!--<li id="reports_daily_salesup">
                    <a href="<?= site_url('reports/daily_sales_up') ?>">
                        <i class="fa fa-calendar-check-o"></i><span
                            class="text"> <?= lang('Urban Piper Daily Sales'); ?></span>  <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
                    </a>
                </li>-->
                <?php
            }
            if ($GP['reports-monthly_sales']) {
                ?>
                <li id="reports_monthly_sales">
                    <a href="<?= site_url('reports/monthly_sales') ?>">
                        <i class="fa fa-calendar-o"></i><span
                            class="text"> <?= lang('monthly_sales'); ?></span>
                    </a>
                </li>
            <?php } ?>                                                  
            <?php
            if ($GP['reports-sales']) {
                ?>
                <li id="reports_sales">
                    <a href="<?= site_url('reports/sales') ?>">
                        <i class="fa fa-line-chart"></i><span
                            class="text"> <?= lang('sales_report'); ?></span>
                    </a>
                </li>
                <li id="reports_sales_gst">
                    <a href="<?= site_url('reports/sales_gst_report') ?>">
                        <i class="fa fa-line-chart"></i><span class="text"> <?= lang('sales_report'); ?>
                            GST </span>
                    </a>
                </li>
                <?php if ($GP['reports-warehouse_sales_report']) { ?>
                    <li id="reports_warehouse_sales">
                        <a href="<?= site_url('reports/warehouse_sales') ?>">
                            <i class="fa fa-line-chart"></i><span class="text"> Compare Warehouse <?= lang('sales_report'); ?></span>
                        </a>
                    </li>
                <?php } ?>
                <?php
            } ?>
 <?php if ($GP['reports-gst_reports']) { ?>
             <li id="reports_new_gst_reports">
                <a href="<?= site_url('reports_new/gst_reports') ?>">
                    <i class="fa fa-line-chart"></i><span class="text"> Simple TAX/GST Reports <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" /></span>
                </a>
            </li>
            <?php } ?>

           <?php  if ($GP['reports-payments']) {
                ?>
                <li id="reports_payments">
                    <a href="<?= site_url('reports/payments') ?>">
                        <i class="fa fa-money"></i><span
                            class="text"> <?= lang('payments_report'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['reports-daily_purchases']) {
                ?>
                <li id="reports_daily_purchases">
                    <a href="<?= site_url('reports/daily_purchases') ?>">
                        <i class="fa fa-calendar-check-o"></i><span
                            class="text"> <?= lang('daily_purchases'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['reports-monthly_purchases']) {
                ?>
                <li id="reports_monthly_purchases">
                    <a href="<?= site_url('reports/monthly_purchases') ?>">
                        <i class="fa fa-calendar"></i><span
                            class="text"> <?= lang('monthly_purchases'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['reports-purchases']) {
                ?>
                <li id="reports_purchases">
                    <a href="<?= site_url('reports/purchases') ?>">
                        <i class="fa fa-cart-plus"></i><span
                            class="text"> <?= lang('purchases_report'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['report_purchase_gst']) {
                ?>

                <li id="reports_purchases_gst">
                    <a href="<?= site_url('reports/purchases_gst_report') ?>">
                        <i class="fa fa-line-chart"></i><span
                            class="text"> <?= lang('purchases_report'); ?>
                            GST </span>
                    </a>
                </li>

                <?php
            }
            if ($GP['reports-expenses']) {
                ?>
                <li id="reports_expenses">
                    <a href="<?= site_url('reports/expenses') ?>">
                        <i class="fa fa-star"></i><span
                            class="text"> <?= lang('expenses_report'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['reports-customers']) {
                ?>
                <li id="reports_customer_report">
                    <a href="<?= site_url('reports/customers') ?>">
                        <i class="fa fa-users"></i><span
                            class="text"> <?= lang('customers_report'); ?></span>
                    </a>
                </li>
                <?php
            }
            if ($GP['reports-suppliers']) {
                ?>
                <li id="reports_supplier_report">
                    <a href="<?= site_url('reports/suppliers') ?>">
                        <i class="fa fa-truck"></i><span
                            class="text"> <?= lang('suppliers_report'); ?></span>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </li>
<?php } ?>
<?php
if ($GP['printer-setting']) {
    ?>
    <li id="printers">
        <a href="<?= site_url('system_settings/printers'); ?>">
            <i class="fa fa-print"></i>
            <span class="text">  Manage Printers Option</span>
        </a>
    </li>
<?php } ?>