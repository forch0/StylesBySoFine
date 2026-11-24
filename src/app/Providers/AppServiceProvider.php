<?php

namespace App\Providers;

use App\Models\NavigationItem;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::define('admin', fn (User $user) => $user->isAdmin());

        // Share site settings + nav with storefront layouts
        View::composer('storefront.*', function ($view) {
            $cartCount = \App\Models\CartItem::whereHas('cart', fn ($q) => $q->where('session_id', session()->getId()))
                ->sum('quantity');

            $view->with('cartCount', (int) $cartCount);
            $view->with('navItems', \Illuminate\Support\Facades\Cache::remember(
                'nav.items', 3600,
                fn () => NavigationItem::active()->whereNull('parent_id')->with('children')->get()
            ));
            $view->with('settings', SiteSetting::getMany([
                'store_name' => 'Styles By So Fine',
                'announcement_bar' => '',
                'contact_email' => '',
                'whatsapp_number' => '',
                'instagram_url' => '',
                'footer_about' => '',
                'currency' => 'NGN',
            ]));
        });
    }
}
