<li class="mm_products">
    <a class="dropmenu" href="#">
        <i class="fa fa-archive"></i>
        <span class="text"> <?= lang('products'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="products_index">
            <a class="submenu" href="<?= site_url('products'); ?>">
                <i class="fa fa-barcode"></i>
                <span class="text"> <?= lang('list_products'); ?></span>
            </a>
        </li>
        <li id="products_add">
            <a class="submenu" href="<?= site_url('products/add'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('add_product'); ?></span>
            </a>
        </li>
        <li id="products_import_csv">
            <a class="submenu" href="<?= site_url('products/import_csv'); ?>">
                <i class="fa fa-file-text"></i>
                <span class="text"> <?= lang('import_products'); ?></span>
            </a>
        </li>
        <li id="products_print_barcodes">
            <a class="submenu"
               href="<?= site_url('products/print_barcodes'); ?>">
                <i class="fa fa-tags"></i>
                <span class="text"> <?= lang('print_barcode_label'); ?></span>
            </a>
        </li>
        <li id="products_quantity_adjustments">
            <a class="submenu"
               href="<?= site_url('products/quantity_adjustments'); ?>">
                <i class="fa fa-filter"></i>
                <span class="text"> <?= lang('quantity_adjustments'); ?></span>
            </a>
        </li>
        <li id="products_add_adjustment">
            <a class="submenu"
               href="<?= site_url('products/add_adjustment'); ?>">
                <i class="fa fa-filter"></i>
                <span class="text"> <?= lang('add_adjustment'); ?></span>
            </a>
        </li>
        <li id="products_stock_counts">
            <a class="submenu" href="<?= site_url('products/stock_counts'); ?>">
                <i class="fa fa-list-ol"></i>
                <span class="text"> <?= lang('stock_counts'); ?></span>
            </a>
        </li>
        <li id="products_count_stock">
            <a class="submenu" href="<?= site_url('products/count_stock'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('count_stock'); ?></span>
            </a>
        </li>
        <?php if($Settings->product_batch_setting > 0) { ?>
        <li id="products_batches">
            <a href="<?= site_url('products/batches') ?>">
                <i class="fa fa-database"></i><span
                    class="text"> <?= lang('Manage Batches'); ?></span> <img src="<?= site_url('themes/default/assets/images/new.gif') ?>" height="30px" alt="new">
            </a>
        </li>  
        <?php } ?>
    </ul>
</li>
<li class="mm_orders">
    <a class="dropmenu" href="#">
        <i class="fa fa-bar-chart"></i>
        <span class="text"> <?= lang('orders'); ?> 
        </span> <span class="chevron closed"></span>
    </a>
    <ul>                                                        
    <?php if($Settings->active_eshop) { ?>
        <li id="orders_eshop_order">
            <a class="submenu" href="<?= site_url('orders/eshop_order'); ?>">
                <i class="fa fa-list-ol"></i>
                <span class="text"> Eshop Orders</span>
            </a>
        </li>
    <?php } ?>
        <li id="orders_order_items">
            <a class="submenu" href="<?= site_url('orders/order_items'); ?>">
                <i class="fa fa-list-ol"></i>
                <span class="text"> <?= lang('Order_Items_List'); ?></span>
            </a>
        </li>
        <?php if (in_array($Settings->pos_type, ['fruits_vegetables', 'fruits_vegetabl', 'grocerylite', 'grocery'])) { ?>
            <li id="orders_order_items_stocks">
                <a class="submenu" href="<?= site_url('orders/order_items_stocks'); ?>">
                    <i class="fa fa-list-ol"></i>
                    <span class="text"> <?= lang('Order Products Quantity'); ?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
</li>
<li class="mm_sales mm_eshop_sales mm_pos">
    <a class="dropmenu" href="#">
        <i class="fa fa-bar-chart"></i>
        <span class="text"> <?= lang('sales'); ?>
        </span> <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="sales_index">
            <a class="submenu" href="<?= site_url('sales'); ?>">
                <i class="fa fa-heart"></i>
                <span class="text"> <?= lang('list_sales'); ?></span>
            </a>
        </li>
    <?php if($Settings->active_eshop) { ?>    
        <li id="eshop_sales_sales">
            <a class="submenu" href="<?= site_url('eshop_sales/sales'); ?>">
                <i class="fa fa-heart"></i>
                <span class="text"> Eshop Sales</span>
            </a>
        </li>
    <?php } ?>
        <li id="offline_sales">
            <a class="submenu" href="<?= site_url('offline/sales'); ?>">
                <i class="fa fa-heart"></i>
                <span class="text">  Offline Sales</span>
            </a>
        </li>
        <?php if (POS) { ?>
            <li id="pos_sales">
                <a class="submenu" href="<?= site_url('pos/sales'); ?>">
                    <i class="fa fa-heart"></i>
                    <span class="text"> <?= lang('pos_sales'); ?></span>
                </a>
            </li>
        <?php } ?>
        <?php if($Settings->active_urbanpiper) { ?>        
            <li class="urban_piper_sales"> 
                <a class="submenu" href="<?= site_url('urban_piper/sales'); ?>">
                    <i class="fa fa-plus-circle"></i>
                    <span class="text"> <?= lang('Urban Piper Sales'); ?></span>
                </a>
            </li>  
        <?php } ?> 

        <li id="sales_all_sale_lists">
            <a class="submenu" href="<?= site_url('sales/all_sale_lists'); ?>">
                <i class="fa fa-heart"></i>
                <span class="text"> <?= lang('All_Sale_List'); ?> </span>
            </a>
        </li>
        <li id="sales_add">
            <a class="submenu" href="<?= site_url('sales/add'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('add_sale'); ?></span>
            </a>
        </li>
        <li id="sales_sale_by_csv">
            <a class="submenu" href="<?= site_url('sales/sale_by_csv'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('add_sale_by_csv'); ?></span>
            </a>
        </li>
        <li id="sales_deliveries">
            <a class="submenu" href="<?= site_url('sales/deliveries'); ?>">
                <i class="fa fa-truck"></i>
                <span class="text"> <?= lang('deliveries'); ?></span>
            </a>
        </li>
        <li id="sales_gift_cards">
            <a class="submenu" href="<?= site_url('sales/gift_cards'); ?>">
                <i class="fa fa-gift"></i>
                <span class="text"> <?= lang('list_gift_cards'); ?></span>
            </a>
        </li>
        <li id="sales_credit_note">
            <a class="submenu" href="<?= site_url('sales/credit_note'); ?>">
                <i class="fa fa-gift"></i>
                <span class="text"> <?= lang('List_Credit_Note'); ?></span>
            </a>
        </li>
    </ul>
</li>


<li class="mm_challans">
    <a class="dropmenu" href="#">
        <i class="fa fa-file-text-o"></i>
        <span class="text"> <?= lang('Challans'); ?> <img src="<?= site_url('themes/default/assets/images/new.gif') ?>" height="30px" alt="new"></span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="sales_challans">
            <a class="submenu" href="<?= site_url('sales/challans'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('Challans List'); ?> </span>
            </a>
        </li>                                                        
        <li id="sales_challans">
            <a class="submenu" href="<?= site_url('sales/add?sale_action=chalan'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('Add Challan'); ?></span>
            </a>
        </li>
    </ul>
</li>
<li class="mm_quotes">
    <a class="dropmenu" href="#">
        <i class="fa fa-file-text-o"></i>
        <span class="text"> <?= lang('quotes'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="quotes_index">
            <a class="submenu" href="<?= site_url('quotes'); ?>">
                <i class="fa fa-heart-o"></i>
                <span class="text"> <?= lang('list_quotes'); ?></span>
            </a>
        </li>
        <li id="quotes_add">
            <a class="submenu" href="<?= site_url('quotes/add'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('add_quote'); ?></span>
            </a>
        </li>
    </ul>
</li>

<li class="mm_purchases">
    <a class="dropmenu" href="#">
        <i class="fa fa-shopping-cart"></i>
        <span class="text"> <?= lang('purchases'); ?>
        </span> <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="purchases_index">
            <a class="submenu" href="<?= site_url('purchases'); ?>">
                <i class="fa fa-star"></i>
                <span class="text"> <?= lang('list_purchases'); ?></span>
            </a>
        </li>
        <li id="purchases_add">
            <a class="submenu" href="<?= site_url('purchases/add'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('add_purchase'); ?></span>
            </a>
        </li>
        <li id="purchases_purchase_by_csv">
            <a class="submenu"
               href="<?= site_url('purchases/purchase_by_csv'); ?>">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('add_purchase_by_csv'); ?></span>
            </a>
        </li>
        <li id="purchases_expenses">
            <a class="submenu" href="<?= site_url('purchases/expenses'); ?>">
                <i class="fa fa-dollar"></i>
                <span class="text"> <?= lang('list_expenses'); ?></span>
            </a>
        </li>
        <li id="purchases_add_expense">
            <a class="submenu" href="<?= site_url('purchases/add_expense'); ?>"
               data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus-circle"></i>
                <span class="text"> <?= lang('add_expense'); ?></span>
            </a>
        </li>
      <?php  if($Settings->synced_data_sales){ ?>
        <li id="purchases_noification">
            <a class="submenu" href="<?= site_url('purchases/purchase_notification'); ?>">
                <i class="fa fa-dollar"></i>
                <span class="text"> <?= lang('Purchase_Notification'); ?></span>
            </a>
        </li>
       <?php } ?>

    </ul>
</li>
<!--<li class="mm_transfersnew">
    <a class="dropmenu" href="#">
        <i class="fa fa-exchange"></i>
        <span class="text"> <?= lang('New Transfers'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="transfersnew_index">
            <a class="submenu" href="<?= site_url('transfersnew'); ?>">
                <i class="fa fa-star-o"></i><span
                    class="text"> <?= lang('list_transfers'); ?></span>
            </a>
        </li>
        <li id="transfersnew_add">
            <a class="submenu" href="<?= site_url('transfersnew/add'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_transfer'); ?></span>
            </a>
        </li>
        <li id="transfersnew_transfer_by_csv">
            <a class="submenu"
               href="<?= site_url('transfersnew/transfer_by_csv'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_transfer_by_csv'); ?></span>
            </a>
        </li>
        
        <li id="transfersnew_request">
            <a class="submenu"
               href="<?= site_url('transfersnew/request'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('Add Products Request'); ?></span>
            </a>
        </li>
         
    </ul>
</li>-->
<li class="mm_transfers">
    <a class="dropmenu" href="#">
        <i class="fa fa-exchange"></i>
        <span class="text"> <?= lang('transfers'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="transfers_index">
            <a class="submenu" href="<?= site_url('transfers'); ?>">
                <i class="fa fa-star-o"></i><span
                    class="text"> <?= lang('list_transfers'); ?></span>
            </a>
        </li>
        <li id="transfers_add">
            <a class="submenu" href="<?= site_url('transfers/add'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_transfer'); ?></span>
            </a>
        </li>
        <li id="transfers_transfer_by_csv">
            <a class="submenu"
               href="<?= site_url('transfers/transfer_by_csv'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_transfer_by_csv'); ?></span>
            </a>
        </li>

        <li id="transfers_request">
            <a class="submenu"
               href="<?= site_url('transfers/request'); ?>">
                <i class="fa fa-exchange"></i><span
                    class="text"> <?= lang('Requests'); ?> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" /></span>
            </a>
        </li>
         <li id="transfers_add_request">
            <a class="submenu"
               href="<?= site_url('transfers/add_request'); ?>">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('Add Request'); ?> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" /></span>
            </a>
        </li>
    </ul>
</li>

<li class="mm_auth mm_customers mm_suppliers mm_billers">
    <a class="dropmenu" href="#">
        <i class="fa fa-users"></i>
        <span class="text"> <?= lang('people'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <?php if ($Owner) { ?>
            <li id="auth_users">
                <a class="submenu" href="<?= site_url('users'); ?>">
                    <i class="fa fa-users"></i><span
                        class="text"> <?= lang('list_users'); ?></span>
                </a>
            </li>
            <li id="auth_create_user">
                <a class="submenu" href="<?= site_url('users/create_user'); ?>">
                    <i class="fa fa-user-plus"></i><span
                        class="text"> <?= lang('new_user'); ?></span>
                </a>
            </li>
            <li id="billers_index">
                <a class="submenu" href="<?= site_url('billers'); ?>">
                    <i class="fa fa-users"></i><span
                        class="text"> <?= lang('list_billers'); ?></span>
                </a>
            </li>
            <li id="billers_index">
                <a class="submenu" href="<?= site_url('billers/add'); ?>"
                   data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-plus-circle"></i><span
                        class="text"> <?= lang('add_biller'); ?></span>
                </a>
            </li>
            <li id="employees_index">
                <a class="submenu" href="<?= site_url('employees/index'); ?>">
                    <i class="fa fa-users"></i><span
                        class="text"> <?= lang('List_Employees'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
                </a>
            </li>
<!--            <li id=delivery_person_index">
                <a class="submenu" href="<?= site_url('sales_person/deliveryPerson'); ?>">
                    <i class="fa fa-users"></i><span
                        class="text"> <?= lang('List_Delivery_Person'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
                </a>
            </li>-->
            <li id="employees_add">
                <a class="submenu" href="<?= site_url('employees/add'); ?>"
                   data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-plus-circle"></i><span
                        class="text"> <?= lang('Add_Employee'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
                </a>
            </li>

        <?php } ?>
        <li id="customers_index">
            <a class="submenu" href="<?= site_url('customers'); ?>">
                <i class="fa fa-users"></i><span
                    class="text"> <?= lang('list_customers'); ?></span>
            </a>
        </li>
        <li id="customers_index">
            <a class="submenu" href="<?= site_url('customers/add'); ?>"
               data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_customer'); ?></span>
            </a>
        </li>
        <li id="suppliers_index">
            <a class="submenu" href="<?= site_url('suppliers'); ?>">
                <i class="fa fa-users"></i><span
                    class="text"> <?= lang('list_suppliers'); ?></span>
            </a>
        </li>
        <li id="suppliers_index">
            <a class="submenu" href="<?= site_url('suppliers/add'); ?>"
               data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus-circle"></i><span
                    class="text"> <?= lang('add_supplier'); ?></span>
            </a>
        </li>
    </ul>
</li>
<li class="mm_notifications">
    <a class="submenu" href="<?= site_url('notifications'); ?>">
        <i class="fa fa-info-circle"></i><span
            class="text"> <?= lang('notifications'); ?></span>
    </a>
</li>

<li class="">
    <a class="submenu" href="<?= site_url('smsdashboard'); ?>">
        <i class="fa fa-envelope"></i><span
            class="text"> <?= lang('CRM Portal'); ?></span>
    </a>
</li>
<?php if ($Owner) { ?>
<?php if($Settings->active_eshop) { ?>
    <li class="mm_eshop_admin <?= strtolower($this->router->fetch_method()) != 'eshop_admin' ? '' : 'eshop_admin' ?>">
        <a class="dropmenu" href="#">
            <i class="fa fa-cart-plus"></i><span
                class="text">Eshop Settings</span>
            <span class="chevron closed"></span>
        </a>
        <ul>
            <li id="eshop_admin_pages">
                <a href="<?= site_url('eshop_admin/pages'); ?>">
                    <i class="fa fa-newspaper-o"></i>
                    <span class="text"> Eshop Custom Pages</span>
                </a>
            </li>
            <li id="eshop_admin_shipping_methods">
                <a href="<?= site_url('eshop_admin/shipping_methods'); ?>">
                    <i class="fa fa-cog"></i>
                    <span class="text"> Shipping & Deliveries</span>
                </a>
            </li>
            <li id="eshop_admin_settings">
                <a href="<?= site_url('eshop_admin/settings'); ?>">
                    <i class="fa fa-image"></i>
                    <span class="text"> Media & Settings</span>
                </a>
            </li>
            <li id="eshop_admin_manage_products">
                <a href="<?= site_url('eshop_admin/manage_products'); ?>">
                    <i class="fa fa-list-ol"></i>
                    <span class="text"> Manage Products</span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
                </a>
            </li>
        </ul>
    </li>
<?php } ?>    
<?php if($Settings->active_webshop) { ?>
    <li class="mm_webshop_settings">
        <a class="dropmenu" href="#">
            <i class="fa fa-cart-plus"></i><span
                class="text">Ecommerce </span><img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            <span class="chevron closed"></span>
        </a>
        <ul>
            <li id="webshop_settings_index">
                <a href="<?= site_url('webshop_settings'); ?>">
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="text"> Homepage Layout</span>
                </a>
            </li>
            <li id="webshop_settings_sections">
                <a href="<?= site_url('webshop_settings/sections'); ?>">
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span class="text"> Homepage Sections</span>
                </a>
            </li>
            <li id="webshop_settings_sliders">
                <a href="<?= site_url('webshop_settings/sliders'); ?>">
                    <i class="fa fa-image" aria-hidden="true"></i>
                    <span class="text"> Homepage Sliders</span>
                </a>
            </li>
            <li id="webshop_settings_shipping_methods">
                <a href="<?= site_url('webshop_settings/shipping_methods'); ?>">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                    <span class="text"> Shipping Methods</span>
                </a>
            </li>
            <li id="webshop_settings_manage_products">
                <a href="<?= site_url('webshop_settings/manage_products'); ?>">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                    <span class="text"> Manage Products</span>
                </a>
            </li>
            <li id="webshop_settings_custom_pages">
                <a href="<?= site_url('webshop_settings/custom_pages'); ?>">
                    <i class="fa fa-file-text" aria-hidden="true"></i>
                    <span class="text"> Custom Pages</span>
                </a>
            </li>
        </ul>
    </li>
<?php } ?>
<?php if($Settings->active_urbanpiper) { ?>
        <!--  Urbanpiper -->
        <li class="mm_urban_piper" >
            <a class="dropmenu" href="#">
                <i class="fa fa-magnet"></i>
                <span class="text"> <?= lang('Urbanpiper'); ?> </span>
                <span class="chevron closed"></span>
            </a>
            <ul>
                <li id="urban_piper_settings">
                    <a href="<?= site_url('urban_piper/settings') ?>">
                        <i class="fa fa-cogs" aria-hidden="true"></i>
                        <span  class="text" > Urbanpiper Settings </span>   
                    </a>
                </li>
                <li id="urban_piper_store_info">
                    <a href="<?= site_url('urban_piper/store_info') ?>">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span  class="text" > Manage Stores </span>
                    </a>    
                </li>
                <li id="urban_piper_product_platform">
                    <a href="<?= site_url('urban_piper/product_platform') ?>">
                        <i class="fa fa-archive" aria-hidden="true"></i>
                        <span  class="text" > Manage Catalogue </span>
                    </a> 

                </li>
                <li id="urban_piper_index">
                    <a href="<?= site_url('urban_piper') ?>">
                        <i class="fa fa-list" aria-hidden="true"></i>
                        <span  class="text" > Manage Orders </span>
                    </a>    
                </li>


                <!--                                                <li id="urbanpiper_category">
                                                                    <a href="<?= site_url('urban_piper/category') ?>">
                                                                        <i class="fa fa-folder-open" aria-hidden="true"></i>
                                                                        <span  class="text" > Category </span>
                                                                    </a> 

                                                                </li>-->

               <!--<li id="urbanpiper_product">
                    <a href="<?= site_url('urban_piper/product') ?>">
                        <i class="fa fa-archive" aria-hidden="true"></i>
                        <span  class="text" > Product </span>
                    </a> 

                </li>-->
                <!--    <li id="urbanpiper_product_platform">
                       <a href="<?= site_url('urban_piper/groups_option') ?>">
                           <i class="fa fa-archive" aria-hidden="true"></i>
                           <span  class="text" > Option Groups</span>
                       </a> 
                   </li>-->



            </ul>
        </li> 
        <!-- Urbanpiper -->
    <?php }//end if ?>

    <li class="mm_system_settings <?= strtolower($this->router->fetch_method()) != 'settings' ? '' : 'mm_pos' ?>">
        <a class="dropmenu" href="#">
            <i class="fa fa-cog"></i><span
                class="text"> <?= lang('settings'); ?> </span>
            <span class="chevron closed"></span>
        </a>
        <ul>
            <li id="system_settings_index">
                <a href="<?= site_url('system_settings') ?>">
                    <i class="fa fa-cog"></i><span
                        class="text"> <?= lang('system_settings'); ?></span>
                </a>
            </li>
            <?php if (POS) { ?>
                <li id="pos_settings">
                    <a href="<?= site_url('pos/settings') ?>">
                        <i class="fa fa-th-large"></i><span
                            class="text"> <?= lang('pos_settings'); ?></span>
                    </a>
                </li>
            <?php } ?>
            <li id="system_settings_custom_fields">
                <a href="<?= site_url('system_settings/custom_fields') ?>">
                    <i class="fa fa-cog"></i><span class="text"> <?= lang('custom_fields'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
                </a>
            </li>
            <li id="system_settings_change_logo">
                <a href="<?= site_url('system_settings/change_logo') ?>"
                   data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-upload"></i><span
                        class="text"> <?= lang('change_logo'); ?></span>
                </a>
            </li>
            <li id="system_settings_currencies">
                <a href="<?= site_url('system_settings/currencies') ?>">
                    <i class="fa fa-money"></i><span
                        class="text"> <?= lang('currencies'); ?></span>
                </a>
            </li>
            <li id="system_settings_customer_groups">
                <a href="<?= site_url('system_settings/customer_groups') ?>">
                    <i class="fa fa-chain"></i><span
                        class="text"> <?= lang('customer_groups'); ?></span>
                </a>
            </li>
            <li id="system_settings_price_groups">
                <a href="<?= site_url('system_settings/price_groups') ?>">
                    <i class="fa fa-dollar"></i><span
                        class="text"> <?= lang('price_groups'); ?></span>
                </a>
            </li>
            <?php if ($Settings->pos_type == 'restaurant') { ?>
                <li id="system_settings_restaurant_tables">
                    <a href="<?= site_url('system_settings/restaurant_tables') ?>">
                        <i class="fa fa-dollar"></i><span class="text"><?= lang('Restaurant_Tables'); ?> </span>
                    </a>
                </li>
            <?php } ?>
            <li id="system_settings_categories">
                <a href="<?= site_url('system_settings/categories') ?>">
                    <i class="fa fa-folder-open"></i><span
                        class="text"> <?= lang('categories'); ?></span>
                </a>
            </li>
            <li id="system_settings_expense_categories">
                <a href="<?= site_url('system_settings/expense_categories') ?>">
                    <i class="fa fa-folder-open"></i><span
                        class="text"> <?= lang('expense_categories'); ?></span>
                </a>
            </li>
            <li id="system_settings_units">
                <a href="<?= site_url('system_settings/units') ?>">
                    <i class="fa fa-wrench"></i><span
                        class="text"> <?= lang('units'); ?></span>
                </a>
            </li>
            <li id="system_settings_brands">
                <a href="<?= site_url('system_settings/brands') ?>">
                    <i class="fa fa-th-list"></i><span
                        class="text"> <?= lang('brands'); ?></span>
                </a>
            </li>
            <li id="system_settings_variants">
                <a href="<?= site_url('system_settings/variants') ?>">
                    <i class="fa fa-tags"></i><span
                        class="text"> <?= lang('variants'); ?></span>
                </a>
            </li>
            <li id="system_settings_tax_rates">
                <a href="<?= site_url('system_settings/tax_rates') ?>">
                    <i class="fa fa-plus-circle"></i><span
                        class="text"> <?= lang('tax_rates'); ?></span>
                </a>
            </li>
            <li id="system_settings_tax_rates_attr">
                <a href="<?= site_url('system_settings/tax_rates_attr') ?>">
                    <i class="fa fa-plus-circle"></i><span
                        class="text"> <?= lang('tax_rates'); ?>
                        Attributes </span>
                </a>
            </li>
            <li id="system_settings_warehouses">
                <a href="<?= site_url('system_settings/warehouses') ?>">
                    <i class="fa fa-building-o"></i><span
                        class="text"> <?= lang('warehouses'); ?></span>
                </a>
            </li>
            <li id="system_settings_email_templates">
                <a href="<?= site_url('system_settings/email_templates') ?>">
                    <i class="fa fa-envelope"></i><span
                        class="text"> <?= lang('email_templates'); ?></span>
                </a>
            </li>
            <li id="system_settings_user_groups">
                <a href="<?= site_url('system_settings/user_groups') ?>">
                    <i class="fa fa-key"></i><span
                        class="text"> <?= lang('group_permissions'); ?></span>
                </a>
            </li>
            <li id="system_settings_backups">
                <a href="<?= site_url('system_settings/backups') ?>">
                    <i class="fa fa-database"></i><span
                        class="text"> <?= lang('backups'); ?></span>
                </a>
            </li>

            <li id="coupon">
                <a href="<?= site_url('system_settings/discount_coupon_list'); ?>" >
                    <i class="fa fa-gift" aria-hidden="true"></i>
                    <span class="text"> Discount  Coupon <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" /></span>
                </a>    
            </li>
            

           
            <li id="system_settings_offer_list">
                <a href="<?= site_url('system_settings/offer_list'); ?>" >
                    <i class="fa fa-gift" aria-hidden="true"></i>
                    <span class="text">  Offer </span>
                </a>    
            </li>
            <li id="system_settings_offercategory">
                <a href="<?= site_url('system_settings/offercategory'); ?>" >
                    <i class="fa fa-gift" aria-hidden="true"></i>
                    <span class="text">  Offer Category 
                </a>    
            </li>
            <li id="system_settings_sms_configs">
                <a href="<?= site_url('system_settings/sms_configs'); ?>" >
                    <i class="fa fa-send" aria-hidden="true"></i>
                    <span class="text"> SMS Config 
                </a>    
            </li>
            <li id="system_settings_printers">
                <a href="<?= site_url('system_settings/printers'); ?>">
                    <i class="fa fa-print"></i>
                    <span class="text">  Manage Printers Option</span>
                </a>
            </li>
            <li id="printers_wifi" style="display:none">
                <a href="javascript:window.MyHandler.OpenWifiPrinterDialog()">
                    <i class="fa fa-wifi"></i>
                    <span class="text"> Wifi Printer Setting</span>
                </a>
            </li>
        </ul>
    </li>
<?php } ?>
<!--<li class="mm_reports">
    <a class="dropmenu" href="#">
        <i class="fa fa-pie-chart"></i>
        <span class="text"> <?= lang('Old Reports'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="reports_daily_sales">
            <a href="<?= site_url('reports/daily_sales') ?>">
                <i class="fa fa-calendar-check-o"></i><span
                    class="text"> <?= lang('daily_sales'); ?></span>
            </a>
        </li>
        <li id="reports_monthly_sales">
            <a href="<?= site_url('reports/monthly_sales') ?>">
                <i class="fa fa-calendar"></i><span
                    class="text"> <?= lang('monthly_sales'); ?></span>
            </a>
        </li>
        <li id="reports_daily_sales_up">
            <a href="<?= site_url('reports/daily_sales_up') ?>">
                <i class="fa fa-calendar-check-o"></i><span
                    class="text"> <?= lang('Urban Piper Daily Sales'); ?></span>  
            </a>
        </li> 
        <li id="reports_sales_gst_report">
            <a href="<?= site_url('reports/sales_gst_report') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('sales_report'); ?> GST </span>
            </a>
        </li>        
        <li id="reports_overdue_payments">
            <a href="<?= site_url('reports/overdue_payments') ?>">
                <i class="fa fa-credit-card"></i><span
                    class="text"> <?= lang(' Due Payment Report'); ?></span>
            </a>
        </li> 
        <li id="reports_daily_purchases">
            <a href="<?= site_url('reports/daily_purchases') ?>">
                <i class="fa fa-cart-plus"></i><span
                    class="text"> <?= lang('daily_purchases'); ?></span>
            </a>
        </li>
        <li id="reports_monthly_purchases">
            <a href="<?= site_url('reports/monthly_purchases') ?>">
                <i class="fa fa-calendar"></i><span
                    class="text"> <?= lang('monthly_purchases'); ?></span>
            </a>
        </li> 
        <li id="reports_purchases_gst_report">
            <a href="<?= site_url('reports/purchases_gst_report') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('purchases_report'); ?>
                    GST </span>
            </a>
        </li> 
    </ul>
</li> -->
<li class="mm_reports mm_reports_new">
    <a class="dropmenu" href="#">
        <i class="fa fa-pie-chart"></i>
        <span class="text"> <?= lang('reports'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="reports_index">
            <a href="<?= site_url('reports') ?>">
                <i class="fa fa-bars"></i><span
                    class="text"><?= lang('overview_chart'); ?></span>
            </a>
        </li>
        <li id="reports_warehouse_stock">
            <a href="<?= site_url('reports/warehouse_stock') ?>">
                <i class="fa fa-building"></i><span
                    class="text"> <?= lang('warehouse_stock'); ?></span>
            </a>
        </li>
        <li id="reports_best_sellers">
            <a href="<?= site_url('reports/best_sellers') ?>">
                <i class="fa fa-thumbs-up"></i><span
                    class="text"> <?= lang('best_sellers'); ?></span>
            </a>
        </li>
        <li id="reports_payment_chart_details">
            <a href="<?= site_url('reports/payment_chart_details') ?>">
                <i class="fa fa-thumbs-up"></i><span
                    class="text"> <?= lang('Payment Chart Details'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_sale_purchase_chart_details">
            <a href="<?= site_url('reports/sale_purchase_chart_details') ?>">
                <i class="fa fa-thumbs-up"></i><span
                    class="text"> <?= lang('Sale Purchase Chart Details'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_user_log_action">
            <a href="<?= site_url('reports/user_log_action') ?>">
                <i class="fa fa-thumbs-up"></i><span
                    class="text"> <?= lang('User Log Action'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_categories_brand_chart_details">
            <a href="<?= site_url('reports/categories_brand_chart_details') ?>">
                <i class="fa fa-thumbs-up"></i><span
                    class="text"> <?= lang('Categories & Brand Chart Details'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_get_customer_wise_sales">
            <a href="<?= site_url('reports/get_customer_wise_sales') ?>">
                <i class="fa fa-thumbs-up"></i><span
                    class="text"> <?= lang('Customer_wise_Sale_Report'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <?php if (POS) { ?>
            <li id="reports_register">
                <a href="<?= site_url('reports/register') ?>">
                    <i class="fa fa-th-large"></i><span
                        class="text"> <?= lang('register_report'); ?></span>
                </a>
            </li>
        <?php } ?>
        <li id="reports_quantity_alerts">
            <a href="<?= site_url('reports/quantity_alerts') ?>">
                <i class="fa fa-sort-amount-desc"></i><span
                    class="text"> <?= lang('product_quantity_alerts'); ?></span>
            </a>
        </li>
        <?php if ($Settings->product_expiry) { ?>
            <li id="reports_expiry_alerts">
                <a href="<?= site_url('reports/expiry_alerts') ?>">
                    <i class="fa fa-bar-chart-o"></i><span
                        class="text"> <?= lang('product_expiry_alerts'); ?></span>
                </a>
            </li>
        <?php } ?>
        <li id="reports_products">
            <a href="<?= site_url('reports/products') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('products_report'); ?></span>
            </a>
        </li>
        <li id="reports_products_transactions">
            <a href="<?= site_url('reports/products_transactions') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Products_Transactions_Report'); ?></span><img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_products_ledgers">
            <a href="<?= site_url('reports/products_ledgers') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Products_Ledgers'); ?></span><img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_products_combo_items">
            <a href="<?= site_url('reports/products_combo_items') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Products_Combo_Items'); ?></span> 
            </a>
        </li>
        <li id="reports_products_profitloss">
            <a href="<?= site_url('reports/products_profitloss') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Products_Profit_&_Loss'); ?></span> 
            </a>
        </li>
        <li id="reports_products_costing">
            <a href="<?= site_url('reports/products_costing') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Products_Costings'); ?></span> 
            </a>
        </li>
        <li id="reports_product_varient_stock_report">
            <a href="<?= site_url('reports/product_varient_stock_report') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Product_Varient_Stock_Report'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_product_varient_sale_report">
            <a href="<?= site_url('reports/product_varient_sale_report') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Product_Varient_Sale_Report'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_product_varient_purchase_report">
            <a href="<?= site_url('reports/product_varient_purchase_report') ?>">
                <i class="fa fa-barcode"></i><span
                    class="text"> <?= lang('Product_Varient_Purchase_Report'); ?></span> <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>
        <li id="reports_adjustments">
            <a href="<?= site_url('reports/adjustments') ?>">
                <i class="fa fa-filter"></i><span
                    class="text"> <?= lang('adjustments_report'); ?></span>
            </a>
        </li>
        <li id="reports_categories_report">
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

        <li id="reports_new_daily_sales">
            <a href="<?= site_url('reports_new/daily_sales') ?>">
                <i class="fa fa-calendar-check-o"></i><span
                    class="text"> <?= lang('daily_sales'); ?></span>
            </a>
        </li>
        <li id="reports_new_monthly_sales">
            <a href="<?= site_url('reports_new/monthly_sales') ?>">
                <i class="fa fa-calendar"></i><span
                    class="text"> <?= lang('monthly_sales'); ?></span>
            </a>
        </li>

        <li id="reports_sales_person_report">
            <a href="<?= site_url('reports/sales_person_report') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('Sales_Person_Report'); ?></span> 
            </a>
        </li>
        <li id="reports_sales">
            <a href="<?= site_url('reports/sales') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('sales_report'); ?></span>
            </a>
        </li>
        <li id="reports_new_gst_reports">
            <a href="<?= site_url('reports_new/gst_reports') ?>">
                <i class="fa fa-line-chart"></i><span class="text"> Simple TAX/GST Reports <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" /></span>
            </a>
        </li>
        <li id="reports_sales_due">
            <a href="<?= site_url('reports/sales_due') ?>">
                <i class="fa fa-line-chart"></i><span class="text"> <?= lang('Due Sales_Report'); ?></span>
            </a>
        </li>
        <li id="reports_new_sales_extended_report">
            <a href="<?= site_url('reports_new/sales_extended_report') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('Sales_Extended_Report'); ?> </span>
            </a>
        </li>
        <li id="reports_new_sales_gst_reportnew">
            <a href="<?= site_url('reports_new/sales_gst_reportnew') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('Sales_GST_Report'); ?></span>
            </a>
        </li>
        <li id="reports_challans">
            <a href="<?= site_url('reports/challans') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('Challans Report'); ?> </span>
            </a>
        </li>
        <!----->
        <li id="reports_warehouse_sales">
            <a href="<?= site_url('reports/warehouse_sales') ?>">
                <i class="fa fa-line-chart"></i><span class="text"> Warehouse <?= lang('sales_report'); ?> </span>
            </a>
        </li>
        <li id="reports_payments">
            <a href="<?= site_url('reports/payments') ?>">
                <i class="fa fa-credit-card"></i><span
                    class="text"> <?= lang('payments_report'); ?></span>
            </a>
        </li>
        <li id="reports_paymentssummary">
            <a href="<?= site_url('reports/paymentssummary') ?>">
                <i class="fa fa-credit-card"></i><span
                    class="text"> <?= lang('Payments Summary'); ?></span>
            </a>
        </li>


        <li id="reports_profit_loss">
            <a href="<?= site_url('reports/profit_loss') ?>">
                <i class="fa fa-money"></i><span
                    class="text"> Profit & Loss</span>
            </a>
        </li>

        <li id="reports_new_daily_purchases">
            <a href="<?= site_url('reports_new/daily_purchases') ?>">
                <i class="fa fa-cart-plus"></i><span
                    class="text"> <?= lang('daily_purchases'); ?></span>
            </a>
        </li>
        <li id="reports_new_monthly_purchases">
            <a href="<?= site_url('reports_new/monthly_purchases') ?>">
                <i class="fa fa-calendar"></i><span
                    class="text"> <?= lang('monthly_purchases'); ?></span>
            </a>
        </li>
        <li id="reports_purchases">
            <a href="<?= site_url('reports/purchases') ?>">
                <i class="fa fa-file-text"></i><span
                    class="text"> <?= lang('purchases_report'); ?></span>
            </a>
        </li>

        <li id="reports_purchases_due">
            <a href="<?= site_url('reports/purchases_due') ?>">
                <i class="fa fa-file-text"></i><span
                    class="text"> <?= lang('Due Purchases Report'); ?></span>
            </a>
        </li>
        <li id="reports_new_purchases_gst_report">
            <a href="<?= site_url('reports_new/purchases_gst_report') ?>">
                <i class="fa fa-line-chart"></i><span
                    class="text"> <?= lang('purchases_report'); ?>
                    GST </span>
            </a>
        </li>

        <li id="reports_taxreports">
            <a href="<?= site_url('reports/taxreports') ?>">
                <i class="fa fa-line-chart"></i>
                <span class="text"> <?= lang('Tax_Report'); ?></span>
            </a>
        </li>
        <li id="reports_hsncode_reports">
            <a href="<?= site_url('reports/hsncode_reports') ?>">
                <i class="fa fa-line-chart"></i>
                <span class="text"> <?= lang('HSN_Report'); ?></span><img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
            </a>
        </li>

        <li id="reports_expenses">
            <a href="<?= site_url('reports/expenses') ?>">
                <i class="fa fa-star"></i><span
                    class="text"> <?= lang('expenses_report'); ?></span>
            </a>
        </li>
        <li id="reports_customers">
            <a href="<?= site_url('reports/customers') ?>">
                <i class="fa fa-users"></i><span
                    class="text"> <?= lang('customers_report'); ?></span>
            </a>
        </li>
        <li id="reports_suppliers">
            <a href="<?= site_url('reports/suppliers') ?>">
                <i class="fa fa-truck"></i><span
                    class="text"> <?= lang('suppliers_report'); ?></span>
            </a>
        </li>
        <li id="reports_users">
            <a href="<?= site_url('reports/users') ?>">
                <i class="fa fa-user" aria-hidden="true"></i><span
                    class="text"> <?= lang('staff_report'); ?></span>
            </a>
        </li>

      <li id="reports_transfer_request">
            <a href="<?= site_url('reports/transfer_request') ?>">
                <i class="fa fa-user" aria-hidden="true"></i><span
                    class="text"> <?= lang('Transfer_Request'); ?></span>
                    <img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
                    
            </a>
        </li>
        <!--<li id="reports_products_orderReport">
         <a href="<?= site_url('reports/products_orderReport') ?>">
             <i class="fa fa-barcode"></i><span
                 class="text"> <?= lang('Order_Details_Report'); ?></span><img src="<?= $assets ?>images/new.gif" height="30px" alt="new" />
         </a>
     </li>-->

    </ul>
</li>
<li class="mm_help">
    <a class="dropmenu" href="#">
        <i class="fa fa-info-circle"></i>
        <span class="text"> <?= lang('Help'); ?> </span>
        <span class="chevron closed"></span>
    </a>
    <ul>
        <li id="help_statelist">
            <a href="<?= site_url('help/statelist') ?>">
                <i class="fa fa-bars"></i><span
                    class="text"><?= lang('State List'); ?></span>
            </a>
        </li>
    </ul>
</li>
