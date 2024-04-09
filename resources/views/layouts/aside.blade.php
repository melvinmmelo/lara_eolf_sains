<aside class="main-sidebar sidebar-light-primary">
    <!-- elevation-4 shadow for sidebar -->
    <!-- Brand Logo -->

    <a href="index3.html" class="brand-link">
        <img src="{{ asset('img/eolf_heart_logo.png') }}" alt="EOLF Logo" class="brand-image" style="opacity: .8">
        <span class="brand-text text-primary">EOLF Food Trading</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="info">
                <a href="#" class="d-block">Username</a>
                Branch
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                    aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>




        <!-- Sidebar Menu -->

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt" style="color: #74C0FC;"></i>
                        <p>
                            Dashboard
                            <i class="right"></i>
                        </p>
                    </a>

                    <!-- <li class="nav-header">MULTI LEVEL EXAMPLE</li> -->

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-tie" style="color: #74C0FC;"></i>
                        <p>
                            Administration
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="company" class="nav-link">
                                <i class="far fa-building nav-icon"></i>
                                <p>Company</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/branch" class="nav-link">
                                <i class="fas fa-store nav-icon"></i>
                                <p>Branch</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/users" class="nav-link">
                                <i class="fas fa-users nav-icon"></i>
                                <p>User</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/vehicles" class="nav-link">
                                <i class="fas fa-truck nav-icon"></i>
                                <p>Vehicles</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="/delivery-persons" class="nav-link">
                                <i class="fas fa-biking nav-icon"></i>
                                <p>Delivery Person</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="customers" class="nav-link">
                                <i class="fas fa-id-card nav-icon"></i>
                                <p>Customers</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="product-types" class="nav-link">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Products Type</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="products" class="nav-link">
                                <i class="fas fa-ice-cream nav-icon"></i>
                                <p>Products</p>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a href="product-variants" class="nav-link">
                                <i class="fas fa-list-ol nav-icon"></i>
                                <p>Product Variants</p>
                            </a>
                        </li>
                    </ul>
                </li>



                <li class="nav-item">
                    <a href="#" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out nav-icon" style="color: #74C0FC;"></i>
                        <p>
                            Logout
                        </p>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </a>
                </li>

                {{-- <!-- Inventory Menu -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fas fa-warehouse"></i>
                        <p>
                            Inventory
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Level 2</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Level 2
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Level 2</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sales Menu -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                            Sales
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Level 2</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>
                                    Level 2
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-dot-circle nav-icon"></i>
                                        <p>Level 3</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Level 2</p>
                            </a>
                        </li>
                    </ul>
                </li>


            </ul> --}}
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<!-- set the link active when click -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.nav-link').click(function() {
            // Remove active class from all nav-links
            $('.nav-link').removeClass('active');

            // Add active class to the clicked nav-link
            $(this).addClass('active');
        });
    });
</script>
