<aside class="main-sidebar elevation-4 sidebar-light-success">
    <!-- Brand Logo -->
    <a href="<?=base_url('storeapp')?>" class="brand-link navbar-danger">
      <img src="<?=$assets?>AdminLTE_3_0_4/dist/img/AdminLTELogo.png" alt="Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light text-white">MY STORE</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?=$assets?>AdminLTE_3_0_4/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">User Name</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          
          <li class="nav-item">
            <a href="<?=base_url('storeapp/index')?>" class="nav-link">
             <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard                 
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url('storeapp/orders')?>" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Orders
<!--                <span class="right badge badge-danger">42</span>-->
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url('storeapp/sales')?>" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Sales
<!--                <span class="right badge badge-danger">42</span>-->
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url('storeapp/products')?>" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Products
<!--                <span class="right badge badge-danger">742</span>-->
              </p>
            </a>
          </li>          
          <li class="nav-item">
            <a href="<?=base_url('storeapp/payments')?>" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Payments
              </p>
            </a>
          </li>          
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Settings                
              </p>
            </a>
          </li> 
          <li class="nav-item">
            <a href="<?=base_url('pos')?>" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                POS Sales                
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?=base_url('reports')?>" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Reports                
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= site_url('logout'); ?>" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p class="text-danger">
                Logout                
              </p>
            </a>
          </li>         
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>