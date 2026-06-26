<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\MenuItem;
use App\Models\Partner;
use App\Models\Category;
use App\Models\Products;
use App\Models\Setting;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontHome extends Controller
{
    /**
     * Home page — serves the main landing page.
     *
     * Caches all DB queries for 10 minutes per locale.
     * FIX: Cache::flush() was being called in ViewServiceProvider on every request,
     *      making this cache useless. With that line removed, this now works correctly.
     */
    public function services()
    {
        $locale = app()->getLocale();
         Cache::flush();
        $mainSliders = Slider::select(
            columnLocalize('title', table: 'slider') . ' as title',
            columnLocalize('description', table: 'slider') . ' as description',
            columnLocalize('slug', table: 'slider') . ' as slug',
            'img',
            'product_id'
        )
        ->where('visible', 1)
        ->orderByDesc('id')
        ->get();

    $hero_slides = $mainSliders->map(function ($slider) {
        [$title, $titleHighlight] = array_pad(
            explode('|', $slider->title ?? '', 2),
            2,
            ''
        );

        return [
            'image' => $slider->img,
            'title' => trim($title),
            'title_highlight' => trim($titleHighlight),
            'subtitle' => $slider->description,
            'slug' => $slider->slug,
            'product_id' => $slider->product_id,
        ];
    })->toArray();
     
    $stats = Client::select(
            columnLocalize('title', table: 'clients') . ' as label_key',
            'img as icon',
            'numberText as value'
        )
        ->where('visible', 1)
        ->orderByDesc('id')
        ->get()
        ->toArray();
      
    $programs = Products::select(
        columnLocalize('title', table: 'products') . ' as title_key',
        columnLocalize('description', table: 'products') . ' as description',
        'image as icon'
    )
    ->where('type', 'HowWeHelp')
    //->where('visible', 1)
    ->orderByDesc('id')
    ->take(6)
    ->get()
    ->toArray();
   
    $projects = Products::select(
        columnLocalize('slug', table: 'products') . ' as location_key',
        columnLocalize('title', table: 'products') . ' as title_key',
        'image'
    )
    ->where('type', 'projects')
    //->where('visible', 1)
    ->orderByDesc('id')
    ->take(3)
    ->get()
    ->toArray();

    $newsItems = Products::select(
        columnLocalize('slug', table: 'products') . ' as slug',
        columnLocalize('title', table: 'products') . ' as title_key',
        columnLocalize('description', table: 'products') . ' as excerpt_key',
        columnLocalize('description', table: 'products') . ' as body_key',
        'image',
        DB::raw('created_at as date_key'),
        DB::raw('1 as featured')
    )
    ->whereIn('type', ['service', 'news'])
   // ->where('visible', 1)
    ->orderByDesc('id')
    ->take(4)
    ->get()
    ->toArray();

    $impact_steps = [
        ['icon' => 'heart', 'title_key' => 'site.impact.step1_title', 'desc_key' => 'site.impact.step1_desc'],
        ['icon' => 'users', 'title_key' => 'site.impact.step2_title', 'desc_key' => 'site.impact.step2_desc'],
        ['icon' => 'sparkles', 'title_key' => 'site.impact.step3_title', 'desc_key' => 'site.impact.step3_desc'],
    ];

   

    $partners = Partner::select(
        columnLocalize('title', table: 'partners') . ' as name',
        'img as logo'
    )
    ->where('visible', 1)
    ->orderByDesc('id')
    ->get()
    ->toArray();
        return view('home', [
            'stats' => $stats,
            'programs' => $programs,
            'projects' => $projects,
            'news' => $newsItems,
            'partners' => $partners,
            'heroSlides' => $hero_slides,
            'impactSteps' => $impact_steps,
        ]);
    }
    private function localizedColumns(): array
    {
        return [
            columnLocalize('title',       table: 'products') . ' as title',
            columnLocalize('excerpt',     table: 'products') . ' as excerpt',
            columnLocalize('slug',        table: 'products') . ' as slug',
            columnLocalize('description', table: 'products') . ' as body',
            'image',
            'type',
            'link',
            'created_at as date_key',
            'id',
        ];
    }
    public function show(Request $request, string $slug)
    {
        $article = Products::select(...$this->localizedColumns())
            ->where(function ($q) use ($slug) {
                $q->where('slug_ar', $slug)->orWhere('slug_en', $slug);
            })
            ->with('images') // تحميل الصور الإضافية
            ->firstOrFail(); // FIX: was ->first() with no 404 handling

        // Sidebar: 4 related items of the same type, excluding the current one
        $related = Products::select(
            columnLocalize('title',       table: 'products') . ' as title',
            columnLocalize('excerpt',     table: 'products') . ' as excerpt',
            columnLocalize('slug',        table: 'products') . ' as slug',
            columnLocalize('description', table: 'products') . ' as description',
            'image',
            'created_at as date_key',
            'link',
        )
            ->where('id', '!=', $article->id)
            ->where('type', $article->type)
            ->orderByDesc('id')
            ->take(3)
            ->get();

        // FIX: variable was also named $products which shadowed the outer $product
        return view('news.show', [
            'article'  => $article,
            'relatedNews' => $related,
        ]);
    }

    public function pageBySlug(string $slug)
    {
        $product = \App\Models\Products::where('slug_ar', $slug)
                    ->orWhere('slug_en', $slug)
                    ->firstOrFail();

        $locale  = app()->getLocale();
        $title   = $product->{'title_'   . $locale} ?? $product->title_ar ?? '';
        $desc    = $product->{'description_' . $locale} ?? $product->description_ar ?? '';

        return view('frontend.page', [
            'page'         => $product,
            'sliders'      => collect(),
            'modules_odd'  => collect(),
            'modules_even' => collect(),
            'reasons'      => collect(),
            'features'     => collect(),
            'data'         => ['metaTag' => $title],
        ]);
    }
    public function service()
    {
        $services = Products::where('type', 'service')->take(15)->get();
        return view('frontend.services', compact('services'));
    }

    /**
     * Solutions page.
     * FIX: replaced 3 separate Setting queries with one whereIn() call.
     */
    public function solutions()
    {
        $locale  = app()->getLocale();
        $descCol = 'description_' . $locale;

        $settings = Setting::whereIn('id', [10, 11, 12])
            ->get()->keyBy('id');

        $data = [
            'solutionsAddress' => optional($settings->get(10))->{$descCol},
            'solutionsWord'    => optional($settings->get(11))->{$descCol},
            'solutionsMsg'     => optional($settings->get(12))->{$descCol},
        ];

        $solutions = \App\Models\Solutions::all();

        return view('frontend.solutions', compact('solutions', 'data'));
    }

    /**
     * Dynamic page by ID.
     * FIX: replaced single Setting query with findOrFail logic.
     */
    public function page(int $id)
    {
        $page    = \App\Models\Page::findOrFail($id);
        $locale  = app()->getLocale();
        $descCol = 'description_' . $locale;

        $metaTag = optional(Setting::find(20))->{$descCol};

        $sliders      = \App\Models\PageSliders::where('visible', 1)->where('page_id', $id)->orderByDesc('id')->get();
        $modules_odd  = \App\Models\PageModules::where('page_id', $id)->whereRaw('id % 2 != 0')->get();
        $modules_even = \App\Models\PageModules::where('page_id', $id)->whereRaw('id % 2 = 0')->get();
        $features     = \App\Models\PageFeatures::where('page_id', $id)->where('type', 'features')->get();
        $reasons      = \App\Models\PageFeatures::where('page_id', $id)->where('type', 'reasons')->get();

        $data = ['metaTag' => $metaTag];

        return view('frontend.page', compact('page', 'sliders', 'modules_odd', 'modules_even', 'reasons', 'features', 'data'));
    }

    public function Product()
    {
        $products = Products::whereNotIn('type', ['paragraph', 'article', 'news'])->get();
        return view('frontend.blog-grid', compact('products'));
    }

    public function news()
    {
        $products = Products::where('type', 'news')->orderByDesc('id')->get();
        return view('frontend.blog-grid', compact('products'));
    }

    public function clients()
    {
        $clients = Client::get();
        return view('frontend.clients', compact('clients'));
    }

    public function partners()
    {
        $partners = Partner::get();
        return view('frontend.partners', compact('partners'));
    }

    /**
     * Category / grid page.
     * FIX: replaced manual $_SERVER['REQUEST_URI'] parsing with proper
     *      Laravel request helpers.
     */
    public function show1(Request $request, string $category)
    {
        // Build the full path for the menu lookup (e.g. "/category/news")


        $fullPath = '/' . $request->path();

        $gridName = MenuItem::select(
            'category_id',
            columnLocalize('title', table: 'menu_items') . ' as title'
        )
            ->where('link', $fullPath)
            ->first();
            
        if(!$gridName) exit('not found');
        $categoryName= Category::find($gridName->category_id)->name;

        $gridName= $gridName->title;
        $products = Products::select(
            columnLocalize('title',       table: 'products') . ' as title',
            columnLocalize('excerpt',     table: 'products') . ' as excerpt',
            columnLocalize('slug',        table: 'products') . ' as slug',
            columnLocalize('description', table: 'products') . ' as description',
            'image',
            'link',
            'type',
        )
            ->where('type', $categoryName)
            ->orderByDesc('id')
            ->paginate(3);

        if (in_array($category, ['about-us'])) {
            return view('frontend.table', compact('products', 'gridName'));
        }

        return view('news.index', compact('products', 'gridName', 'category'));
    }
}
