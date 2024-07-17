<style>
    .sidebar-light-primary {
        background-color: #f3f7fd;
    }
</style>


<aside class="main-sidebar sidebar-light-primary elevation-1">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('img/eolf_heart_logo.png') }}" alt="EOLF Logo" class="brand-image" style="opacity: .8">
        <span class="brand-text text-primary"><small>EOLF Food Trading OPC</small></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="info">
                <strong><a href="#" class="d-block">{{ auth()->user()->fullName }}</a></strong>

                <small>{{ session('branch_code') }}</small>
            </div>
        </div>

        <!-- Sidebar Menu -->

        <nav class="mt-2" id="sidebar">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ Route::currentRouteNamed('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-gauge" style="color: #74C0FC;"></i>
                        <p>
                            Dashboard
                            <i class="right"></i>
                        </p>
                    </a>
                </li>

                <li
                    class="nav-item {{ Route::currentRouteNamed('company') || Route::currentRouteNamed('branch') || Route::currentRouteNamed('users') || Route::currentRouteNamed('vehicles') || Route::currentRouteNamed('delivery-persons') || Route::currentRouteNamed('customers') || Route::currentRouteNamed('productType.index') || Route::currentRouteNamed('products.index') || Route::currentRouteNamed('productVariant.index') || Route::currentRouteNamed('pricing-level.index') || Route::currentRouteNamed('pricing.index') || Route::currentRouteNamed('equipment.index') || Route::currentRouteNamed('equipment-store.index') || Route::currentRouteNamed('equipment.history') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-tie" style="color: #74C0FC;"></i>
                        <p>
                            Administration
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li>
                            <a href="{{ route('company') }}"
                                class="nav-link {{ Route::currentRouteNamed('company') ? 'active' : '' }}">
                                <i class="far fa-building nav-icon"></i>
                                <p>Company</p>
                            </a>
                        </li>

                        <li>
                            <a href="/branch" class="nav-link {{ Route::currentRouteNamed('branch') ? 'active' : '' }}">
                                <i class="fas fa-store nav-icon"></i>
                                <p>Branch</p>
                            </a>
                        </li>

                        <li>
                            <a href="/users" class="nav-link {{ Route::currentRouteNamed('users') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>User</p>
                            </a>
                        </li>

                        <li>
                            <a href="/vehicles"
                                class="nav-link {{ Route::currentRouteNamed('vehicles') ? 'active' : '' }}">
                                <i class="fas fa-truck nav-icon"></i>
                                <p>Vehicles</p>
                            </a>
                        </li>

                        <li>
                            <a href="/delivery-persons"
                                class="nav-link {{ Route::currentRouteNamed('delivery-persons') ? 'active' : '' }}">
                                <i class="fas fa-truck-ramp-box nav-icon"></i>
                                <p>Delivery Person</p>
                            </a>
                        </li>


                        <li>
                            <a href="/customers"
                                class="nav-link {{ Route::currentRouteNamed('customers') || Route::currentRouteNamed('equipment-store.index') ? 'active' : '' }}">
                                <i class="fas fa-id-card nav-icon"></i>
                                <p>Customers</p>
                            </a>
                        </li>

                        <li>
                            <a href="/product-types"
                                class="nav-link {{ Route::currentRouteNamed('productType.index') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>Products Type</p>
                            </a>
                        </li>

                        <li>
                            <a href="/products"
                                class="nav-link {{ Route::currentRouteNamed('products.index') ? 'active' : '' }}">
                                <i class="fas fa-ice-cream nav-icon"></i>
                                <p>Products</p>
                            </a>
                        </li>


                        <li>
                            <a href="/product-variants"
                                class="nav-link {{ Route::currentRouteNamed('productVariant.index') ? 'active' : '' }}">
                                <i class="fas fa-list-ol nav-icon"></i>
                                <p>Product Variants</p>
                            </a>
                        </li>

                        <li>
                            <a href="/pricing-level"
                                class="nav-link {{ Route::currentRouteNamed('pricing-level.index') ? 'active' : '' }}">
                                <i class="fas fa-receipt nav-icon"></i>
                                <p>Pricing Level</p>
                            </a>
                        </li>

                        <li>
                            <a href="/pricing"
                                class="nav-link {{ Route::currentRouteNamed('pricing.index') ? 'active' : '' }}">
                                <i class="fas fa-peso-sign nav-icon"></i>
                                <p>Pricing</p>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('equipment.index') }}"
                                class="nav-link {{ Route::currentRouteNamed('equipment.index') || Route::currentRouteNamed('equipment.history') ? 'active' : '' }}">
                                <i class="fas fa-box nav-icon"></i>
                                <p>Equipment</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li
                    class="nav-item {{ Route::currentRouteNamed('order.index') || Route::currentRouteNamed('order.processTwo') || Route::currentRouteNamed('badOrders.index') || Route::currentRouteNamed('addbadorder.create') || Route::currentRouteNamed('order.create') || Route::currentRouteNamed('order.edit') || Route::currentRouteNamed('deliveryreceipt.index') || Route::currentRouteNamed('drprint') || Route::currentRouteNamed('generate-ticket')|| Route::currentRouteNamed('print-ticket') || Route::currentRouteNamed('index-ticket') || Route::currentRouteNamed('inbounds-ticket') ? 'menu-is-opening menu-open' : '' }}">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-coins" style="color: #74C0FC;"></i>
                        <p>
                            Sales
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li>
                            <a href="/orders"
                                class="nav-link {{ Route::currentRouteNamed('order.index') || Route::currentRouteNamed('order.processTwo') || Route::currentRouteNamed('order.create') || Route::currentRouteNamed('order.edit') ? 'active' : '' }}">
                                <i class="fas fa-circle-left nav-icon"></i>
                                <p>Outbound</p>
                            </a>
                        </li>

                        <li>
                            <a href="/generate-ticket"
                                class="nav-link {{ Route::currentRouteNamed('generate-ticket') || Route::currentRouteNamed('print-ticket') || Route::currentRouteNamed('index-ticket') || Route::currentRouteNamed('inbounds-ticket') ? 'active' : '' }}">
                                <i class="fas fa-circle-left nav-icon"></i>
                                <p>Ticket</p>
                            </a>
                        </li>

                        <li>
                            <a href="/bad-orders-list" class="nav-link {{ Route::currentRouteNamed('badOrders.index') || Route::currentRouteNamed('addbadorder.create') ? 'active' : '' }}">
                                <i class="fas fa-circle-left nav-icon"></i>
                                <p>Bad Order</p>
                            </a>
                        </li>

                        <li>
                            <a href="/deliveryreceipt" class="nav-link {{ Route::currentRouteNamed('deliveryreceipt.index') || Route::currentRouteNamed('drprint') ? 'active' : '' }}">
                                <i class="fas fa-print nav-icon"></i>
                                <p>Delivery Receipt</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ Route::currentRouteNamed('delivery-purchase-receipts.index') || Route::currentRouteNamed('itemdata.index') || Route::currentRouteNamed('drp.products') || Route::currentRouteNamed('materialsInventory.index') || Route::currentRouteNamed('materialsInventory.history')  ? 'menu-is-opening menu-open' : '' }}"
                    ||>
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-warehouse" style="color: #74C0FC;"></i>
                        <p>
                            Inventory
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">

                        <li>
                            <a href="{{ route('delivery-purchase-receipts.index') }}"
                                class="nav-link {{ Route::currentRouteNamed('delivery-purchase-receipts.index') || Route::currentRouteNamed('drp.products') ? 'active' : '' }}">
                                <i class="fas fa-circle-right nav-icon"></i>
                                <p>Inbound</p>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('itemdata.index') }}"
                                class="nav-link {{ Route::currentRouteNamed('itemdata.index') ? 'active' : '' }}">
                                <i class="fas fa-database nav-icon"></i>
                                <p>Item master data</p>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('materialsInventory.index') }}"
                                class="nav-link {{ Route::currentRouteNamed('materialsInventory.index') || Route::currentRouteNamed('materialsInventory.history') ? 'active' : '' }}">
                                <i class="fas fa-database nav-icon"></i>
                                <p>Materials Inventory</p>
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

                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: none;">
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

    // document.addEventListener('DOMContentLoaded', function() {
    //     // Get the sidebar element
    //     var sidebarContent = document.getElementById('sidebar');
    //     // Retrieve stored content from session storage
    //     var storedContent = sessionStorage.getItem('nav-item');
    //     // If stored content exists, restore it
    //     if (storedContent) {
    //         sidebarContent.innerHTML = storedContent;
    //     }
    // });

    // // Store content in session storage before page is unloaded (refreshed)
    // window.addEventListener('beforeunload', function() {
    //     // Get the content wrapper element
    //     var sidebarContent = document.getElementById('sidebar');
    //     // Store the current content in session storage
    //     sessionStorage.setItem('nav-item', sidebarContent.innerHTML);
    // });


    // function toggleTreeview(treeviewId, redirectUrl) {
    //     // Collapse all treeviews except for the specified one
    //     $('.nav-treeview').not('#' + treeviewId).hide();
    //     // Toggle the specified treeview
    //     $('#' + treeviewId).toggle();

    //     // Redirect to the specified URL after a short delay
    //     if (redirectUrl) {
    //         setTimeout(function() {
    //             window.location.href = redirectUrl;
    //         }, 0); // Adjust the delay (in milliseconds) as needed
    //     }
    // }
</script>
