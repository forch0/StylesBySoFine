<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Storefront;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', [Storefront\HomeController::class, 'index'])->name('home');
Route::get('/sitemap.xml', [Storefront\SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [Storefront\SitemapController::class, 'robots'])->name('robots');

Route::get('/shop', [Storefront\ShopController::class, 'index'])->name('shop');
Route::get('/shop/{category:slug}', [Storefront\ShopController::class, 'category'])->name('shop.category');
Route::get('/product/{product:slug}', [Storefront\ProductController::class, 'show'])->name('product.show');

Route::get('/cart', [Storefront\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [Storefront\CartController::class, 'add'])->middleware('throttle:30,1')->name('cart.add');
Route::patch('/cart/{item}', [Storefront\CartController::class, 'update'])->middleware('throttle:30,1')->name('cart.update');
Route::delete('/cart/{item}', [Storefront\CartController::class, 'remove'])->middleware('throttle:30,1')->name('cart.remove');

Route::get('/checkout', [Storefront\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [Storefront\CheckoutController::class, 'store'])->middleware('throttle:6,1')->name('checkout.store');
Route::get('/payment/callback', [Storefront\PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/webhooks/paystack', [Storefront\PaymentController::class, 'webhook'])->name('payment.webhook');

Route::get('/order/{orderNumber}/confirmation', [Storefront\OrderController::class, 'confirmation'])->name('order.confirmation');
Route::get('/track', [Storefront\OrderController::class, 'trackForm'])->name('track');
Route::post('/track', [Storefront\OrderController::class, 'track'])->middleware('throttle:10,1')->name('track.submit');

Route::get('/wishlist', [Storefront\WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/{product:uuid}', [Storefront\WishlistController::class, 'toggle'])->middleware('throttle:30,1')->name('wishlist.toggle');

Route::get('/contact', [Storefront\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [Storefront\PageController::class, 'sendContact'])->middleware('throttle:5,1')->name('contact.send');
Route::post('/newsletter', [Storefront\PageController::class, 'newsletter'])->middleware('throttle:5,1')->name('newsletter');
Route::get('/newsletter/unsubscribe/{token}', [Storefront\PageController::class, 'unsubscribeConfirm'])->name('newsletter.unsubscribe.confirm');
Route::post('/newsletter/unsubscribe/{token}', [Storefront\PageController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/pages/{page:slug}', [Storefront\PageController::class, 'show'])->name('page.show');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [Admin\AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [Admin\AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [Admin\AuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware('staff')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // POS
    Route::get('/pos', [Admin\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [Admin\PosController::class, 'store'])->name('pos.store');
    Route::get('/pos/receipt/{order}', [Admin\PosController::class, 'receipt'])->name('pos.receipt');
    Route::get('/pos/search', [Admin\PosController::class, 'search'])->name('pos.search');

    Route::resource('products', Admin\ProductController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->except('show', 'create', 'edit');
    Route::resource('collections', Admin\CollectionController::class)->except('show', 'create', 'edit');

    Route::get('/analytics', [Admin\AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/fulfilment', [Admin\OrderController::class, 'updateFulfilment'])->name('orders.fulfilment');
    Route::post('/orders/{order}/cancel', [Admin\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/notes', [Admin\OrderController::class, 'addNote'])->name('orders.notes');

    Route::get('/inventory', [Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/{variant}/adjust', [Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::get('/inventory/{variant}/history', [Admin\InventoryController::class, 'history'])->name('inventory.history');

    Route::resource('coupons', Admin\CouponController::class)->except('show', 'create', 'edit');

    // CMS
    Route::get('/homepage', [Admin\HomepageController::class, 'index'])->name('homepage.index');
    Route::post('/homepage/{preset}/activate', [Admin\HomepageController::class, 'activate'])->name('homepage.activate');
    Route::get('/homepage/{preset}/sections', [Admin\HomepageController::class, 'sections'])->name('homepage.sections');
    Route::patch('/homepage/sections/{section}', [Admin\HomepageController::class, 'updateSection'])->name('homepage.sections.update');
    Route::post('/homepage/{preset}/reorder', [Admin\HomepageController::class, 'reorder'])->name('homepage.reorder');

    Route::resource('pages', Admin\PageController::class)->except('show');
    Route::resource('navigation', Admin\NavigationController::class)->only('index', 'store', 'update', 'destroy');

    Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    Route::resource('delivery-zones', Admin\DeliveryZoneController::class)->except('show', 'create', 'edit');

    Route::get('/customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{email}', [Admin\CustomerController::class, 'show'])->name('customers.show');

    Route::get('/messages', [Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');

    Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [Admin\ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Admin-only
    Route::middleware('can:admin')->group(function () {
        Route::resource('staff', Admin\StaffController::class)->except('show', 'create', 'edit');
        Route::get('/audit-log', [Admin\AuditLogController::class, 'index'])->name('audit.index');
        Route::post('/demo/seed', [Admin\DemoController::class, 'seed'])->name('demo.seed');
        Route::post('/demo/wipe', [Admin\DemoController::class, 'wipe'])->name('demo.wipe');
    });
});
