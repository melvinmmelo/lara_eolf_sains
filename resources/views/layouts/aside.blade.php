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
                <a href="#" class="d-block">{{ auth()->user()->fullName }}</a>

                <small>Branch</small>
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

        <nav class="mt-2" id="sidebar">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->



                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="toggleTreeview();">
                        <i class="nav-icon fas fa-user-tie" style="color: #74C0FC;"></i>
                        <p>
                            Dashboard
                            <i class="right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" id="dashboard-treeview">
                        <li>

                        </li>


                    </ul>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-tie" style="color: #74C0FC;"></i>
                        <p>
                            Administration
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li>
                            <a href="/company" class="nav-link">
                                <i class="far fa-building nav-icon"></i>
                                <p>Company</p>
                            </a>
                        </li>

                        <li>
                            <a href="/branch" class="nav-link">
                                <i class="fas fa-store nav-icon"></i>
                                <p>Branch</p>
                            </a>
                        </li>

                        <li>
                            <a href="/users" class="nav-link">
                                <i class="fas fa-users nav-icon"></i>
                                <p>User</p>
                            </a>
                        </li>

                        <li>
                            <a href="/vehicles" class="nav-link">
                                <i class="fas fa-truck nav-icon"></i>
                                <p>Vehicles</p>
                            </a>
                        </li>

                        <li>
                            <a href="/delivery-persons" class="nav-link">
                                <i class="fas fa-truck-ramp-box nav-icon"></i>
                                <p>Delivery Person</p>
                            </a>
                        </li>


                        <li>
                            <a href="customers" class="nav-link">
                                <i class="fas fa-id-card nav-icon"></i>
                                <p>Customers</p>
                            </a>
                        </li>

                        <li>
                            <a href="product-types" class="nav-link">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Products Type</p>
                            </a>
                        </li>

                        <li>
                            <a href="products" class="nav-link">
                                <i class="fas fa-ice-cream nav-icon"></i>
                                <p>Products</p>
                            </a>
                        </li>


                        <li>
                            <a href="product-variants" class="nav-link">
                                <i class="fas fa-list-ol nav-icon"></i>
                                <p>Product Variants</p>
                            </a>
                        </li>

                        <li>
                            <a href="pricing-level" class="nav-link">
                                <i class="fas fa-receipt nav-icon"></i>
                                <p>Pricing Level</p>
                            </a>
                        </li>

                        <li>
                            <a href="pricing" class="nav-link">
                                <i class="fas fa-peso-sign nav-icon"></i>
                                <p>Pricing</p>
                            </a>
                        </li>

                        <li>
                            <a href="equipment" class="nav-link">
                                <i class="fas fa-box nav-icon"></i>
                                <p>Equipment</p>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('order.index') }}" class="nav-link">
                                <i class="fas fa-file-invoice nav-icon"></i>
                                <p>Order</p>
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



        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>




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

    // Check if the DOMContentLoaded event has fired
    document.addEventListener('DOMContentLoaded', function() {
        // Get the sidebar element
        var sidebarContent = document.getElementById('sidebar');
        // Retrieve stored content from session storage
        var storedContent = sessionStorage.getItem('nav-item');
        // If stored content exists, restore it
        if (storedContent) {
            sidebarContent.innerHTML = storedContent;
        }

        // Remove the session item and revert to original state after 1 second
        setTimeout(function() {
            sessionStorage.removeItem('nav-item');
            // Revert to original state here if needed
        }, 1000); // 1000 milliseconds = 1 second
    });

    // Store content in session storage before page is unloaded (refreshed)
    window.addEventListener('beforeunload', function() {
        // Get the content wrapper element
        var sidebarContent = document.getElementById('sidebar');
        // Store the current content in session storage
        sessionStorage.setItem('nav-item', sidebarContent.innerHTML);
    });


    function toggleTreeview() {
        // Collapse all treeviews except for the Dashboard treeview
        $('.nav-treeview').not('#dashboard-treeview').hide();
        // Toggle the Dashboard treeview
        $('#dashboard-treeview').toggle();

        // Redirect to the dashboard page after a short delay
        setTimeout(function() {
            window.location.href = '/dashboard';
        }, 0); // Adjust the delay (in milliseconds) as needed
    }
</script>
