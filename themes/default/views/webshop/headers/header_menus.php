                <nav id="primary-navigation" class="primary-navigation" aria-label="Primary Navigation" data-nav="flex-menu">
                    <ul id="menu-primary-menu" class="nav yamm">
                        
<!--                        <li class="menu-item menu-item-has-children animate-dropdown dropdown">
                            <a title="My Account" data-toggle="dropdown" class="dropdown-toggle" aria-haspopup="true" href="#">My Account <span class="caret"></span></a>
                            <ul role="menu" class=" dropdown-menu">
                                <li class="menu-item animate-dropdown">
                                    <a title="Wishlist" href="#">Wishlist</a>
                                </li>
                                <li class="menu-item animate-dropdown">
                                    <a title="Add to compare" href="#">My Orders</a>
                                </li>
                                <li class="menu-item animate-dropdown">
                                    <a title="Addresses" href="#">Addresses</a>
                                </li>
                            </ul>
                        </li>-->
                        <li class="sale-clr yamm-fw menu-item animate-dropdown">
                            <a title="My Account" href="<?=base_url('webshop/your_account')?>">My Account</a>
                        </li>
                        <li class="yamm-fw menu-item menu-item-has-children">
                            <a title="My Shopping" href="<?=base_url('webshop/your_orders')?>">My Shopping</a>
                        </li>
                        <li class="yamm-fw menu-item menu-item-has-children">
                            <a title="My Address" href="<?=base_url('webshop/your_address')?>">My Address</a>
                        </li>
                        <li class="yamm-fw menu-item menu-item-has-children">
                            <a title="My Wishlist" href="<?=base_url('webshop/wishlist')?>">Wishlist</a>
                        </li>
                        
                        <li class="menu-item animate-dropdown">
                            <a title="" href="<?=base_url('webshop/cart')?>">My Cart</a>
                        </li>
                        <li class="techmarket-flex-more-menu-item dropdown">
                            <a title="..." href="#" data-toggle="dropdown" class="dropdown-toggle">...</a>
                            <ul class="overflow-items dropdown-menu"></ul>
                            <!-- . -->
                        </li>
                    </ul>
                    <!-- .nav -->
                </nav>
                <!-- .primary-navigation -->
                <nav id="secondary-navigation" class="secondary-navigation" aria-label="Secondary Navigation" data-nav="flex-menu">
                    <ul id="menu-secondary-menu" class="nav">
                        <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2802 animate-dropdown">
                            <a title="Track Your Order" href="#">
                                <i class="tm tm-order-tracking"></i>Track Your Order</a>
                        </li>                        
                        <li class="menu-item">
                            <a title="My Account" href="<?=base_url("webshop/login")?>">
                                <i class="tm tm-login-register"></i>Register or Sign in</a>
                        </li>
                        <li class="techmarket-flex-more-menu-item dropdown">
                            <a title="..." href="#" data-toggle="dropdown" class="dropdown-toggle">...</a>
                            <ul class="overflow-items dropdown-menu"></ul>
                        </li>
                    </ul>
                    <!-- .nav -->
                </nav>
                <!-- .secondary-navigation -->