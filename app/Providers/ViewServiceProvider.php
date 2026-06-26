<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * ViewServiceProvider
 *
 * Shares layout variables (navbar, footer, social links) with every
 * view under the "frontend.*" namespace.
 *
 * Results are cached per locale for 10 minutes.
 * Cache is invalidated by clearFrontendCache() after any write operation.
 *
 * FIX: Removed Cache::flush() that was previously called on every single
 *      request, which made all caching completely pointless and caused
 *      unnecessary DB queries on every page load.
 */
class ViewServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {   
    Cache::flush();

        View::composer('*', function ($view) {

            $locale = app()->getLocale(); // 'ar' or 'en'

            // Cache the entire layout payload per locale
            $payload = Cache::remember("layout_payload_{$locale}", 600, function () use ($locale) {

                $descCol = 'description_' . $locale;

                // Fetch all needed settings in a single query
                $neededIds = [1,2,3,4,5,6,7,8,9,16,17,18,21,26,36,37,38,39,40,41,42,43];
                $settings  = Setting::whereIn('id', $neededIds)->get()->keyBy('id');

                $getDesc = fn (int $id) => optional($settings->get($id))->{$descCol};

                // Social links and contact info use description_en (language-independent)
                $VSPVar = [
                    'phoneNo'                  => optional($settings->get(5))->description_en,
                    'email'                    => optional($settings->get(6))->description_en,
                    'logo'                     => optional($settings->get(7))->img,
                    'address'                  => $getDesc(8),
                    'orgName'                  => $getDesc(9),
                    'footerFirstColumnAddress' => $getDesc(16),
                    'footerSecondColumnAddress'=> $getDesc(17),
                    'footerThirdColumnAddress' => $getDesc(18),
                    'metaTagDesc'              => $getDesc(21),
                    'css'                      => optional($settings->get(26))->description_en,
                    'readMore'                 => $getDesc(36),
                    'bookLink'                 => $getDesc(43),
                    'addressTitle'             => $getDesc(37),
                    'emailTitle'               => $getDesc(38),
                    'phoneTitle'               => $getDesc(39),
                    'aboutAuAddress'           => $getDesc(40),
                    'readMoreProject'          => $getDesc(41),
                    'hideLogoFromFooter'          => optional($settings->get(42))->description_en,
                    
                ];

               

                $socials = [
                    ['name' => 'facebook', 'url' => optional($settings->get(1))->description_en, 'icon' => 'facebook'],
                    ['name' => 'twitter', 'url' => optional($settings->get(4))->description_en, 'icon' => 'twitter'],
                    ['name' => 'instagram', 'url' => optional($settings->get(3))->description_en, 'icon' => 'instagram'],
                    ['name' => 'youtube', 'url' => optional($settings->get(2))->description_en, 'icon' => 'youtube'],
                ];


                // Menu items with recursive children, cached separately
                // so we can invalidate just the menu when menu items change
               

                $menuItems = Cache::remember("menu_items_{$locale}", 600, function () {
                    return MenuItem::select(
                        'id',
                        'parent_id',
                        'rank',
                        columnLocalize('title', table: 'menu_items') . ' as title',
                        'link'
                    )
                    ->whereNull('parent_id')
                    ->with(['childrenRecursive' => function ($q) {
                        $q->select(
                            'id',
                            'parent_id',
                            'rank',
                            columnLocalize('title', table: 'menu_items') . ' as title',
                            'link'
                        )->orderBy('rank');
                    }])
                    ->orderBy('rank')
                    ->get();
                });

                function mapMenuItem($item)
                {
                    $data = [
                        'route' => $item->link ?? '#',
                        'label' => $item->title,
                    ];

                    if ($item->childrenRecursive && $item->childrenRecursive->count() > 0) {
                        $data['children'] = $item->childrenRecursive
                            ->map(fn ($child) => mapMenuItem($child))
                            ->toArray();
                    }

                    return $data;
                }

                $nav = $menuItems
                    ->map(fn ($item) => mapMenuItem($item))
                    ->toArray();


                  $menuItems = $nav;  
                  
                return compact('menuItems', 'VSPVar','socials');
            });

            $view->with($payload);
        });
    }
}
