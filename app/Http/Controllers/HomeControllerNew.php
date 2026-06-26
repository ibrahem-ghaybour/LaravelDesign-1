<?php

namespace App\Http\Controllers;

use App\Repositories\NewsRepository;
use Illuminate\View\View;
use App\Models\Slider;
use App\Models\Client;
use App\Models\Products;
use App\Models\Partner;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(): View
    {   
    
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
}
