<?php

use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CompanyDetailsController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DeliveryPurchaseReceiptController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\DriversController;
use App\Http\Controllers\InboundController;
use App\Http\Controllers\PricelevelsController;
use App\Http\Controllers\PricesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\PhAddrController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentStoreController;
use App\Http\Controllers\ItemMasterDataController;
use App\Http\Controllers\addbadorderController;
use App\Http\Controllers\TempBadOrderController;
use App\Http\Controllers\BadOrderController;
use App\Http\Controllers\MaterialsInventoryController;
use App\Http\Controllers\DeliveryReceiptController;
use App\Http\Controllers\EquipmentHistoryController;
use App\Http\Controllers\NewBadOrderController;
use App\Http\Controllers\OrderSlipController;
use App\Http\Controllers\ReportGeneratorController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\MaterialWithdrawalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockReconciliationController;
use App\Http\Controllers\InventoryBadOrderController;
use App\Http\Controllers\PullOutFormController;

Route::get('/', function () {
    if (auth()->check()) {

        if (session('branch_code') == null) {
            return redirect()->route('branch-select');
        } else {
            return redirect()->route('dashboard');
        }
    }

    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    
    Route::get('/reports/sales', [ReportGeneratorController::class, 'salesReport'])->middleware('can:admin')->name('report.sales');
    Route::get('/reports/sales/export', [ReportGeneratorController::class, 'exportSalesReport'])->middleware('can:admin')->name('report.sales.export');
    Route::get('/reports/sales-by-customer', [ReportGeneratorController::class, 'salesReportByCustomer'])->name('report.sales-by-customer');
    Route::get('/reports/sales-by-customer/detailed', [ReportGeneratorController::class, 'salesReportByCustomerDetailed'])->name('report.sales-by-customer.detailed');
    Route::get('/reports/sales-by-customer/export-detailed', [ReportGeneratorController::class, 'exportSalesReportByCustomerDetailed'])->name('report.sales-by-customer.export-detailed');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/organize', [OrderSlipController::class, 'organize'])->name('orderSlip.organize');

    Route::get('/organize-update', [OrderSlipController::class, 'organizeUpdate'])->name('orderSlip.organizeUpdate');

    Route::get('/order-slips', [OrderSlipController::class, 'index'])->name('order-slips');

    Route::get('/generate-order-slip', [OrderSlipController::class, 'generate'])->name('generate-order-slip');

    Route::post('/print-order-slip', [OrderSlipController::class, 'print'])->name('print-order-slip');

    Route::get('/order-slip/{code}', [ReportGeneratorController::class, 'orderSlip'])->name('report.orderSlip');

    Route::get('/products-summary', [ReportGeneratorController::class, 'productsSummary'])->name('report.productsSummary');

    Route::get('/products-summary-filtered', [ReportGeneratorController::class, 'productsSummary'])->name('report.productsSummaryFiltered');

    Route::get('/products-summary-v2', [ReportGeneratorController::class, 'productsSummaryv2'])->name('report.productsSummaryv2');

    Route::get('/available-stocks', [ReportGeneratorController::class, 'availableStocks'])->name('report.availableStocks');

    Route::get('/delivery-purchase-receipt-summary', [ReportGeneratorController::class, 'deliveryPurchaseReceiptSummary'])->name('report.deliveryPurchaseReceiptSummary');

    Route::get('/customer-update-form/{customer}', [ReportGeneratorController::class, 'customerUpdateForm'])->name('report.customerUpdateForm');
    Route::get('/pullout-replaced-form/{equipmentStore}', [ReportGeneratorController::class, 'pulloutReplacedForm'])->name('report.pulloutReplacedForm');
    Route::get('/freezer-gatepass-form/{equipment_store_id}', [ReportGeneratorController::class, 'freezerGatepassForm'])->name('report.freezerGatepassForm');
    Route::post('/equipment/store-freezer-gatepass', [EquipmentStoreController::class, 'storeFreezerGatepass'])->name('equipment.store-freezer-gatepass');

    Route::get('/loading-ticket', function () {
        return view('loading-ticket');
    });

    Route::get('/generate-ticket/{print?}', [TicketController::class, 'generate'])->name('generate-ticket');

    Route::post('/print-ticket', [TicketController::class, 'print'])->name('print-ticket');

    Route::post('/reprint-ticket', [TicketController::class, 'reprint'])->name('reprint-ticket');

    Route::get('/tickets', [TicketController::class, 'index'])->name('index-ticket');

    Route::get('/ticket-inbounds/{grp}', [TicketController::class, 'show'])->name('inbounds-ticket');


    Route::get('/lastBadOrderOfCustomer/{customerId}/{storeId}', [BadOrderController::class, 'newFetchLastBadOrderOfCustomer']);

    Route::get('/getBoDetails/{boId}', [NewBadOrderController::class, 'getBoDetails']);

    Route::post('/delivery-receipt', [DeliveryReceiptController::class, 'store'])->name('delivery-receipt.store');

    Route::get('/drprint/{id}', [DeliveryReceiptController::class, 'show'])->name('drprint');

    Route::get('/deliveryreceipt', [DeliveryReceiptController::class, 'index'])->name('deliveryreceipt.index');

    Route::patch('edit-dr', [DeliveryReceiptController::class, 'update'])->name('deliveryreceipt.update');

    Route::get('/deliveryreceipt-done', [DeliveryReceiptController::class, 'indexDone'])->name('deliveryreceipt.indexDone');

    Route::get('/updateDRPrintedDate/{id}', [DeliveryReceiptController::class, 'updateDRPrintedDate'])->name('deliveryreceipt.updateDRPrintedDate');

    Route::get('/api/getCustomerItems/{customerId}', [addbadorderController::class, 'getCustomerItems']);

    Route::get('/fetch-items', [addbadorderController::class, 'fetchItems'])->name('fetch.items');

    Route::get('/get-products/{inboundId}/{customerId}', [addbadorderController::class, 'getProducts']);

    Route::get('/bo-get-price/{pricelevel_id}/{p_code}', [NewBadOrderController::class, 'getPricing']);

    Route::get('/bo-get-prices-by-level/{pricelevel_id}', [NewBadOrderController::class, 'getBadOrderPricesByLevel']);

    Route::get('/bad-orders', [NewBadOrderController::class, 'index'])->name('newbo.index');

    Route::get('/bo-deducted', [NewBadOrderController::class, 'badOrdersDeducted'])->name('newbo.deducted');

    Route::delete('/bad-orders/delete', [NewBadOrderController::class, 'destroy'])->name('bo.destroy');

    Route::post('/save-temp-bad-order', [TempBadOrderController::class, 'store']);

    Route::post('/clear-temp-bad-orders', [TempBadOrderController::class, 'clear']);

    Route::get('/inventory', [DeliveryPurchaseReceiptController::class, 'index'])->name('delivery-purchase-receipts.index');

    Route::post('/delivery-purchase-receipts', [DeliveryPurchaseReceiptController::class, 'store'])->name('delivery-purchase-receipts.store');

    Route::get('/dpr-products/{dprId}', [DeliveryPurchaseReceiptController::class, 'products'])->name('drp.products');

    Route::get('/dpr-products-edit-live/{dprId}', [DeliveryPurchaseReceiptController::class, 'productsEdit'])->name('drp.products-edit');

    Route::post('/dpr-submit-live-edit', [DeliveryPurchaseReceiptController::class, 'update'])->name('drp.products-update');

    Route::post('/dpr-product/store', [DeliveryPurchaseReceiptController::class, 'storeProduct'])->name('dpr-product.store');

    Route::get('/dpr-save/{id}', [DeliveryPurchaseReceiptController::class, 'saveAndInventoryProduct'])->name('dpr.save');

    Route::post('/delivery-purchase-receipts/{dprId}/move-branch', [DeliveryPurchaseReceiptController::class, 'moveToBranch'])->middleware('can:admin')->name('dpr.moveBranch');

    Route::get('/dpr-delete/{drid}/{pcode}', [DeliveryPurchaseReceiptController::class, 'delete'])->name('dpr.delete');

    Route::post('/dpr-hold', [DeliveryPurchaseReceiptController::class, 'holdProduct'])->name('dpr.holdProduct');

    Route::delete('/dpr/{dprId}/destroy', [DeliveryPurchaseReceiptController::class, 'destroyDPR'])->middleware('can:admin')->name('dpr.destroyDPR');

    // Stock Reconciliation Tool Routes
    Route::prefix('stock-reconciliation')->name('stock-reconciliation.')->middleware(['auth'])->group(function () {
        Route::get('/', [StockReconciliationController::class, 'index'])->name('index');
        Route::get('/product/{productCode}', [StockReconciliationController::class, 'showProduct'])->name('product');
        Route::post('/fix/{productCode}', [StockReconciliationController::class, 'fixStock'])->name('fix');
        Route::get('/history/{productCode}', [StockReconciliationController::class, 'productHistory'])->name('history');
        Route::get('/reconcile-all', [StockReconciliationController::class, 'reconcileAll'])->name('reconcile-all');
    });

    Route::get('/problematic-orders', [InboundController::class, 'problematicOrders'])->name('orders.problematic');

    Route::get('/inventory-items', function () {
        return view('inventory-items');
    });

    Route::get('/branch-select', function () {
        return view('branch-select');
    })->name('branch-select');



    Route::get('/bad-order/create/{q?}', [NewBadOrderController::class, 'create'])->name('newbo.create');

    Route::get('/bad-order/edit/{q?}', [NewBadOrderController::class, 'edit'])->name('newbo.edit');

    Route::post('/bad-order/create', [NewBadOrderController::class, 'store'])->name('newbo.store');

    Route::post('/bad-order-temp-product/store', [NewBadOrderController::class, 'storeTempProduct'])->name('newbo.storetp');

    Route::delete('/newbo/{id}/delete', [NewBadOrderController::class, 'deleteTempProduct'])->name('newbo.deletetp');

    Route::delete('/newbo-item/{id}/delete', [NewBadOrderController::class, 'deleteBOItem'])->name('newbo.deleteboitem');

    Route::patch('/newbo-item/update', [NewBadOrderController::class, 'updateBOItem'])->name('newbo.updateboitem');

    Route::post('/newbo/save', [NewBadOrderController::class, 'saveBO'])->name('newbo.save');

    Route::post('/newbo/add-item', [NewBadOrderController::class, 'addItemToBO'])->name('newbo.additem');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // views
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // backend
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // backend

    Route::get('/company', [CompanyDetailsController::class, 'index'])->name('company');
    Route::get('/edit-company', [CompanyDetailsController::class, 'edit'])->name('company.edit');
    Route::put('/edit-company/{companyDetails}', [CompanyDetailsController::class, 'update'])->name('company.update');

    Route::get('/users', [UsersController::class, 'index'])->name('users');
    Route::patch('/users', [UsersController::class, 'update'])->name('user.update');
    Route::get('/user/{id}', [UsersController::class, 'delete'])->name('user.delete');

    Route::get('/branch', [BranchesController::class, 'index'])->name('branch');
    Route::post('/branch/store', [BranchesController::class, 'store']);
    Route::get('/edit-branch/{id}', [BranchesController::class, 'edit'])->name('branch.edit');

    Route::get('/customers', [CustomersController::class, 'index'])->name('customers');
    Route::get('/customers/{id}/edit', [CustomersController::class, 'edit'])->name('customer.edit');
    Route::get('/customers/create', [CustomersController::class, 'create'])->name('customer.create');
    Route::post('/customers/store', [CustomersController::class, 'store'])->name('customers.store');
    Route::delete('/customers/{id}', [CustomersController::class, 'destroy'])->name('customer.destroy');
    Route::patch('/customers', [CustomersController::class, 'update'])->name('customer.update');
    Route::delete('/customers/{customer}/store/{store}', [CustomersController::class, 'destroyStore'])->name('customer.store.destroy');

    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::post('/equipment/store', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::get('/equipment/{id}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
    Route::patch('/equipment', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/equipment/bulk-delete', [EquipmentController::class, 'bulkDelete'])->name('equipment.bulk-delete');

    Route::get('/equipment-history/{dno}', [EquipmentHistoryController::class, 'equipmentHistory'])->name('equipment.history');

    Route::get('/get-regions', [PhAddrController::class, 'getRegions']);
    Route::get('/get-provinces/{regionId}', [PhAddrController::class, 'getProvinces']);
    Route::get('/get-cities/{provinceId}', [PhAddrController::class, 'getCities']);
    Route::get('/get-brgy/{cityId}', [PhAddrController::class, 'getBrgy']);

    Route::get('/store/create', [StoreController::class, 'create'])->name('store.create');
    Route::get('/store-info', [StoreController::class, 'index'])->name('store-info.index');
    Route::post('/store-info/store', [StoreController::class, 'store'])->name('store-info.store');
    Route::delete('/store-info/{id}', [StoreController::class, 'destroy'])->name('store-info.destroy');
    Route::patch('/store-info/update', [StoreController::class, 'update'])->name('store-info.update');

    Route::get('/equipment-store', [EquipmentStoreController::class, 'index'])->name('equipment-store.index');
    Route::get('/equipment-store/create', [EquipmentStoreController::class, 'create'])->name('equipment-store.create');
    Route::post('/equipment-storestore', [EquipmentStoreController::class, 'store'])->name('equipment-store.store');
    Route::patch('/equipment-store/update', [EquipmentStoreController::class, 'update'])->name('equistore-info.update');
    Route::delete('/equipment-store/{id}', [EquipmentStoreController::class, 'destroy'])->name('equipment-store.destroy');
    Route::post('/equipment-store/update-pull-status', [EquipmentStoreController::class, 'updatePullStatus'])->name('equipment-store.updatePullStatus');

    Route::get('/vehicles', [VehiclesController::class, 'index'])->name('vehicles');
    Route::get('/vehicles/{id}/edit', [VehiclesController::class, 'edit'])->name('vehicle.edit');
    Route::get('/vehicles/create', [VehiclesController::class, 'create'])->name('vehicle.create');
    Route::post('/vehicles/store', [VehiclesController::class, 'store']);
    Route::put('/vehicles/{id}', [VehiclesController::class, 'update']);
    Route::delete('/vehicles/{id}', [VehiclesController::class, 'destroy'])->name('vehicle.destroy');
    Route::patch('/vehicles', [VehiclesController::class, 'update'])->name('vehicle.update');

    Route::get('/delivery-persons', [DriversController::class, 'index'])->name('delivery-persons');
    Route::get('/edit-delivery-person/{id}', [DriversController::class, 'edit'])->name('delivery-person.edit');
    Route::put('/dp/update', [DriversController::class, 'update'])->name('delivery-person.update');

    Route::get('/dp-details/{id}', [DriversController::class, 'getDetails'])->name('dp.details');

    Route::post('/Drivers/store', [DriversController::class, 'store']);

    Route::get('/pricing-level', [PriceLevelsController::class, 'index'])->name('pricing-level.index');
    Route::post('/pricing-level/store', [PriceLevelsController::class, 'store']);

    Route::put('/pricing-level/update', [PriceLevelsController::class, 'update'])->name('pricing-level.update');

    Route::get('/pricing', [PricesController::class, 'index'])->name('pricing.index');
    Route::post('/pricing/store', [PricesController::class, 'store']);
    Route::patch('/pricing/update', [PricesController::class, 'update'])->name('price.update');

    Route::get('/product-types', [ProductTypeController::class, 'index'])->name('productType.index');
    Route::post('/product-types', [ProductTypeController::class, 'store'])->name('productType.store');

    Route::patch('/product-types', [ProductTypeController::class, 'update'])->name('productType.update');

    Route::get('/product-variants', [ProductVariantController::class, 'index'])->name('productVariant.index');
    Route::post('/product-variants', [ProductVariantController::class, 'store'])->name('productVariant.store');

    Route::patch('/product-variants', [ProductVariantController::class, 'update'])->name('productVariant.update');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');

    Route::patch('/product/update', [ProductController::class, 'update'])->name('product.update');

    Route::get('/product/{id}/toggle-status', [ProductController::class, 'toggleStatus'])->name('product.toggleStatus');
    Route::post('/product/archive', [ProductController::class, 'archive'])->name('product.archive');
    Route::post('/product/restore', [ProductController::class, 'restore'])->name('product.restore');
    Route::get('/product-type/{id}/toggle-status', [ProductTypeController::class, 'toggleStatus'])->name('productType.toggleStatus');

    Route::get('/orders', [InboundController::class, 'index'])->name('order.index');

    Route::get('/order/create', [InboundController::class, 'create'])->name('order.create');

    Route::get('/order/{inboundId}/edit/{vm?}', [InboundController::class, 'edit'])->name('order.edit');

    Route::get('/order/{inboundId}/view', [InboundController::class, 'view'])->name('order.view');

    Route::get('/free-orders', [InboundController::class, 'freeOrders'])->name('orders.free');

    Route::get('/paid-orders', [InboundController::class, 'paidOrders'])->name('orders.paid');

    Route::put('/order/update', [InboundController::class, 'updateInbound'])->name('order.updateInbound');

    Route::get('/productsin/{code}', [InboundController::class, 'ajaxProductList'])->name('products.ajaxProductList');

    Route::get('/inboundin/{code}/{qty}/{pid}/{inboundId?}', [InboundController::class, 'ajaxInboundList'])->name('inbound.inboundList');

    Route::get('/delete-inboundin/{pcode}/{inboundId?}', [InboundController::class, 'deleteAInbound'])->name('inbound.deleteAInbound');

    Route::post('/inbound', [InboundController::class, 'store'])->name('inbound.store');

    Route::patch('/inbound/add-payment', [InboundController::class, 'addPayment'])->name('inbound.addPayment');

    Route::get('/inbound-updateProdQty/{code}/{action}/{inboundId?}', [InboundController::class, 'update'])->name('inbound.update');

    Route::delete('/inbound-destroy', [InboundController::class, 'destroy'])->name('inbound.destroy');

    Route::get('/set-branch/{code}', [BranchesController::class, 'setBranchSession'])->name('branch.setBranchSession');

    Route::get('/item-master-data', [ItemMasterDataController::class, 'index'])->name('itemdata.index');

    Route::post('/idm-addQtyFromHold', [ItemMasterDataController::class, 'addQtyFromHold'])->name('imd.addQtyFromHold');

    Route::post('/revert-order-items/{inbound}', [ItemMasterDataController::class, 'revertOrderItems'])->name('itemdata.revertOrderItems');

    Route::put('/branch', [BranchesController::class, 'update'])->name('branch.update');

    Route::post('/update-item-stocks', [ItemMasterDataController::class, 'updateStocks'])->middleware('can:admin')->name('update.item.stocks');

    Route::get('/materials-inventory', [MaterialsInventoryController::class, 'index'])->name('materialsInventory.index');
    Route::post('/materials-inventory', [MaterialsInventoryController::class, 'store'])->name('materialsInventory.store');
    Route::patch('/materials-inventory', [MaterialsInventoryController::class, 'update'])->name('materialsInventory.update');
    Route::delete('/materials-inventory', [MaterialsInventoryController::class, 'destroy'])->name('materialsInventory.destroy');
    Route::get('/materials-inventory/{id}/history', [MaterialsInventoryController::class, 'history'])->name('materialsInventory.history');
    Route::get('/materials-inventory/receive', [MaterialsInventoryController::class, 'receive'])->name('materialsInventory.receive');
    Route::post('/materials-inventory/bulk-receive', [MaterialsInventoryController::class, 'bulkReceive'])->name('materialsInventory.bulkReceive');

    Route::post('/orders/update-status', [InboundController::class, 'updateStatus'])->name('order.updateStatus');

    // Sales Invoice Management
    Route::get('/sales-invoices', [InboundController::class, 'salesInvoices'])->name('sales-invoices.index');
    Route::post('/sales-invoices/update', [InboundController::class, 'updateSalesInvoice'])->name('sales-invoices.update');
    Route::post('/sales-invoices/bulk-update', [InboundController::class, 'bulkUpdateSalesInvoice'])->name('sales-invoices.bulkUpdate');

    Route::patch('/user/reset', [UsersController::class, 'reset'])->name('user.reset');

    Route::get('/update-stocks', [ItemMasterDataController::class, 'updateStocksPage'])->middleware('can:admin')->name('update.stocks.page');

    Route::get('/bulk-update-stocks', [ItemMasterDataController::class, 'bulkUpdateStocksPage'])->middleware('can:admin')->name('bulk.update.stocks.page');
    Route::post('/bulk-update-stocks', [ItemMasterDataController::class, 'bulkUpdateStocks'])->middleware('can:admin')->name('bulk.update.stocks');

    // Material Withdrawals
    Route::get('/material-withdrawals', [MaterialWithdrawalController::class, 'index'])->name('material-withdrawals.index');
    Route::get('/material-withdrawals/list', [MaterialWithdrawalController::class, 'list'])->name('material-withdrawals.list');
    Route::get('/material-withdrawals/search', [MaterialWithdrawalController::class, 'search'])->name('material-withdrawals.search');
    Route::post('/material-withdrawals/review', [MaterialWithdrawalController::class, 'review'])->name('material-withdrawals.review');
    Route::get('/material-withdrawals/{id}/print', [MaterialWithdrawalController::class, 'print'])->name('material-withdrawals.print');
    Route::post('/material-withdrawals', [MaterialWithdrawalController::class, 'store'])->name('material-withdrawals.store');

    Route::get('/inventory/bad-orders', [InventoryBadOrderController::class, 'index'])->name('inventory.bad-orders');
    Route::get('/inventory/bad-orders/product/{productCode}', [InventoryBadOrderController::class, 'product'])->name('inventory.bad-orders.product');

    Route::get('/inventory/bad-orders/create', [InventoryBadOrderController::class, 'create'])->name('inventory.bad-orders.create');
    Route::post('/inventory/bad-orders', [InventoryBadOrderController::class, 'store'])->name('inventory.bad-orders.store');
    Route::post('/inventory/bad-orders/{badOrder}/rollback', [InventoryBadOrderController::class, 'rollback'])->name('inventory.bad-orders.rollback');
    Route::get('/report/pullout-replaced-form/{degic_no}/{customer_id}', [PullOutFormController::class, 'show'])->name('report.pullout-replaced-form');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/ajaxreq.php';