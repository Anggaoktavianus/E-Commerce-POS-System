<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS only in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            // Settings
            $settings = Cache::remember('home.settings', 300, fn() => DB::table('settings')->pluck('value', 'key'));
            // Social links
            $socialLinks = Cache::remember('home.social_links', 300, fn() => DB::table('social_links')->where('is_active', true)->orderBy('sort_order')->get());

            // Header menu links (multi-level)
            $headerMenu = Cache::remember('home.header_menu', 300, fn() => DB::table('navigation_menus')->where(['location' => 'header', 'is_active' => true])->first());
            $headerLinks = Cache::remember('home.header_links', 300, function() use ($headerMenu){
                if (!$headerMenu) return collect();

                $allLinks = DB::table('navigation_links')
                    ->leftJoin('pages', 'navigation_links.page_id', '=', 'pages.id')
                    ->where('navigation_links.navigation_menu_id', $headerMenu->id)
                    ->where('navigation_links.is_active', true)
                    ->select('navigation_links.*', 'pages.slug as page_slug')
                    ->orderBy('navigation_links.sort_order')
                    ->get();

                $grouped = $allLinks->groupBy('parent_id');

                $buildTree = function ($parentId) use (&$buildTree, $grouped) {
                    $children = $grouped->get($parentId, collect());
                    return $children->map(function ($link) use (&$buildTree, $grouped) {
                        $link->children = $buildTree($link->id);
                        return $link;
                    });
                };

                return $buildTree(null);
            });

            // Footer menus
            $footerMenus = Cache::remember('home.footer_menus', 300, function(){
                return DB::table('navigation_menus')
                    ->where('location', 'like', 'footer_column_%')
                    ->where('is_active', true)
                    ->orderBy('location')
                    ->get()
                    ->map(function ($menu) {
                        $links = DB::table('navigation_links')
                            ->leftJoin('pages', 'navigation_links.page_id', '=', 'pages.id')
                            ->where('navigation_links.navigation_menu_id', $menu->id)
                            ->whereNull('navigation_links.parent_id')
                            ->where('navigation_links.is_active', true)
                            ->select('navigation_links.*', 'pages.slug as page_slug')
                            ->orderBy('navigation_links.sort_order')
                            ->get();
                        $menu->links = $links;
                        return $menu;
                    });
            });

            $view->with([
                'settings' => $settings,
                'socialLinks' => $socialLinks,
                'headerLinks' => $headerLinks,
                'footerMenus' => $footerMenus,
            ]);
        });
    }
}
